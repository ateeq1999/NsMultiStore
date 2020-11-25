<?php
namespace App\Crud;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Services\CrudService;
use App\Services\Users;
use App\Exceptions\NotAllowedException;
use App\Models\User;
use App\Services\Helper;
use TorMorten\Eventy\Facades\Events as Hook;
use Exception;
use Modules\NsMultiStore\Jobs\DismantleStoreJob;
use Modules\NsMultiStore\Jobs\SetupStoreJob;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;

class StoreCrud extends CrudService
{
    /**
     * define the base table
     * @param  string
     */
    protected $table      =   'nexopos_stores';

    /**
     * default identifier
     * @param  string
     */
    protected $identifier   =   'multistore/stores';

    /**
     * Define namespace
     * @param  string
     */
    protected $namespace  =   'ns.multistore';

    /**
     * Model Used
     * @param  string
     */
    protected $model      =   Store::class;

    /**
     * Define permissions
     * @param  array
     */
    protected $permissions  =   [
        'create'    =>  true,
        'read'      =>  true,
        'update'    =>  true,
        'delete'    =>  true,
    ];

    /**
     * Adding relation
     * @param  array
     */
    public $relations   =  [
        [ 'nexopos_users as user', 'user.id', '=', 'nexopos_stores.author' ]
    ];

    /**
     * all tabs mentionned on the tabs relations
     * are ignored on the parent model.
     */
    protected $tabsRelations    =   [
        // 'tab_name'      =>      [ YourRelatedModel::class, 'localkey_on_relatedmodel', 'foreignkey_on_crud_model' ],
    ];

    /**
     * Pick
     * Restrict columns you retreive from relation.
     * Should be an array of associative keys, where 
     * keys are either the related table or alias name.
     * Example : [
     *      'user'  =>  [ 'username' ], // here the relation on the table nexopos_users is using "user" as an alias
     * ]
     */
    public $pick        =   [];

    /**
     * Define where statement
     * @var  array
    **/
    protected $listWhere    =   [];

    /**
     * Define where in statement
     * @var  array
     */
    protected $whereIn      =   [];

    /**
     * Fields which will be filled during post/put
     */
        public $fillable    =   [];

    /**
     * Define Constructor
     * @param  
     */
    public function __construct()
    {
        parent::__construct();

        Hook::addFilter( $this->namespace . '-crud-actions', [ $this, 'setActions' ], 10, 2 );
    }

    /**
     * Return the label used for the crud 
     * instance
     * @return  array
    **/
    public function getLabels()
    {
        return [
            'list_title'            =>  __( 'Stores List' ),
            'list_description'      =>  __( 'Display all stores.' ),
            'no_entry'              =>  __( 'No stores has been registered' ),
            'create_new'            =>  __( 'Add a new store' ),
            'create_title'          =>  __( 'Create a new store' ),
            'create_description'    =>  __( 'Register a new store and save it.' ),
            'edit_title'            =>  __( 'Edit store' ),
            'edit_description'      =>  __( 'Modify  Store.' ),
            'back_to_list'          =>  __( 'Return to Stores' ),
        ];
    }

    /**
     * Check whether a feature is enabled
     * @return  boolean
    **/
    public function isEnabled( $feature )
    {
        return false; // by default
    }

    /**
     * Fields
     * @param  object/null
     * @return  array of field
     */
    public function getForm( $entry = null ) 
    {
        return [
            'main' =>  [
                'label'         =>  __( 'Name' ),
                'name'          =>  'name',
                'value'         =>  $entry->name ?? '',
                'description'   =>  __( 'Provide a name to the resource.' )
            ],
            'tabs'  =>  [
                'general'   =>  [
                    'label'     =>  __( 'General' ),
                    'fields'    =>  [
                        [
                            'type'  =>  'switch',
                            'name'  =>  'status',
                            'description'   =>  __( 'Determine wether the store is available or not.' ),
                            'options'   =>  Helper::kvToJsOptions([
                                Store::STATUS_CLOSED =>  __( 'Closed' ),
                                Store::STATUS_OPENED =>  __( 'Opened' )
                            ]),
                            'label' =>  __( 'Status' ),
                            'value' =>  $entry->status ?? '',
                        ], [
                            'type'          =>  'media',
                            'name'          =>  'thumb',
                            'label'         =>  __( 'Preview' ),
                            'description'   =>  __( 'A graphical preview of the store.' ),
                            'value'         =>  $entry->thumb ?? '',
                        ], [
                            'type'  =>  'textarea',
                            'name'  =>  'description',
                            'description'   =>  __( 'Further details about the store.' ),
                            'label' =>  __( 'Description' ),
                            'value' =>  $entry->description ?? '',
                        ], 
                    ]
                ]
            ]
        ];
    }

    /**
     * Filter POST input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPostInputs( $inputs )
    {
        $inputs[ 'slug' ]       =   Str::slug( $inputs[ 'name' ] );
        $inputs[ 'status' ]     =   Store::STATUS_BUILDING;
        $inputs[ 'author' ]     =   Auth::id();

        // we shoudl create tables for that store

        return $inputs;
    }

    /**
     * Filter PUT input fields
     * @param  array of fields
     * @return  array of fields
     */
    public function filterPutInputs( $inputs, Store $entry )
    {
        $inputs[ 'slug' ]       =   Str::slug( $inputs[ 'name' ] );
        $inputs[ 'author' ]     =   Auth::id();

        return $inputs;
    }

    /**
     * Before saving a record
     * @param  Request $request
     * @return  void
     */
    public function beforePost( $request )
    {
        if ( $this->permissions[ 'create' ] !== false ) {
            ns()->restrict( $this->permissions[ 'create' ] );
        } else {
            throw new NotAllowedException;
        }

        return $request;
    }

    /**
     * After saving a record
     * @param  Request $request
     * @param  Store $entry
     * @return  void
     */
    public function afterPost( $request, Store $store )
    {
        SetupStoreJob::dispatch( $store )
            ->delay( now() );
        
        return $request;
    }

    
    /**
     * get
     * @param  string
     * @return  mixed
     */
    public function get( $param )
    {
        switch( $param ) {
            case 'model' : return $this->model ; break;
        }
    }

    /**
     * Before updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function beforePut( $request, $entry )
    {
        if ( $this->permissions[ 'update' ] !== false ) {
            ns()->restrict( $this->permissions[ 'update' ] );
        } else {
            throw new NotAllowedException;
        }

        return $request;
    }

    /**
     * After updating a record
     * @param  Request $request
     * @param  object entry
     * @return  void
     */
    public function afterPut( $request, $entry )
    {
        return $request;
    }

    /**
     * Before Delete
     * @return  void
     */
    public function beforeDelete( $namespace, $id, $model ) {
        if ( $namespace == 'ns.multistore' ) {
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }

            DismantleStoreJob::dispatch( $model )
                ->delay( now() );

            $model->status      =   Store::STATUS_DISMANTLING;
            $model->save();

            return [
                'status'    =>  'success',
                'message'   =>  sprintf( __( '"%s" is about to be dismantled' ), $model->name )
            ];
        }
    }

    /**
     * Define Columns
     * @return  array of columns configuration
     */
    public function getColumns() {
        return [
            'name'  =>  [
                'label'  =>  __( 'Name' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'status'  =>  [
                'label'  =>  __( 'Status' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'created_at'  =>  [
                'label'  =>  __( 'Created At' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ],
            'user_username'  =>  [
                'label'  =>  __( 'Author' ),
                '$direction'    =>  '',
                '$sort'         =>  false
            ]
        ];
    }

    /**
     * Define actions
     */
    public function setActions( $entry, $namespace )
    {
        // Don't overwrite
        $entry->{ '$checked' }  =   false;
        $entry->{ '$toggled' }  =   false;
        $entry->{ '$id' }       =   $entry->id;

        switch( $entry->status ) {
            case Store::STATUS_OPENED :         $entry->status = __( 'Opened' );break;
            case Store::STATUS_BUILDING :       $entry->status = __( 'Building' );break;
            case Store::STATUS_CLOSED :         $entry->status = __( 'Closed' );break;
            case Store::STATUS_DISMANTLING :    $entry->status = __( 'Dismantling' );break;
            default: $entry->status = __( 'Unknown Status' );break;
        }

        // you can make changes here
        $entry->{'$actions'}    =   [
            [
                'label'         =>      __( 'Edit' ),
                'namespace'     =>      'edit',
                'type'          =>      'GOTO',
                'url'           =>      url( '/dashboard/' . 'multistore/stores' . '/edit/' . $entry->id )
            ], [
                'label'     =>  __( 'Delete' ),
                'namespace' =>  'delete',
                'type'      =>  'DELETE',
                'url'       =>  url( '/api/nexopos/v4/crud/ns.multistore/' . $entry->id ),
                'confirm'   =>  [
                    'message'  =>  __( 'Would you like to delete this ?' ),
                ]
            ]
        ];

        return $entry;
    }

    
    /**
     * Bulk Delete Action
     * @param    object Request with object
     * @return    false/array
     */
    public function bulkAction( Request $request ) 
    {
        /**
         * Deleting licence is only allowed for admin
         * and supervisor.
         */

        if ( $request->input( 'action' ) == 'delete_selected' ) {

            /**
             * Will control if the user has the permissoin to do that.
             */
            if ( $this->permissions[ 'delete' ] !== false ) {
                ns()->restrict( $this->permissions[ 'delete' ] );
            } else {
                throw new NotAllowedException;
            }

            $status     =   [
                'success'   =>  0,
                'failed'    =>  0
            ];

            foreach ( $request->input( 'entries' ) as $id ) {
                $entity     =   $this->model::find( $id );
                if ( $entity instanceof Store ) {
                    $entity->delete();
                    $status[ 'success' ]++;
                } else {
                    $status[ 'failed' ]++;
                }
            }
            return $status;
        }

        return Hook::filter( $this->namespace . '-catch-action', false, $request );
    }

    /**
     * get Links
     * @return  array of links
     */
    public function getLinks()
    {
        return  [
            'list'      =>  url( 'dashboard/' . 'multistore/stores' ),
            'create'    =>  url( 'dashboard/' . 'multistore/stores/create' ),
            'edit'      =>  url( 'dashboard/' . 'multistore/stores/edit/' ),
            'post'      =>  url( 'api/nexopos/v4/crud/' . 'ns.multistore' ),
            'put'       =>  url( 'api/nexopos/v4/crud/' . 'ns.multistore/{id}' . '' ),
        ];
    }

    /**
     * Get Bulk actions
     * @return  array of actions
    **/
    public function getBulkActions()
    {
        return Hook::filter( $this->namespace . '-bulk', [
            [
                'label'         =>  __( 'Delete Selected Groups' ),
                'identifier'    =>  'delete_selected',
                'url'           =>  route( 'crud.bulk-actions', [
                    'namespace' =>  $this->namespace
                ])
            ]
        ]);
    }

    /**
     * get exports
     * @return  array of export formats
    **/
    public function getExports()
    {
        return [];
    }
}
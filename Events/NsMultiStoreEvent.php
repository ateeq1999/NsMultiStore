<?php
namespace Modules\NsMultiStore\Events;

use App\Classes\Output;
use Modules\NsMultiStore\Crud\StoreCrud;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use App\Exceptions\NotAllowedException;
use Modules\NsMultiStore\Services\StoresService;
use Modules\NsMultiStore\Http\Middleware\DetectStoreMiddleware;

/**
 * Register Events
**/
class NsMultiStoreEvent
{
    static $ignored_tables  =   [
        'nexopos_users',
        'nexopos_roles',
        'nexopos_permissions',
        'nexopos_role_permission',
        'nexopos_stores',
    ];

    static $routePrefix     =   'ns.multistore--';

    /**
     * Registers the crud component for 
     * creating and managing stores
     * @param string
     * @return string|\App\Services\Crud;
     */
    public static function registerCrud( $crud )
    {
        switch( $crud ) {
            case 'ns.multistore': return StoreCrud::class;
            default: return $crud;
        }
    }

    /**
     * Will overwite the way the url 
     * helper is being used while on sub store
     * @param string url
     * @return string;
     */
    public static function setUrl( $url )
    {
        if ( ns()->store->isMultiStore() ) {

            $isDashboardUrl     =   Str::of( url( $url ) )->contains( url( '/dashboard/' ) );
            $isApiUrl           =   Str::of( url( $url ) )->contains( url( '/api/' ) );

            /**
             * if while dividing the url using the segment "/dashboard"
             * the result equals 2, then that means the URL point to the "dashboard"
             * and not the "api".
             */
            if ( $isDashboardUrl ) {
                $url    =   collect( explode( 'dashboard', $url ) )
                    ->splice( 1 )
                    ->prepend( 'dashboard/store/' . ns()->store->getCurrentStore()->id )
                    ->prepend( url('/') )
                    ->map( fn( $slice ) => ( string ) Str::of( $slice )->trim('/') )
                    ->join( '/' );
            } else if ( $isApiUrl ) {
                $url    =   collect( explode( 'nexopos/v4', $url ) )
                    ->splice( 1 )
                    ->prepend( 'api/nexopos/v4/store/' . ns()->store->getCurrentStore()->id )
                    ->prepend( url('/') )
                    ->map( fn( $slice ) => ( string ) Str::of( $slice )->trim('/') )
                    ->join( '/' );
            }
        }

        return $url;
    }

    /**
     * register menus
     * @param array menus
     * @return array
     */
    public static function dashboardMenus( $menus )
    {
        /**
         * If we're browsing the multistore
         * let's display the default menus.
         */
        if ( ns()->store->isMultiStore() ) {
            unset( $menus[ 'modules' ] );
            unset( $menus[ 'users' ] );
            return $menus;
        }

        $menus  =   array_insert_before( $menus, 'modules', [
            'ns.multistore-dashboard'    =>  [
                'label'     =>  __( 'Dashboard' ),
                'icon'      =>  'la-home',
                'href'      =>  route( 'ns.multistore-dashboard' ),
            ]
        ]);

        $menus  =   array_insert_before( $menus, 'modules', [
            'ns.multistore-stores'    =>  [
                'label'     =>  __( 'Stores' ),
                'icon'      =>  'la-store',
                'childrens' =>  [
                    [
                        'label' =>  __( 'List' ),
                        'href'      =>  route( 'ns.multistore-stores' ),
                    ], [
                        'label' =>  __( 'Create' ),
                        'href'      =>  route( 'ns.multistore-stores.create' ),
                    ]
                ]
            ]
        ]);

        // $menus  =   array_insert_before( $menus, 'modules', [
        //     'ns.multistore-settings'    =>  [
        //         'label'     =>  __( 'MultiStore Settings' ),
        //         'icon'      =>  'la-cogs',
        //         'href'      =>  route( 'ns.multistore-settings' ),
        //     ]
        // ]);

        return collect( $menus )->filter( function( $menu, $index ) {
            return in_array( $index, [ 'modules', 'ns.multistore-settings', 'ns.multistore-dashboard', 'ns.multistore-stores', 'users' ] );
        });
    }

    /**
     * This will make sure to overwrite the route
     * when the system is browing a single store
     * @param boolean 
     * @param string
     * @param array
     * @return boolean|string
     */
    public static function builRoute( $final, $route, $params )
    {
        if ( ns()->store->isMultiStore() ) {
            if ( in_array( $route, [
                'ns.dashboard.modules-list',
                'ns.dashboard.modules-upload',
                'ns.dashboard.modules-upload',
                'ns.dashboard.modules-migrate',
                'ns.dashboard.users.profile',
                'ns.login',
                'ns.register',
                'ns.register.post',
                'ns.logout',
                'ns.database-update',
            ]) ) {
                return route( $route );
            }

            return route( self::$routePrefix . $route, array_merge([
                'store_id'  =>  ns()->store->getCurrentStore()->id,
            ], $params ) );
        } else {
            switch( $route ) {
                case 'ns.dashboard.home':
                    return route( 'ns.multistore-dashboard' );
            }
        }

        return $final;
    }

    /**
     * We'll inject the store selection menu.
     */
    public static function overWriteHeader( $path )
    {
        return 'NsMultiStore::dashboard.header';
    }

    /**
     * Will provide a prefix on every named rounde
     * that are being registered as a sub store route
     * @param string
     * @return string
     */
    public static function customizeRouteNames( $name )
    {
        if ( ns()->store->isMultiStore() ) {
            return self::$routePrefix . $name;
        } 

        return $name;
    }

    /**
     * We might want to check wether the user has some permission
     * to access the multistore dashboard. Preferably, we need to create
     * various dasboard for users roles, 
     */
    public static function defaultRouteAfterAuthentication( $route, $hadIntension )
    {
        if ( ! $hadIntension ) {
            return route( 'ns.multistore-dashboard' );
        }

        return $route;
    }

    /**
     * will force run the middleware on common routes
     * and check if an unauthorized access is detected
     */
    public static function disableDefaultComponents( $response, $request, $next )
    {
        $detectStoreMiddleware  =   new DetectStoreMiddleware;
        $response               =   $detectStoreMiddleware->handle( $request, $next );

        if ( ! ns()->store->isMultiStore() ) {
            throw new NotAllowedException( __( 'Unable to access to this page when the multistore is enabled.' ) );
        }

        return $response;
    }

    /**
     * will prefix model table while the 
     * model is being used on  multistore
     * @param string $table
     * @return string
     */
    public static function prefixModelTable( $table )
    {
        $table  =   trim( $table );

        if ( ns()->store->isMultiStore() && ! in_array( trim( $table ), self::$ignored_tables ) ) {
            return 'store_' . ns()->store->getCurrentStore()->id . '_' . $table;
        }

        return $table;
    }

    public static function injectHttpClientListener( Output $output )
    {
        $output->addView( 'NsMultiStore::dashboard.footer-http-overrider' );
    }

    /**
     * We would like to prevent the store table to be ereased
     * while performing a reset.
     * @param string $table name
     * @return mixed $table or boolean
     */
    public static function preventTableTruncatingOnMultiStore( $table ) {
        if (  in_array( $table, [
            'nexopos_stores',
            'nexopos_options',
        ] ) ) {
            return false;
        }

        return $table;
    }

    public static function changeMediaDirectory( $path )
    {
        if ( ns()->store->isMultiStore() ) {
            return 'store_' . ns()->store->getCurrentStore()->id . DIRECTORY_SEPARATOR . $path;
        }

        return $path;
    }
}
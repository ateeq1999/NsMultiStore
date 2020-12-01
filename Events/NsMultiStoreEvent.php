<?php
namespace Modules\NsMultiStore\Events;

use App\Crud\StoreCrud;
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
            $url    =   collect( explode( '/dashboard', $url ) )
                ->skipUntil( fn( $value, $index ) => $index === 0 )
                ->prepend( ns()->store->getStoreID() . '/' )
                ->prepend( 'store/' )
                ->prepend( 'dashboard/' )
                ->filter( fn( $slug ) => ! empty( $slug ) )
                ->join('');

            return Str::replaceArray( '//', [ '/' ], $url );
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

        $menus  =   array_insert_before( $menus, 'modules', [
            'ns.multistore-settings'    =>  [
                'label'     =>  __( 'MultiStore Settings' ),
                'icon'      =>  'la-cogs',
                'href'      =>  route( 'ns.multistore-settings' ),
            ]
        ]);

        return collect( $menus )->filter( function( $menu, $index ) {
            return in_array( $index, [ 'modules', 'ns.multistore-settings', 'ns.multistore-dashboard', 'ns.multistore-stores' ] );
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
            return route( $route, [
                'store_id'  =>  ns()->store->getStoreID(),
                ...$params
            ]);
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
            return 'multistore-' . $name;
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
        $newRequest             =   $detectStoreMiddleware->handle( $request, $next );

        if ( ! ns()->store->isMultiStore() ) {
            throw new NotAllowedException( __( 'Unable to access to this page when the multistore is enabled.' ) );
        }

        return $response;
    }

    public static function overWriteDashboardRoute( $route )
    {
        return 'ns.multistore-dashboard';
    }
}
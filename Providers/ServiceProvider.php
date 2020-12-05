<?php
namespace Modules\NsMultiStore\Providers;

use App\Classes\Hook;
use App\Providers\ModulesServiceProvider;
use App\Services\CustomerService;
use App\Services\ModulesService;
use App\Services\OrdersService;
use Modules\NsMultiStore\Events\NsMultiStoreEvent;
use Modules\NsMultiStore\Listeners\StoreOrderEventSubscriber;
use Modules\NsMultiStore\Services\StoresService;

class ServiceProvider extends ModulesServiceProvider
{
    public function register()
    {
        $this->app->singleton( StoresService::class, fn() => new StoresService );
        
        ns()->store  =   app()->make( StoresService::class );
    
        Hook::addFilter( 'ns-dashboard-menus', [ NsMultiStoreEvent::class, 'dashboardMenus' ]);
        Hook::addFilter( 'ns.crud-resource', [ NsMultiStoreEvent::class, 'registerCrud' ]);
        Hook::addFilter( 'ns-route', [ NsMultiStoreEvent::class, 'builRoute' ], 10, 3 );
        Hook::addFilter( 'ns-dashboard-header', [ NsMultiStoreEvent::class, 'overWriteHeader' ]);
        Hook::addFilter( 'ns-url', [ NsMultiStoreEvent::class, 'setUrl' ]);
        Hook::addFilter( 'ns-route-name', [ NsMultiStoreEvent::class, 'customizeRouteNames' ]);
        Hook::addFilter( 'ns-table-name', [ NsMultiStoreEvent::class, 'prefixModelTable' ]);
        Hook::addFilter( 'ns-common-routes', [ NsMultiStoreEvent::class, 'disableDefaultComponents' ], 10, 3 ); 
        Hook::addFilter( 'ns-login-redirect', [ NsMultiStoreEvent::class, 'defaultRouteAfterAuthentication' ], 10, 2 ); 
        Hook::addFilter( 'ns-model-table',[ NsMultiStoreEvent::class, 'prefixModelTable' ]);
        hook::addAction( 'ns-dashboard-footer', [ NsMultiStoreEvent::class, 'injectHttpClientListener' ]);
        hook::addFilter( 'ns-reset-table', [ NsMultiStoreEvent::class, 'preventTableTruncatingOnMultiStore' ]);
        hook::addFilter( 'ns-media-path', [ NsMultiStoreEvent::class, 'changeMediaDirectory' ]);
        hook::addFilter( 'ns-register-subscribers', fn() => false );
    }

    public function boot( ModulesService $moduleService )
    {
        // ...
    }
}
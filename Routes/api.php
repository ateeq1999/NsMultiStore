<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Modules\NsMultiStore\Http\Controllers\MultiStoreController;
use Modules\NsMultiStore\Http\Middleware\DetectStoreMiddleware;
use Modules\NsMultiStore\Models\Store;

Route::prefix( 'nexopos/v4' )
    ->middleware([ 'auth:sanctum' ])
    ->group( function() {
        Route::get( '/multistores/stores', [ MultiStoreController::class, 'getStores' ]);
        Route::get( '/multistores/stores-details', [ MultiStoreController::class, 'getStoreDetails' ]);
        Route::prefix( '/store/{store_id}' )
            ->middleware([ 
                DetectStoreMiddleware::class . ':api', 
                SubstituteBindings::class 
            ])
            ->group( function() {
                ns()->store->defineStoreRoutes( function() {
                    include( base_path( 'routes/api/dashboard.php' ) );    
                    include( base_path( 'routes/api/categories.php' ) );    
                    include( base_path( 'routes/api/customers.php' ) );
                    include( base_path( 'routes/api/expenses.php' ) );
                    include( base_path( 'routes/api/medias.php' ) );
                    include( base_path( 'routes/api/notifications.php' ) );
                    include( base_path( 'routes/api/orders.php' ) );
                    include( base_path( 'routes/api/procurements.php' ) );
                    include( base_path( 'routes/api/products.php' ) );
                    include( base_path( 'routes/api/fields.php' ) );
                    include( base_path( 'routes/api/providers.php' ) );
                    include( base_path( 'routes/api/registers.php' ) );
                    include( base_path( 'routes/api/reset.php' ) );
                    include( base_path( 'routes/api/settings.php' ) );
                    include( base_path( 'routes/api/rewards.php' ) );
                    include( base_path( 'routes/api/transfer.php' ) );
                    include( base_path( 'routes/api/taxes.php' ) );
                    include( base_path( 'routes/api/crud.php' ) );
                    include( base_path( 'routes/api/forms.php' ) );
                    include( base_path( 'routes/api/units.php' ) );
                });
            });
});
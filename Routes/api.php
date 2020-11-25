<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\NsMultiStore\Http\Controllers\MultiStoreController;

Route::prefix( 'nexopos/v4' )
    ->middleware([ 'auth:sanctum' ])
    ->group( function() {
        Route::get( '/multistores/stores', [ MultiStoreController::class, 'getStores' ]);

        Route::prefix( '/store/{store_id}' )
            ->middleware([ DetectStoreMiddleware::class ])
            ->group( function() {
                /**
                 * Normally, the store id is detected from the URL. However while defining
                 * the routes, we need to fakely define the store ID to enforce stores route
                 * has specifice and unique name.
                 */
                ns()->store->setStoreID( true );

                include( base_path( 'routes/api/dashboard.php' ) );    
                include( base_path( 'routes/api/categories.php' ) );    
                include( base_path( 'routes/api/customers.php' ) );
                include( base_path( 'routes/api/expenses.php' ) );
                include( base_path( 'routes/api/modules.php' ) );
                include( base_path( 'routes/api/medias.php' ) );
                include( base_path( 'routes/api/notifications.php' ) );
                include( base_path( 'routes/api/orders.php' ) );
                include( base_path( 'routes/api/procurements.php' ) );
                include( base_path( 'routes/api/products.php' ) );
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
                include( base_path( 'routes/api/users.php' ) );

                /**
                 * As it's not more needed, we'll
                 * clear the fakely defined URL
                 */
                ns()->store->clearStoreID();
            });
});
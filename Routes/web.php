<?php

use App\Http\Middleware\CheckMigrationStatus;
use Illuminate\Support\Facades\Route;
use Modules\NsMultiStore\Http\Controllers\MultiStoreController;
use Modules\NsMultiStore\Http\Middleware\DetectStoreMiddleware;

Route::middleware([ CheckMigrationStatus::class, 'auth' ])->group( function() {
    Route::get( '/dashboard/multistore', [ MultiStoreController::class, 'home' ])->name( 'ns.multistore-dashboard' );
    Route::get( '/dashboard/multistore/settings', [ MultiStoreController::class, 'settings' ])->name( 'ns.multistore-settings' );
    Route::get( '/dashboard/multistore/stores', [ MultiStoreController::class, 'stores' ])->name( 'ns.multistore-stores' );
    Route::get( '/dashboard/multistore/stores/create', [ MultiStoreController::class, 'create' ])->name( 'ns.multistore-stores.create' );
    Route::get( '/dashboard/multistore/stores/edit/{store}', [ MultiStoreController::class, 'edit' ])->name( 'ns.multistore-stores.edit' );
});

Route::prefix( '/dashboard/store/{store_id}' )
    ->middleware([ DetectStoreMiddleware::class, 'auth' ])
    ->group( function() {
        /**
         * Normally, the store id is detected from the URL. However while defining
         * the routes, we need to fakely define the store ID to enforce stores route
         * has specifice and unique name.
         */
        ns()->store->setStoreID( true );

        include( base_path() . '/routes/nexopos.php' );

        /**
         * As it's not more needed, we'll
         * clear the fakely defined URL
         */
        ns()->store->clearStoreID();
});
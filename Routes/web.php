<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\CheckMigrationStatus;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Support\Facades\Route;
use Modules\NsMultiStore\Http\Controllers\MultiStoreController;
use Modules\NsMultiStore\Http\Middleware\DetectStoreMiddleware;
use Modules\NsMultiStore\Models\Store;

Route::middleware([ CheckMigrationStatus::class, SubstituteBindings::class, 'auth' ])->group( function() {
    Route::get( '/dashboard/multistore', [ MultiStoreController::class, 'home' ])->name( 'ns.multistore-dashboard' );
    Route::get( '/dashboard/multistore/settings', [ MultiStoreController::class, 'settings' ])->name( 'ns.multistore-settings' );
    Route::get( '/dashboard/multistore/stores', [ MultiStoreController::class, 'stores' ])->name( 'ns.multistore-stores' );
    Route::get( '/dashboard/multistore/stores/create', [ MultiStoreController::class, 'create' ])->name( 'ns.multistore-stores.create' );
    Route::get( '/dashboard/multistore/stores/edit/{store}', [ MultiStoreController::class, 'edit' ])->name( 'ns.multistore-stores.edit' );
});

Route::prefix( '/dashboard/store/{store_id}' )
    ->middleware([ 
        DetectStoreMiddleware::class, 
        Authenticate::class, 
        SubstituteBindings::class 
    ])
    ->group( function() {
        ns()->store->defineStoreRoutes( function() {
            include( base_path() . '/routes/nexopos.php' );
        });
});
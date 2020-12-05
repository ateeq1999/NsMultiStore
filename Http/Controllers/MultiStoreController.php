<?php

/**
 * NexoPOS MultiStore Controller
 * @since  1.0
 * @package  modules/NsMultiStore
**/

namespace Modules\NsMultiStore\Http\Controllers;

use Modules\NsMultiStore\Crud\StoreCrud;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;
use App\Services\DateService;
use Illuminate\Support\Facades\DB;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;

class MultiStoreController extends DashboardController
{
    /**
     * @var StoresService
     */
    protected $storesService;

    /**
     * @var DateService
     */
    protected $dateService;

    public function __construct(
        StoresService $stores,
        DateService $dateService
    )
    {
        parent::__construct();

        $this->storesService    =   $stores;
        $this->dateService      =   $dateService;
    }

    /**
     * Index Controller Page
     * @return  view
     * @since  1.0
    **/
    public function home()
    {
        return $this->view( 'NsMultiStore::dashboard.home', [
            'title'     =>      __( 'MultiStore Dashboard' )
        ]);
    }

    public function stores()
    {
        return StoreCrud::table();
    }

    public function create()
    {
        return StoreCrud::form();
    }

    public function edit( Store $store )
    {
        return StoreCrud::form( $store );
    }

    public function getStores()
    {
        return $this->storesService->getOpened();
    }

    public function getStoreDetails()
    {
        return Store::status( Store::STATUS_OPENED )
            ->get()
            ->map( function( $store ) {
                $today                      =   $this->dateService->copy()->now();
                $yesterday                  =   $this->dateService->copy()->now()->subDay();

                $store->yesterday_report    =   DB::table( 'store_' . $store->id . '_nexopos_dashboard_days' )
                    ->where( 'range_starts', '>=', $yesterday->startOfDay()->toDateTimeString() )
                    ->where( 'range_ends', '<=', $yesterday->endOfDay()->toDateTimeString() )
                    ->first();

                $store->today_report        =   DB::table( 'store_' . $store->id . '_nexopos_dashboard_days' )
                    ->where( 'range_starts', '>=', $today->startOfDay()->toDateTimeString() )
                    ->where( 'range_ends', '<=', $today->endOfDay()->toDateTimeString() )
                    ->first();

                return $store;
            });
    }
}

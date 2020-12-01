<?php

/**
 * NexoPOS MultiStore Controller
 * @since  1.0
 * @package  modules/NsMultiStore
**/

namespace Modules\NsMultiStore\Http\Controllers;

use App\Crud\StoreCrud;
use Illuminate\Support\Facades\View;
use App\Http\Controllers\DashboardController;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;

class MultiStoreController extends DashboardController
{
    /**
     * @var StoresService
     */
    protected $storesService;

    public function __construct(
        StoresService $stores
    )
    {
        parent::__construct();
        $this->storesService    =   $stores;
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
}

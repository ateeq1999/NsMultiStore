<?php
namespace Modules\NsMultiStore\Listeners;

use App\Models\DashboardDay;
use Modules\NsMultiStore\Jobs\StoreComputeDayReportJob;
use Modules\NsMultiStore\Jobs\StoreInitializeDailyReportJob;
use Modules\NsMultiStore\Models\Store;

class QueueListeners 
{
    public static function dispatchInitStoreReport( $event )
    {
        if ( ! DashboardDay::forToday() instanceof DashboardDay ) {
            StoreInitializeDailyReportJob::dispatch( ns()->store->getCurrentStore() )
                ->delay( now() );
        }
    }

    public static function handleOrderUpdate()
    {
        StoreComputeDayReportJob::dispatch( ns()->store->getCurrentStore() )
            ->delay( now() );
    }
}
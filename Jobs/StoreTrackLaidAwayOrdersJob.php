<?php
namespace Modules\NsMultiStore\Jobs;

use App\Jobs\ExecuteExpensesJob;
use App\Jobs\PurgeOrderStorageJob;
use App\Jobs\TrackLaidAwayOrdersJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreTrackLaidAwayOrdersJob extends TrackLaidAwayOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        Store::status( Store::STATUS_OPENED )
            ->get()
            ->each( function( $store ) {
                ns()->store->setStore( $store );
                parent::handle();
            });
    }
}
<?php 
namespace Modules\NsMultiStore\Jobs;

use App\Events\ProductAfterStockAdjustmentEvent;
use App\Jobs\HandleStockAdjustmentJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreHandleStockAdjustmentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $history;
    public $store;

    public function __construct( Store $store, ProductAfterStockAdjustmentEvent $history )
    {   
        $this->history      =   $history;
        $this->store        =   $store;
    }

    public function handle()
    {
        ns()->store->setStore( $this->store );
        ( new HandleStockAdjustmentJob( $this->event ) )->handle();
    }
}
<?php

namespace Modules\NsMultiStore\Jobs;

use App\Jobs\ComputeCashierSalesJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreComputeCashierSalesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Store
     */
    public $store;
    public $event;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Store $store, $event )
    {
        $this->store    =   $store;
        $this->event    =   $event;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ns()->store->setStore( $this->store );

        ( new ComputeCashierSalesJob( $this->event ) )->handle();

        return false;
    }
}

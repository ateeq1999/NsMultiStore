<?php
namespace Modules\NsMultiStore\Jobs;

use App\Jobs\ComputeDashboardExpensesJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreComputeDashboardExpensesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct( Store $store, $event )
    {
        $this->store    =   $store;
        $this->event    =   $event;
    }

    public function handle()
    {
        ns()->store->setStore( $this->store );
        ( new ComputeDashboardExpensesJob( $this->event ) )->handle();
    }
}
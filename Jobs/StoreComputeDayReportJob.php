<?php

namespace Modules\NsMultiStore\Jobs;

use App\Jobs\ComputeDayReportJob;
use App\Services\ReportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreComputeDayReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Store
     */
    public $store;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct( Store $store )
    {
        $this->store    =   $store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ns()->store->setStore( $this->store );

        ( new ComputeDayReportJob )->handle();
    }
}

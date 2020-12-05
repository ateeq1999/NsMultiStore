<?php
namespace Modules\NsMultiStore\Jobs;

use App\Jobs\InitializeDailyReportJob;
use App\Models\Role;
use App\Services\NotificationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;

class StoreInitializeDailyReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $store;

    public function __construct( Store $store )
    {
        $this->store    =   $store;
    }

    public function handle()
    {
        ns()->store->setStore( $this->store );
        ( new InitializeDailyReportJob )->handle();
    }
}
<?php
namespace Modules\NsMultiStore\Jobs;

use App\Models\Role;
use App\Services\NotificationService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;

class DismantleStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $store;

    public function __construct( Store $store )
    {
        $this->store    =   $store;
    }

    public function handle()
    {
        if ( in_array( $this->store->status, [ Store::STATUS_DISMANTLING ] ) ) {
            /**
             * @var StoresService
             */
            $storeService   =   app()->make( StoresService::class );
            /**
             * @var NotificationService
             */
            $notificationService   =   app()->make( NotificationService::class );

            $storeService->dismantleStore( $this->store );

            $totalStore     =   ( int ) ns()->option->get( 'ns.multistores.stores', 0 );
            ns()->option->set( 'ns.multistores.stores', --$totalStore );

            return $notificationService->create([
                'title'         =>  __( 'Store Dismantling Status' ),
                'description'   =>  sprintf( __( 'The Store "%s" has been successfully dismantled..' ), $this->store->name ),
                'url'           =>  url( '/dashboard/multistores/stores' )
            ])->dispatchForGroup([
                Role::namespace( 'admin' ),
                Role::namespace( 'supervisor' ),
            ]);
        }

        throw new Exception( sprintf( __( 'Wrong status for dismantling a store %s' ), $this->store->status ) );
    }
}
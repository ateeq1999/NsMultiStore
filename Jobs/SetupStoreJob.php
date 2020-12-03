<?php
namespace Modules\NsMultiStore\Jobs;

use App\Models\Role;
use App\Services\NotificationService;
use App\Services\Options;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;
use Throwable;

class SetupStoreJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $store;

    public function __construct( Store $store )
    {
        $this->store    =   $store;
    }

    public function handle()
    {
        if ( $this->store->status ===  Store::STATUS_BUILDING ) {
            /**
             * @var StoresService
             */
            $storeService   =   app()->make( StoresService::class );

            /**
             * @var NotificationService
             */
            $notificationService   =   app()->make( NotificationService::class );

            $storeService->createStoreTables( $this->store );

            $this->store->status  =   Store::STATUS_OPENED;
            $this->store->save();

            $notificationService->create([
                'title'         =>  __( 'Store Crafting Status' ),
                'description'   =>  sprintf( __( 'The store "%s" has been successfully built. It\'s awaiting to be used.' ), $this->store->name ),
                'url'           =>  url( '/dashboard/store/' . $this->store->id )
            ])->dispatchForGroup([
                Role::namespace( 'admin' ),
                Role::namespace( 'supervisor' ),
            ]);

            $totalStore     =   ( int ) ns()->option->get( 'ns.multistores.stores', 0 );
            ns()->option->set( 'ns.multistores.stores', ++$totalStore );
        }
    }

    public function failed( Throwable $exception )
    {
        /**
         * @var NotificationService
         */
        $notificationService   =   app()->make( NotificationService::class );

        $notificationService->create([
            'title'         =>  __( 'Store Creation Failed' ),
            'description'   =>  sprintf( __( 'Unable to complete the mantling of the store %s. The request.' ), $this->store->name ),
            'url'           =>  url( '/dashboard/store/' . $this->store->id )
        ])->dispatchForGroup([
            Role::namespace( 'admin' ),
            Role::namespace( 'supervisor' ),
        ]);
    }
}
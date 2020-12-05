<?php
namespace Modules\NsMultiStore\Providers;

use App\Events\AfterCustomerAccountHistoryCreatedEvent;
use App\Events\BeforeDispatchingJobEvent;
use App\Events\ExpenseAfterCreateEvent;
use App\Events\ExpenseHistoryAfterCreatedEvent;
use App\Events\OrderAfterCreatedEvent;
use App\Events\OrderAfterPaymentCreatedEvent;
use App\Events\OrderAfterProductRefundedEvent;
use App\Events\OrderAfterRefundedEvent;
use App\Events\OrderBeforeDeleteEvent;
use App\Events\OrderBeforePaymentCreatedEvent;
use App\Events\ProcurementAfterCreateEvent;
use App\Events\ProcurementAfterDeleteEvent;
use App\Events\ProcurementAfterDeleteProductEvent;
use App\Events\ProcurementAfterUpdateEvent;
use App\Events\ProcurementAfterUpdateProductEvent;
use App\Events\ProcurementBeforeDeleteEvent;
use App\Events\ProcurementBeforeUpdateEvent;
use App\Events\ProcurementBeforeUpdateProductEvent;
use App\Events\ProcurementCancelationEvent;
use App\Events\ProductAfterDeleteEvent;
use App\Events\ProductAfterStockAdjustmentEvent;
use App\Events\ProductBeforeDeleteEvent;
use App\Events\WhileDispatchingJobEvent;
use App\Listeners\CustomerEventSubscriber;
use App\Listeners\ProductEventsSubscriber;
use App\Models\Provider;
use App\Providers\EventServiceProvider as ProvidersEventServiceProvider;
use App\Services\CustomerService;
use App\Services\ExpenseService;
use App\Services\ProcurementService;
use App\Services\ProductService;
use App\Services\ProviderService;
use Illuminate\Support\Facades\Event;
use Modules\NsMultiStore\Events\MultiStoreWebLoadedEvent;
use Modules\NsMultiStore\Jobs\StoreComputeDashboardExpensesJob;
use Modules\NsMultiStore\Jobs\StoreHandleStockAdjustmentJob;
use Modules\NsMultiStore\Listeners\QueueListeners;
use Modules\NsMultiStore\Listeners\StoreOrderEventSubscriber;

class EventServiceProvider extends ProvidersEventServiceProvider
{
    /**
     * @var StoreOrderEventSubscriber
     */
    public $orderEvents;

    /**
     * @var ProductService
     */
    public $productService;

    /**
     * @var ProviderService
     */
    public $providerService;

    /**
     * @var CustomerService
     */
    public $customerService;

    /**
     * @var ExpenseService
     */
    public $expenseService;

    /**
     * @var ProcurementService
     */
    public $procurementService;

    public function __construct()
    {
        $this->orderEvents          =   app()->make( StoreOrderEventSubscriber::class );
        $this->productService       =   app()->make( ProductService::class );
        $this->providerService      =   app()->make( ProviderService::class );
        $this->procurementService   =   app()->make( ProcurementService::class );
        $this->expenseService       =   app()->make( ExpenseService::class );
    }

    public function register()
    {
        Event::listen( function( MultiStoreWebLoadedEvent $event ) {
            QueueListeners::dispatchInitStoreReport( $event );
        });

        Event::listen( function( OrderAfterCreatedEvent $event ) {
            $this->orderEvents->handleOrder( $event );
        });

        Event::listen( function( OrderBeforeDeleteEvent $event ) {
            $this->orderEvents->handleOrder( $event );
        });

        Event::listen( function( OrderAfterPaymentCreatedEvent $event ) {
            $this->orderEvents->handleOrderPayment( $event );
        });

        Event::listen( function( OrderBeforePaymentCreatedEvent $event ) {
            $this->orderEvents->handleBeforeOrderPayment( $event );
        });

        Event::listen( function( OrderAfterRefundedEvent $event ) {
            $this->orderEvents->handleRefund( $event );
        });

        Event::listen( function( OrderAfterRefundedEvent $event ) {
            $this->orderEvents->handleRefund( $event );
        });

        Event::listen( function( OrderBeforePaymentCreatedEvent $event ) {
            $this->orderEvents->handleBeforeOrderPayment( $event );
        });

        Event::listen( function( ProductBeforeDeleteEvent $event ) {
            $productEventSubscriber     =   app()->make( ProductEventsSubscriber::class );
            $productEventSubscriber->beforeDeleteProduct( $event );
        });

        Event::listen( function( ProductAfterDeleteEvent $event ) {
            $productEventSubscriber     =   app()->make( ProductEventsSubscriber::class );
            $productEventSubscriber->beforeDeleteProduct( $event );
        });

        Event::listen( function( ProductAfterStockAdjustmentEvent $event ) {
            StoreHandleStockAdjustmentJob::dispatch( ns()->store->current(), $event )
            ->delay( now() );
        });

        Event::listen( function( ProcurementAfterCreateEvent $event ) {
            $this->procurementService->refresh( $event->procurement );
            $this->providerService->computeSummary( $event->procurement->provider );
            $this->procurementService->handleProcurement( $event->procurement );
        });

        Event::listen( function( ProcurementBeforeUpdateEvent $event ) {
            $this->providerService->cancelPaymentForProcurement( $event->procurement );
        });

        Event::listen( function( ProcurementAfterUpdateEvent $event ) {
            $this->providerService->computeSummary( $event->procurement->provider );
            $this->procurementService->refresh( $event->procurement );
            $this->procurementService->handleProcurement( $event->procurement );
        });

        Event::listen( function( ProcurementAfterUpdateProductEvent $event ) {
            $this->productService->procurementStockEntry( $event->product, $event->fields );
            $this->procurementService->refresh( $event->product->procurement );
        });

        Event::listen( function( ProcurementAfterDeleteProductEvent $event ) {
            $this->procurementService->refresh( $event->procurement_id );
        });

        Event::listen( function( ProcurementAfterDeleteEvent $event ) {
            $this->providerService->computeSummary( 
                Provider::find( $event->procurement_data[ 'provider_id' ] ) 
            );
        });

        Event::listen( function( ProcurementBeforeDeleteEvent $event ) {
            $this->procurementService->attemptProductsStockRemoval( $event->procurement );
            $this->procurementService->deleteProcurementProducts( $event->procurement );
        });

        Event::listen( function( ExpenseAfterCreateEvent $event ) {
            if ( ! $event->expense->recurring ) {
                $this->expenseService->triggerExpense( $event->expense );
            }
        });

        Event::listen( function( ExpenseHistoryAfterCreatedEvent $event ) {
            StoreComputeDashboardExpensesJob::dispatch( 
                ns()->store->curren(), 
                $event 
            );
        });

        Event::listen( function( AfterCustomerAccountHistoryCreatedEvent $event ) {
            $this->customerService->updateCustomerAccount( $event->customerAccount );
        });
    }

    public function boot()
    {
        // ...
    }
}
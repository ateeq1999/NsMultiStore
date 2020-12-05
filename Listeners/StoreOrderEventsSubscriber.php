<?php
namespace Modules\NsMultiStore\Listeners;

use App\Models\OrderPayment;
use App\Services\CustomerService;
use App\Services\OrdersService;
use Exception;
use Illuminate\Support\Facades\Event;
use Modules\NsMultiStore\Jobs\StoreComputeCashierSalesJob;
use Modules\NsMultiStore\Jobs\StoreComputeCustomerAccountJob;
use Modules\NsMultiStore\Jobs\StoreComputeDayReportJob;

class StoreOrderEventSubscriber
{
    /**
     * @var OrdersService
     */
    public $ordersService;

    /**
     * @var CustomerService
     */
    public $customerService;

    public function __construct(
        OrdersService $ordersService,
        CustomerService $customerService
    )
    {
        $this->ordersService        =   $ordersService;
        $this->customerService      =   $customerService;
    }

    public function handleRefund( $event )
    {
        $this->ordersService->refreshOrder( $event->order );
        $this->handleOrder( $event );
    }

    public function handleBeforeOrderPayment( $event )
    {
        if ( $event->payment[ 'identifier' ] === OrderPayment::PAYMENT_ACCOUNT ) {
            $this->customerService->canReduceCustomerAccount( $event->customer, $event->payment[ 'value' ] );
        }
    }

    public function handleOrderPayment( $event )
    {
        $this->customerService->increaseOrderPurchases( 
            $event->order->customer, 
            $event->orderPayment->value 
        );
    }

    public function handleOrder( $event )
    {
        StoreComputeDayReportJob::dispatch( 
            ns()->store->current(), 
            $event 
        )->delay( now() );

        StoreComputeCustomerAccountJob::dispatch( 
            ns()->store->current(), 
            $event 
        )->delay( now() );

        StoreComputeCashierSalesJob::dispatch( 
            ns()->store->current(), 
            $event 
        )->delay( now() );        
    }
}
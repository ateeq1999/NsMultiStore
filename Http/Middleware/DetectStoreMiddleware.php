<?php

namespace Modules\NsMultiStore\Http\Middleware;

use App\Exceptions\NotAllowedException;
use App\Exceptions\NotFoundException;
use App\Models\Migration;
use App\Services\Helpers\App;
use App\Services\ModulesService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Modules\NsMultiStore\Events\MultiStoreWebLoadedEvent;
use Modules\NsMultiStore\Events\MultiStoreApiLoadedEvent;
use Modules\NsMultiStore\Models\Store;
use Modules\NsMultiStore\Services\StoresService;

class DetectStoreMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $type = 'web' )
    {
        $store      =   Store::find( $request->route( 'store_id' ) );

        if ( ! $store instanceof Store ) {
            throw new NotFoundException( __( 'Unable to find the requested store.' ) );
        }

        if ( $store->status !== Store::STATUS_OPENED ) {
            throw new NotAllowedException( __( 'Unable to access to a store that is not opened.' ) );
        }

        $request->route()->forgetParameter( 'store_id' );

        /**
         * @param StoresService
         */
        $storeService   =   app()->make( StoresService::class );        
        $storeService->setStore( $store );
        
        if ( $type === 'web' ) {
            event( new MultiStoreWebLoadedEvent( $store ) );
        } else {
            event( new MultiStoreApiLoadedEvent( $store ) );
        }


        return $next($request);
    }
}

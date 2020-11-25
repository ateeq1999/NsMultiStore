<?php

namespace Modules\NsMultiStore\Http\Middleware;

use App\Models\Migration;
use App\Services\Helpers\App;
use App\Services\ModulesService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
    public function handle(Request $request, Closure $next)
    {
        /**
         * @param StoresService
         */
        $storeService   =   app()->make( StoresService::class );        
        $storeService->setStoreID( $request->route( 'store_id' ) );

        return $next($request);
    }
}

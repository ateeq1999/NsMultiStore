<?php
namespace Modules\NsMultiStore;

use App\Classes\Hook;
use Illuminate\Support\Facades\Event;
use App\Services\Module;
use Modules\NsMultiStore\Events\NsMultiStoreEvent;

class NsMultiStoreModule extends Module
{
    public function __construct()
    {
        parent::__construct( __FILE__ );
    }
}
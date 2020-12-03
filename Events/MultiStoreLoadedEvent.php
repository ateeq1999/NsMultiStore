<?php
namespace Modules\NsMultiStore\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MultiStoreLoadedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
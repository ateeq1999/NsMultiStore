<?php
namespace Modules\NsMultiStore\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MultiStoreApiLoadedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
}
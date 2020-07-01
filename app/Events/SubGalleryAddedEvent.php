<?php

namespace App\Events;

use App\Photos\SubGalleries\SubGallery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubGalleryAddedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subGallery;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SubGallery $subGallery)
    {
        $this->subGallery = $subGallery;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

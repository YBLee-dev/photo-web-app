<?php

namespace App\Events;

use App\Photos\Galleries\Gallery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GalleryUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $updatedGallery;
    public $previewGallery;

    /**
     * Create a new event instance.
     *
     * @param Gallery $updatedGallery
     * @param Gallery $previewGallery
     */
    public function __construct(Gallery $updatedGallery, Gallery $previewGallery)
    {
        $this->updatedGallery = $updatedGallery;
        $this->previewGallery = $previewGallery;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}

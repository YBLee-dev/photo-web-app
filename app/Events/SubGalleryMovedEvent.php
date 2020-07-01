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

class SubGalleryMovedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subGallery;
    public $previewParentGalleryId;

    /**
     * Create a new event instance.
     *
     * @param SubGallery $subGallery
     * @param int $previewParentGalleryId
     */
    public function __construct(SubGallery $subGallery, int $previewParentGalleryId)
    {
        $this->subGallery = $subGallery;
        $this->previewParentGalleryId = $previewParentGalleryId;
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

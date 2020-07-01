<?php

namespace App\Events;

use App\Photos\Photos\Photo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewPhotoUploadedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $photo;
    public $subGalleryId;

    /**
     * Create a new event instance.
     *
     * @param Photo $photo
     * @param int $subGalleryId
     */
    public function __construct(Photo $photo, int $subGalleryId)
    {
        $this->photo = $photo;
        $this->subGalleryId = $subGalleryId;
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

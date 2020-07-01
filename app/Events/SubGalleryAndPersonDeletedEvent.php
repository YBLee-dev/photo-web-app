<?php

namespace App\Events;

use App\Photos\People\Person;
use App\Photos\SubGalleries\SubGallery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SubGalleryAndPersonDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $subGallery;
    public $person;

    /**
     * Create a new event instance.
     *
     * @param SubGallery $subGallery
     * @param Person $person
     */
    public function __construct(SubGallery $subGallery, Person $person)
    {
        $this->subGallery = $subGallery;
        $this->person = $person;
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

<?php

namespace App\Events;

use App\Photos\People\Person;
use App\Photos\Photos\Photo;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CroppedFaceDeletedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $person;
    public $photo;

    /**
     * Create a new event instance.
     *
     * @param Person $person
     * @param Photo $photo
     */
    public function __construct(Person $person, Photo $photo)
    {
        $this->person = $person;
        $this->photo = $photo;
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

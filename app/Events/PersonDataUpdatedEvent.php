<?php

namespace App\Events;

use App\Photos\People\Person;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PersonDataUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $updatedPerson;
    public $previewPerson;

    /**
     * Create a new event instance.
     *
     * @param Person $updatedPerson
     * @param Person $previewPerson
     */
    public function __construct(Person $updatedPerson, Person $previewPerson)
    {
        $this->updatedPerson = $updatedPerson;
        $this->previewPerson = $previewPerson;
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

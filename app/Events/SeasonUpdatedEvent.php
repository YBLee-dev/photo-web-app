<?php

namespace App\Events;

use App\Photos\Seasons\Season;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SeasonUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $updatedSeason;
    public $previewSeason;

    /**
     * Create a new event instance.
     *
     * @param Season $updatedSeason
     * @param Season $previewSeason
     */
    public function __construct(Season $updatedSeason, Season $previewSeason)
    {
        $this->updatedSeason = $updatedSeason;
        $this->previewSeason = $previewSeason;
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

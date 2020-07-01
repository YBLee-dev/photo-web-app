<?php

namespace App\Events;

use App\Ecommerce\Orders\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CustomerDataUpdatedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $updatedOrder;
    public $previewOrder;

    /**
     * Create a new event instance.
     *
     * @param Order $updatedOrder
     * @param Order $previewOrder
     */
    public function __construct(Order $updatedOrder, Order $previewOrder)
    {
        $this->updatedOrder = $updatedOrder;
        $this->previewOrder = $previewOrder;
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

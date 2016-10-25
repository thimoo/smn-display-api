<?php

namespace App\Events;

use App\Value;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewValue
{
    use InteractsWithSockets, SerializesModels;

    public $value;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Value $data)
    {
        $this->value = $data;
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

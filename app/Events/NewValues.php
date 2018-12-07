<?php

namespace App\Events;

use App\Value;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewValues
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Stores the collection of value between the data
     * and the profile
     *
     * @var array
     */
    public $values = array();

    /**
     * Create a array new event instance with the given value.
     *
     * @param array  $values  array of values
     * @return void
     */
    public function __construct($values)
    {
        $this->values = $values;
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

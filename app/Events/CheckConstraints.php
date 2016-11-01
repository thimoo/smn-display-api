<?php

namespace App\Events;

use App\Data;
use App\Profile;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class CheckConstraints
{
    use InteractsWithSockets, SerializesModels;

    public $profile;

    public $data;

    public $values;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Profile $profile, Data $data, $values)
    {
        $this->profile = $profile;
        $this->data = $data;
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

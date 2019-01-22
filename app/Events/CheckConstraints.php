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

    /**
     * Stores the profile
     *
     * @var App\Profile
     */
    public $profile;

    /**
     * Stores the data
     *
     * @var App\Data
     */
    public $data;

    /**
     * Stores the collection of value between the data
     * and the profile
     *
     * @var \Illuminate\Database\Eloquent\Collection
     */
    public $values;

    /**
     * Create a new event instance
     *
     * @param App\Profile                               $profile
     * @param App\Data                                  $data
     * @param \Illuminate\Database\Eloquent\Collection  $values  collection of values
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

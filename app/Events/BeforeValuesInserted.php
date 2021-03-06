<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class BeforeValuesInserted
{
    use InteractsWithSockets, SerializesModels;

    /**
     * Current profile
     *
     * @var string
     */
    public $profile;

    /**
     * Stores the time
     *
     *
     */
    public $time;

    /**
     * Create a array new event instance with the given profile.
     * @param  string  $data
     * @return void
     */
    public function __construct(string $data, $time)
    {
      $this->profile = $data;
      $this->time = $time;
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

<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NoValues
{
    use InteractsWithSockets, SerializesModels;

    /**
     * The time used to insert the no-data values in
     * the database
     * 
     * @var \Carbon\Carbon
     */
    public $forTime;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Carbon $date)
    {
        $this->forTime = $date;
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

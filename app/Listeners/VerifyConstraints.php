<?php

namespace App\Listeners;

use App\Events\NewValue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyConstraints
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewValue  $event
     * @return void
     */
    public function handle(NewValue $event)
    {
        //
    }
}

<?php

namespace App\Listeners;

use App\Events\NoValues;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class InsertNoDataValues
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
     * @param  NoValues  $event
     * @return void
     */
    public function handle(NoValues $event)
    {
        //
    }
}

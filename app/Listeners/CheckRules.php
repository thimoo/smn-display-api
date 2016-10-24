<?php

namespace App\Listeners;

use App\Events\ValuesInserted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckRules
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
     * @param  ValuesInserted  $event
     * @return void
     */
    public function handle(ValuesInserted $event)
    {
        //
    }
}

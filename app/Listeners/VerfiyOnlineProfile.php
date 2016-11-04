<?php

namespace App\Listeners;

use App\Profile;
use App\Events\CheckProfiles;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerfiyOnlineProfile
{
    /**
     * Stores the profile
     * 
     * @var App\Profile
     */
    protected $profile;

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
     * @param  CheckProfiles  $event
     * @return void
     */
    public function handle(CheckProfiles $event)
    {
        $this->profile = $event->profile;

        $this->check();
    }

    protected function check()
    {
        // get all data
        // get all no-data for each data
        // if each > 36
        // -> update
    }

    protected function setOffline()
    {
        // update the online to false
    }

    protected function setOnline()
    {
        
    }
}

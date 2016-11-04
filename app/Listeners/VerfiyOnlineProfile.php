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

    /**
     * Check if the profile must be set online or
     * offline
     * 
     * @return void
     */
    protected function check()
    {
        if ($this->profile->isOnline()) 
        {
            $this->checkToSetOffline();
        }
        else $this->checkToSetOnline();
    }

    /**
     * Check if the profile displays no data. If yes,
     * then the profile is set offline
     * 
     * @return void
     */
    protected function checkToSetOffline()
    {
        if ($this->profile->getNumberDisplays() == 0)
        {
            $this->profile->setOffline();
        }
    }

    /**
     * Check if the profile displays data. If yes,
     * then the profile is set to online
     * 
     * @return void
     */
    protected function checkToSetOnline()
    {
        if ($this->profile->getNumberDisplays() > 0)
        {
            $this->profile->setOnline();
        }
    }
}

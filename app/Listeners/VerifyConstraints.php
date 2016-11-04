<?php

namespace App\Listeners;

use App\Data;
use App\Value;
use App\Profile;
use App\Events\NewValue;
use App\Events\CheckConstraints;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyConstraints
{
    /**
     * The profile object attach to the current value
     * 
     * @var App\Profile
     */
    protected $profile;

    /**
     * The data object attach to the current value
     * 
     * @var App\Data
     */
    protected $data;

    /**
     * Handle the event.
     *
     * @param  NewValue  $event
     * @return void
     */
    public function handle(NewValue $event)
    {
        // Unpack the value given in the event message
        // and store it in the current object
        $value = $event->value;


        // Get the profile and the data attach to the
        // new value given
        $this->profile = Profile::find($value->profile_stn_code);
        $this->data = Data::find($value->data_code);

        // If the data is null, then the data is not
        // present in database and the value must be
        // ignored
        if ($this->data)
        {
            // Get the collection for the data and the profile
            // and trigger the CheckConstraints event
            $collection = Value::getCollectionFor($this->profile, $this->data);

            event(new CheckConstraints($this->profile, $this->data, $collection));
        }
    }
}

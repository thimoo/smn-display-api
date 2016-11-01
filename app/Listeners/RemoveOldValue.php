<?php

namespace App\Listeners;

use App\Data;
use App\Profile;
use App\Events\NewValue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveOldValue
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

        // Unpack the datetime attached to the value
        $currentTime = $value->date;

        // Compute the limit datetime to determine all
        // older values to delete
        $minutes = 143 * 10;
        $limitTime = $currentTime->copy()->subMinutes($minutes);

        // Get the profile and the data attach to the
        // new value given
        $this->profile = Profile::find($value->profile_stn_code);
        $this->data = Data::where('smn_code', $value->data_code)->first();

        // If the data is null, then the data is not
        // present in database and the value must be
        // ignored
        if ($this->data)
        {
            // Query the database to deletes all values that have
            // a date lesser than the computed limit datetime
            DB::table('values')
                ->where('data_code', $this->data->code)
                ->where('profile_stn_code', $this->profile->stn_code)
                ->where('date', '<', $limitTime)
                ->delete();
        }
    }
}

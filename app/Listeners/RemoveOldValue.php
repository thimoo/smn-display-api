<?php

namespace App\Listeners;

use \DB;
use \Log;
use Carbon\Carbon;
use App\Events\BeforeValuesInserted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveOldValue
{
    /**
     * Handle the event.
     *
     * @param  BeforeValuesInserted  $event
     * @return void
     */
    public function handle(BeforeValuesInserted $event)
    {

        DB::table('values')->truncate();

        // V1
        // // Get the current datetime in database
        // $currentTime = $this->getDatabaseTime();
        //
        //
        // if ($currentTime)
        // {
        //     // Compute the limit datetime to determine all
        //     // older values to delete
        //     $minutes = 143 * 10;
        //     $limitTime = $currentTime->copy()->subMinutes($minutes);
        //
        //     // Query the database to deletes all values that have
        //     // a date lesser than the computed limit datetime
        //     DB::table('values')
        //         ->where('date', '<', $limitTime)
        //         ->delete();
        // }
    }
}

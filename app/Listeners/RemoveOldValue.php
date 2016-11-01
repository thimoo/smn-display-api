<?php

namespace App\Listeners;

use \DB;
use Carbon\Carbon;
use App\Events\ValuesInserted;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RemoveOldValue
{
    /**
     * Handle the event.
     *
     * @param  ValuesInserted  $event
     * @return void
     */
    public function handle(ValuesInserted $event)
    {
        // Get the current datetime in database
        $currentTime = $this->getDatabaseTime();

        if ($currentTime)
        {
            // Compute the limit datetime to determine all
            // older values to delete
            $minutes = 143 * 10;
            $limitTime = $currentTime->copy()->subMinutes($minutes);

            // Query the database to deletes all values that have
            // a date lesser than the computed limit datetime
            DB::table('values')
                ->where('date', '<', $limitTime)
                ->delete();
        }
    }

    /**
     * Retreive the latest update datetime from profiles
     * and set the databaseUpdateTime. If no profile was
     * found, then the databaseUpdateTime is set to null
     * 
     * @return Carbon\Carbon or null if no profile in database
     */
    private function getDatabaseTime()
    {
        $res = DB::table('profiles')->max('last_update');
        if ($res == null) return null;
        else return new Carbon($res);
    }
}

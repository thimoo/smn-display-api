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
      // Get the current datetime in database
      $currentTime = $this->getDatabaseTime();

      if ($currentTime)
      {
          // Compute the limit datetime to determine all
          // older values to delete
          $minutes = 12 * 10;
          $limitTime2h = $currentTime->copy()->subMinutes($minutes);

          $minutes = 143 * 10;
          $limitTime24h = $currentTime->copy()->subMinutes($minutes);

          // Query the database to deletes all values that have
          // a date lesser than the computed limit datetime
          $query=DB::table('values')
              ->where('profile_stn_code', '=', $event->profile)
              ->where('date', '>', $limitTime2h)
              ->orWhere('date', '<', $limitTime24h)
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
        $res = DB::table('profiles')->min('last_update');
        if ($res == null) return null;
        else return new Carbon($res);
    }
}

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
<<<<<<< HEAD
      // Get the current datetime in database
      $currentTime = $this->getDatabaseTime();
=======
      DB::table('values')
          ->where('profile_stn_code', $event->profile)
          ->delete();
        // DB::table('values')->truncate();
>>>>>>> 65674cfc02f7718e434e3033c202349b91d2a4d5

      if ($currentTime)
      {
          // Compute the limit datetime to determine all
          // older values to delete
          $minutes = 12 * 10;
          $limitTime = $currentTime->copy()->subMinutes($minutes);

          // Query the database to deletes all values that have
          // a date lesser than the computed limit datetime
          $query=DB::table('values')
              ->where('profile_stn_code', '=', $event->profile)
              ->where('date', '>', $limitTime)
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

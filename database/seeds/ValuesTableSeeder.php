<?php

use App\Data;
use App\Value;
use App\Profile;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ValuesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        $profiles = Profile::all();
        $data = Data::all();

        foreach ($profiles as $profile) {
            foreach ($data as $d) {

                $date = Carbon::now();

                // insert 144 value with timestamp to [now ... -24 hours]
                for ($i=0; $i < 144; $i++) { 
                    // create a random value
                    $value = factory(Value::class, 1)->make();
                    
                    // with profile, data, date
                    $value->data_code = $d->code;
                    $value->profile_stn_code = $profile->stn_code;
                    $value->date = $date;

                    // save the value
                    $value->save();

                    // decrement the date
                    $date->subMinutes(10);
                }
            }
        }
    }
}

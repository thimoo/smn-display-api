<?php

use App\Profile;
use Illuminate\Database\Seeder;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Profile::create([
            'stn_code' => 'cha',
            'altitude' => 1607,
            'infos' => json_encode(new StdClass),
            'last_update' => null,
            'last_time_online' => null,
            'online' => true,
        ]);

        Profile::create([
            'stn_code' => 'jun',
            'altitude' => 4158,
            'infos' => json_encode(new StdClass),
            'last_update' => null,
            'last_time_online' => null,
            'online' => true,
        ]);
    }
}

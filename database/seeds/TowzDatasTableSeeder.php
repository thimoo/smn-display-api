<?php

use App\Data;
use Illuminate\Database\Seeder;

class TowzDatasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Data::create([
          'code' => 'temp_towz',
          'smn_code' => 'ta1tows0',
        ]);

        Data::create([
            'code' => 'wind_dir_towz',
            'smn_code' => 'dv1towz0',
        ]);

        Data::create([
            'code' => 'wind_towz',
            'smn_code' => 'fu3towz0',
        ]);

        Data::create([
            'code' => 'wind_gusts_towz',
            'smn_code' => 'fu3towz1',
        ]);

        Data::create([
            'code' => 'humidity_towz',
            'smn_code' => 'uortows0',
        ]);

    }
}

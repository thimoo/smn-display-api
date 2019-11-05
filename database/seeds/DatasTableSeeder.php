<?php

use App\Data;
use Illuminate\Database\Seeder;

class DatasTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Data::create([
            'code' => 'temp',
            'smn_code' => 'tre200s0',
        ]);

        Data::create([
            'code' => 'sun',
            'smn_code' => 'sre000z0',
        ]);

        Data::create([
            'code' => 'precipitation',
            'smn_code' => 'rre150z0',
        ]);

        Data::create([
            'code' => 'wind_dir',
            'smn_code' => 'dkl010z0',
        ]);

        Data::create([
            'code' => 'wind',
            'smn_code' => 'fu3010z0',
        ]);

        Data::create([
            'code' => 'qnh',
            'smn_code' => 'pp0qnhs0',
        ]);

        Data::create([
            'code' => 'wind_gusts',
            'smn_code' => 'fu3010z1',
        ]);

        Data::create([
            'code' => 'humidity',
            'smn_code' => 'ure200s0',
        ]);

        Data::create([
            'code' => 'qfe',
            'smn_code' => 'prestas0',
        ]);

        Data::create([
            'code' => 'qff',
            'smn_code' => 'pp0qffs0',
        ]);
    }
}

<?php

use App\Display;
use App\Profile;
use App\Data;
use Illuminate\Database\Seeder;

class DisplaysTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Generate 5 relation
        factory(Display::class, 5)->make()->each(function($d) {
            // ...
        });
    }
}

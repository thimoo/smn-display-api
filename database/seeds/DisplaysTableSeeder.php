<?php

use App\Data;
use App\Profile;
use Faker\Factory as Faker;
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
        $faker = Faker::create();

        $profiles = Profile::all();
        $data = Data::all();

        foreach ($profiles as $profile) {
            foreach ($data as $d) {

                // randomize the creation for the relation
                if ($faker->boolean) {

                    // generate a random boolean value 
                    // for data and collection attributes
                    $bool = $faker->boolean;

                    // attatch a profile 
                    $d->profiles()->attach(
                        $profile->stn_code, 
                        [
                            'data' => $bool, 
                            'collection' => $bool
                        ]
                    );    
                }
            }
        }
        
    }
}

<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Reset the complete database
        DB::table('data')->delete();
        DB::table('profiles')->delete();
        DB::table('displays')->delete();
        DB::table('values')->delete();

        // Seeds all registered data
        $this->call(DatasTableSeeder::class);
        // $this->call(TowzDatasTableSeeder::class);
        // $this->call(ProfilesTableSeeder::class);
        // $this->call(DisplaysTableSeeder::class);
        // $this->call(ValuesTableSeeder::class);
    }
}

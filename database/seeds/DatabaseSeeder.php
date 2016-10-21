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
        DB::table('data')->delete();
        DB::table('profiles')->delete();
        DB::table('displays')->delete();
        DB::table('values')->delete();

        $this->call(DatasTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(DisplaysTableSeeder::class);
        $this->call(ValuesTableSeeder::class);
    }
}

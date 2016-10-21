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
        // $this->call(UsersTableSeeder::class);

        DB::table('data')->truncate();
        DB::table('profiles')->truncate();
        DB::table('displays')->truncate();
        DB::table('values')->truncate();

        $this->call(DatasTableSeeder::class);
        $this->call(ProfilesTableSeeder::class);
        $this->call(DisplaysTableSeeder::class);
        $this->call(ValuesTableSeeder::class);
    }
}

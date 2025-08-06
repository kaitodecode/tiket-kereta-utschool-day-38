<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(StationSeeder::class);
        $this->call(TrainSeeder::class);
        $this->call(RouteSeeder::class);
        $this->call(ScheduleSeeder::class);
    }
}

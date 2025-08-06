<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\Schedule;
use App\Models\Station;
use App\Models\Train;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 10; $i++) {
            $train = Train::inRandomOrder()->first();
            Schedule::create([
                "train_id" => $train->id,
                "route_id" => Route::inRandomOrder()->first()->id,
                "departure_time" => now(),
                "arrival_time" => now()->addHour(),
                "seat_available" => $train->seat - rand(1, $train->capacity),
                "price" => rand(150000, 1000000),
            ]);
        }
    }
}

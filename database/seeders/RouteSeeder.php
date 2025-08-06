<?php

namespace Database\Seeders;

use App\Models\Route;
use App\Models\Station;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RouteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $stationIds = Station::pluck('id')->toArray();
        
        // Ensure we have at least 2 stations
        if (count($stationIds) < 2) {
            throw new \Exception('Need at least 2 stations to create routes');
        }

        // Create at least 10 unique routes with different origin and destination
        $createdRoutes = [];
        $routeCount = 0;
        
        while ($routeCount < 10) {
            $origin = $stationIds[array_rand($stationIds)];
            $destination = $stationIds[array_rand($stationIds)];
            
            // Skip if origin and destination are the same
            if ($origin === $destination) {
                continue;
            }
            
            // Skip if this route combination already exists
            $routeKey = $origin . '-' . $destination;
            if (in_array($routeKey, $createdRoutes)) {
                continue;
            }
            
            Route::create([
                'origin_id' => $origin,
                'destination_id' => $destination,
            ]);
            
            $createdRoutes[] = $routeKey;
            $routeCount++;
        }
    }
}

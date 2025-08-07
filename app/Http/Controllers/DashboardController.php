<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Models\Train;
use App\Models\Route;
use App\Models\Schedule;
use App\Models\Booking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/dashboard/admin",
     *     summary="Get admin dashboard data",
     *     tags={"Dashboard"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Dashboard Admin"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="total_station", type="integer", example=10),
     *                 @OA\Property(property="total_train", type="integer", example=5),
     *                 @OA\Property(property="total_route", type="integer", example=15),
     *                 @OA\Property(property="total_schedule", type="integer", example=30),
     *                 @OA\Property(property="total_booking", type="integer", example=100),
     *                 @OA\Property(
     *                     property="profit_4_last_month",
     *                     type="object",
     *                     example={"January": 1000000, "February": 1500000, "March": 2000000, "April": 2500000}
     *                 ),
     *                 @OA\Property(
     *                     property="profit_7_last_week",
     *                     type="object",
     *                     example={"April 1": 100000, "April 2": 150000, "April 3": 200000}
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function DashboardAdmin()
    {
        $data = [
            "total_station" => Station::count(),
            "total_train" => Train::count(),
            "total_route" => Route::count(),
            "total_schedule" => Schedule::count(),
            "total_booking" => Booking::count(),
            // Get profits for last 4 months
            "profit_4_last_month" => $this->getLastMonthsProfits(4),
            "profit_7_last_week" => $this->getLastWeekProfits(7)
        ];

        return $this->json($data, "Dashboard Admin", 200);
    }

    private function getLastMonthsProfits($months)
    {
        $profits = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $profits[date('F', $date->timestamp)] = Booking::whereMonth('created_at', $date->format('m'))
                ->whereYear('created_at', $date->format('Y'))
                ->sum('total_price');
        }
        return $profits;
    }

    private function getLastWeekProfits($days)
    {
        $profits = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $profits[date('F j', $date->timestamp)] = Booking::whereDate('created_at', $date->format('Y-m-d'))
                ->sum('total_price');
        }
        return $profits;
    }
}

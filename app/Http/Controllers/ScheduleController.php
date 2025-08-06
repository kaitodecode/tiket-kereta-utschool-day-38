<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Http\Requests\StoreScheduleRequest;
use App\Http\Requests\UpdateScheduleRequest;
use App\Models\Route;
use App\Models\Train;
use Exception;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $req)
    {
        try {
            $query = Schedule::query();

            $origin_id = $req->query("origin_id");
            $destination_id = $req->query("destination_id");

            if (!$origin_id || !$destination_id) {
                return $this->json(null, "origin_id and destination_id is required", 400);
            }

            $route = Route::query()->where("origin_id", $origin_id)->where("destination_id", $destination_id)->first();

            if (!$route) {
                return $this->json(null, "Route from origin and destination not found", 400);
            }

            $query = $query->where("route_id", $route->id);

            if ($req->has("departure_time")) {
                $query = $query->whereDate("departure_time", ">=", $req->query("departure_time"));
            }

            $schedules = $query->paginate(10);

            return $this->json($schedules);
        } catch (Exception $th) {
            return $this->json($th->getMessage(), "Bad Response", 400);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreScheduleRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreScheduleRequest $request)
    {
        try {
            $data = $request->validated();

            $trainExist = Train::find($data['train_id']);
            $routeExist = Route::find($data['route_id']);

            if (!$trainExist) {
                return $this->json(null, "Train not found", 400);
            }

            if (!$routeExist) {
                return $this->json(null, "Route not found", 400);
            }

            // Check if there's any existing schedule for this train
            $lastSchedule = Schedule::where('train_id', $data['train_id'])
                ->where('departure_time', '<=', $data['departure_time'])
                ->orderBy('departure_time', 'desc')
                ->first();

            // If there's a previous schedule, validate that new departure time is after previous arrival
            if ($lastSchedule && $data['departure_time'] <= $lastSchedule->arrival_time) {
                return $this->json(null, "Train schedule conflicts with existing schedule. Departure time must be after previous arrival time.", 400);
            }

            // Check for any future schedules that might conflict
            $nextSchedule = Schedule::where('train_id', $data['train_id'])
                ->where('departure_time', '>=', $data['departure_time'])
                ->orderBy('departure_time', 'asc')
                ->first();

            // If there's a next schedule, validate that new arrival time is before next departure
            if ($nextSchedule && $data['arrival_time'] >= $nextSchedule->departure_time) {
                return $this->json(null, "Train schedule conflicts with existing schedule. Arrival time must be before next departure time.", 400);
            }

            $data['seat_available'] = $trainExist->capacity;
            $schedule = Schedule::create($data);

            return $this->json($schedule, "Schedule created", 201);
        } catch (Exception $th) {
            return $this->json($th, "Bad Response", 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function show(Schedule $schedule)
    {
        try {
            $schedule = Schedule::find($schedule->id);
            
            if (!$schedule) {
                return $this->json(null, "Schedule not found", 404);
            }

            return $this->json($schedule, "Schedule found", 200);
        } catch (Exception $e) {
            return $this->json(null, $e->getMessage(), 400);
        }
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateScheduleRequest  $request
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateScheduleRequest $request, Schedule $schedule)
    {
        try {
            $data = $request->validated();

            $schedule = Schedule::find($schedule->id);

            if (!$schedule) {
                $this->json(null, "Schedule not found", 400);
                return;
            }

            $trainExist = Train::find($data['train_id']);
            $routeExist = Route::find($data['route_id']);

            if (!$trainExist) {
                return $this->json(null, "Train not found", 400);
            }

            if (!$routeExist) {
                return $this->json(null, "Route not found", 400);
            }

            // Check if there's any existing schedule for this train
            $lastSchedule = Schedule::where('train_id', $data['train_id'])
                ->where('departure_time', '<=', $data['departure_time'])
                ->where('id', '!=', $schedule->id)
                ->orderBy('departure_time', 'desc')
                ->first();

            // If there's a previous schedule, validate that new departure time is after previous arrival
            if ($lastSchedule && $data['departure_time'] <= $lastSchedule->arrival_time) {
                return $this->json(null, "Train schedule conflicts with existing schedule. Departure time must be after previous arrival time.", 400);
            }

            // Check for any future schedules that might conflict
            $nextSchedule = Schedule::where('train_id', $data['train_id'])
                ->where('departure_time', '>=', $data['departure_time'])
                ->where('id', '!=', $schedule->id)
                ->orderBy('departure_time', 'asc')
                ->first();

            // If there's a next schedule, validate that new arrival time is before next departure
            if ($nextSchedule && $data['arrival_time'] >= $nextSchedule->departure_time) {
                return $this->json(null, "Train schedule conflicts with existing schedule. Arrival time must be before next departure time.", 400);
            }

            $data['seat_available'] = $trainExist->capacity;
            $schedule->update($data);

            return $this->json($schedule, "Schedule updated", 200);
        } catch (Exception $th) {
            return $this->json($th, "Bad Response", 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        try {
            $schedule = Schedule::find($schedule->id);
            
            if (!$schedule) {
                return $this->json(null, "Schedule not found", 404);
            }

            $schedule->delete();

            return $this->json($schedule, "Schedule deleted", 200);
        } catch (Exception $e) {
            return $this->json(null, $e->getMessage(), 400);
        }
    }
}

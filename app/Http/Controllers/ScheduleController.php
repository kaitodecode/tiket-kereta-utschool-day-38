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

            if (!$origin_id || !$destination_id){
                return $this->json(null, "origin_id and destination_id is required", 400);
            }

            $route = Route::query()->where("origin_id", $origin_id)->where("destination_id", $destination_id)->first();

            if(!$route){
                return $this->json(null, "Route from origin and destination not found", 400);
            }

            $query = $query->where("route_id", $route->id);

            if($req->has("departure_time")){
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

            $schedule = Schedule::create($data);

            $this->json($schedule);

        } catch (Exception $th) {
            $this->json($th, "Bad Response", 400);
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
                $this->json(null, "Schedule not found", 400);
                return;
            }

            $this->json($schedule);
        } catch (Exception $th) {
            $this->json($th, "Bad Response", 400);
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

            $schedule->update($data);

            $this->json($schedule);
        } catch (Exception $th) {
            $this->json($th, "Bad Response", 400);
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
                $this->json(null, "Schedule not found", 400);
                return;
            }

            $schedule->delete();

            $this->json($schedule);
        } catch (Exception $th) {
            $this->json($th, "Bad Response", 400);
        }
    }
}

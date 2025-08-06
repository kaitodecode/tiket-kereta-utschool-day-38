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
            $destionation_id = $req->query("destionation_id");

            if (!$origin_id || !$destionation_id){
                $this->json(null, "origin_id and destionation_id is required", 400);
            }

            $route = Route::query()->where("origin_id", $origin_id)->where("destionation_id", $destionation_id)->first();

            if(!$route){
                $this->json(null, "Route from origin and destionation not found", 400);
            }

            $query = $query->where("route_id", $route->id)->where("departure_time", ">=", date("Y-m-d H:i:s", strtotime("+1 hour")));

            if($req->query("departure_time")){
                $query = $query->where("departure_time", $req->query("departure_time"));
            }

            $schedules = $query->paginate(10);

            return $this->json($schedules);

        } catch (Exception $th) {
            return $this->json($th->getMessage(), "Bad Response", 400);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
                throw new \Exception('Train not found');
            }

            if (!$routeExist) {
                throw new \Exception('Route not found');
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
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function edit(Schedule $schedule)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Schedule  $schedule
     * @return \Illuminate\Http\Response
     */
    public function destroy(Schedule $schedule)
    {
        //
    }
}

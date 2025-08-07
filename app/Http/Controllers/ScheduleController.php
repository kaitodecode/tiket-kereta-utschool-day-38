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
     * @OA\Get(
     *     path="/api/schedules",
     *     summary="Get list of schedules",
     *     tags={"Schedules"},
     *     @OA\Parameter(
     *         name="origin_id",
     *         in="query",
     *         required=true,
     *         description="Origin station ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="destination_id", 
     *         in="query",
     *         required=true,
     *         description="Destination station ID",
     *         @OA\Schema(type="string", format="uuid")
     *     ),
     *     @OA\Parameter(
     *         name="departure_time",
     *         in="query",
     *         required=false,
     *         description="Departure date filter (Y-m-d)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
   public function index(Request $req)
{
    try {
        $origin_id = $req->query("origin_id");
        $destination_id = $req->query("destination_id");

        if (!$origin_id || !$destination_id) {
            return $this->json(null, "origin_id and destination_id is required", 400);
        }

        $route = Route::query()
            ->where("origin_id", $origin_id)
            ->where("destination_id", $destination_id)
            ->first();

        if (!$route) {
            return $this->json(null, "Route from origin and destination not found", 400);
        }

        $query = Schedule::with(['train', 'route.origin', 'route.destination'])
            ->where("route_id", $route->id);

        if ($req->has("departure_time")) {
            $query->whereDate("departure_time", ">=", $req->query("departure_time"));
        }

        $schedules = $query->paginate(10);

        return $this->json($schedules);
    } catch (Exception $th) {
        return $this->json($th->getMessage(), "Bad Response", 400);
    }
}




    /**
     * @OA\Get(
     *     path="/api/schedules/pagination",
     *     summary="Get paginated list of all schedules",
     *     tags={"Schedules"},
     *     @OA\Response(
     *         response=200,
     *         description="List of schedules retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer"),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(type="object")
     *             ),
     *             @OA\Property(property="total", type="integer"),
     *             @OA\Property(property="per_page", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function pagination(Request $req)
    {
        $schedules = Schedule::with(['train', 'route.origin', 'route.destination'])->paginate(10);
        return $this->json($schedules);
    }

    /**
     * @OA\Get(
     *     path="/api/schedules/{schedule}",
     *     summary="Get a specific schedule",
     *     tags={"Schedules"},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="Schedule ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
     */
    public function show(Schedule $schedule)
    {
        try {
            $schedule = Schedule::with(['train', 'route.origin', 'route.destination'])->find($schedule->id);
            
            if (!$schedule) {
                return $this->json(null, "Schedule not found", 404);
            }

            return $this->json($schedule, "Schedule retrieved successfully", 200);
        } catch (Exception $e) {
            return $this->json(null, $e->getMessage(), 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/schedules",
     *     summary="Create a new schedule",
     *     tags={"Schedules"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"train_id","route_id","departure_time","arrival_time","price"},
     *             @OA\Property(property="train_id", type="string"),
     *             @OA\Property(property="route_id", type="string"),
     *             @OA\Property(property="departure_time", type="string", format="datetime"),
     *             @OA\Property(property="arrival_time", type="string", format="datetime"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Schedule created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
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

            return $this->json($schedule, "Schedule created successfully", 201);
        } catch (Exception $th) {
            return $this->json($th->getMessage(), "Bad Response", 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/schedules/{schedule}",
     *     summary="Update a schedule",
     *     tags={"Schedules"},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="Schedule ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"train_id","route_id","departure_time","arrival_time","price"},
     *             @OA\Property(property="train_id", type="string"),
     *             @OA\Property(property="route_id", type="string"),
     *             @OA\Property(property="departure_time", type="string", format="datetime"),
     *             @OA\Property(property="arrival_time", type="string", format="datetime"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/schedules/{schedule}",
     *     summary="Delete a schedule",
     *     tags={"Schedules"},
     *     @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         required=true,
     *         description="Schedule ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Schedule deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Schedule not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
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

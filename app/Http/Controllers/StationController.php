<?php

namespace App\Http\Controllers;

use App\Models\Station;
use App\Http\Requests\StoreStationRequest;
use App\Http\Requests\UpdateStationRequest;
use Exception;
use Illuminate\Http\Request;

class StationController extends Controller
{
    // Mengganti nama function json() menjadi respondJson()
    protected function respondJson($data = null, $status = 200)
    {
        return response()->json([
            'success' => $status === 200,
            'data' => $data,
        ], $status);
    }

    /**
     * @OA\Get(
     *     path="/api/stations",
     *     summary="Get list of stations",
     *     tags={"Stations"},
     *     @OA\Parameter(
     *         name="city",
     *         in="query",
     *         required=false,
     *         description="Filter stations by city",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of stations retrieved successfully"
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
            $query = Station::query();

            // Apply filters if provided
            if ($req->has("city")) {
                $query = $query->where("city", $req->query("city"));
            }

            $stations = $query->paginate(10);
            return $this->respondJson($stations, 200); // Using the renamed method
        } catch (Exception $th) {
            return $this->respondJson(['error' => $th->getMessage()], 400); // Using the renamed method
        }
    }

    /**
     * @OA\Get(
     *     path="/api/stations/{station}",
     *     summary="Get a specific station",
     *     tags={"Stations"},
     *     @OA\Parameter(
     *         name="station",
     *         in="path",
     *         required=true,
     *         description="Station ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Station retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Station not found"
     *     )
     * )
     */
    public function show(Station $station)
    {
        try {
            return $this->respondJson($station, 200); // Using the renamed method
        } catch (Exception $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400); // Using the renamed method
        }
    }

    /**
     * @OA\Post(
     *     path="/api/stations",
     *     summary="Create a new station",
     *     tags={"Stations"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "latitude", "longitude", "city"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="latitude", type="string"),
     *             @OA\Property(property="longitude", type="string"),
     *             @OA\Property(property="city", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Station created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(StoreStationRequest $request)
    {
        try {
            $data = $request->validated();
            $station = Station::create($data);

            return $this->respondJson($station, 201); // Using the renamed method
        } catch (Exception $th) {
            return $this->respondJson(['error' => $th->getMessage()], 400); // Using the renamed method
        }
    }

    /**
     * @OA\Put(
     *     path="/api/stations/{station}",
     *     summary="Update a station",
     *     tags={"Stations"},
     *     @OA\Parameter(
     *         name="station",
     *         in="path",
     *         required=true,
     *         description="Station ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "code", "latitude", "longitude", "city"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="latitude", type="string"),
     *             @OA\Property(property="longitude", type="string"),
     *             @OA\Property(property="city", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Station updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Station not found"
     *     )
     * )
     */
    public function update(UpdateStationRequest $request, Station $station)
    {
        try {
            $data = $request->validated();

            if (!$station) {
                return $this->respondJson(['error' => "Station not found"], 404); // Using the renamed method
            }

            $station->update($data);

            return $this->respondJson($station, 200); // Using the renamed method
        } catch (Exception $th) {
            return $this->respondJson(['error' => $th->getMessage()], 400); // Using the renamed method
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/stations/{station}",
     *     summary="Delete a station",
     *     tags={"Stations"},
     *     @OA\Parameter(
     *         name="station",
     *         in="path",
     *         description="Station ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Station deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Station not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function destroy(Station $station)
    {
        try {
            if (!$station) {
                return $this->respondJson(['error' => "Station not found"], 404); // Using the renamed method
            }

            $station->delete();

            return $this->respondJson($station, 200); // Using the renamed method
        } catch (Exception $e) {
            return $this->respondJson(['error' => $e->getMessage()], 400); // Using the renamed method
        }
    }
/**
 * @OA\Get(
 *     path="/api/stations/all",
 *     summary="Get all stations without pagination",
 *     tags={"Stations"},
 *     @OA\Response(
 *         response=200,
 *         description="All stations retrieved successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Bad request"
 *     )
 * )
 */
public function allRoutes(Request $req)
{
    try {
        // Mengambil semua data tanpa pagination
        $stations = Station::all(); // Menggunakan get() untuk mengambil semua data tanpa pagination

        // Mengembalikan hasil sebagai JSON
        return response()->json([
            'success' => true,
            'data' => $stations
        ], 200);
    } catch (\Exception $th) {
        return response()->json([
            'error' => $th->getMessage()
        ], 400); // Menangani error jika terjadi
    }
}
}

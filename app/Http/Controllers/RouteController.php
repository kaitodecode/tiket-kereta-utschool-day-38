<?php

namespace App\Http\Controllers;

use App\Models\Route;
use App\Models\Station;
use Exception;
use Illuminate\Http\Request;

class RouteController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/routes",
     *     summary="Get list of routes",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="origin_id",
     *         in="query",
     *         description="Origin station ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="destination_id",
     *         in="query",
     *         description="Destination station ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of routes retrieved successfully"
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
            // Filter routes based on origin and destination station IDs
            $query = Route::query();

            if ($req->has("origin_id")) {
                $query = $query->where("origin_id", $req->query("origin_id"));
            }

            if ($req->has("destination_id")) {
                $query = $query->where("destination_id", $req->query("destination_id"));
            }

            $routes = $query->paginate(10);
            return $this->json($routes, 200);
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400);
        }
    }
    // Endpoint untuk mendapatkan daftar rute tanpa pagination
    /**
     * @OA\Get(
     *     path="/api/routes/all",
     *     summary="Get all routes without pagination",
     *     tags={"Routes"},
     *     @OA\Response(
     *         response=200,
     *         description="All routes retrieved successfully"
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
            $query = Route::query();

            // Filter berdasarkan origin_id dan destination_id jika ada
            if ($req->has("origin_id")) {
                $query = $query->where("origin_id", $req->query("origin_id"));
            }

            if ($req->has("destination_id")) {
                $query = $query->where("destination_id", $req->query("destination_id"));
            }

            $routes = $query->get(); // Mengambil semua data tanpa pagination
            return $this->json($routes, 200); // Menggunakan method json() untuk mengembalikan response
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400); // Error handling
        }
    }

    /**
     * @OA\Get(
     *     path="/api/routes/{route}",
     *     summary="Get a specific route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="Route ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     )
     * )
     */
    public function show(Route $route)
    {
        try {
            return $this->json($route, 200); // Return the specific route
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/routes",
     *     summary="Create a new route",
     *     tags={"Routes"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origin_id", "destination_id"},
     *             @OA\Property(property="origin_id", type="string"),
     *             @OA\Property(property="destination_id", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Route created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            // Validating the origin_id and destination_id
            $request->validate([
                'origin_id' => 'required|exists:stations,id',
                'destination_id' => 'required|exists:stations,id',
            ]);

            // Creating a new route
            $route = Route::create([
                'origin_id' => $request->origin_id,
                'destination_id' => $request->destination_id,
            ]);

            return $this->json($route, 201); // Return the newly created route
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/routes/{route}",
     *     summary="Update a route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="Route ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"origin_id", "destination_id"},
     *             @OA\Property(property="origin_id", type="string"),
     *             @OA\Property(property="destination_id", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     )
     * )
     */
    public function update(Request $request, Route $route)
    {
        try {
            // Validating the input data
            $request->validate([
                'origin_id' => 'required|exists:stations,id',
                'destination_id' => 'required|exists:stations,id',
            ]);

            // Update the route
            $route->update([
                'origin_id' => $request->origin_id,
                'destination_id' => $request->destination_id,
            ]);

            return $this->json($route, 200); // Return the updated route
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/routes/{route}",
     *     summary="Delete a route",
     *     tags={"Routes"},
     *     @OA\Parameter(
     *         name="route",
     *         in="path",
     *         required=true,
     *         description="Route ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Route deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Route not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function destroy(Route $route)
    {
        try {
            if (!$route) {
                return $this->json(['error' => "Route not found"], 404); // Use custom json() method
            }

            $route->delete();

            return $this->json($route, 200); // Return the deleted route
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400);
        }
    }
}

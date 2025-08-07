<?php

namespace App\Http\Controllers;

use App\Models\Train;
use App\Http\Requests\StoreTrainRequest;
use App\Http\Requests\UpdateTrainRequest;
use Exception;
use Illuminate\Http\Request;

class TrainController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/trains",
     *     summary="Get list of trains",
     *     tags={"Trains"},
     *     @OA\Parameter(
     *         name="class",
     *         in="query",
     *         required=false,
     *         description="Filter trains by class",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="capacity",
     *         in="query",
     *         required=false,
     *         description="Filter trains by capacity",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="pagination",
     *         in="query",
     *         required=false,
     *         description="Enable or disable pagination. Default is true.",
     *         @OA\Schema(type="boolean", default=true)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of trains retrieved successfully"
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
            $query = Train::query();

            // Apply filters if provided
            if ($req->has("class")) {
                $query = $query->where("class", $req->query("class"));
            }

            if ($req->has("capacity")) {
                $query = $query->where("capacity", ">=", $req->query("capacity"));
            }

            // Get pagination value from query (default is true)
            $pagination = $req->query('pagination', true);

            if ($pagination === "false") {
                // If pagination is false, get all data without pagination
                $trains = $query->get();  // Use get() to retrieve all records without pagination
            } else {
                // Use pagination if true
                $trains = $query->paginate(10);
            }

            // Return data as JSON
            return $this->json([
                'success' => true,
                'data' => $trains
            ], 200);
        } catch (Exception $th) {
            return $this->json([
                'error' => $th->getMessage()
            ], 400); // Handle error if any
        }
    }







    /**
     * @OA\Get(
     *     path="/api/trains/{train}",
     *     summary="Get a specific train",
     *     tags={"Trains"},
     *     @OA\Parameter(
     *         name="train",
     *         in="path",
     *         required=true,
     *         description="Train ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Train retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Train not found"
     *     )
     * )
     */
    public function show(Train $train)
    {
        try {
            // Laravel will automatically inject the model by its ID
            return $this->json($train, 200); // Directly use Laravel's json() method
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400); // Directly use Laravel's json() method
        }
    }


    /**
     * @OA\Post(
     *     path="/api/trains",
     *     summary="Create a new train",
     *     tags={"Trains"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "class", "code", "capacity"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="class", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="capacity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Train created successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function store(StoreTrainRequest $request)
    {
        try {
            $data = $request->validated();
            $train = Train::create($data);

            return $this->json($train, 201); // Directly use Laravel's json() method
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400); // Directly use Laravel's json() method
        }
    }

    /**
     * @OA\Put(
     *     path="/api/trains/{train}",
     *     summary="Update a train",
     *     tags={"Trains"},
     *     @OA\Parameter(
     *         name="train",
     *         in="path",
     *         required=true,
     *         description="Train ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "class", "code", "capacity"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="class", type="string"),
     *             @OA\Property(property="code", type="string"),
     *             @OA\Property(property="capacity", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Train updated successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Train not found"
     *     )
     * )
     */
    public function update(UpdateTrainRequest $request, Train $train)
    {
        try {
            $data = $request->validated();

            if (!$train) {
                return $this->json(['error' => "Train not found"], 404); // Directly use Laravel's json() method
            }

            $train->update($data);

            return $this->json($train, 200); // Directly use Laravel's json() method
        } catch (Exception $th) {
            return $this->json(['error' => $th->getMessage()], 400); // Directly use Laravel's json() method
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/trains/{train}",
     *     summary="Delete a train",
     *     tags={"Trains"},
     *     @OA\Parameter(
     *         name="train",
     *         in="path",
     *         required=true,
     *         description="Train ID",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Train deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Train not found"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function destroy(Train $train)
    {
        try {
            if (!$train) {
                return $this->json(['error' => "Train not found"], 404); // Directly use Laravel's json() method
            }

            $train->delete();

            return $this->json($train, 200); // Directly use Laravel's json() method
        } catch (Exception $e) {
            return $this->json(['error' => $e->getMessage()], 400); // Directly use Laravel's json() method
        }
    }
    /**
     * @OA\Get(
     *     path="/api/trains/allnopgnation",
     *     summary="Get all trains without pagination",
     *     tags={"Trains"},
     *     @OA\Parameter(
     *         name="Accept",
     *         in="header",
     *         required=true,
     *         description="Accept header for JSON response",
     *         @OA\Schema(type="string", default="application/json")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="All trains retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     )
     * )
     */
    public function allRoutesNoPagination(Request $req)
    {
        try {
            // Mengambil semua data tanpa pagination
            $trains = Train::all(); // Menggunakan all() untuk mengambil semua data tanpa pagination

            // Mengembalikan hasil sebagai JSON menggunakan this->json()
            return $this->json([
                'success' => true,
                'data' => $trains
            ], 200);
        } catch (\Exception $th) {
            return $this->json([
                'error' => $th->getMessage()
            ], 400); // Menangani error jika terjadi
        }
    }
}

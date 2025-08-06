<?php



use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TrainController;

/*
|---------------------------
-----------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('trains', TrainController::class);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("auth")->controller(AuthController::class)->group(function(){
    Route::post("/login", "login");
    Route::post("/register", "register");
    Route::post("/logout", "logout");
    Route::get("/me", "me")->middleware("auth:sanctum");
});

Route::prefix("schedules")->controller(ScheduleController::class)->group(function () {
    Route::get("/", "index");
    Route::get("/pagination", "pagination");
    Route::get("/{schedule}", "show");
    Route::post("/", "store")->middleware("auth:sanctum");
    Route::put("/{schedule}", "update")->middleware("auth:sanctum");
    Route::delete("/{schedule}", "destroy")->middleware("auth:sanctum");
});

Route::prefix("trains")->controller(TrainController::class)->group(function () {
    Route::get("/", "index");
    Route::get("/{train}", "show");
    Route::post("/", "store");
    Route::put("/{train}", "update");
    Route::delete("/{train}", "destroy");
});
Route::prefix("bookings")->controller(BookingController::class)->group(function () {
    Route::get("/", "index");
    Route::get("/history", "history")->middleware("auth:sanctum");
    Route::get("/{booking}", "show");
    Route::post("/", "store")->middleware("auth:sanctum");
    Route::put("/{booking}", "update")->middleware("auth:sanctum");
    Route::delete("/{booking}", "destroy")->middleware("auth:sanctum");
});
























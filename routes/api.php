<?php

use App\Http\Controllers\ScheduleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::prefix("schedule")->controller(ScheduleController::class)->group(function () {
    Route::get("/", "index");
    Route::get("/{schedule}", "show");
    Route::post("/", "store");
    Route::put("/{schedule}", "update");
    Route::delete("/{schedule}", "destroy");
});

<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/payment/success/{booking}', [\App\Http\Controllers\BookingController::class, 'success']);
Route::get('/payment/failure/{booking}', [\App\Http\Controllers\BookingController::class, 'failure']);

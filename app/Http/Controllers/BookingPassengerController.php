<?php

namespace App\Http\Controllers;

use App\Models\BookingPassenger;
use App\Http\Requests\StoreBookingPassengerRequest;
use App\Http\Requests\UpdateBookingPassengerRequest;

class BookingPassengerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\StoreBookingPassengerRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreBookingPassengerRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\BookingPassenger  $bookingPassenger
     * @return \Illuminate\Http\Response
     */
    public function show(BookingPassenger $bookingPassenger)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\BookingPassenger  $bookingPassenger
     * @return \Illuminate\Http\Response
     */
    public function edit(BookingPassenger $bookingPassenger)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateBookingPassengerRequest  $request
     * @param  \App\Models\BookingPassenger  $bookingPassenger
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateBookingPassengerRequest $request, BookingPassenger $bookingPassenger)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\BookingPassenger  $bookingPassenger
     * @return \Illuminate\Http\Response
     */
    public function destroy(BookingPassenger $bookingPassenger)
    {
        //
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends BaseModel
{

    public function passengers()
    {
        return $this->hasMany(BookingPassenger::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}

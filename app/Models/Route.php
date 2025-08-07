<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Route extends BaseModel
{
    use HasFactory;

    // Relasi dengan model Station (origin station)
    public function origin()
    {
        return $this->belongsTo(Station::class, 'origin_id');
    }

    // Relasi dengan model Station (destination station)
    public function destination()
    {
        return $this->belongsTo(Station::class, 'destination_id');
    }
}

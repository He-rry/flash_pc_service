<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    protected $fillable = ['route_name', 'waypoints', 'distance', 'duration'];

    protected $casts = [
        'waypoints' => 'array',
    ];
}

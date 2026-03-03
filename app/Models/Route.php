<?php

namespace App\Models;

use App\Http\Controllers\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Route extends Model
{
    use LogsActivity;
    protected $fillable = ['route_name', 'waypoints', 'distance', 'duration'];

    protected $casts = [
        'waypoints' => 'array',
    ];
}

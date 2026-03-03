<?php

namespace App\Models;

use App\Http\Controllers\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    use LogsActivity;
    protected $fillable = ['service_name'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

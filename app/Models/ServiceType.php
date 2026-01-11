<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceType extends Model
{
    protected $fillable = ['service_name'];

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

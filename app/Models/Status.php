<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['status_name'];
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

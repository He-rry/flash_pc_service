<?php

namespace App\Models;

use App\Http\Controllers\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    use LogsActivity;
    protected $fillable = ['status_name'];
    public function services()
    {
        return $this->hasMany(Service::class);
    }
}

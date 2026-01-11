<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_name',
        'customer_phone',
        'customer_email',
        'customer_address',
        'lat',
        'long',
        'pc_model',
        'issue_description',
        'status_id',
        'service_type_id'
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}

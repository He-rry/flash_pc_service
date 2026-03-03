<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id',
        'action',
        'description',
        'shop_id',
        'module',
        'changes',
        'ip_address'
    ];

    // JSON Data to array 
    protected $casts = [
        'changes' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
    public function services()
    {
        return $this->belongsTo(Service::class);
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }
    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }
}

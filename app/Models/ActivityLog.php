<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    // Migration နဲ့ အညီ အကုန်ဖြည့်လိုက်ပါ
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
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

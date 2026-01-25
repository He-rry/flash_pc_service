<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'lat',
        'lng',
        'address',
        'region',
        'waypoints',
        'created_at', // ဒါလေး ထည့်ပေးပါ
        'updated_at'  // ဒါလေး ထည့်ပေးပါ
    ];

    protected $casts = [
        'waypoints' => 'array',
    ];
    public function scopeApplyFilters($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->when($filters['region'] ?? null, function ($q, $region) {
            $q->where('region', $region);
        })->when($filters['period'] ?? null, function ($q, $period) {
            if ($period !== 'all') {
                $q->where('created_at', '<=', now()->subMonths((int)$period)->startOfDay());
            }
        });
    }
}

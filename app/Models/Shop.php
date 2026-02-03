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
        'added_by',
        'waypoints',
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'waypoints' => 'array',
    ];
    public function admin()
    {
        return $this->belongsTo(User::class, 'added_by');
    }
    public function activity()
    {
        return $this->hasMany(ActivityLog::class, 'shop_id')->latest();
    }
    public function scopeApplyFilters($query, array $filters)
    {
        return $query->when($filters['search'] ?? null, function ($q, $search) {
            $q->where('name', 'like', '%' . $search . '%');
        })->when($filters['region'] ?? null, function ($q, $region) {
            $q->where('region', $region);
        })
            ->when($filters['from_date'] ?? null, function ($q, $from) {
                $q->whereDate('created_at', '>=', $from);
            })
            ->when($filters['to_date'] ?? null, function ($q, $to) {
                $q->whereDate('created_at', '<=', $to);
            })
            ->when($filters['period'] ?? null, function ($q, $period) use ($filters) {
                if (empty($filters['from_date']) && empty($filters['to_date'])) {
                    if ($period === 'today') {
                        $q->whereDate('created_at', now());
                    } elseif (is_numeric($period)) {
                        // 3, 6, 12 လ အတွက် logic
                        $q->where('created_at', '<=', now()->subMonths((int)$period));
                    }
                }
            });
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityLogResource;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with(['user', 'shop']);

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        $logs = $query->latest()->paginate(15);
        return ActivityLogResource::collection($logs);
    }

    public function show($id)
    {
        $log = ActivityLog::with(['user', 'shop'])->findOrFail($id);
        return new ActivityLogResource($log);
    }
}

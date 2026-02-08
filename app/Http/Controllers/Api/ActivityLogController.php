<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');

        if ($request->filled('shop_id')) {
            $query->where('shop_id', $request->shop_id);
        }
        $logs = $query->latest()->paginate(15);

        // json to view
        return response()->json($logs);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Gate;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-logs');
        $query = ActivityLog::with('user');
        if ($request->has('shop_id') && $request->shop_id != '') {
            $query->where('shop_id', $request->shop_id);
        }

        $logs = $query->latest()->paginate(15);
        return view('auth.logs.index', compact('logs'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ActivityLog;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ActivityLog::with('user');
        if ($request->has('shop_id') && $request->shop_id != '') {
            $query->where('shop_id', $request->shop_id);
        }

        $logs = $query->latest()->paginate(15);
        return view('auth.logs.index', compact('logs'));
    }
}

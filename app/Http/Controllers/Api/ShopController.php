<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::with('admin')->applyFilters($request->all())->latest();

        $allFiltered = (clone $query)->get();
        $paginated = $query->paginate(10);
        /** @var \App\Models\User $user */ // 
        // လက်ရှိ Login ဝင်ထားတဲ့ User ရဲ့ Permission များကို စစ်ဆေးခြင်း
        $user = Auth::user();
        $permissions = [
            'can_view_logs' => $user ? $user->can('view-logs') : false,
            'can_edit_shop' => $user ? $user->can('shop-edit') : false,
            'can_delete_shop' => $user ? $user->can('shop-delete') : false,
            'can_import' => $user ? $user->can('shop-import') : false,
            'can_export' => $user ? $user->can('shop-export') : false,
        ];

        return response()->json(array_merge(
            $paginated->toArray(),
            [
                'all_filtered' => $allFiltered,
                'user_permissions' => $permissions // Permission data
            ]
        ));
    }
}

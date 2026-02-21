<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Services\ShopService;
use App\Http\Requests\StoreShopRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    protected $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }
    public function index(Request $request)
    {
        $query = Shop::with('admin')->applyFilters($request->all())->latest();
        $allFiltered = (clone $query)->get();
        $paginated = $query->paginate(10);

        $user = Auth::user();
        $permissions = [
            'can_view_logs' => $user ? $user->can('view-logs') : false,
            'can_edit_shop' => $user ? $user->can('shop-edit') : false,
            'can_delete_shop' => $user ? $user->can('shop-delete') : false,
        ];

        return response()->json([
            'data' => $paginated->items(),
            'all_filtered' => $allFiltered,
            'total' => $paginated->total(),
            'last_page' => $paginated->lastPage(),
            'links' => $paginated->linkCollection(),
            'user_permissions' => $permissions
        ]);
    }

    public function store(StoreShopRequest $request)
    {
        $shop = $this->service->store($request->validated());
        return response()->json(['status' => 'success', 'data' => $shop], 201);
    }

    public function update(Request $request, $id)
    {
        $shop = $this->service->update($id, $request->all());
        return response()->json(['status' => 'success', 'data' => $shop]);
    }

    public function destroy($id)
    {
        $this->service->delete($id);
        return response()->json(['status' => 'success', 'message' => 'Deleted successfully']);
    }

    public function show($id) 
    {
        $logs = $this->service->getShopLogs($id);
        return response()->json($logs);
    }
}

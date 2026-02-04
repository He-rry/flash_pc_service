<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\ShopService;
use App\Http\Requests\StoreShopRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ShopController extends Controller
{
    protected $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
        $this->middleware('permission:manage-shops')->only(['create', 'store', 'update', 'import', 'export', 'downloadDuplicates']);
        $this->middleware('permission:delete-shops')->only(['destroy']);
        $this->middleware('permission:view-logs')->only(['getLogs']);
    }

    /**
     * ဆိုင်များစာရင်းနှင့် Filter ပြုလုပ်ခြင်း
     */
    public function create(Request $request)
    {
        $shops = Shop::applyFilters($request->all())->latest()->paginate(10);
        $shops->appends($request->all());

        $regions = Shop::whereNotNull('region')->distinct()->pluck('region');

        return view('auth.maps.create', compact('shops', 'regions'));
    }
    public function store(StoreShopRequest $request)
    {
        Gate::authorize('manage-shops');
        try {
            $this->service->store($request->validated());
            return redirect()->back()->with('success', 'ဆိုင်အသစ်ကို အောင်မြင်စွာ ထည့်သွင်းပြီးပါပြီ။');
        } catch (\Exception $e) {
            return back()->withErrors(['lat' => $e->getMessage()])->withInput();
        }
    }
    public function update(Request $request, $id)
    {
        Gate::authorize('manage-shops');
        try {
            $shop = $this->service->update($id, $request->only(['name', 'region', 'lat', 'lng']));

            return response()->json([
                'status'  => 'success',
                'message' => 'Shop updated successfully!',
                'data'    => $shop
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ပြင်ဆင်မှု မအောင်မြင်ပါ - ' . $e->getMessage()
            ], 500);
        }
    }
    public function destroy($id)
    {
        Gate::authorize('delete-shops');
        try {
            $this->service->delete($id);

            return response()->json([
                'status'  => 'success',
                'message' => 'Shop deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'ဖျက်၍မရပါ - ' . $e->getMessage()
            ], 500);
        }
    }
    public function import(Request $request)
    {
        Gate::authorize('manage-shops');
        try {
            $result = $this->service->importShops($request->file('file'), 'skip');

            if (count($result['duplicates']) > 0) {
                session()->put('duplicates', $result['duplicates']);
                session()->put('warning_msg', 'ထပ်နေသော ဒေတာ ' . count($result['duplicates']) . ' ခု တွေ့ရှိရပါသည်။');
                return back()->with('warning', true);
            }

            return back()->with('success', 'Imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import မအောင်မြင်ပါ - ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        Gate::authorize('manage-shops');
        $filters = $request->only(['search', 'region', 'period', 'from_date', 'to_date']);
        return $this->service->exportShops($filters);
    }
    public function downloadDuplicates()
    {
        Gate::authorize('manage-shops');
        $duplicates = session('duplicates', []);

        if (empty($duplicates)) {
            return back()->with('error', 'Download ချရန် ဒေတာမရှိပါ။');
        }

        $file = $this->service->exportDuplicates($duplicates);
        session()->forget(['duplicates', 'warning_msg']);

        return $file;
    }
    public function getLogs($id)
    {
        Gate::authorize('view-logs');
        try {
            $logs = $this->service->getShopLogs($id);
            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

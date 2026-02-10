<?php

namespace App\Http\Controllers;

use App\Repositories\ShopRepository;
use App\Services\ShopService;
use App\Http\Requests\StoreShopRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ShopController extends Controller
{
    protected $service;
    protected $repo;

    public function __construct(ShopService $service, ShopRepository $repo)
    {
        $this->service = $service;
        $this->repo = $repo;

        $this->middleware('permission:view-shop-management')->only(['create', 'store', 'update', 'import', 'export', 'downloadDuplicates']);
        $this->middleware('permission:delete-shops')->only(['destroy']);
        $this->middleware('permission:view-logs')->only(['getLogs']);
    }

    public function create(Request $request)
    {
        $shops = $this->repo->getFilteredShops($request->all());
        $regions = $this->repo->getDistinctRegions();
        $permissions = Auth::user()->getAllPermissions()->pluck('name')->toArray();

        return view('auth.maps.create', compact('shops', 'regions', 'permissions'));
    }

    public function store(StoreShopRequest $request)
    {
        try {
            $this->service->store($request->validated());
            return redirect()->back()->with('success', 'ဆိုင်အသစ်ကို အောင်မြင်စွာ ထည့်သွင်းပြီးပါပြီ။');
        } catch (\Exception $e) {
            return back()->withErrors(['lat' => $e->getMessage()])->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $data = $request->only(['name', 'region', 'lat', 'lng']);
            $shop = $this->service->update($id, $data);

            return response()->json([
                'status'  => 'success',
                'message' => 'Shop updated successfully!',
                'data'    => $shop
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        Gate::authorize('delete-shops');
        try {
            $this->service->delete($id);
            return response()->json(['status' => 'success', 'message' => 'Shop deleted successfully!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function import(Request $request)
    {
        try {
            $result = $this->service->importShops($request->file('file'), 'skip');
            if (count($result['duplicates']) > 0) {
                session()->put('duplicates', $result['duplicates']);
                session()->put('warning_msg', 'ထပ်နေသော ဒေတာ ' . count($result['duplicates']) .
                    ' ခု တွေ့ရှိရပါသည်။');
                return back()->with('warning', true);
            }
            return back()->with('success', 'Imported successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Import မအောင်မြင်ပါ - ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        return $this->service->exportShops($request->only(['search', 'region', 'period', 'from_date', 'to_date']));
    }
    public function downloadDuplicates()
    {
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
        try {
            $logs = $this->service->getShopLogs($id);
            return response()->json($logs);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

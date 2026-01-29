<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Services\ShopService;
use App\Http\Requests\StoreShopRequest;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    protected $service;

    public function __construct(ShopService $service)
    {
        $this->service = $service;
    }
    public function create(Request $request)
    {
        $shops = Shop::applyFilters($request->all())->latest()->paginate(10);
        $shops->appends($request->all());
        $regions = Shop::whereNotNull('region')->distinct()->pluck('region');
        return view('auth.maps.create', compact('shops', 'regions'));
    }

    /**
     * Store a newly created shop in storage
     */
    public function store(StoreShopRequest $request)
    {
        try {
            $this->service->create($request->validated());

            return redirect()->back()->with('success', 'ဆိုင်အသစ်ကို အောင်မြင်စွာ ထည့်သွင်းပြီးပါပြီ။');
        } catch (\Exception $e) {
            return back()->withErrors(['lat' => $e->getMessage()])->withInput();
        }
    }

    /**
     * Excel Import လုပ်ခြင်း
     */
    public function import(Request $request)
    {
        $result = $this->service->importShops($request->file('file'), 'skip');

        if (count($result['duplicates']) > 0) {
            session()->put('duplicates', $result['duplicates']);
            session()->put('warning_msg', 'ထပ်နေသော ဒေတာ ' . count($result['duplicates']) . ' ခု တွေ့ရှိရပါသည်။');
            return back()->with('warning', true);
        }

        return back()->with('success', 'Imported successfully!');
    }

    /**
     * Export Shops to Excel with current filters
     */
    public function export(Request $request)
    {
        $filters = [
            'search'    => $request->query('search'),
            'region'    => $request->query('region'),
            'period'    => $request->query('period'),
            'from_date' => $request->query('from_date'),
            'to_date'   => $request->query('to_date'),
        ];

        return $this->service->exportShops($filters);
    }

    /**
     * Download Duplicate Data session
     */
    public function downloadDuplicates(Request $request)
    {
        $duplicates = session('duplicates', []);

        if (empty($duplicates)) {
            return back()->with('error', 'Download ချရန် ဒေတာမရှိပါ။');
        }

        $file = $this->service->exportDuplicates($duplicates);
        session()->forget(['duplicates', 'warning_msg']);

        return $file;
    }
}

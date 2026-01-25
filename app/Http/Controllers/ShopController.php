<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ShopService;
use App\Models\Shop;
use Carbon\Carbon;

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
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'name'   => 'required|string|max:255|unique:shops,name',
            'region' => 'required|string|max:255',
            'lat'    => 'required|numeric',
            'lng'    => 'required|numeric',
        ], [
            'name.unique'   => 'ဤဆိုင်နာမည်သည် ရှိပြီးသား ဖြစ်နေပါသည်။',
            'lat.required'  => 'မြေပုံညွှန်း (Latitude) လိုအပ်ပါသည်။',
            'lng.required'  => 'မြေပုံညွှန်း (Longitude) လိုအပ်ပါသည်။',
        ]);


        $exists = Shop::where('lat', $request->lat)
            ->where('lng', $request->lng)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'lat' => 'ဤတည်နေရာ (Coordinates) တွင် ဆိုင်တစ်ဆိုင် ရှိနှင့်ပြီးသား ဖြစ်ပါသည်။'
            ])->withInput();
        }

        Shop::create($request->all());

        return redirect()->back()->with('success', 'Shop added successfully!');
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
    public function export(Request $request)
    {
        // Filter parameters များကို စုစည်းခြင်း
        $filters = [
            'search' => $request->query('search'),
            'region' => $request->query('region'),
            'period' => $request->query('period'),
        ];
        return $this->service->exportShops($filters);
    }

    public function show($id)
    {
        return back();
    }
}

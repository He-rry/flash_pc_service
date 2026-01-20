<?php

namespace App\Http\Controllers;

use App\Interfaces\RouterInterface;
use Illuminate\Http\Request;

use App\Models\Shop;
use App\Models\Route;

class RouteController extends Controller
{
    protected $routeRepo;

    public function __construct(RouterInterface $routeRepo)
    {
        $this->routeRepo = $routeRepo;
    }

    public function index()
    {
        $shops = Shop::all(['name', 'lat', 'lng']); // DB ကနေ ဆွဲထုတ်
        $routes = Route::latest()->get();

        return view('auth.maps.index', compact('shops', 'routes'));
    }
    public function create(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'lat'  => 'required',
            'lng'  => 'required',
        ]);

        Shop::create([
            'name' => $request->name,
            'lat'  => $request->lat,
            'lng'  => $request->lng,
        ]);

        return redirect()->route('maps.index')->with('success', 'Shop registered successfully!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required',
            'waypoints' => 'required'
        ]);

        $data = $request->all();

        // JSON String လာတာသေချာရင် decode လုပ်မယ်၊ မဟုတ်ရင် အတိုင်းထားမယ်
        $data['waypoints'] = is_string($request->waypoints) ? json_decode($request->waypoints, true) : $request->waypoints;

        $this->routeRepo->store($data);
        return redirect()->back()->with('success', 'Route saved successfully!');
    }

    public function destroy($id)
    {
        $this->routeRepo->delete($id);
        return redirect()->back()->with('success', 'Route deleted!');
    }
    public function savedRoutes()
    {
        $routes = Route::latest()->get();
        return view('auth.maps.saved_map_route', compact('routes'));
    }

    // ၂။ ရွေးချယ်လိုက်တဲ့ Route တစ်ခုကို Map ပေါ်မှာ အသေးစိတ်ကြည့်ရန်
    public function showRoute($id)
    {
        $route = Route::findOrFail($id);
        // သင့် folder structure က auth/maps/ ဖြစ်နေလို့ ဒါကို သုံးပါ
        return view('auth.maps.show_route', compact('route'));
    }
}

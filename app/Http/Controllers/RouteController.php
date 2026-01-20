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

        return view('auth.routes.index', compact('shops', 'routes'));
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

        return redirect()->route('routes.index')->with('success', 'Shop registered successfully!');
    }
    public function store(Request $request)
    {
        $request->validate([
            'route_name' => 'required',
            'waypoints' => 'required' // JSON string လာမှာဖြစ်ပါတယ်
        ]);

        $data = $request->all();
        $data['waypoints'] = json_decode($request->waypoints, true);

        $this->routeRepo->store($data);
        return redirect()->back()->with('success', 'Route saved successfully!');
    }

    public function destroy($id)
    {
        $this->routeRepo->delete($id);
        return redirect()->back()->with('success', 'Route deleted!');
    }
}

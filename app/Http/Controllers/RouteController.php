<?php

namespace App\Http\Controllers;

use App\Services\RouteService;
use App\Http\Requests\StoreShopRequest;
use App\Http\Requests\StoreRouteRequest;
use App\Services\ShopService;
use Illuminate\Support\Facades\Gate;

class RouteController extends Controller
{
    protected RouteService $routeService;
    protected ShopService $shopService;
    public function __construct(RouteService $routeService, ShopService $shopService)
    {
        $this->routeService = $routeService;
        $this->shopService = $shopService;
    }

    public function index()
    {
        $shops = $this->shopService->list();
        $routes = $this->routeService->list();

        return view('auth.maps.index', compact('shops', 'routes'));
    }
    public function create(StoreShopRequest $request)
    {
        $this->shopService->create($request->validated());

        return redirect()->route('maps.index')->with('success', 'Shop registered successfully!');
    }
    public function store(StoreRouteRequest $request)
    {
        Gate::authorize('manage-routes');
        $data = $request->validated();

        $this->routeService->create($data + ['waypoints' => $request->waypoints]);

        return redirect()->back()->with('success', 'Route saved successfully!');
    }

    public function destroy($id)
    {
        Gate::authorize('manage-routes');
        $this->routeService->delete($id);

        return redirect()->back()->with('success', 'Route deleted!');
    }
    public function savedRoutes()
    {
        $routes = $this->routeService->list();

        return view('auth.maps.saved_map_route', compact('routes'));
    }
    public function showRoute($id)
    {
        $route = $this->routeService->find($id);

        if (! $route) {
            abort(404);
        }

        return view('auth.maps.show_route', compact('route'));
    }
}

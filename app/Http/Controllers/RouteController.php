<?php

namespace App\Http\Controllers;

use App\Services\RouteService;
use App\Http\Requests\StoreRouteRequest;
use Illuminate\Support\Facades\Gate;

class RouteController extends Controller
{
    protected RouteService $routeService;
    public function __construct(RouteService $routeService)
    {
        $this->routeService = $routeService;
        $this->middleware('permission:manage-routes')->only(['store', 'destroy']);
    }

    public function index()
    {
        $routes = $this->routeService->list();
        return view('auth.maps.index', compact('routes'));
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

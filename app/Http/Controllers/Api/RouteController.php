<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\ApiResponse;
use App\Services\RouteService;
use App\Http\Requests\StoreRouteRequest;

class RouteController extends Controller
{
    use ApiResponse;

    protected RouteService $service;

    public function __construct(RouteService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->list();
        return $this->success($data, 'Routes retrieved');
    }

    public function show($id)
    {
        $route = $this->service->find($id);

        if (! $route) {
            return $this->error('Route not found', 404);
        }

        return $this->success($route, 'Route retrieved');
    }

    public function store(StoreRouteRequest $request)
    {
        $payload = $request->validated();

        $route = $this->service->create($payload + ['waypoints' => $request->waypoints ?? null]);

        return $this->success($route, 'Route created', 201);
    }

    public function destroy($id)
    {
        $deleted = $this->service->delete($id);

        if (! $deleted) {
            return $this->error('Route not found or could not be deleted', 404);
        }

        return $this->success(null, 'Route deleted');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\RoutePlannerService;

class RoutePlannerController extends Controller
{
    protected $service;

    public function __construct(RoutePlannerService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $data = $this->service->list();
        $shops = $data['shops'];
        $routes = $data['routes'];
        return view('auth.index', compact('shops', 'routes'));
    }
}

<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\RoutePlannerService;

class RoutePlannerController extends Controller {
    protected $service;

    public function __construct(RoutePlannerService $service) {
        $this->service = $service;
    }

    public function index() {
        $data = $this->service->list();
        return response()->json([
            'shops' => $data['shops'],
            'routes' => $data['routes']
        ]);
    }
}
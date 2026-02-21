<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use App\Http\Resources\ServiceResource;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ServiceController extends Controller
{
    use AuthorizesRequests;

    protected $service;

    public function __construct(ServiceService $service)
    {
        $this->service = $service; 
    }

    public function index()
    {
        $services = $this->service->getServiceList();
        return ServiceResource::collection($services);
    }

    public function store(StoreServiceRequest $request)
    {
        $this->authorize('manage-services');
        $service = $this->service->createReport($request->validated());
        return new ServiceResource($service);
    }

    public function storeCustomerReport(StoreServiceRequest $request)
    {
        $service = $this->service->createReport($request->validated());
        return response()->json([
            'message' => 'လက်ခံရရှိပါပြီ။',
            'data' => new ServiceResource($service)
        ], 201);
    }

    public function show($id)
    {
        $service = $this->service->find($id);
        return new ServiceResource($service);
    }

    public function update(UpdateServiceRequest $request, $id)
    {
        $this->authorize('manage-services');
        $service = $this->service->updateRecord($id, $request->validated()); 
        return new ServiceResource($service);
    }

    public function destroy($id)
    {
        $this->authorize('manage-services');
        $this->service->deleteRecord($id); //
        return response()->json(['message' => 'Deleted successfully!']);
    }

    public function track(Request $request)
    {
        $request->validate(['phone' => 'required']);
        $service = $this->service->getTrackInfo($request->phone);
        
        if (!$service) {
            return response()->json(['message' => 'ရှာမတွေ့ပါ'], 404);
        }
        return new ServiceResource($service);
    }
}
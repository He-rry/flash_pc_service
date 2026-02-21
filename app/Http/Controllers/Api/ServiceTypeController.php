<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ServiceTypeService;
use App\Http\Resources\ServiceTypeResource;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
class ServiceTypeController extends Controller
{
    use AuthorizesRequests;

    protected $service;

    public function __construct(ServiceTypeService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $types = $this->service->list();
        return ServiceTypeResource::collection($types);
    }

    public function store(Request $request)
    {
        $this->authorize('manage-services');

        $validated = $request->validate([
            'service_name' => 'required|unique:service_types,service_name'
        ]);

        $type = $this->service->create($validated);
        return new ServiceTypeResource($type);
    }
    public function show($id)
    {
        $type = $this->service->find($id);
        return new ServiceTypeResource($type);
    }
    public function update(Request $request, $id)
    {
        $this->authorize('manage-services');

        $validated = $request->validate([
            'service_name' => 'required'
        ]);

        $type = $this->service->update($id, $validated);
        return new ServiceTypeResource($type);
    }

    public function destroy($id)
    {
        $this->authorize('manage-services');

        try {
            $this->service->delete($id);
            return response()->json(['message' => 'Service Type deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'ဖျက်လို့မရပါဘူး။ ဒီ Service Type ကို အသုံးပြုထားတဲ့ Service records တွေ ရှိနေပါသေးတယ်။'], 400);
        }
    }
}

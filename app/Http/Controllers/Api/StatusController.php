<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StatusService;
use App\Http\Resources\StatusResource;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class StatusController extends Controller
{
    use AuthorizesRequests;
    protected $service;

    public function __construct(StatusService $service)
    {
        $this->service = $service;
    }
    public function index()
    {
        $statuses = $this->service->list();
        return StatusResource::collection($statuses);
    }
    public function store(Request $request)
    {
        $this->authorize('manage-services');

        $validated = $request->validate([
            'status_name' => 'required|unique:statuses|max:50'
        ]);

        $status = $this->service->create($validated);
        return new StatusResource($status);
    }

    public function show($id)
    {
        $status = $this->service->find($id);
        return new StatusResource($status);
    }
    public function update(Request $request, $id)
    {
        $this->authorize('manage-services');

        $validated = $request->validate([
            'status_name' => 'required|max:50'
        ]);

        $status = $this->service->update($id, $validated);
        return new StatusResource($status);
    }
    public function destroy($id)
    {
        $this->authorize('manage-services');
        
        try {
            $this->service->delete($id);
            return response()->json(['message' => 'Status deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Cannot delete status. It might be in use.'], 400);
        }
    }
}
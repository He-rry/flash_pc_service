<?php

namespace App\Repositories;

use App\Interfaces\ServiceInterface;
use App\Models\Service;
use App\Models\Status;
use App\Models\ServiceType;

class ServiceRepository implements ServiceInterface {
    
    public function getAllServices() {
        return Service::with(['status', 'serviceType'])->latest()->paginate(10);
    }

    public function findById($id) {
        return Service::findOrFail($id);
    }

    public function storeService(array $data) {
        return Service::create($data);
    }

    public function updateService($id, array $data) {
        $service = Service::findOrFail($id);
        $service->update($data);
        return $service;
    }

    public function deleteService($id) {
        $service = Service::findOrFail($id);
        return $service->delete();
    }

    public function findByPhone($phone) {
        return Service::where('customer_phone', $phone)->with('status')->latest()->first();
    }

    // Status နဲ့ Type တွေကို View မှာ ပြဖို့အတွက်
    public function getAllStatuses() { return Status::all(); }
    public function getAllServiceTypes() { return ServiceType::all(); }
}
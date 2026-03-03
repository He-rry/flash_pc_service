<?php

namespace App\Repositories;

use App\Interfaces\ServiceTypeInterface;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;
use App\Events\ActivityLogged;

class ServiceTypeRepository implements ServiceTypeInterface
{
    protected $model;

    public function __construct(ServiceType $model)
    {
        $this->model = $model;
    }

    public function getAllServiceTypes()
    {
        $result = DB::select("CALL sp_GetAllServiceTypes()");
        return ServiceType::hydrate($result);
    }

    public function findServiceTypeById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function createServiceType(array $data)
    {
        DB::statement("CALL sp_CreateServiceType(?)", [
            $data['service_name']
        ]);
        $newServiceType = ServiceType::latest()->first();
        event(new ActivityLogged($newServiceType, 'ADD', "Created new ServiceType: " . $newServiceType->service_name));

        return $newServiceType;
    }

    public function updateServiceType($id, array $data)
    {
        DB::statement("CALL sp_UpdateServiceType(?, ?)", [
            $id,
            $data['service_name']
        ]);
        $updatedServiceType = $this->model->find($id);
        event(new ActivityLogged($updatedServiceType, 'UPDATE', "Updated ServiceType ID: " . $id));

        return $updatedServiceType;
    }

    public function deleteServiceType($id)
    {
        $serviceType = $this->model->find($id);
        
        if ($serviceType) {
            event(new ActivityLogged($serviceType, 'DELETE', "Deleted ServiceType: " . $serviceType->service_name));
        }
        return DB::statement("CALL sp_DeleteServiceType(?)", [$id]);
    }
}
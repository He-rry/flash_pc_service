<?php

namespace App\Repositories;

use App\Interfaces\ServiceTypeInterface;
use App\Models\ServiceType;
use Illuminate\Support\Facades\DB;

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
        return DB::statement("CALL sp_CreateServiceType(?)", [
            $data['service_name']
        ]);
    }

    public function updateServiceType($id, array $data)
    {
        return DB::statement("CALL sp_UpdateServiceType(?, ?)", [
            $id,
            $data['service_name']
        ]);
    }

    public function deleteServiceType($id)
    {
       return DB::statement("CALL sp_DeleteServiceType(?)", [$id]);
    }
}

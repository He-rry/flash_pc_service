<?php

namespace App\Repositories;

use App\Interfaces\ServiceTypeInterface;
use App\Models\ServiceType;

class ServiceTypeRepository implements ServiceTypeInterface
{
    protected $model;

    public function __construct(ServiceType $model)
    {
        $this->model = $model;
    }

    public function getAllServiceTypes()
    {
        return $this->model->all();
    }

    public function findServiceTypeById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function createServiceType(array $data)
    {
        return $this->model->create($data);
    }

    public function updateServiceType($id, array $data)
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function deleteServiceType($id)
    {
        $item = $this->model->findOrFail($id);
        return $item->delete();
    }
}

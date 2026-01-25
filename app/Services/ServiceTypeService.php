<?php

namespace App\Services;

use App\Interfaces\ServiceTypeInterface;

class ServiceTypeService
{
    protected $repo;

    public function __construct(ServiceTypeInterface $repo)
    {
        $this->repo = $repo;
    }

    public function list()
    {
        return $this->repo->getAllServiceTypes();
    }

    public function find($id)
    {
        return $this->repo->findServiceTypeById($id);
    }

    public function create(array $data)
    {
        return $this->repo->createServiceType($data);
    }

    public function update($id, array $data)
    {
        return $this->repo->updateServiceType($id, $data);
    }

    public function delete($id)
    {
        return $this->repo->deleteServiceType($id);
    }
}

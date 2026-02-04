<?php

namespace App\Services;

use App\Interfaces\ServiceTypeInterface;
use Illuminate\Support\Facades\Gate;

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
        Gate::authorize('manage-services');
        return $this->repo->createServiceType($data);
    }

    public function update($id, array $data)
    {
        Gate::authorize('manage-services');
        return $this->repo->updateServiceType($id, $data);
    }

    public function delete($id)
    {
        Gate::authorize('manage-services');
        return $this->repo->deleteServiceType($id);
    }
}

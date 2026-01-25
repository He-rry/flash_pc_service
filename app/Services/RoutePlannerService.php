<?php

namespace App\Services;

use App\Interfaces\RoutePlannerRepositoryInterface;

class RoutePlannerService
{
    protected $repo;

    public function __construct(RoutePlannerRepositoryInterface $repo)
    {
        $this->repo = $repo;
    }

    public function getAllShops()
    {
        return $this->repo->getAllShops();
    }

    public function getAllRoutes()
    {
        return $this->repo->getAllRoutes();
    }

    public function list()
    {
        return [
            'shops' => $this->getAllShops(),
            'routes' => $this->getAllRoutes(),
        ];
    }

    public function findRoute($id)
    {
        return $this->repo->findRouteById($id);
    }

    public function createRoute(array $data)
    {
        return $this->repo->createRoute($data);
    }

    public function updateRoute($id, array $data)
    {
        return $this->repo->updateRoute($id, $data);
    }

    public function deleteRoute($id)
    {
        return $this->repo->deleteRoute($id);
    }
}

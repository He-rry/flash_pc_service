<?php

namespace App\Repositories;

use App\Interfaces\RoutePlannerRepositoryInterface;
use App\Models\Shop;
use App\Models\Route;

class RoutePlannerRepository implements RoutePlannerRepositoryInterface
{
    protected $shop;
    protected $route;

    public function __construct(Shop $shop, Route $route)
    {
        $this->shop = $shop;
        $this->route = $route;
    }

    public function getAllShops()
    {
        return $this->shop->all();
    }

    public function getAllRoutes()
    {
        return $this->route->all();
    }

    public function findRouteById($id)
    {
        return $this->route->findOrFail($id);
    }

    public function createRoute(array $data)
    {
        return $this->route->create($data);
    }

    public function updateRoute($id, array $data)
    {
        $item = $this->route->findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function deleteRoute($id)
    {
        $item = $this->route->findOrFail($id);
        return $item->delete();
    }
}

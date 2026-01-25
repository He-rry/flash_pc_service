<?php

namespace App\Interfaces;

interface RoutePlannerRepositoryInterface
{
    public function getAllShops();
    public function getAllRoutes();
    public function findRouteById($id);
    public function createRoute(array $data);
    public function updateRoute($id, array $data);
    public function deleteRoute($id);
}

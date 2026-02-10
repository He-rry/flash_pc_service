<?php

namespace App\Interfaces;

interface ShopRepositoryInterface
{
    public function getFilteredShops(array $filters, $perPage = 10);
    public function findShopById($id);
    public function createShop(array $data);
    public function updateShop($id, array $data);
    public function deleteShop($id);
    public function checkLocationExists($lat, $lng);
    public function getDistinctRegions();
    public function getLogsByShopId($id);
}
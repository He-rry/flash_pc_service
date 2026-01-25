<?php

namespace App\Interfaces;

interface ShopRepositoryInterface
{
    public function getAllShops();
    public function findShopById($id);
    public function createShop(array $data);
    public function updateShop($id, array $data);
    public function deleteShop($id);
}

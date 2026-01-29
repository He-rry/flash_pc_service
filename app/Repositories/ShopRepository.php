<?php

namespace App\Repositories;

use App\Interfaces\ShopRepositoryInterface;
use App\Models\Shop;

class ShopRepository implements ShopRepositoryInterface
{
    protected $model;

    public function __construct(Shop $model)
    {
        $this->model = $model;
    }

    public function getAllShops()
    {
        return $this->model->all();
    }

    public function findShopById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function createShop(array $data)
    {
        return $this->model->create($data);
    }

    public function updateShop($id, array $data)
    {
        $item = $this->model->findOrFail($id);
        $item->update($data);
        return $item;
    }

    public function deleteShop($id)
    {
        $item = $this->model->findOrFail($id);
        return $item->delete();
    }
    public function checkLocationExists($lat, $lng) {
        return $this->model->where('lat', $lat)->where('lng', $lng)->exists();
    }
}

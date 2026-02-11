<?php

namespace App\Repositories;

use App\Interfaces\ShopRepositoryInterface;
use App\Models\Shop;
use App\Models\ActivityLog; 

class ShopRepository implements ShopRepositoryInterface
{
    protected $model;

    public function __construct(Shop $model)
    {
        $this->model = $model;
    }
    public function getFilteredShops(array $filters, $perPage = 10)
    {
        return $this->model->applyFilters($filters)
            ->with('admin') 
            ->latest()
            ->paginate($perPage)
            ->appends($filters);
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
        $shop = $this->findShopById($id);
        $shop->update($data);
        return $shop;
    }

    public function deleteShop($id)
    {
        $shop = $this->findShopById($id);
        return $shop->delete();
    }

    public function checkLocationExists($lat, $lng)
    {
        return $this->model->where('lat', $lat)->where('lng', $lng)->exists();
    }

    public function getDistinctRegions()
    {
        return $this->model->whereNotNull('region')->distinct()->pluck('region');
    }

    //single shop log
    public function getLogsByShopId($id)
    {
        return ActivityLog::where('shop_id', $id)
            ->with('user:id,name') 
            ->latest()
            ->get();
    }
}
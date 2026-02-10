<?php

namespace App\Repositories;

use App\Interfaces\ShopRepositoryInterface;
use App\Models\Shop;
use App\Models\ActivityLog; // ActivityLog Model ကို သုံးဖို့ မမေ့ပါနဲ့

class ShopRepository implements ShopRepositoryInterface
{
    protected $model;

    public function __construct(Shop $model)
    {
        $this->model = $model;
    }

    /**
     * Filter များအလိုက် ဆိုင်များကို Pagination ဖြင့် ပြန်ပေးရန်
     */
    public function getFilteredShops(array $filters, $perPage = 10)
    {
        return $this->model->applyFilters($filters)
            ->with('admin') // Admin အမည်ပြချင်ရင် eager load လုပ်ထားတာ ပိုကောင်းပါတယ်
            ->latest()
            ->paginate($perPage)
            ->appends($filters);
    }

    public function findShopById($id)
    {
        return $this->model->findOrFail($id); // find အစား findOrFail သုံးတာ ပိုစိတ်ချရပါတယ်
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

    /**
     * ဆိုင်တစ်ဆိုင်ချင်းစီ၏ လုပ်ဆောင်ချက် မှတ်တမ်း (Logs) များကို ယူရန်
     */
    public function getLogsByShopId($id)
    {
        // ActivityLog table ထဲမှာ shop_id နဲ့ user relationship ရှိရပါမယ်
        return ActivityLog::where('shop_id', $id)
            ->with('user:id,name') // လုပ်ဆောင်သူအမည်ပါ တစ်ခါတည်းယူမယ်
            ->latest()
            ->get();
    }
}
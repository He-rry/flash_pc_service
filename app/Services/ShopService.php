<?php

namespace App\Services;

use App\Interfaces\ShopRepositoryInterface;
use App\Interfaces\RouterInterface;
use App\Models\ActivityLog;
use App\Exports\ShopsExport;
use Illuminate\Support\Facades\Gate;
use App\Imports\ShopsImport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Exports\DuplicateShopsExport;


class ShopService
{
    protected $repo;
    protected $routeRepo;

    public function __construct(ShopRepositoryInterface $repo, RouterInterface $routeRepo)
    {
        $this->repo = $repo;
        $this->routeRepo = $routeRepo;
    }

    public function store(array $data)
    {
        if ($this->repo->checkLocationExists($data['lat'], $data['lng'])) {
            throw new \Exception('ဤတည်နေရာတွင် ဆိုင်ရှိနှင့်ပြီးသား ဖြစ်ပါသည်။');
        }

        $data['added_by'] = Auth::id();
        if (!empty($data['created_at'])) {
            $data['created_at'] = Carbon::parse($data['created_at']);
        }

        $shop = $this->repo->createShop($data);
        $this->logActivity('ADD', "Added new shop: " . $shop->name, $shop->id);
        return $shop;
    }

    public function update($id, array $data)
    {
        $shop = $this->repo->findShopById($id);
        if (!$shop) throw new \Exception('ဆိုင်ကို ရှာမတွေ့ပါ။');

        // ပြောင်းလဲမှုများကို ခြေရာခံခြင်း
        $changes = $this->getChanges($shop, $data);

        $updatedShop = $this->repo->updateShop($id, $data);

        $description = count($changes) > 0
            ? "Updated fields: " . implode(', ', $changes)
            : "Updated shop details (No fields changed)";

        $this->logActivity('UPDATE', $description, $id);
        return $updatedShop;
    }

    public function delete($id)
    {
        $shop = $this->repo->findShopById($id);
        if (!$shop) throw new \Exception('ဆိုင်ကို ရှာမတွေ့ပါ။');

        $name = $shop->name;
        $this->logActivity('DELETE', "Deleted shop: $name (ID: $id)", $id);
        $this->repo->deleteShop($id);
        return true;
    }

    public function importShops($file, $action = 'skip')
    {
        $import = new ShopsImport($action, Auth::id());
        Excel::import($import, $file);
        $this->logActivity('IMPORT', "Imported shops from Excel (Action: $action)");
        return ['duplicates' => $import->duplicateRows];
    }

    public function exportShops(array $filters)
    {
        $this->logActivity('EXPORT', "Exported shop list to Excel");
        return Excel::download(new ShopsExport($filters), 'shops_report.xlsx');
    }

    
    public function exportDuplicates($duplicates)
    {
        Gate::authorize('manage-shops');
        $this->logActivity('EXPORT', "Exported duplicate shops list");
        return Excel::download(new DuplicateShopsExport($duplicates, 'yellow'), 'duplicates.xlsx');
    }

    private function getChanges($shop, $data)
    {
        $changes = [];
        $fields = ['name', 'region', 'lat', 'lng'];
        foreach ($fields as $field) {
            if (isset($data[$field]) && (string)$shop->$field !== (string)$data[$field]) {
                $changes[] = ucfirst($field) . " ({$shop->$field} -> {$data[$field]})";
            }
        }
        return $changes;
    }

    private function logActivity($action, $description, $shopId = null)
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'shop_id'     => $shopId,
            'action'      => $action,
            'module'      => 'SHOPS',
            'description' => $description,
        ]);
    }
    public function getShopLogs($id)
    {
        Gate::authorize('view-logs');
        return $this->repo->getLogsByShopId($id);
    }
}

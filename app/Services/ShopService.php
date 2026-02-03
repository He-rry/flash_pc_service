<?php

namespace App\Services;

use App\Interfaces\ShopRepositoryInterface;
use App\Interfaces\RouterInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopsImport;
use App\Exports\ShopsExport;
use App\Exports\DuplicateShopsExport;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ShopService
{
    protected $repo;
    protected $routeRepo;

    public function __construct(ShopRepositoryInterface $repo, RouterInterface $routeRepo)
    {
        $this->repo = $repo;
        $this->routeRepo = $routeRepo;
    }

    // --- CRUD Methods ---
    public function store(array $data)
    {
        if ($this->repo->checkLocationExists($data['lat'], $data['lng'])) {
            throw new \Exception('ဤတည်နေရာတွင် ဆိုင်ရှိနှင့်ပြီးသား ဖြစ်ပါသည်။');
        }
        $data['added_by'] = Auth::id();
        if (!empty($data['created_at'])) $data['created_at'] = Carbon::parse($data['created_at']);

        $shop = $this->repo->createShop($data);
        $this->logActivity('ADD', "Added new shop: " . $shop->name, $shop->id);
        return $shop;
    }

    public function update($id, array $data)
    {
        $shop = $this->repo->findShopById($id);

        $changes = [];
        if ($shop->name !== $data['name']) {
            $changes[] = "Name ({$shop->name} -> {$data['name']})";
        }
        if ($shop->region !== $data['region']) {
            $changes[] = "Region ({$shop->region} -> {$data['region']})";
        }
        if ((string)$shop->lat !== (string)$data['lat']) {
            $changes[] = "Latitude ({$shop->lat} -> {$data['lat']})";
        }
        if ((string)$shop->lng !== (string)$data['lng']) {
            $changes[] = "Longitude ({$shop->lng} -> {$data['lng']})";
        }
        $updatedShop = $this->repo->updateShop($id, $data);
        if (count($changes) > 0) {
            $description = "Updated fields: " . implode(', ', $changes);
        } else {
            $description = "Updated shop details (No fields changed)";
        }
        $this->logActivity('UPDATE', $description, $id);

        return $updatedShop;
    }
    public function delete($id)
    {
        $shop = $this->repo->findShopById($id);
        if (!$shop) {
            throw new \Exception('ဆိုင်ကို ရှာမတွေ့ပါ။');
        }

        $name = $shop->name;
        $this->logActivity('DELETE', "Deleted shop: $name (ID: $id)", $id);

        $this->repo->deleteShop($id);

        return true;
    }
    public function exportShops(array $filters)
    {
        $this->logActivity('EXPORT', "Exported shop list to Excel");
        return Excel::download(new ShopsExport($filters), 'shops_report.xlsx');
    }
    public function importShops($file, $action = 'skip')
    {
        $import = new ShopsImport($action, Auth::id());
        Excel::import($import, $file);

        $this->logActivity('IMPORT', "Imported shops from Excel (Action: $action)");

        return [
            'duplicates' => $import->duplicateRows
        ];
    }

    public function exportDuplicates($duplicates)
    {
        $this->logActivity('EXPORT', "Exported duplicate shops list");
        return Excel::download(new DuplicateShopsExport($duplicates, 'yellow'), 'duplicates.xlsx');
    }

    // --- Logging & Logs ---
    public function getShopLogs($id)
    {
        return ActivityLog::with('user:id,name')->where('shop_id', $id)->latest()->get();
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

    // --- Waypoints & Routes ---
    public function parseExcelToWaypoints($file): array
    {
        $sheets = Excel::toArray(null, $file);
        $rows = $sheets[0] ?? [];
        if (empty($rows)) return [];

        $start = (isset($rows[0][1]) && is_string($rows[0][1]) && preg_match('/[a-zA-Z]/', $rows[0][1])) ? 1 : 0;
        $waypoints = [];
        $uniqueKeys = [];

        foreach (array_slice($rows, $start) as $row) {
            $vals = array_values($row);
            $lat = $vals[1] ?? null;
            $lng = $vals[2] ?? null;
            if (!is_numeric($lat) || !is_numeric($lng)) continue;
            $posKey = (float)$lat . '|' . (float)$lng;
            if (in_array($posKey, $uniqueKeys)) continue;
            $uniqueKeys[] = $posKey;
            $waypoints[] = [
                'name' => $vals[0] ?? null,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'region' => $vals[3] ?? null,
            ];
        }
        return $waypoints;
    }

    public function createRouteFromWaypoints(string $routeName, array $waypoints)
    {
        $route = $this->routeRepo->store([
            'route_name' => $routeName,
            'waypoints' => $waypoints,
        ]);
        $this->logActivity('ROUTE_CREATE', "Created a new map route: $routeName");
        return $route;
    }
    public function list()
    {
        return $this->repo->getAllShops();
    }
}

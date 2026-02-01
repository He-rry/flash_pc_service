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
    public function list()
    {
        return $this->repo->getAllShops();
    }
    public function find($id)
    {
        return $this->repo->findShopById($id);
    }
    public function create(array $data)
    {
        $data['added_by'] = Auth::check() ? Auth::id() : null;
        $exists = $this->repo->checkLocationExists($data['lat'], $data['lng']);
        if ($exists) {
            throw new \Exception('ဤတည်နေရာတွင် ဆိုင်ရှိနှင့်ပြီးသား ဖြစ်ပါသည်။');
        }
        if (!empty($data['created_at'])) {
            $data['created_at'] = Carbon::parse($data['created_at']);
        }
        $shop = $this->repo->createShop($data);
        $this->logActivity('ADD', "Added a new shop: " . $shop->name);

        return $shop;
    }

    public function update($id, array $data)
    {
        $shop = $this->repo->updateShop($id, $data);
        $this->logActivity('UPDATE', "Updated information for shop: " . $shop->name);
        return $shop;
    }
    public function delete($id)
    {
        $shop = $this->find($id);
        $this->logActivity('DELETE', "Deleted shop: " . $shop->name);
        return $this->repo->deleteShop($id);
    }
    public function exportShops($filters = [])
    {
        $this->logActivity('EXPORT', "Exported shops list to Excel.");
        return Excel::download(new ShopsExport($filters), 'shops_report.xlsx');
    }

    public function importShops($file, $action = 'skip')
    {
        $import = new ShopsImport($action, Auth::id());
        Excel::import($import, $file);

        $this->logActivity('IMPORT', "Imported shops from Excel file (Action: $action).");

        return [
            'duplicates' => $import->duplicateRows,
            'action' => $action,
        ];
    }
    public function parseExcelToWaypoints($file): array
    {
        $sheets = Excel::toArray(null, $file);
        $rows = $sheets[0] ?? [];

        $start = 0;
        if (!empty($rows)) {
            $first = array_values($rows[0]);
            if (isset($first[1]) && is_string($first[1]) && preg_match('/[a-zA-Z]/', $first[1])) {
                $start = 1;
            }
        }

        $waypoints = [];
        $uniqueKeys = [];

        for ($i = $start; $i < count($rows); $i++) {
            $row = $rows[$i];
            $vals = array_values($row);
            $name = $vals[0] ?? null;
            $lat = $vals[1] ?? null;
            $lng = $vals[2] ?? null;
            $region = $vals[3] ?? null;

            if ($lat === null || $lng === null) continue;
            if (!is_numeric($lat) || !is_numeric($lng)) continue;
            $posKey = (float)$lat . '|' . (float)$lng;

            if (in_array($posKey, $uniqueKeys)) {
                continue;
            }

            $uniqueKeys[] = $posKey;
            $waypoints[] = [
                'name' => $name,
                'lat' => (float)$lat,
                'lng' => (float)$lng,
                'region' => $region,
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

        $this->logActivity('ROUTE_CREATE', "Created a new map route: " . $routeName);
        return $route;
    }
    public function exportDuplicates($duplicates, $color = 'yellow')
    {
        $this->logActivity('EXPORT', "Exported duplicate shops list.");
        $duplicateExport = new DuplicateShopsExport($duplicates, $color);
        return Excel::download(
            $duplicateExport,
            'duplicate_shops_' . date('Y-m-d_His') . '.xlsx'
        );
    }
    private function logActivity($action, $description)
    {
        ActivityLog::create([
            'user_id'     => Auth::id(),
            'action'      => $action,
            'module'      => 'SHOPS',
            'description' => $description,
            'ip_address'  => request()->ip(),
        ]);
    }
}

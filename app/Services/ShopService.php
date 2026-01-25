<?php

namespace App\Services;

use App\Interfaces\ShopRepositoryInterface;
use App\Interfaces\RouterInterface;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ShopsImport;
use App\Exports\ShopsExport;
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
        return $this->repo->createShop($data);
    }
    public function update($id, array $data)
    {
        return $this->repo->updateShop($id, $data);
    }
    public function delete($id)
    {
        return $this->repo->deleteShop($id);
    }
    public function exportShops($filters = [])
    {
        return Excel::download(new ShopsExport($filters), 'shops_report.xlsx');
    }
    public function importShops($file, $action = 'skip')
    {
        $import = new ShopsImport($action);
        Excel::import($import, $file);

        return [
            'duplicates' => $import->duplicateRows,
            'action' => $action
        ];
    }

    /**
     * Excel ဖိုင်မှ Waypoints (Route) ကို Parse လုပ်ရန်
     * Lat/Lng ထပ်နေပါက စစ်ထုတ်ပေးသည်
     */
    public function parseExcelToWaypoints($file): array
    {
        $sheets = Excel::toArray(null, $file);
        $rows = $sheets[0] ?? [];

        $start = 0;
        if (!empty($rows)) {
            $first = array_values($rows[0]);
            // Header row ပါမပါ စစ်ဆေးခြင်း
            if (isset($first[1]) && is_string($first[1]) && preg_match('/[a-zA-Z]/', $first[1])) {
                $start = 1;
            }
        }

        $waypoints = [];
        $uniqueKeys = []; // Lat|Lng အတွဲလိုက်ကို မှတ်ထားရန်

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
        return $this->routeRepo->store([
            'route_name' => $routeName,
            'waypoints' => $waypoints,
        ]);
    }
    /**
     * Export duplicate shops to Excel
     */
    public function exportDuplicates($duplicates, $color = 'yellow')
    {
        $duplicateExport = new DuplicateShopsExport($duplicates, $color);
        return Excel::download(
            $duplicateExport,
            'duplicate_shops_' . date('Y-m-d_His') . '.xlsx'
        );
    }
}

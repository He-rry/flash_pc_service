<?php

namespace App\Imports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function Symfony\Component\Clock\now;

class ShopsImport implements ToModel, WithHeadingRow
{
    public $duplicateRows = [];
    protected $action;
    protected $added_by;

    public function __construct($action, $admin_id)
    {
        $this->action = $action;
        $this->added_by = $admin_id;
    }
    public function model(array $row)
    {
        if (empty($row['shop_name']) || empty($row['latitude'])) {
            return null;
        }
        $existingByName = Shop::where('name', $row['shop_name'])->first();

        $existingByLocation = Shop::where('lat', $row['latitude'])
            ->where('lng', $row['longitude'])
            ->first();

        if ($existingByName || $existingByLocation) {
            $this->duplicateRows[] = [
                'shop_name' => $row['shop_name'],
                'latitude'  => $row['latitude'],
                'longitude' => $row['longitude'],
                'region'    => $row['region'],
                'dup_name' => (bool)$existingByName,
                'dup_location' => (bool)$existingByLocation,
                'reason' => $existingByName ? ($existingByLocation ? 'နာမည်နှင့် တည်နေရာနှစ်ခုလုံးထပ်နေသည်' : 'နာမည်ထပ်နေသည်') : 'တည်နေရာထပ်နေသည်'
            ];
            return null;
        }
        return new Shop([
            'name'       => $row['shop_name'],
            'lat'        => $row['latitude'],
            'lng'        => $row['longitude'],
            'region'     => $row['region'],
            'created_at' => now(),
            'updated_at' => now(),
            'added_by' => $this->added_by,
        ]);
    }
}

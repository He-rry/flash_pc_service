<?php

namespace App\Imports;

use App\Models\Shop;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

use function Symfony\Component\Clock\now;

class ShopsImport implements ToModel, WithHeadingRow
{
    public $duplicateRows = [];
    protected $action;

    public function __construct($action)
    {
        $this->action = $action;
    }
    public function model(array $row)
    {
        // Row အလွတ်ဖြစ်နေရင် ကျော်သွားမယ်
        if (empty($row['shop_name']) || empty($row['latitude'])) {
            return null;
        }
        // ၁။ နာမည်တူ စစ်မယ်
        $existingByName = Shop::where('name', $row['shop_name'])->first();

        // ၂။ Lat/Lng တူ စစ်မယ်
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

        // အသစ်ဆိုမှ Insert လုပ်မယ်
        return new Shop([
            'name'       => $row['shop_name'],
            'lat'        => $row['latitude'],
            'lng'        => $row['longitude'],
            'region'     => $row['region'],
            'created_at' =>now(),
            'updated_at' =>now()
        ]);
    }
}

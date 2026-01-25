<?php

namespace App\Exports;

use App\Models\Shop;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ShopsExport implements FromCollection, WithHeadings
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    // file: App\Exports\ShopsExport.php
    public function collection()
    {
        return Shop::applyFilters($this->filters)
            ->select('name', 'lat', 'lng', 'region', 'created_at')
            ->get();
    }

    public function headings(): array
    {
        return ["Shop Name", "Latitude", "Longitude", "Region"];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class DuplicateShopsExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, ShouldAutoSize
{
    protected $duplicates;
    protected $highlightColor;

    /**
     * @param array 
     * @param string
     */
    public function __construct($duplicates, $highlightColor = 'yellow')
    {
        $this->duplicates = $duplicates;
        $this->highlightColor = $highlightColor;
    }

    public function collection()
    {
        return collect($this->duplicates)->map(function ($item, $index) {
            return [
                'no' => $index + 1,
                'shop_name' => $item['shop_name'] ?? '-',
                'latitude' => $item['latitude'] ?? '-',
                'longitude' => $item['longitude'] ?? '-',
                'region' => $item['region'] ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Shop Name', 'Latitude', 'Longitude', 'Region'];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = count($this->duplicates) + 1;
        $lightYellow = 'FFF9C4';
        $sheet->getStyle('A1:F1')->getFont()->setBold(true);
        $sheet->getStyle('A1:F1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F2F2F2');
        $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);
        foreach ($this->duplicates as $index => $data) {
            $currentRow = $index + 2;

            if ($data['dup_name'] ?? false) {
                $sheet->getStyle('B' . $currentRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($lightYellow);
            }
            if ($data['dup_location'] ?? false) {
                $sheet->getStyle('C' . $currentRow . ':D' . $currentRow)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($lightYellow);
            }
        }

        return [];
    }
    public function columnWidths(): array
    {
        return [
            'A' => 8,
            'B' => 15,
            'C' => 10,
            'D' => 10,
        ];
    }
}

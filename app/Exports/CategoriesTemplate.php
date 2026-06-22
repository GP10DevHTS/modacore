<?php

namespace App\Exports;

use App\Models\InventoryCategory;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CategoriesTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return InventoryCategory::ordered()
            ->get(['name', 'description', 'code'])
            ->map(fn (InventoryCategory $category) => [
                $category->name,
                $category->description ?? '',
                $category->code ?? '',
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Description',
            'Code',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\VariantType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InitialStockTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        $variantValues = VariantType::with('values')
            ->get()
            ->map(fn ($type) => $type->values->first()?->label)
            ->filter()
            ->implode(', ');

        return [
            // optional sample row
            ['Sample Item', 'Category A',
                $variantValues, 10, 5000, 8000],
        ];
    }

    public function headings(): array
    {
        $variantTypes = VariantType::pluck('name')->implode(', ');

        return [
            'Item',
            'Category',
            $variantTypes,
            'Quantity',
            'Cost Price (Each)',
            'Rental Price (Each)',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Row 1 = headings
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}

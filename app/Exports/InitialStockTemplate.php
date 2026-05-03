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
        $types = VariantType::with('values')->orderBy('sort_order')->get();

        $sampleRow = [
            'Sample Item', // Item
            'Sample Category', // Category
        ];

        foreach ($types as $type) {
            $sampleRow[] = $type->values->first()?->label ?? 'Value';
        }

        $sampleRow[] = 10; // Quantity
        $sampleRow[] = 50.00; // Cost Price
        $sampleRow[] = 80.00; // Rental Price

        return [$sampleRow];
    }

    public function headings(): array
    {
        $headings = ['Item', 'Category'];

        $types = VariantType::orderBy('sort_order')->pluck('name')->toArray();
        foreach ($types as $type) {
            $headings[] = $type;
        }

        $headings[] = 'Quantity';
        $headings[] = 'Cost Price (Each)';
        $headings[] = 'Rental Price (Each)';

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

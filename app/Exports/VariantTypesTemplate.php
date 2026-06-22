<?php

namespace App\Exports;

use App\Models\VariantType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VariantTypesTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return VariantType::ordered()
            ->get(['name', 'sort_order'])
            ->map(fn (VariantType $type) => [
                $type->name,
                $type->sort_order,
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Sort Order',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

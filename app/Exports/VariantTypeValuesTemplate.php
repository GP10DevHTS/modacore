<?php

namespace App\Exports;

use App\Models\VariantTypeValue;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VariantTypeValuesTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return VariantTypeValue::with('type')
            ->ordered()
            ->get()
            ->map(fn (VariantTypeValue $value) => [
                $value->type?->name ?? '',
                $value->label,
                $value->sort_order,
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Variant Type',
            'Label',
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

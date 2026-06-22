<?php

namespace App\Exports;

use App\Models\InventoryItem;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ItemsTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    public function array(): array
    {
        return InventoryItem::with('category')
            ->latest()
            ->get()
            ->map(fn (InventoryItem $item) => [
                $item->name,
                $item->category?->name ?? '',
                (float) $item->base_rental_price,
                $item->cost_price !== null ? (float) $item->cost_price : '',
                $item->description ?? '',
                $item->is_active ? 'yes' : 'no',
            ])
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Category',
            'Base Rental Price',
            'Cost Price',
            'Description',
            'Is Active',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

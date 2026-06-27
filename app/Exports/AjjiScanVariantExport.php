<?php

namespace App\Exports;

use App\Models\InventoryVariant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AjjiScanVariantExport implements FromCollection, ShouldAutoSize, WithHeadings
{
    public function __construct(
        protected int $itemId
    ) {}

    public function collection(): Collection
    {
        return InventoryVariant::query()
            ->where('inventory_item_id', $this->itemId)
            ->where('is_active', true)
            ->get()
            ->map(function (InventoryVariant $variant) {
                return [
                    'label' => $variant->label
                        ? "{$variant->item->name} ({$variant->label})"
                        : $variant->sku,

                    'code' => $variant->sku,
                ];
            });
    }

    public function headings(): array
    {
        return [
            'label',
            'code',
            'prefix',
        ];
    }
}

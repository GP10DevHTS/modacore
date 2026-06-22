<?php

namespace App\Exports;

use App\Models\InventoryVariant;
use App\Models\VariantType;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VariationsTemplate implements FromArray, ShouldAutoSize, WithHeadings, WithStyles
{
    private ?array $variantTypeNames = null;

    /**
     * @return array<int, string>
     */
    public function variantTypeNames(): array
    {
        if ($this->variantTypeNames === null) {
            $this->variantTypeNames = VariantType::ordered()
                ->pluck('name')
                ->values()
                ->toArray();
        }

        return $this->variantTypeNames;
    }

    public function array(): array
    {
        $variantTypeNames = $this->variantTypeNames();
        $variantTypeCount = count($variantTypeNames);

        return InventoryVariant::with(['item.category', 'attributeValues.type'])
            ->orderBy('id')
            ->get()
            ->map(function (InventoryVariant $variant) use ($variantTypeNames, $variantTypeCount) {
                $attributeValues = $variant->attributeValues
                    ->keyBy(fn ($v) => $v->type->name);

                $variantColumns = array_fill(0, $variantTypeCount, '');

                foreach ($variantTypeNames as $i => $typeName) {
                    if (isset($attributeValues[$typeName])) {
                        $variantColumns[$i] = $attributeValues[$typeName]->label;
                    }
                }

                return [
                    $variant->item?->name ?? '',
                    $variant->sku ?? '',
                    ...$variantColumns,
                    $variant->rental_price !== null ? (float) $variant->rental_price : '',
                    $variant->cost_price !== null ? (float) $variant->cost_price : '',
                    $variant->stock_quantity,
                    $variant->is_active ? 'yes' : 'no',
                ];
            })
            ->toArray();
    }

    public function headings(): array
    {
        return [
            'Item',
            'SKU',
            ...$this->variantTypeNames(),
            'Rental Price',
            'Cost Price',
            'Stock Quantity',
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

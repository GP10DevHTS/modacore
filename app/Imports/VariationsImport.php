<?php

namespace App\Imports;

use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantType;
use App\Services\InventorySkuService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VariationsImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headings = $rows->first()->map(fn ($h) => trim($h))->toArray();
        $rows = $rows->skip(1);

        // Fixed columns that always exist
        $fixedStart = ['Item', 'SKU'];
        $fixedEnd = ['Rental Price', 'Cost Price', 'Stock Quantity', 'Is Active'];

        // Variant columns = everything between SKU and Rental Price
        $variantColumns = array_slice(
            $headings,
            count($fixedStart),
            count($headings) - count($fixedStart) - count($fixedEnd)
        );

        // Preload variant types
        $variantTypes = VariantType::with('values')->get()->keyBy('name');

        // Preload items for lookup
        $items = InventoryItem::all()->keyBy(fn ($item) => strtolower($item->name));

        $skuService = app(InventorySkuService::class);

        foreach ($rows as $row) {
            $row = $row->toArray();

            if (! array_filter($row)) {
                continue;
            }

            $data = array_combine($headings, $row);

            $itemName = trim($data['Item'] ?? '');
            $sku = trim($data['SKU'] ?? '');

            if (empty($itemName)) {
                continue;
            }

            $item = $items->get(strtolower($itemName));

            if (! $item) {
                continue;
            }

            // Resolve variant attribute values
            $valueIds = [];

            foreach ($variantColumns as $column) {
                $valueLabel = trim($data[$column] ?? '');

                if (! $valueLabel) {
                    continue;
                }

                $type = $variantTypes->get($column);

                if (! $type) {
                    continue;
                }

                $value = $type->values->firstWhere('label', $valueLabel);

                if (! $value) {
                    $value = $type->values()->create(['label' => $valueLabel]);
                }

                $valueIds[] = $value->id;
            }

            if (empty($valueIds)) {
                continue;
            }

            $rentalPrice = isset($data['Rental Price']) && $data['Rental Price'] !== ''
                ? (float) str_replace(',', '', $data['Rental Price'])
                : null;

            $costPrice = isset($data['Cost Price']) && $data['Cost Price'] !== ''
                ? (float) str_replace(',', '', $data['Cost Price'])
                : null;

            $stockQty = (int) ($data['Stock Quantity'] ?? 1);
            $isActive = in_array(strtolower(trim($data['Is Active'] ?? 'yes')), ['yes', 'true', '1', 'active']);

            // Create tracked variants (one per unit of stock)
            for ($i = 0; $i < max(1, $stockQty); $i++) {
                $skuService->createTrackedVariant(
                    $item,
                    $valueIds,
                    $rentalPrice,
                    $costPrice,
                    $isActive,
                );

                $item->increment('stock_quantity');
                $item->increment('available_quantity');
            }
        }
    }
}

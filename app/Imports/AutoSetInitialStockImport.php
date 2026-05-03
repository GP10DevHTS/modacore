<?php

namespace App\Imports;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class AutoSetInitialStockImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        // First row = headings
        $headings = $rows->first()->map(fn ($h) => trim($h))->toArray();
        $rows = $rows->skip(1);

        // Identify fixed columns
        $fixedStart = ['Item', 'Category'];
        $fixedEnd = ['Quantity', 'Cost Price (Each)', 'Rental Price (Each)'];

        // Variant columns = everything in between
        $variantColumns = array_slice(
            $headings,
            count($fixedStart),
            count($headings) - count($fixedStart) - count($fixedEnd)
        );

        // Preload variant types
        $variantTypes = VariantType::with('values')->get()->keyBy('name');

        foreach ($rows as $row) {
            $row = $row->toArray();

            if (! array_filter($row)) {
                continue;
            } // skip empty rows

            $data = array_combine($headings, $row);

            // ----------------------------
            // CATEGORY
            // ----------------------------
            $category = InventoryCategory::firstOrCreate([
                'name' => trim($data['Category']),
            ]);

            // ----------------------------
            // ITEM
            // ----------------------------
            $item = InventoryItem::firstOrCreate(
                [
                    'name' => trim($data['Item']),
                    'category_id' => $category->id,
                ],
                [
                    'cost_price' => $data['Cost Price (Each)'] ?? 0,
                    'base_rental_price' => $data['Rental Price (Each)'] ?? 0,
                    'stock_quantity' => 0,
                    'available_quantity' => 0,
                ]
            );

            // ----------------------------
            // VARIANT RESOLUTION
            // ----------------------------
            $variantAttributes = [];

            foreach ($variantColumns as $column) {
                $valueLabel = trim($data[$column] ?? '');

                if (! $valueLabel) {
                    continue;
                }

                $type = $variantTypes->get($column);

                if (! $type) {
                    continue;
                } // unknown variant type

                $value = $type->values
                    ->firstWhere('label', $valueLabel);

                if (! $value) {
                    // optionally auto-create missing values
                    $value = $type->values()->create([
                        'label' => $valueLabel,
                    ]);
                }

                $variantAttributes[$type->id] = $value->id;
            }

            // ----------------------------
            // CREATE VARIANT
            // ----------------------------
            if (! empty($variantAttributes)) {

                $variant = InventoryVariant::create([
                    'inventory_item_id' => $item->id,
                    'stock_quantity' => (int) $data['Quantity'],
                    'available_quantity' => (int) $data['Quantity'],
                    'cost_price' => $data['Cost Price (Each)'] ?? 0,
                    'rental_price' => $data['Rental Price (Each)'] ?? 0,
                ]);

                // attach variant values (assuming pivot)
                $variant->variantValues()->sync(array_values($variantAttributes));

            } else {
                // ----------------------------
                // NO VARIANTS → DIRECT STOCK
                // ----------------------------
                $qty = (int) $data['Quantity'];

                $item->increment('stock_quantity', $qty);
                $item->increment('available_quantity', $qty);
            }
        }
    }
}

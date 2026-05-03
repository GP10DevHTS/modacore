<?php

namespace App\Imports;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\StockImport;
use App\Models\User;
use App\Models\StockImportItem;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use App\Services\InventorySkuService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AutoSetInitialStockImport implements ToCollection, WithHeadingRow
{
    protected StockImport $importBatch;

    public function __construct(StockImport $importBatch)
    {
        $this->importBatch = $importBatch;
    }

    public function collection(Collection $rows): void
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->processRow($row);
            }
        });
    }

    protected function processRow(Collection $row): void
    {
        $itemName = trim($row->get('item') ?? '');
        $categoryName = trim($row->get('category') ?? '');
        $quantity = (int) ($row->get('quantity') ?? 0);
        $costPrice = (float) ($row->get('cost_price_each') ?? 0);
        $rentalPrice = (float) ($row->get('rental_price_each') ?? 0);

        if (empty($itemName) || $quantity <= 0) {
            return;
        }

        // 1. Category
        $category = null;
        if (!empty($categoryName)) {
            $category = InventoryCategory::where('name', $categoryName)->first();
            if (!$category) {
                $category = InventoryCategory::create([
                    'name' => $categoryName,
                    'user_id' => auth()->id() ?? User::whereHas('roles', fn($q) => $q->where('name', 'superadmin'))->first()?->id
                ]);
                $this->track($category);
            }
        }

        // 2. Item
        $item = InventoryItem::where('name', $itemName)
            ->when($category, fn($q) => $q->where('category_id', $category->id))
            ->first();

        if (!$item) {
            $item = InventoryItem::create([
                'name' => $itemName,
                'category_id' => $category?->id,
                'cost_price' => $costPrice,
                'base_rental_price' => $rentalPrice,
                'stock_quantity' => 0,
                'available_quantity' => 0,
            ]);
            $this->track($item);
        }

        // 3. Resolve Variants
        $variantAttributes = $this->resolveVariants($row);

        // 4. Create Individual Units
        $skuService = app(InventorySkuService::class);
        $valueIds = array_values($variantAttributes);

        for ($i = 0; $i < $quantity; $i++) {
            $variant = $skuService->createTrackedVariant(
                $item,
                $valueIds,
                $rentalPrice ?: $item->base_rental_price,
                $costPrice ?: $item->cost_price,
            );

            $this->track($variant);

            // Increment item totals
            $item->increment('stock_quantity');
            $item->increment('available_quantity');
        }
    }

    protected function resolveVariants(Collection $row): array
    {
        $variantAttributes = [];
        $fixedKeys = ['item', 'category', 'quantity', 'cost_price_each', 'rental_price_each'];

        // Everything else in the row might be a variant type
        foreach ($row as $key => $value) {
            if (in_array($key, $fixedKeys) || empty($value)) {
                continue;
            }

            // The key in heading row is slugified/snake_cased by Maatwebsite Excel
            // We need to match it back to VariantType name or create it
            $typeName = str_replace('_', ' ', $key);
            $typeName = ucwords($typeName);

            $type = VariantType::where('name', 'like', $typeName)->first();
            if (!$type) {
                // If not found by like, maybe try exact match on a more "raw" name if we had it.
                // For now, let's create it if it doesn't look like a standard field.
                $type = VariantType::create([
                    'name' => $typeName,
                    'sort_order' => VariantType::max('sort_order') + 1
                ]);
                $this->track($type);
            }

            $valueLabel = trim($value);
            $typeValue = VariantTypeValue::where('variant_type_id', $type->id)
                ->where('label', $valueLabel)
                ->first();

            if (!$typeValue) {
                $typeValue = VariantTypeValue::create([
                    'variant_type_id' => $type->id,
                    'label' => $valueLabel,
                    'sort_order' => VariantTypeValue::where('variant_type_id', $type->id)->max('sort_order') + 1
                ]);
                $this->track($typeValue);
            }

            $variantAttributes[$type->id] = $typeValue->id;
        }

        return $variantAttributes;
    }

    protected function track(Model $model): void
    {
        StockImportItem::create([
            'stock_import_id' => $this->importBatch->id,
            'importable_type' => get_class($model),
            'importable_id' => $model->id,
        ]);
    }
}

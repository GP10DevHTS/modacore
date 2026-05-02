<?php

namespace App\Services;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantTypeValue;
use Illuminate\Support\Str;

class InventorySkuService
{
    public function nextCategoryCode(): string
    {
        $lastCode = InventoryCategory::query()
            ->whereNotNull('code')
            ->orderByRaw('LENGTH(code) DESC')
            ->orderByDesc('code')
            ->value('code');

        return $this->incrementAlphaCode($lastCode);
    }

    public function nextItemCode(int $categoryId): string
    {
        $lastCode = InventoryItem::query()
            ->where('category_id', $categoryId)
            ->whereNotNull('code')
            ->orderByRaw('LENGTH(code) DESC')
            ->orderByDesc('code')
            ->value('code');

        return $this->incrementAlphaCode($lastCode);
    }

    public function itemSku(InventoryCategory $category, string $itemCode): string
    {
        return $category->code.$itemCode;
    }

    /**
     * @param  array<int, int|string>  $valueIds
     */
    public function compositionKey(array $valueIds): string
    {
        return collect($valueIds)
            ->filter()
            ->map(fn (int|string $valueId) => (int) $valueId)
            ->sort()
            ->values()
            ->implode('-');
    }

    /**
     * @param  array<int, int|string>  $valueIds
     */
    public function compositionLabel(array $valueIds): string
    {
        return VariantTypeValue::query()
            ->whereIn('id', array_map('intval', array_filter($valueIds)))
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('label')
            ->join(' / ');
    }

    public function nextVariantSku(InventoryItem $item): string
    {
        $lastSku = InventoryVariant::query()
            ->where('inventory_item_id', $item->id)
            ->whereNotNull('sku')
            ->orderByDesc('id')
            ->value('sku');

        $nextNumber = 1;

        if ($lastSku && preg_match('/(\d+)$/', $lastSku, $matches) === 1) {
            $nextNumber = ((int) $matches[1]) + 1;
        }

        return $item->sku.'-'.str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * @param  array<int, int|string>  $valueIds
     */
    public function createTrackedVariant(
        InventoryItem $item,
        array $valueIds,
        ?float $rentalPrice,
        ?float $costPrice,
        bool $isActive = true,
    ): InventoryVariant {
        $variant = $item->variants()->create([
            'label' => $this->compositionLabel($valueIds),
            'composition_key' => $this->compositionKey($valueIds),
            'sku' => $this->nextVariantSku($item),
            'rental_price' => $rentalPrice,
            'cost_price' => $costPrice,
            'stock_quantity' => 1,
            'available_quantity' => 1,
            'is_active' => $isActive,
        ]);

        $variant->attributeValues()->sync(array_map('intval', array_filter($valueIds)));

        return $variant;
    }

    private function incrementAlphaCode(?string $code): string
    {
        if (! $code) {
            return 'A';
        }

        $letters = str_split(Str::upper($code));
        $index = count($letters) - 1;

        while ($index >= 0) {
            if ($letters[$index] !== 'Z') {
                $letters[$index] = chr(ord($letters[$index]) + 1);

                return implode('', $letters);
            }

            $letters[$index] = 'A';
            $index--;
        }

        return 'A'.implode('', $letters);
    }
}

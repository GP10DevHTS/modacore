<?php

namespace App\Imports;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ItemsImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headings = $rows->first()->map(fn ($h) => trim($h))->toArray();
        $rows = $rows->skip(1);

        $categories = InventoryCategory::all()->keyBy('name');

        foreach ($rows as $row) {
            $row = $row->toArray();

            if (! array_filter($row)) {
                continue;
            }

            $data = array_combine($headings, $row);

            $name = trim($data['Name'] ?? '');
            $categoryName = trim($data['Category'] ?? '');
            $baseRentalPrice = (float) str_replace(',', '', $data['Base Rental Price'] ?? 0);
            $costPrice = isset($data['Cost Price']) && $data['Cost Price'] !== ''
                ? (float) str_replace(',', '', $data['Cost Price'])
                : null;
            $description = trim($data['Description'] ?? '');
            $isActive = in_array(strtolower(trim($data['Is Active'] ?? 'yes')), ['yes', 'true', '1', 'active']);

            if (empty($name) || empty($categoryName)) {
                continue;
            }

            $category = $categories->get($categoryName);

            if (! $category) {
                $category = InventoryCategory::create(['name' => $categoryName]);
                $categories->put($categoryName, $category);
            }

            InventoryItem::updateOrCreate(
                ['name' => $name, 'category_id' => $category->id],
                [
                    'base_rental_price' => $baseRentalPrice,
                    'cost_price' => $costPrice,
                    'description' => $description ?: null,
                    'is_active' => $isActive,
                ]
            );
        }
    }
}

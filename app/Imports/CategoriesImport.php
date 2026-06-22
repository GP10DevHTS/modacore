<?php

namespace App\Imports;

use App\Models\InventoryCategory;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class CategoriesImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headings = $rows->first()->map(fn ($h) => trim($h))->toArray();
        $rows = $rows->skip(1);

        foreach ($rows as $row) {
            $row = $row->toArray();

            if (! array_filter($row)) {
                continue;
            }

            $data = array_combine($headings, $row);

            $name = trim($data['Name'] ?? '');
            $description = trim($data['Description'] ?? '');
            $code = trim($data['Code'] ?? '');

            if (empty($name)) {
                continue;
            }

            InventoryCategory::updateOrCreate(
                ['name' => $name],
                array_filter([
                    'description' => $description ?: null,
                    'code' => $code ?: null,
                ], fn ($v) => $v !== null)
            );
        }
    }
}

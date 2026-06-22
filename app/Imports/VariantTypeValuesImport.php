<?php

namespace App\Imports;

use App\Models\VariantType;
use App\Models\VariantTypeValue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VariantTypeValuesImport implements ToCollection
{
    public function collection(Collection $rows): void
    {
        if ($rows->isEmpty()) {
            return;
        }

        $headings = $rows->first()->map(fn ($h) => trim($h))->toArray();
        $rows = $rows->skip(1);

        $variantTypes = VariantType::all()->keyBy('name');

        foreach ($rows as $row) {
            $row = $row->toArray();

            if (! array_filter($row)) {
                continue;
            }

            $data = array_combine($headings, $row);

            $typeName = trim($data['Variant Type'] ?? '');
            $label = trim($data['Label'] ?? '');
            $sortOrder = (int) ($data['Sort Order'] ?? 0);

            if (empty($typeName) || empty($label)) {
                continue;
            }

            $type = $variantTypes->get($typeName);

            if (! $type) {
                $type = VariantType::create(['name' => $typeName, 'sort_order' => 0]);
                $variantTypes->put($typeName, $type);
            }

            VariantTypeValue::updateOrCreate(
                [
                    'variant_type_id' => $type->id,
                    'label' => $label,
                ],
                ['sort_order' => $sortOrder]
            );
        }
    }
}

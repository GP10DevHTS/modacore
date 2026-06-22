<?php

namespace App\Imports;

use App\Models\VariantType;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class VariantTypesImport implements ToCollection
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
            $sortOrder = (int) ($data['Sort Order'] ?? 0);

            if (empty($name)) {
                continue;
            }

            VariantType::updateOrCreate(
                ['name' => $name],
                ['sort_order' => $sortOrder]
            );
        }
    }
}

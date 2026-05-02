<?php

namespace Database\Seeders;

use App\Models\VariantType;
use App\Models\VariantTypeValue;
use Illuminate\Database\Seeder;

class VariantsSeeder extends Seeder
{
    public function run(): void
    {
        $variants = [
            'size' => ["XS", "S", "M", "L", "XL", "XXL", "3XL"],
            'color' => ["Black", "White", "Red", "Blue", "Green", "Yellow", "Pink", "Grey", "Navy"],
//            'sleeve' => ["short", "long", "3/4"],
//            'fit' => ["slim", "regular", "loose"],
//            'material' => ["cotton", "linen", "silk", "polyester", "wool"],
//            'occasion' => ["casual", "formal", "wedding", "party", "traditional"],
//            'gender' => ["men", "women", "unisex"],
        ];

        foreach ($variants as $type => $values) {

            $variantType = VariantType::firstOrCreate([
                'name' => $type,
            ]);

            foreach ($values as $value) {
                VariantTypeValue::firstOrCreate([
                    'variant_type_id' => $variantType->id,
                    'label' => $value,
                ]);
            }
        }
    }
}

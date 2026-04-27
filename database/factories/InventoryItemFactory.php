<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryItemFactory extends Factory
{
    protected $model = InventoryItem::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'category_id' => \App\Models\InventoryCategory::factory(),
            'sku' => $this->faker->unique()->bothify('SKU-####'),
            'base_rental_price' => $this->faker->randomFloat(2, 10, 100),
            'stock_quantity' => 0,
            'is_active' => true,
        ];
    }
}

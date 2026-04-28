<?php

namespace Database\Factories;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<InventoryItem>
 */
class InventoryItemFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'description' => null,
            'category_id' => InventoryCategory::factory(),
            'sku' => fake()->unique()->bothify('SKU-####'),
            'base_rental_price' => fake()->randomFloat(2, 500, 20000),
            'stock_quantity' => fake()->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}

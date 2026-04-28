<?php

namespace Database\Factories;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    public function definition(): array
    {
        $qty = fake()->numberBetween(1, 20);
        $cost = fake()->randomFloat(2, 500, 10000);

        return [
            'purchase_order_id' => PurchaseOrder::factory(),
            'inventory_item_id' => InventoryItem::factory(),
            'quantity' => $qty,
            'received_quantity' => 0,
            'invoiced_quantity' => 0,
            'unit_cost' => $cost,
            'subtotal' => round($qty * $cost, 2),
        ];
    }
}

<?php

namespace Database\Factories;

use App\Enums\ClosureStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReceiptStatus;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'po_number' => 'PO-'.fake()->unique()->numerify('#####'),
            'supplier_id' => Supplier::factory(),
            'order_status' => OrderStatus::Draft,
            'receipt_status' => ReceiptStatus::NotReceived,
            'invoice_status' => InvoiceStatus::NotInvoiced,
            'payment_status' => PaymentStatus::Unpaid,
            'closure_status' => ClosureStatus::Open,
            'total_amount' => fake()->randomFloat(2, 100, 50000),
            'notes' => null,
            'expected_at' => fake()->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
            'created_by' => null,
        ];
    }

    public function draft(): static
    {
        return $this->state(['order_status' => OrderStatus::Draft]);
    }

    public function sent(): static
    {
        return $this->state([
            'order_status' => OrderStatus::Sent,
            'sent_at' => now(),
        ]);
    }

    public function approved(): static
    {
        return $this->state([
            'order_status' => OrderStatus::Approved,
            'approved_at' => now(),
        ]);
    }
}

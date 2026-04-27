<?php

namespace Tests\Feature;

use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\User;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProcurementLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_procurement_lifecycle_and_cancellation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $supplier = Supplier::factory()->create();
        $item1 = InventoryItem::factory()->create(['stock_quantity' => 10]);
        $item2 = InventoryItem::factory()->create(['stock_quantity' => 5]);

        $service = new PurchaseOrderService();

        // 1. Create Draft PO
        $po = PurchaseOrder::create([
            'po_number' => 'PO-TEST-001',
            'supplier_id' => $supplier->id,
            'order_status' => 'draft',
            'total_amount' => 1000,
        ]);
        $po->refresh();

        $poItem1 = $po->items()->create([
            'inventory_item_id' => $item1->id,
            'quantity' => 10,
            'unit_cost' => 60,
            'subtotal' => 600,
        ]);

        $poItem2 = $po->items()->create([
            'inventory_item_id' => $item2->id,
            'quantity' => 5,
            'unit_cost' => 80,
            'subtotal' => 400,
        ]);

        $this->assertEquals('not_received', $po->receipt_status);

        // 2. Approve and Send
        $service->approveOrder($po, 'Approved for testing');
        $po->refresh();
        $this->assertEquals('approved', $po->order_status);
        $this->assertCount(1, $po->approvals);

        $po->update(['order_status' => 'sent']);

        // 3. Partial Receipt
        $service->recordReceipt($po, [
            ['purchase_order_item_id' => $poItem1->id, 'quantity' => 5]
        ], 'GRN-001');

        $po->refresh();
        $this->assertEquals('partially_received', $po->receipt_status);
        $this->assertEquals(15, $item1->refresh()->stock_quantity); // 10 + 5

        // 4. Record Invoice for received items
        $invoice = $service->recordInvoice($po, [
            ['purchase_order_item_id' => $poItem1->id, 'quantity' => 5, 'unit_price' => 60]
        ], 'INV-001', now(), now()->addDays(30));

        $po->refresh();
        $this->assertEquals('partially_invoiced', $po->invoice_status);
        $this->assertEquals('unpaid', $po->payment_status);

        // 5. Partial Payment
        $service->recordPayment($po, $invoice, 150, 'bank_transfer');

        $po->refresh();
        $this->assertEquals('partially_paid', $po->payment_status);

        // 5b. Verify 3-way matching
        $matchResults = $service->performThreeWayMatch($po, $invoice);
        $this->assertTrue($matchResults['matched']);

        // Test mismatch
        $item3 = InventoryItem::factory()->create();
        $poItem3 = $po->items()->create(['inventory_item_id' => $item3->id, 'quantity' => 1, 'unit_cost' => 10, 'subtotal' => 10]);
        $invoice2 = $service->recordInvoice($po, [
            ['purchase_order_item_id' => $poItem3->id, 'quantity' => 1, 'unit_price' => 20] // Price mismatch
        ], 'INV-MISMATCH', now(), now());

        $matchResults2 = $service->performThreeWayMatch($po, $invoice2);
        $this->assertFalse($matchResults2['matched']);
        $this->assertStringContainsString('Price mismatch', $matchResults2['mismatches'][0]);

        // 6. Cancellation with side effects
        $service->cancelOrder($po, 'Damaged goods and change of mind');

        $po->refresh();
        $this->assertEquals('cancelled', $po->order_status);
        $this->assertEquals('after_payment', $po->cancellation_type);

        // Verify RTS (Return to Supplier)
        $this->assertCount(1, $po->returns);
        // Verify Credit Note (2 invoices were created, INV-001 and INV-MISMATCH)
        $this->assertCount(2, $po->creditNotes);
        // Verify Refund
        $this->assertCount(1, $po->refunds);
        // Verify Stock Reversal
        $this->assertEquals(10, $item1->refresh()->stock_quantity); // 15 - 5

        // Verify Audit Trail
        $this->assertGreaterThan(0, $po->auditTrails()->count());
    }
}

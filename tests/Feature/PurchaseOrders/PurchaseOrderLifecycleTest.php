<?php

use App\Enums\CancellationType;
use App\Enums\ClosureStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\ReceiptStatus;
use App\Models\InventoryItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use App\Services\CancellationService;
use App\Services\PurchaseOrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\HttpException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->service = app(PurchaseOrderService::class);
});

// ── Helpers ──────────────────────────────────────────────────────────────────

function makeSentPo(): array
{
    $supplier = Supplier::factory()->create();
    $item = InventoryItem::factory()->create(['stock_quantity' => 0]);

    $po = PurchaseOrder::factory()->sent()->create([
        'supplier_id' => $supplier->id,
        'created_by' => auth()->id(),
    ]);

    $poItem = PurchaseOrderItem::factory()->create([
        'purchase_order_id' => $po->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
        'subtotal' => 50000,
    ]);

    return compact('po', 'item', 'poItem');
}

// ── Approve ───────────────────────────────────────────────────────────────────

it('approves a draft order', function () {
    $po = PurchaseOrder::factory()->draft()->create(['created_by' => $this->user->id]);

    $this->service->approve($po);
    $po->refresh();

    expect($po->order_status)->toBe(OrderStatus::Approved)
        ->and($po->approved_at)->not->toBeNull()
        ->and($po->approved_by)->toBe($this->user->id);
});

it('cannot approve a sent order', function () {
    $po = PurchaseOrder::factory()->sent()->create();

    expect(fn () => $this->service->approve($po))->toThrow(HttpException::class);
});

// ── Send ─────────────────────────────────────────────────────────────────────

it('sends a draft order to supplier', function () {
    $po = PurchaseOrder::factory()->draft()->create();

    $this->service->send($po);
    $po->refresh();

    expect($po->order_status)->toBe(OrderStatus::Sent)
        ->and($po->sent_at)->not->toBeNull();
});

it('sends an approved order to supplier', function () {
    $po = PurchaseOrder::factory()->approved()->create();

    $this->service->send($po);

    expect($po->fresh()->order_status)->toBe(OrderStatus::Sent);
});

// ── Goods Receipt ─────────────────────────────────────────────────────────────

it('records a full goods receipt and updates stock', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $grn = $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 10,
    ]]);

    expect($grn->grn_number)->toStartWith('GRN-')
        ->and($po->fresh()->receipt_status)->toBe(ReceiptStatus::FullyReceived)
        ->and($item->fresh()->stock_quantity)->toBe(10);
});

it('records a partial goods receipt', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 4,
    ]]);

    expect($po->fresh()->receipt_status)->toBe(ReceiptStatus::PartiallyReceived)
        ->and($item->fresh()->stock_quantity)->toBe(4);
});

it('cannot receive goods on a non-sent order', function () {
    $po = PurchaseOrder::factory()->draft()->create();
    $item = InventoryItem::factory()->create();
    $poItem = PurchaseOrderItem::factory()->create(['purchase_order_id' => $po->id, 'inventory_item_id' => $item->id, 'quantity' => 5]);

    expect(fn () => $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 5,
    ]]))->toThrow(HttpException::class);
});

// ── Invoice ───────────────────────────────────────────────────────────────────

it('creates a supplier invoice and marks invoice status', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $invoice = $this->service->createInvoice($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
    ]]);

    expect($invoice->invoice_number)->toStartWith('INV-')
        ->and($invoice->total_amount)->toBe('50000.00')
        ->and($po->fresh()->invoice_status)->toBe(InvoiceStatus::FullyInvoiced);
});

// ── Payment ───────────────────────────────────────────────────────────────────

it('records a full payment and marks invoice fully paid', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $invoice = $this->service->createInvoice($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
    ]]);

    $payment = $this->service->recordPayment($invoice, 50000, now()->format('Y-m-d'));

    expect($payment->payment_reference)->toStartWith('PAY-')
        ->and($invoice->fresh()->payment_status)->toBe(PaymentStatus::FullyPaid)
        ->and($po->fresh()->payment_status)->toBe(PaymentStatus::FullyPaid);
});

it('records a partial payment', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $invoice = $this->service->createInvoice($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
    ]]);

    $this->service->recordPayment($invoice, 20000, now()->format('Y-m-d'));

    expect($invoice->fresh()->payment_status)->toBe(PaymentStatus::PartiallyPaid)
        ->and($invoice->fresh()->outstandingBalance())->toBe(30000.0);
});

// ── Auto-close ────────────────────────────────────────────────────────────────

it('auto-closes PO after full receipt and full payment', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 10,
    ]]);

    $invoice = $this->service->createInvoice($po->fresh(), now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
    ]]);

    $this->service->recordPayment($invoice, 50000, now()->format('Y-m-d'));

    expect($po->fresh()->closure_status)->toBe(ClosureStatus::Closed);
});

// ── Cancellation ─────────────────────────────────────────────────────────────

it('cancels a draft order directly', function () {
    $po = PurchaseOrder::factory()->draft()->create();

    $cancellation = app(CancellationService::class)->initiate($po, 'No longer needed.');

    expect($cancellation->cancellation_type)->toBe(CancellationType::Direct)
        ->and($po->fresh()->order_status)->toBe(OrderStatus::Cancelled);
});

it('requires RTS when goods have been received', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 5,
    ]]);

    $cancellation = app(CancellationService::class)->initiate($po->fresh(), 'Wrong items delivered.');

    expect($cancellation->cancellation_type)->toBe(CancellationType::WithReturnToSupplier)
        ->and($cancellation->requires_rts)->toBeTrue()
        ->and($po->fresh()->order_status)->not->toBe(OrderStatus::Cancelled);
});

it('completes cancellation after RTS is resolved', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $this->service->receiveGoods($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 5,
    ]]);

    $cancellationService = app(CancellationService::class);
    $cancellation = $cancellationService->initiate($po->fresh(), 'Wrong items.');
    $cancellationService->completeRequirements($cancellation, ['rts_completed' => true]);

    expect($po->fresh()->order_status)->toBe(OrderStatus::Cancelled)
        ->and($po->fresh()->closure_status)->toBe(ClosureStatus::ForceClosed);
});

it('requires refund when payment has been made', function () {
    ['po' => $po, 'item' => $item, 'poItem' => $poItem] = makeSentPo();

    $invoice = $this->service->createInvoice($po, now()->format('Y-m-d'), [[
        'purchase_order_item_id' => $poItem->id,
        'inventory_item_id' => $item->id,
        'quantity' => 10,
        'unit_cost' => 5000,
    ]]);

    $this->service->recordPayment($invoice, 50000, now()->format('Y-m-d'));

    $cancellation = app(CancellationService::class)->initiate($po->fresh(), 'Overpaid.');

    expect($cancellation->cancellation_type)->toBe(CancellationType::WithRefund)
        ->and($cancellation->requires_refund)->toBeTrue();
});

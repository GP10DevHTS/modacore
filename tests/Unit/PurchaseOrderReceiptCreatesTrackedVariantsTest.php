<?php

use App\Enums\OrderStatus;
use App\Models\GoodsReceipt;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use App\Services\InventorySkuService;
use App\Services\PurchaseOrderService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);

    DB::purge('sqlite');

    Schema::create('suppliers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('inventory_categories', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('code')->nullable();
        $table->foreignId('user_id')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('inventory_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->foreignId('category_id');
        $table->string('sku')->nullable();
        $table->string('code')->nullable();
        $table->decimal('base_rental_price', 10, 2)->default(0);
        $table->decimal('cost_price', 12, 2)->nullable();
        $table->unsignedInteger('stock_quantity')->default(0);
        $table->unsignedInteger('available_quantity')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('inventory_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventory_item_id');
        $table->string('label')->nullable();
        $table->string('composition_key')->nullable();
        $table->string('sku')->nullable();
        $table->decimal('rental_price', 10, 2)->nullable();
        $table->decimal('cost_price', 12, 2)->nullable();
        $table->unsignedInteger('stock_quantity')->default(0);
        $table->unsignedInteger('available_quantity')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('variant_types', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->unsignedInteger('sort_order')->default(0);
        $table->timestamps();
    });

    Schema::create('variant_type_values', function (Blueprint $table) {
        $table->id();
        $table->foreignId('variant_type_id');
        $table->string('label', 100);
        $table->unsignedInteger('sort_order')->default(0);
        $table->timestamps();
    });

    Schema::create('inventory_variant_attribute_values', function (Blueprint $table) {
        $table->foreignId('inventory_variant_id');
        $table->foreignId('variant_type_value_id');
        $table->primary(['inventory_variant_id', 'variant_type_value_id']);
    });

    Schema::create('purchase_orders', function (Blueprint $table) {
        $table->id();
        $table->string('po_number');
        $table->foreignId('supplier_id');
        $table->string('order_status');
        $table->string('receipt_status')->default('not_received');
        $table->string('invoice_status')->default('not_invoiced');
        $table->string('payment_status')->default('unpaid');
        $table->string('closure_status')->default('open');
        $table->decimal('total_amount', 12, 2)->default(0);
        $table->foreignId('created_by')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('purchase_order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_order_id');
        $table->foreignId('inventory_item_id');
        $table->foreignId('inventory_variant_id')->nullable();
        $table->json('variant_value_ids')->nullable();
        $table->string('variant_composition_key')->nullable();
        $table->string('variant_composition_label')->nullable();
        $table->unsignedInteger('quantity');
        $table->unsignedInteger('received_quantity')->default(0);
        $table->unsignedInteger('invoiced_quantity')->default(0);
        $table->decimal('unit_cost', 12, 2)->default(0);
        $table->decimal('subtotal', 12, 2)->default(0);
        $table->timestamps();
    });

    Schema::create('goods_receipts', function (Blueprint $table) {
        $table->id();
        $table->string('grn_number');
        $table->foreignId('purchase_order_id');
        $table->date('received_date');
        $table->text('notes')->nullable();
        $table->foreignId('received_by')->nullable();
        $table->timestamps();
    });

    Schema::create('goods_receipt_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('goods_receipt_id');
        $table->foreignId('purchase_order_item_id');
        $table->foreignId('inventory_item_id');
        $table->unsignedInteger('quantity_received');
        $table->text('notes')->nullable();
        $table->timestamps();
    });

    Schema::create('po_audit_logs', function (Blueprint $table) {
        $table->id();
        $table->foreignId('purchase_order_id');
        $table->foreignId('user_id')->nullable();
        $table->string('action');
        $table->text('description');
        $table->json('old_values')->nullable();
        $table->json('new_values')->nullable();
        $table->timestamps();
    });
});

test('receiving a composition purchase line creates individually tracked variants', function () {
    $category = InventoryCategory::create(['name' => 'Shirts', 'code' => 'C']);
    $item = InventoryItem::create(['name' => 'Shirt', 'category_id' => $category->id]);
    $supplier = Supplier::create(['name' => 'Supplier']);

    $color = VariantType::create(['name' => 'Color', 'sort_order' => 1]);
    $size = VariantType::create(['name' => 'Size', 'sort_order' => 2]);
    $blue = VariantTypeValue::create(['variant_type_id' => $color->id, 'label' => 'Blue', 'sort_order' => 1]);
    $medium = VariantTypeValue::create(['variant_type_id' => $size->id, 'label' => 'Medium', 'sort_order' => 2]);

    $order = PurchaseOrder::create([
        'po_number' => 'PO-00001',
        'supplier_id' => $supplier->id,
        'order_status' => OrderStatus::Sent,
        'total_amount' => 300_000,
    ]);

    $skuService = app(InventorySkuService::class);

    $line = PurchaseOrderItem::create([
        'purchase_order_id' => $order->id,
        'inventory_item_id' => $item->id,
        'variant_value_ids' => [$blue->id, $medium->id],
        'variant_composition_key' => $skuService->compositionKey([$blue->id, $medium->id]),
        'variant_composition_label' => $skuService->compositionLabel([$blue->id, $medium->id]),
        'quantity' => 3,
        'unit_cost' => 100_000,
        'subtotal' => 300_000,
    ]);

    app(PurchaseOrderService::class)->receiveGoods($order, '2026-05-02', [[
        'purchase_order_item_id' => $line->id,
        'inventory_item_id' => $item->id,
        'quantity_received' => 3,
        'notes' => null,
    ]]);

    $variants = InventoryVariant::with('attributeValues')->where('inventory_item_id', $item->id)->orderBy('id')->get();

    expect(GoodsReceipt::count())->toBe(1)
        ->and($line->fresh()->received_quantity)->toBe(3)
        ->and($variants)->toHaveCount(3)
        ->and($variants->pluck('sku')->all())->toBe([$item->sku.'-0001', $item->sku.'-0002', $item->sku.'-0003'])
        ->and($variants->first()->name)->toBe('Blue / Medium');
});

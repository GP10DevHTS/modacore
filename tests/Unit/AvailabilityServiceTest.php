<?php

use App\Livewire\Bookings\Create;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Services\AvailabilityService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);

    DB::purge('sqlite');

    Schema::create('inventory_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
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
        $table->unsignedInteger('stock_quantity')->default(0);
        $table->unsignedInteger('available_quantity')->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
    });

    Schema::create('bookings', function (Blueprint $table) {
        $table->id();
        $table->string('booking_number');
        $table->foreignId('customer_id')->default(1);
        $table->dateTime('hire_from');
        $table->dateTime('hire_to');
        $table->string('status');
        $table->decimal('total_amount', 10, 2)->default(0);
        $table->foreignId('created_by')->default(1);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('booking_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('booking_id');
        $table->foreignId('inventory_item_id');
        $table->foreignId('inventory_variant_id')->nullable();
        $table->unsignedSmallInteger('quantity')->default(1);
        $table->decimal('unit_price', 10, 2)->default(0);
        $table->decimal('subtotal', 10, 2)->default(0);
        $table->string('status')->default('pending');
        $table->timestamps();
    });

    Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('phone')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
});

test('item availability allows remaining bookable stock and rejects excess quantity', function () {
    $item = InventoryItem::create([
        'name' => 'Blue Dress',
        'stock_quantity' => 10,
        'available_quantity' => 4,
        'is_active' => true,
    ]);

    createBookingItem($item, null, 2);

    $availability = app(AvailabilityService::class);
    $hireFrom = Carbon::parse('2026-06-01 10:00:00');
    $hireTo = Carbon::parse('2026-06-03 10:00:00');

    expect($availability->availableQuantity($item->id, null, $hireFrom, $hireTo))
        ->toBe(2)
        ->and($availability->unavailabilityReason($item->id, null, 2, $hireFrom, $hireTo))
        ->toBeNull()
        ->and($availability->unavailabilityReason($item->id, null, 3, $hireFrom, $hireTo))
        ->toBe('Only 2 units available for the selected dates.');
});

test('variant availability uses variant bookable stock', function () {
    $item = InventoryItem::create([
        'name' => 'Suit',
        'stock_quantity' => 10,
        'available_quantity' => 10,
        'is_active' => true,
    ]);

    $blueLarge = InventoryVariant::create([
        'inventory_item_id' => $item->id,
        'label' => 'Blue / L',
        'stock_quantity' => 10,
        'available_quantity' => 4,
        'is_active' => true,
    ]);

    $blueMedium = InventoryVariant::create([
        'inventory_item_id' => $item->id,
        'label' => 'Blue / M',
        'stock_quantity' => 10,
        'available_quantity' => 4,
        'is_active' => true,
    ]);

    createBookingItem($item, $blueLarge, 2);
    createBookingItem($item, $blueMedium, 4);

    $availability = app(AvailabilityService::class);
    $hireFrom = Carbon::parse('2026-06-01 10:00:00');
    $hireTo = Carbon::parse('2026-06-03 10:00:00');

    expect($availability->availableQuantity($item->id, $blueLarge->id, $hireFrom, $hireTo))
        ->toBe(2);
});

test('checked out stock is not double counted against overlapping bookings', function () {
    $item = InventoryItem::create([
        'name' => 'Red Dress',
        'stock_quantity' => 4,
        'available_quantity' => 2,
        'is_active' => true,
    ]);

    createBookingItem($item, null, 2, 'checked_out');

    $availability = app(AvailabilityService::class);
    $hireFrom = Carbon::parse('2026-06-01 10:00:00');
    $hireTo = Carbon::parse('2026-06-03 10:00:00');

    expect($availability->availableQuantity($item->id, null, $hireFrom, $hireTo))
        ->toBe(2);
});

test('availability validation aggregates duplicate lines before comparing stock', function () {
    $item = InventoryItem::create([
        'name' => 'Black Tux',
        'stock_quantity' => 10,
        'available_quantity' => 2,
        'is_active' => true,
    ]);

    $errors = app(AvailabilityService::class)->validateItems([
        ['inventory_item_id' => $item->id, 'inventory_variant_id' => null, 'quantity' => 1],
        ['inventory_item_id' => $item->id, 'inventory_variant_id' => null, 'quantity' => 2],
    ], Carbon::parse('2026-06-01'), Carbon::parse('2026-06-03'));

    expect($errors)
        ->toHaveCount(2)
        ->each->toBe('Only 2 units available for the selected dates.');
});

test('booking line quantity edits cannot exceed available bookable stock', function () {
    $item = InventoryItem::create([
        'name' => 'Green Dress',
        'stock_quantity' => 5,
        'available_quantity' => 2,
        'is_active' => true,
    ]);

    Livewire::test(Create::class)
        ->set('hireFrom', '2026-06-01T10:00')
        ->set('hireTo', '2026-06-03T10:00')
        ->set('lineItems', [[
            'inventory_item_id' => $item->id,
            'inventory_variant_id' => null,
            'quantity' => 1,
            'unit_price' => 100_000,
            'subtotal' => 100_000,
            'item_name' => $item->name,
            'variant_name' => null,
        ]])
        ->call('updateLineQuantity', 0, 3)
        ->assertSet('lineItems.0.quantity', 1)
        ->call('updateLineQuantity', 0, 2)
        ->assertSet('lineItems.0.quantity', 2);
});

function createBookingItem(InventoryItem $item, ?InventoryVariant $variant, int $quantity, string $status = 'pending'): BookingItem
{
    $booking = Booking::create([
        'booking_number' => fake()->unique()->bothify('BK-####'),
        'hire_from' => Carbon::parse('2026-06-01 08:00:00'),
        'hire_to' => Carbon::parse('2026-06-05 08:00:00'),
        'status' => 'confirmed',
        'total_amount' => 0,
    ]);

    return BookingItem::create([
        'booking_id' => $booking->id,
        'inventory_item_id' => $item->id,
        'inventory_variant_id' => $variant?->id,
        'quantity' => $quantity,
        'unit_price' => 0,
        'subtotal' => 0,
        'status' => $status,
    ]);
}

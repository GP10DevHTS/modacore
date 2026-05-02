<?php

use App\Livewire\Bookings\Create;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

uses(TestCase::class);

test('booking variant picker labels variations by selected attribute values', function () {
    config([
        'database.default' => 'sqlite',
        'database.connections.sqlite.database' => ':memory:',
    ]);

    DB::purge('sqlite');

    Schema::create('inventory_items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->decimal('base_rental_price', 10, 2)->default(0);
        $table->boolean('is_active')->default(true);
        $table->timestamps();
        $table->softDeletes();
    });

    Schema::create('inventory_variants', function (Blueprint $table) {
        $table->id();
        $table->foreignId('inventory_item_id');
        $table->string('size')->nullable();
        $table->string('color')->nullable();
        $table->string('label')->nullable();
        $table->integer('stock_quantity')->default(0);
        $table->boolean('is_active')->default(true);
        $table->decimal('rental_price', 10, 2)->nullable();
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
    });

    $item = InventoryItem::create([
        'name' => 'Wedding Dress',
        'base_rental_price' => 100_000,
        'is_active' => true,
    ]);

    $color = VariantType::create(['name' => 'Color', 'sort_order' => 1]);
    $size = VariantType::create(['name' => 'Size', 'sort_order' => 2]);

    $blue = VariantTypeValue::create(['variant_type_id' => $color->id, 'label' => 'Blue', 'sort_order' => 1]);
    $large = VariantTypeValue::create(['variant_type_id' => $size->id, 'label' => 'L', 'sort_order' => 2]);

    $variant = InventoryVariant::create([
        'inventory_item_id' => $item->id,
        'stock_quantity' => 1,
        'is_active' => true,
        'rental_price' => 125_000,
    ]);

    $variant->attributeValues()->attach([$blue->id, $large->id]);

    $component = app(Create::class);
    $component->pickerItemId = (string) $item->id;

    expect($component->selectedItemVariants()->first()->name)
        ->toBe('Blue / L')
        ->not->toBe("Variant #{$variant->id}");
});

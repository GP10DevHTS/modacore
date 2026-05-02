<?php

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\VariantType;
use App\Models\VariantTypeValue;
use App\Services\InventorySkuService;
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
});

test('category and item codes roll from z to aa and prefix item skus', function () {
    InventoryCategory::create(['name' => 'Existing', 'code' => 'Z']);

    $category = InventoryCategory::create(['name' => 'Suits']);
    $firstItem = InventoryItem::create(['name' => 'Jacket', 'category_id' => $category->id]);
    $secondItem = InventoryItem::create(['name' => 'Trouser', 'category_id' => $category->id]);

    expect($category->code)
        ->toBe('AA')
        ->and($firstItem->code)->toBe('A')
        ->and($firstItem->sku)->toBe('AAA')
        ->and($secondItem->sku)->toBe('AAB');
});

test('saving a variation creates bounded tracked units with unique skus', function () {
    $category = InventoryCategory::create(['name' => 'Dresses', 'code' => 'B']);
    $item = InventoryItem::create(['name' => 'Dress', 'category_id' => $category->id]);

    $color = VariantType::create(['name' => 'Color', 'sort_order' => 1]);
    $size = VariantType::create(['name' => 'Size', 'sort_order' => 2]);
    $blue = VariantTypeValue::create(['variant_type_id' => $color->id, 'label' => 'Blue', 'sort_order' => 1]);
    $small = VariantTypeValue::create(['variant_type_id' => $size->id, 'label' => 'Small', 'sort_order' => 2]);

    $skuService = app(InventorySkuService::class);

    for ($unit = 0; $unit < 3; $unit++) {
        $skuService->createTrackedVariant($item, [$blue->id, $small->id], null, null);
    }

    $variants = InventoryVariant::with('attributeValues')->where('inventory_item_id', $item->id)->orderBy('id')->get();

    expect($variants)->toHaveCount(3)
        ->and($variants->pluck('sku')->all())->toBe([$item->sku.'-0001', $item->sku.'-0002', $item->sku.'-0003'])
        ->and($variants->pluck('composition_key')->unique()->values()->all())->toBe([$skuService->compositionKey([$blue->id, $small->id])])
        ->and($variants->first()->name)->toBe('Blue / Small');
});

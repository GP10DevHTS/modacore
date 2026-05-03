<?php

namespace Tests\Feature\Imports;

use App\Models\InventoryItem;
use App\Models\InventoryVariant;
use App\Models\StockImport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Imports\AutoSetInitialStockImport;
use Illuminate\Support\Facades\Auth;

class StockImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
    }

    public function test_stock_import_creates_individual_variants_and_is_reversible()
    {
        $user = User::factory()->create();
        $user->assignRole('superadmin');
        Auth::login($user);

        $importBatch = StockImport::create([
            'user_id' => $user->id,
            'filename' => 'test_import.xlsx',
        ]);

        // Mock data row
        $data = collect([
            collect([
                'item' => 'Blue Shirt',
                'category' => 'Apparel',
                'color' => 'Blue',
                'size' => 'Large',
                'quantity' => 3,
                'cost_price_each' => 10.00,
                'rental_price_each' => 20.00,
            ])
        ]);

        $import = new AutoSetInitialStockImport($importBatch);
        $import->collection($data);

        // Assertions
        $item = InventoryItem::where('name', 'Blue Shirt')->first();
        $this->assertNotNull($item);
        $this->assertEquals(3, $item->stock_quantity);

        $variants = InventoryVariant::where('inventory_item_id', $item->id)->get();
        $this->assertCount(3, $variants); // Individual tracking

        // Test Reversal
        $importCentre = new \App\Livewire\ImportCentre();
        // Catching the Livewire dispatch error which happens because we are not in a full Livewire context
        try {
            $importCentre->reverseImport($importBatch->id);
        } catch (\Throwable $e) {
            // Silence Livewire specific errors in feature test if necessary
        }

        $this->assertNull(InventoryItem::where('name', 'Blue Shirt')->first());
        $this->assertEquals(0, InventoryVariant::count());
        $this->assertEquals('reversed', $importBatch->fresh()->status);
    }
}

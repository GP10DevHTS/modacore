<?php

namespace Database\Seeders;

use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $userId = User::firstOrFail()->id;

        $categories = [
            [
                'name' => 'Wedding Dresses',
                'description' => 'Bridal gowns and wedding dresses for hire.',
                'items' => [
                    ['name' => 'Classic A-Line Wedding Gown', 'sku' => 'WD-001', 'price' => 350000, 'stock' => 3],
                    ['name' => 'Lace Ball Gown', 'sku' => 'WD-002', 'price' => 450000, 'stock' => 2],
                    ['name' => 'Mermaid Wedding Dress', 'sku' => 'WD-003', 'price' => 400000, 'stock' => 2],
                    ['name' => 'Empire Waist Bridal Gown', 'sku' => 'WD-004', 'price' => 300000, 'stock' => 3],
                    ['name' => 'Off-Shoulder Wedding Dress', 'sku' => 'WD-005', 'price' => 380000, 'stock' => 2],
                ],
            ],
            [
                'name' => 'Evening Gowns',
                'description' => 'Formal evening gowns and cocktail dresses.',
                'items' => [
                    ['name' => 'Red Satin Evening Gown', 'sku' => 'EG-001', 'price' => 180000, 'stock' => 4],
                    ['name' => 'Beaded Floor-Length Gown', 'sku' => 'EG-002', 'price' => 220000, 'stock' => 3],
                    ['name' => 'Black Cocktail Dress', 'sku' => 'EG-003', 'price' => 120000, 'stock' => 5],
                    ['name' => 'Gold Sequin Gown', 'sku' => 'EG-004', 'price' => 250000, 'stock' => 3],
                    ['name' => 'Navy Chiffon Evening Dress', 'sku' => 'EG-005', 'price' => 150000, 'stock' => 4],
                ],
            ],
            [
                'name' => 'Suits & Tuxedos',
                'description' => "Men's formal suits, tuxedos and dress shirts.",
                'items' => [
                    ['name' => 'Classic Black Tuxedo', 'sku' => 'ST-001', 'price' => 200000, 'stock' => 5],
                    ['name' => 'Navy Blue Business Suit', 'sku' => 'ST-002', 'price' => 150000, 'stock' => 6],
                    ['name' => 'Grey Three-Piece Suit', 'sku' => 'ST-003', 'price' => 170000, 'stock' => 4],
                    ['name' => 'White Dinner Jacket', 'sku' => 'ST-004', 'price' => 180000, 'stock' => 4],
                    ['name' => 'Slim-Fit Charcoal Suit', 'sku' => 'ST-005', 'price' => 160000, 'stock' => 5],
                ],
            ],
            [
                'name' => 'Traditional Attire',
                'description' => 'Ugandan and East African traditional wear — Gomesi, Kanzu, Busuti.',
                'items' => [
                    ['name' => 'Silk Gomesi (Cream)', 'sku' => 'TA-001', 'price' => 100000, 'stock' => 6],
                    ['name' => 'Embroidered Kanzu', 'sku' => 'TA-002', 'price' => 80000, 'stock' => 8],
                    ['name' => 'Kitenge Busuti', 'sku' => 'TA-003', 'price' => 90000, 'stock' => 5],
                    ['name' => 'Brocade Gomesi (Gold)', 'sku' => 'TA-004', 'price' => 120000, 'stock' => 4],
                    ['name' => 'Formal White Kanzu', 'sku' => 'TA-005', 'price' => 75000, 'stock' => 8],
                ],
            ],
            [
                'name' => 'Bridesmaid Dresses',
                'description' => 'Coordinating dresses for bridesmaids and wedding parties.',
                'items' => [
                    ['name' => 'Dusty Rose Bridesmaid Dress', 'sku' => 'BD-001', 'price' => 130000, 'stock' => 8],
                    ['name' => 'Sage Green A-Line Dress', 'sku' => 'BD-002', 'price' => 120000, 'stock' => 8],
                    ['name' => 'Royal Blue Wrap Dress', 'sku' => 'BD-003', 'price' => 110000, 'stock' => 10],
                    ['name' => 'Lavender Chiffon Dress', 'sku' => 'BD-004', 'price' => 125000, 'stock' => 8],
                ],
            ],
            [
                'name' => 'Accessories',
                'description' => 'Veils, ties, fascinators, gloves and other formal accessories.',
                'items' => [
                    ['name' => 'Bridal Veil (Cathedral Length)', 'sku' => 'AC-001', 'price' => 50000, 'stock' => 10],
                    ['name' => 'Pearl Hair Tiara', 'sku' => 'AC-002', 'price' => 35000, 'stock' => 12],
                    ['name' => 'Silk Bow Tie', 'sku' => 'AC-003', 'price' => 20000, 'stock' => 20],
                    ['name' => 'Formal Cufflinks Set', 'sku' => 'AC-004', 'price' => 25000, 'stock' => 15],
                    ['name' => 'Bridal Gloves (Elbow Length)', 'sku' => 'AC-005', 'price' => 40000, 'stock' => 10],
                    ['name' => 'Fascinator Hat', 'sku' => 'AC-006', 'price' => 45000, 'stock' => 8],
                ],
            ],
        ];

        foreach ($categories as $catData) {
            $category = InventoryCategory::firstOrCreate(
                ['name' => $catData['name']],
                ['description' => $catData['description'], 'user_id' => $userId]
            );

            foreach ($catData['items'] as $itemData) {
                InventoryItem::firstOrCreate(
                    ['sku' => $itemData['sku']],
                    [
                        'name' => $itemData['name'],
                        'category_id' => $category->id,
                        'base_rental_price' => $itemData['price'],
                        'stock_quantity' => 0, // $itemData['stock'],
                        'is_active' => true,
                    ]
                );
            }
        }

        $this->command->info('Demo data seeded: '.count($categories).' categories and '.collect($categories)->sum(fn ($c) => count($c['items'])).' inventory items.');
    }
}

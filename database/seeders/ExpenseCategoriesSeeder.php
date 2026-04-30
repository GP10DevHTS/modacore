<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use App\Models\ExpenseItem;
use Illuminate\Database\Seeder;

class ExpenseCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
                'name' => 'Garment Maintenance',
                'description' => 'Cleaning, dry cleaning, repairs, alterations, pressing and steaming of hire garments.',
                'items' => [
                    ['name' => 'Dry Cleaning', 'description' => 'Professional dry cleaning service for hire garments.'],
                    ['name' => 'Garment Repairs', 'description' => 'Seam repairs, zip replacements, and stitching.'],
                    ['name' => 'Steam Pressing', 'description' => 'Pressing and steaming for presentation-ready garments.'],
                    ['name' => 'Stain Removal', 'description' => 'Chemical stain removal treatments.'],
                    ['name' => 'Embroidery & Lace Repair', 'description' => 'Detailed repair of delicate embroidery and lace.'],
                    ['name' => 'Laundry Service', 'description' => 'General laundry for washable hire items.'],
                ],
            ],
            [
                'name' => 'Inventory Procurement',
                'description' => 'Purchase of new garments, accessories, and replacement items for the hire stock.',
                'items' => [
                    ['name' => 'Dresses & Gowns', 'description' => 'Bridesmaid dresses, evening gowns, and wedding gowns.'],
                    ['name' => 'Men\'s Suits & Tuxedos', 'description' => 'Suits, tuxedos, and formal men\'s wear.'],
                    ['name' => 'Accessories', 'description' => 'Veils, cummerbunds, bow ties, and other accessories.'],
                    ['name' => 'Traditional Wear', 'description' => 'Gomesi, kanzu, kitenge, and other traditional garments.'],
                    ['name' => 'Fabric & Materials', 'description' => 'Raw fabric purchased for custom garment creation.'],
                ],
            ],
            [
                'name' => 'Packaging & Presentation',
                'description' => 'Garment bags, tissue paper, tags, labels, hangers, and presentation materials.',
                'items' => [
                    ['name' => 'Garment Bags', 'description' => 'Cover and protective bags for hire items.'],
                    ['name' => 'Hangers', 'description' => 'Padded and standard hangers for display and storage.'],
                    ['name' => 'Labels & Tags', 'description' => 'Price tags, care labels, and custom branded labels.'],
                    ['name' => 'Tissue & Wrapping', 'description' => 'Acid-free tissue and wrapping materials.'],
                ],
            ],
            [
                'name' => 'Storage & Facilities',
                'description' => 'Shop rent, storage unit costs, utilities including electricity, water, and internet.',
                'items' => [
                    ['name' => 'Shop Rent', 'description' => 'Monthly rent for the main showroom premises.'],
                    ['name' => 'Storage Unit Rent', 'description' => 'Off-site storage for overflow garment stock.'],
                    ['name' => 'Electricity', 'description' => 'Monthly electricity bill for shop and storage.'],
                    ['name' => 'Water', 'description' => 'Monthly water bill.'],
                    ['name' => 'Internet & Phone', 'description' => 'Internet subscription and business phone costs.'],
                    ['name' => 'Equipment Servicing', 'description' => 'Air conditioning, steamer, and facility maintenance.'],
                ],
            ],
            [
                'name' => 'Logistics & Delivery',
                'description' => 'Transport for garment pickup and delivery, fuel, and courier service costs.',
                'items' => [
                    ['name' => 'Fuel', 'description' => 'Fuel costs for garment delivery and collection runs.'],
                    ['name' => 'Courier & Boda', 'description' => 'Boda boda and courier fees for express deliveries.'],
                    ['name' => 'Vehicle Hire', 'description' => 'Hired transport for bulk or out-of-town deliveries.'],
                    ['name' => 'Transport Allowance', 'description' => 'Staff transport allowances for delivery duties.'],
                ],
            ],
            [
                'name' => 'Staff & Labour',
                'description' => 'Casual labour, seamstress wages, attendant pay, and staff training costs.',
                'items' => [
                    ['name' => 'Seamstress Wages', 'description' => 'Pay for alteration and repair seamstress work.'],
                    ['name' => 'Casual Labour', 'description' => 'Casual cleaning and general labour for the showroom.'],
                    ['name' => 'Event Attendants', 'description' => 'Staff hired to assist customers at weekend events.'],
                    ['name' => 'Staff Training', 'description' => 'Workshop and training costs for staff development.'],
                    ['name' => 'Overtime Pay', 'description' => 'Additional pay during peak wedding season.'],
                ],
            ],
            [
                'name' => 'Marketing & Advertising',
                'description' => 'Social media promotions, photography, printed materials, and event sponsorships.',
                'items' => [
                    ['name' => 'Social Media Ads', 'description' => 'Facebook, Instagram, and Google advertising spend.'],
                    ['name' => 'Photography', 'description' => 'Professional shoots for catalogue and social media.'],
                    ['name' => 'Print Materials', 'description' => 'Flyers, brochures, and other printed collateral.'],
                    ['name' => 'Events & Expos', 'description' => 'Booth fees and sponsorships at wedding expos.'],
                ],
            ],
            [
                'name' => 'Administrative Costs',
                'description' => 'Office supplies, accounting, software subscriptions, bank charges, and licenses.',
                'items' => [
                    ['name' => 'Office Supplies', 'description' => 'Stationery, paper, ink cartridges, and consumables.'],
                    ['name' => 'Software Subscriptions', 'description' => 'Accounting and business management software.'],
                    ['name' => 'Bank Charges', 'description' => 'Transaction fees and bank service charges.'],
                    ['name' => 'Business Licenses', 'description' => 'Annual trading licenses and permits.'],
                ],
            ],
            [
                'name' => 'Equipment & Tools',
                'description' => 'Sewing machines, steamers, garment racks, mannequins and related equipment costs.',
                'items' => [
                    ['name' => 'Sewing Equipment', 'description' => 'Sewing machines, needles, thread, and accessories.'],
                    ['name' => 'Steamers & Irons', 'description' => 'Garment steamers and heavy-duty irons.'],
                    ['name' => 'Display Equipment', 'description' => 'Racks, mannequins, and showroom display fittings.'],
                    ['name' => 'Equipment Repairs', 'description' => 'Servicing and repair of business equipment.'],
                ],
            ],
            [
                'name' => 'Insurance & Compliance',
                'description' => 'Business insurance premiums, garment insurance, permits and compliance costs.',
                'items' => [
                    ['name' => 'Business Insurance', 'description' => 'Annual general business insurance premium.'],
                    ['name' => 'Garment Stock Insurance', 'description' => 'Insurance covering the garment hire stock.'],
                    ['name' => 'Fire & Safety Compliance', 'description' => 'Fire safety certificates and safety audit fees.'],
                    ['name' => 'Health & Safety', 'description' => 'Health and safety compliance costs.'],
                ],
            ],
        ];

        foreach ($data as $categoryData) {
            $category = ExpenseCategory::firstOrCreate(
                ['name' => $categoryData['name']],
                ['description' => $categoryData['description']]
            );

            foreach ($categoryData['items'] as $itemData) {
                ExpenseItem::firstOrCreate(
                    ['name' => $itemData['name'], 'category_id' => $category->id],
                    ['description' => $itemData['description']]
                );
            }
        }

        $categoryCount = count($data);
        $itemCount = collect($data)->sum(fn ($c) => count($c['items']));

        $this->command->info("Expense seeder: {$categoryCount} categories and {$itemCount} items created.");
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Fresh Produce Co.',
                'email' => 'orders@freshproduce.com',
                'phone' => '+1-555-1001',
                'address' => '100 Farm Road, Agricultural District, CA 90210',
                'contact_person' => 'John Farmer',
            ],
            [
                'name' => 'Premium Meats Ltd.',
                'email' => 'supply@premiummeats.com',
                'phone' => '+1-555-1002',
                'address' => '200 Butcher Street, Meat District, TX 77001',
                'contact_person' => 'Mike Butcher',
            ],
            [
                'name' => 'Dairy Direct',
                'email' => 'orders@dairydirect.com',
                'phone' => '+1-555-1003',
                'address' => '300 Milk Avenue, Dairy Valley, WI 53701',
                'contact_person' => 'Sarah Dairy',
            ],
            [
                'name' => 'Seafood Suppliers Inc.',
                'email' => 'fresh@seafoodsuppliers.com',
                'phone' => '+1-555-1004',
                'address' => '400 Harbor Drive, Coastal City, FL 33101',
                'contact_person' => 'Captain Fish',
            ],
            [
                'name' => 'Bakery Ingredients Co.',
                'email' => 'supply@bakeryingredients.com',
                'phone' => '+1-555-1005',
                'address' => '500 Flour Street, Baking District, NY 10001',
                'contact_person' => 'Baker Brown',
            ],
            [
                'name' => 'Spice World',
                'email' => 'orders@spiceworld.com',
                'phone' => '+1-555-1006',
                'address' => '600 Spice Lane, Flavor Town, CA 90210',
                'contact_person' => 'Spice Master',
            ],
            [
                'name' => 'Frozen Foods Corp.',
                'email' => 'supply@frozenfoods.com',
                'phone' => '+1-555-1007',
                'address' => '700 Ice Road, Cold Storage, MN 55401',
                'contact_person' => 'Ice Man',
            ],
            [
                'name' => 'Organic Suppliers',
                'email' => 'organic@suppliers.com',
                'phone' => '+1-555-1008',
                'address' => '800 Green Street, Organic Valley, OR 97201',
                'contact_person' => 'Green Thumb',
            ],
            [
                'name' => 'Beverage Distributors',
                'email' => 'drinks@beveragedist.com',
                'phone' => '+1-555-1009',
                'address' => '900 Liquid Lane, Beverage City, CO 80201',
                'contact_person' => 'Drink Master',
            ],
            [
                'name' => 'Kitchen Equipment Co.',
                'email' => 'equipment@kitchenco.com',
                'phone' => '+1-555-1010',
                'address' => '1000 Tool Street, Equipment District, IL 60601',
                'contact_person' => 'Tool Guy',
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}

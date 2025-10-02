<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventory;
use App\Models\Product;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = Product::all();
        
        foreach ($products as $product) {
            Inventory::create([
                'product_id' => $product->id,
                'quantity' => rand(5, 50),
                'minimum_stock' => 10,
                'last_restocked_at' => now()->subDays(rand(1, 30)),
            ]);
        }
    }
}

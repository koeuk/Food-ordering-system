<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryOrderItem;
use App\Models\InventoryOrder;
use App\Models\Product;

class InventoryOrderItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $inventoryOrders = InventoryOrder::all();
        $products = Product::all();
        
        foreach ($inventoryOrders as $inventoryOrder) {
            // Each inventory order has 2-5 items
            $itemCount = rand(2, 5);
            $selectedProducts = $products->random($itemCount);
            
            foreach ($selectedProducts as $product) {
                $quantity = rand(10, 50);
                $unitCost = $product->price * 0.6; // 60% of selling price
                $subtotal = $unitCost * $quantity;
                
                InventoryOrderItem::create([
                    'inventory_order_id' => $inventoryOrder->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_cost' => $unitCost,
                    'subtotal' => $subtotal,
                ]);
            }
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use App\Models\Inventory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Bill;
use App\Models\Supplier;
use App\Models\InventoryOrder;
use App\Models\InventoryOrderItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
            InventorySeeder::class,
            SupplierSeeder::class,
            OrderSeeder::class,
            OrderItemSeeder::class,
            BillSeeder::class,
            InventoryOrderSeeder::class,
            InventoryOrderItemSeeder::class,
        ]);
    }
}
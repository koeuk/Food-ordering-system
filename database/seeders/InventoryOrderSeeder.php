<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\InventoryOrder;
use App\Models\Supplier;
use App\Models\User;

class InventoryOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $managers = User::where('role', 'manager')->get();
        
        for ($i = 1; $i <= 10; $i++) {
            $supplier = $suppliers->random();
            $manager = $managers->first();
            $totalAmount = rand(50000, 200000) / 100; // $500.00 to $2000.00
            
            $statuses = ['pending', 'sent', 'received', 'cancelled'];
            $status = $statuses[array_rand($statuses)];
            
            InventoryOrder::create([
                'supplier_id' => $supplier->id,
                'manager_id' => $manager->id,
                'order_number' => 'INV-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'status' => $status,
                'total_amount' => $totalAmount,
                'sent_at' => in_array($status, ['sent', 'received']) ? now()->subDays(rand(1, 30)) : null,
                'received_at' => $status === 'received' ? now()->subDays(rand(1, 15)) : null,
            ]);
        }
    }
}

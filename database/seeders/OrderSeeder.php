<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $customers = User::where('role', 'user')->get();
        
        for ($i = 1; $i <= 10; $i++) {
            $customer = $customers->random();
            $subtotal = rand(1500, 5000) / 100; // $15.00 to $50.00
            $tax = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $tax;
            
            $statuses = ['pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled'];
            $status = $statuses[array_rand($statuses)];
            
            Order::create([
                'customer_id' => $customer->id,
                'order_number' => 'ORD-' . str_pad($i, 6, '0', STR_PAD_LEFT),
                'status' => $status,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'delivery_address' => $customer->address,
                'notes' => $i % 3 === 0 ? 'Please deliver to the back door.' : null,
                'confirmed_at' => $status !== 'pending' ? now()->subHours(rand(1, 24)) : null,
                'delivered_at' => $status === 'delivered' ? now()->subHours(rand(1, 48)) : null,
            ]);
        }
    }
}

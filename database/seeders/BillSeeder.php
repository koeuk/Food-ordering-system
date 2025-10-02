<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Bill;
use App\Models\Order;

class BillSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $orders = Order::all();
        $paymentMethods = ['cash', 'card', 'online'];
        $paymentStatuses = ['unpaid', 'paid', 'refunded'];
        
        foreach ($orders as $order) {
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];
            $paymentMethod = $paymentStatus === 'paid' ? $paymentMethods[array_rand($paymentMethods)] : null;
            
            Bill::create([
                'order_id' => $order->id,
                'bill_number' => 'BILL-' . str_pad($order->id, 6, '0', STR_PAD_LEFT),
                'amount' => $order->total,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'paid_at' => $paymentStatus === 'paid' ? now()->subHours(rand(1, 72)) : null,
            ]);
        }
    }
}

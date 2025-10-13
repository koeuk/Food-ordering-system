<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Order;

class OrderCoordinatesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add sample coordinates to existing orders for testing
        $orders = Order::whereNull('delivery_latitude')->orWhereNull('delivery_longitude')->get();
        
        // Sample coordinates for Phnom Penh, Cambodia
        $sampleCoordinates = [
            ['lat' => 11.547443, 'lng' => 104.900198], // Toul Kork District
            ['lat' => 11.5564, 'lng' => 104.9282],    // Central Phnom Penh
            ['lat' => 11.5449, 'lng' => 104.8922],    // Russian Market area
            ['lat' => 11.5688, 'lng' => 104.8910],    // Wat Phnom area
            ['lat' => 11.5524, 'lng' => 104.9338],    // Chroy Changvar
        ];

        foreach ($orders as $index => $order) {
            $coords = $sampleCoordinates[$index % count($sampleCoordinates)];
            
            $order->update([
                'delivery_latitude' => $coords['lat'],
                'delivery_longitude' => $coords['lng']
            ]);
            
            $this->command->info("Updated order #{$order->order_number} with coordinates: {$coords['lat']}, {$coords['lng']}");
        }
        
        $this->command->info("Updated " . $orders->count() . " orders with sample coordinates.");
    }
}
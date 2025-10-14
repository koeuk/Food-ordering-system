<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SliderImage;

class SliderImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sliderImages = [
            [
                'title' => 'Welcome to Food Ordering System',
                'description' => 'Order delicious food online with ease',
                'image_url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'Browse Menu',
                'button_url' => '/web/products',
                'order' => 0,
                'is_active' => true
            ],
            [
                'title' => 'Fresh & Delicious Meals',
                'description' => 'Experience the finest cuisine crafted by our expert chefs',
                'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'Order Now',
                'button_url' => '/web/products',
                'order' => 1,
                'is_active' => true
            ],
            [
                'title' => 'Fast Delivery Service',
                'description' => 'Get your favorite meals delivered to your doorstep in minutes',
                'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'Start Ordering',
                'button_url' => '/web/products',
                'order' => 2,
                'is_active' => true
            ],
            [
                'title' => 'Premium Quality Food',
                'description' => 'We use only the freshest ingredients to prepare your meals',
                'image_url' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'Explore Menu',
                'button_url' => '/web/products',
                'order' => 3,
                'is_active' => true
            ]
        ];

        foreach ($sliderImages as $image) {
            SliderImage::create($image);
        }
    }
}

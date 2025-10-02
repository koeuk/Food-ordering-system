<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Appetizers',
                'description' => 'Start your meal with our delicious appetizers and small plates.',
            ],
            [
                'name' => 'Main Course',
                'description' => 'Hearty main dishes perfect for lunch and dinner.',
            ],
            [
                'name' => 'Desserts',
                'description' => 'Sweet treats to end your meal on a perfect note.',
            ],
            [
                'name' => 'Beverages',
                'description' => 'Refreshing drinks, coffee, tea, and specialty beverages.',
            ],
            [
                'name' => 'Salads',
                'description' => 'Fresh and healthy salad options with premium ingredients.',
            ],
            [
                'name' => 'Pizza',
                'description' => 'Authentic Italian-style pizzas with fresh toppings.',
            ],
            [
                'name' => 'Pasta',
                'description' => 'Classic Italian pasta dishes with homemade sauces.',
            ],
            [
                'name' => 'Sandwiches',
                'description' => 'Delicious sandwiches and wraps for a quick meal.',
            ],
            [
                'name' => 'Soups',
                'description' => 'Warm and comforting soups made with fresh ingredients.',
            ],
            [
                'name' => 'Specials',
                'description' => 'Chef\'s special dishes and seasonal menu items.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

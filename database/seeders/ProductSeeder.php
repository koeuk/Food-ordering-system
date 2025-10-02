<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = Category::all();
        
        $products = [
            // Appetizers
            [
                'name' => 'Buffalo Wings',
                'description' => 'Crispy chicken wings tossed in our signature buffalo sauce, served with celery and blue cheese dip.',
                'price' => 12.99,
                'category_id' => $categories->where('name', 'Appetizers')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'Mozzarella Sticks',
                'description' => 'Golden fried mozzarella cheese sticks served with marinara sauce.',
                'price' => 8.99,
                'category_id' => $categories->where('name', 'Appetizers')->first()->id,
                'is_available' => true,
            ],
            
            // Main Course
            [
                'name' => 'Grilled Salmon',
                'description' => 'Fresh Atlantic salmon grilled to perfection, served with seasonal vegetables and rice.',
                'price' => 24.99,
                'category_id' => $categories->where('name', 'Main Course')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'Beef Tenderloin',
                'description' => 'Premium beef tenderloin cooked to your preference, served with mashed potatoes and asparagus.',
                'price' => 32.99,
                'category_id' => $categories->where('name', 'Main Course')->first()->id,
                'is_available' => true,
            ],
            
            // Pizza
            [
                'name' => 'Margherita Pizza',
                'description' => 'Classic Italian pizza with fresh mozzarella, tomato sauce, and basil.',
                'price' => 16.99,
                'category_id' => $categories->where('name', 'Pizza')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'Pepperoni Pizza',
                'description' => 'Traditional pizza topped with spicy pepperoni and mozzarella cheese.',
                'price' => 18.99,
                'category_id' => $categories->where('name', 'Pizza')->first()->id,
                'is_available' => true,
            ],
            
            // Desserts
            [
                'name' => 'Chocolate Lava Cake',
                'description' => 'Warm chocolate cake with a molten chocolate center, served with vanilla ice cream.',
                'price' => 9.99,
                'category_id' => $categories->where('name', 'Desserts')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'Tiramisu',
                'description' => 'Classic Italian dessert with layers of coffee-soaked ladyfingers and mascarpone.',
                'price' => 8.99,
                'category_id' => $categories->where('name', 'Desserts')->first()->id,
                'is_available' => true,
            ],
            
            // Beverages
            [
                'name' => 'Fresh Orange Juice',
                'description' => 'Freshly squeezed orange juice served chilled.',
                'price' => 4.99,
                'category_id' => $categories->where('name', 'Beverages')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'Craft Beer',
                'description' => 'Selection of local craft beers on tap.',
                'price' => 6.99,
                'category_id' => $categories->where('name', 'Beverages')->first()->id,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

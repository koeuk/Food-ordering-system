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
            // ម្ហូបជាតិ
            [
                'name' => 'អាមុក',
                'description' => 'ម្ហូបខ្មែរបុរាណឆ្ងាញ់ ចម្អិនដោយគ្រឿងផ្សំធម្មជាតិ និងប្រកបដោយរសជាតិពិសេស',
                'price' => 8.50,
                'image' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបជាតិ')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'បាយឆា',
                'description' => 'បាយឆាឆ្ងាញ់ បានចម្អិនដោយគ្រឿងផ្សំពិសេស និងបន្លែស្រស់ៗ',
                'price' => 6.50,
                'image' => 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបជាតិ')->first()->id,
                'is_available' => true,
            ],
            
            // ម្ហូបសមុទ្រ
            [
                'name' => 'ត្រីអាំង',
                'description' => 'ត្រីស្រស់អាំងឆ្ងាញ់ បានមកពីសមុទ្រកម្ពុជា ចម្អិនដោយបច្ចេកទេសពិសេស',
                'price' => 12.00,
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបសមុទ្រ')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'ត្រីចម្អិន',
                'description' => 'ត្រីចម្អិនឆ្ងាញ់ បានចម្អិនដោយគ្រឿងផ្សំខ្មែរបុរាណ',
                'price' => 10.50,
                'image' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបសមុទ្រ')->first()->id,
                'is_available' => true,
            ],
            
            // ម្ហូបគោ
            [
                'name' => 'សាច់គោឆា',
                'description' => 'សាច់គោឆាឆ្ងាញ់ បានចម្អិនដោយបន្លែស្រស់ៗ និងគ្រឿងផ្សំពិសេស',
                'price' => 15.00,
                'image' => 'https://images.unsplash.com/photo-1558030006-450675393462?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបគោ')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'សាច់គោអាំង',
                'description' => 'សាច់គោអាំងឆ្ងាញ់ បានចម្អិនដោយបច្ចេកទេសពិសេស',
                'price' => 18.50,
                'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបគោ')->first()->id,
                'is_available' => true,
            ],
            
            // ម្ហូបជ្រូក
            [
                'name' => 'សាច់ជ្រូកឆា',
                'description' => 'សាច់ជ្រូកឆាឆ្ងាញ់ បានចម្អិនដោយបន្លែស្រស់ៗ និងគ្រឿងផ្សំពិសេស',
                'price' => 12.50,
                'image' => 'https://images.unsplash.com/photo-1565299507177-b0ac667e28a2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបជ្រូក')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'សាច់ជ្រូកអាំង',
                'description' => 'សាច់ជ្រូកអាំងឆ្ងាញ់ បានចម្អិនដោយបច្ចេកទេសពិសេស',
                'price' => 14.00,
                'image' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបជ្រូក')->first()->id,
                'is_available' => true,
            ],
            
            // ម្ហូបបាយ
            [
                'name' => 'បាយការ៉េ',
                'description' => 'បាយការ៉េឆ្ងាញ់ បានចម្អិនដោយគ្រឿងផ្សំពិសេស និងបន្លែស្រស់ៗ',
                'price' => 7.50,
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបបាយ')->first()->id,
                'is_available' => true,
            ],
            [
                'name' => 'បាយឆាការ៉េ',
                'description' => 'បាយឆាការ៉េឆ្ងាញ់ បានចម្អិនដោយបច្ចេកទេសពិសេស',
                'price' => 8.00,
                'image' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                'category_id' => $categories->where('name', 'ម្ហូបបាយ')->first()->id,
                'is_available' => true,
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}

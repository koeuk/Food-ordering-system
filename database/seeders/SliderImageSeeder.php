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
                'title' => 'សូមស្វាគមន៍មកកាន់ប្រព័ន្ធបញ្ជាទិញម្ហូបខ្មែរ',
                'description' => 'បញ្ជាទិញម្ហូបខ្មែរឆ្ងាញ់ៗតាមអ៊ីនធឺណិតដោយងាយស្រួល',
                'image_url' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'មើលម៉ឺនុយ',
                'button_url' => '/web/products',
                'order' => 0,
                'is_active' => true
            ],
            [
                'title' => 'ម្ហូបស្រស់ៗ និងឆ្ងាញ់',
                'description' => 'ជួបប្រទះជាមួយម្ហូបដ៏ឆ្ងាញ់បំផុត ដែលចម្អិនដោយអ្នកចម្អិនជំនាញ',
                'image_url' => 'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'បញ្ជាទិញឥឡូវ',
                'button_url' => '/web/products',
                'order' => 1,
                'is_active' => true
            ],
            [
                'title' => 'សេវាកម្មដឹកជញ្ជូនលឿន',
                'description' => 'ទទួលបានម្ហូបដែលអ្នកចូលចិត្តដែលដឹកជញ្ជូនទៅផ្ទះរបស់អ្នកក្នុងរយៈពេលយ៉ាងខ្លី',
                'image_url' => 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'ចាប់ផ្តើមបញ្ជាទិញ',
                'button_url' => '/web/products',
                'order' => 2,
                'is_active' => true
            ],
            [
                'title' => 'ម្ហូបគុណភាពខ្ពស់',
                'description' => 'យើងប្រើតែគ្រឿងផ្សំស្រស់ៗ ដើម្បីរៀបចំម្ហូបរបស់អ្នក',
                'image_url' => 'https://images.unsplash.com/photo-1571091718767-18b5b1457add?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'ស្វែងរកម៉ឺនុយ',
                'button_url' => '/web/products',
                'order' => 3,
                'is_active' => true
            ],
            [
                'title' => 'ម្ហូបខ្មែរបុរាណ',
                'description' => 'រសជាតិខ្មែរបុរាណដ៏ឆ្ងាញ់ ចម្អិនដោយគ្រឿងផ្សំធម្មជាតិ',
                'image_url' => 'https://images.unsplash.com/photo-1551218808-94e220e084d2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80',
                'button_text' => 'ស្វែងរកម្ហូបខ្មែរ',
                'button_url' => '/web/products',
                'order' => 4,
                'is_active' => true
            ]
        ];

        foreach ($sliderImages as $image) {
            SliderImage::create($image);
        }
    }
}

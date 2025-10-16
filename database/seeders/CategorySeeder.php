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
                'name' => 'ម្ហូបជាតិ',
                'description' => 'ម្ហូបខ្មែរបុរាណដែលឆ្ងាញ់ពិសារ បង្កើតឡើងដោយគ្រឿងផ្សំធម្មជាតិ',
            ],
            [
                'name' => 'ម្ហូបសមុទ្រ',
                'description' => 'ម្ហូបត្រី និងសត្វសមុទ្រស្រស់ៗ បានមកពីសមុទ្រកម្ពុជា',
            ],
            [
                'name' => 'ម្ហូបគោ',
                'description' => 'ម្ហូបគោឆ្ងាញ់ៗ ដែលចម្អិនដោយបច្ចេកទេសពិសេស',
            ],
            [
                'name' => 'ម្ហូបជ្រូក',
                'description' => 'ម្ហូបជ្រូកឆ្ងាញ់ និងពេញចិត្ត បានចម្អិនដោយគ្រឿងផ្សំពិសេស',
            ],
            [
                'name' => 'ម្ហូបបាយ',
                'description' => 'បាយឆ្ងាញ់ៗ និងម្ហូបបាយពិសេស ដែលធ្វើឡើងដោយគ្រឿងផ្សំធម្មជាតិ',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

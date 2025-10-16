<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin account
        User::create([
            'name' => 'អ្នកគ្រប់គ្រង',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'admin',
            'phone' => '+855-12-345-678',
            'address' => 'ផ្ទះលេខ ១២៣, ផ្លូវមហាវិថីព្រះមុនីវង្ស, រាជធានីភ្នំពេញ',
        ]);

        // Customer accounts
        User::create([
            'name' => 'សុខា មេត្តា',
            'email' => 'sokha@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-679',
            'address' => 'ផ្ទះលេខ ៤៥៦, ផ្លូវជាតិលេខ ១, ខណ្ឌចំការមន, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'វិចិត្រ សុខុម',
            'email' => 'vicheat@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-680',
            'address' => 'ផ្ទះលេខ ៧៨៩, ផ្លូវជាតិលេខ ២, ខណ្ឌដូនពេញ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'រតនា សុខា',
            'email' => 'ratana@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-681',
            'address' => 'ផ្ទះលេខ ១២៣, ផ្លូវជាតិលេខ ៣, ខណ្ឌពោធិ៍សែនជ័យ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'ចាន់ថា សុខុម',
            'email' => 'chantha@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-682',
            'address' => 'ផ្ទះលេខ ២៣៤, ផ្លូវជាតិលេខ ៤, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'សុខុម រតនា',
            'email' => 'sokhom@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-683',
            'address' => 'ផ្ទះលេខ ៣៤៥, ផ្លូវជាតិលេខ ៥, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'មេត្តា វិចិត្រ',
            'email' => 'metta@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-684',
            'address' => 'ផ្ទះលេខ ៤៥៦, ផ្លូវជាតិលេខ ៦, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'សុខា ចាន់ថា',
            'email' => 'sokha2@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-685',
            'address' => 'ផ្ទះលេខ ៥៦៧, ផ្លូវជាតិលេខ ៧, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'រតនា មេត្តា',
            'email' => 'ratana2@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-686',
            'address' => 'ផ្ទះលេខ ៦៧៨, ផ្លូវជាតិលេខ ៨, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);

        User::create([
            'name' => 'វិចិត្រ ចាន់ថា',
            'email' => 'vicheat2@gmail.com',
            'password' => Hash::make('12345678'),
            'role' => 'customer',
            'phone' => '+855-12-345-687',
            'address' => 'ផ្ទះលេខ ៧៨៩, ផ្លូវជាតិលេខ ៩, ខណ្ឌព្រែកព្នៅ, រាជធានីភ្នំពេញ',
        ]);
    }
}

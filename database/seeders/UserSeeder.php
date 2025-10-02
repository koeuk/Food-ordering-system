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
        // Demo accounts
        User::create([
            'name' => 'John Customer',
            'email' => 'customer@test.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0101',
            'address' => '123 Main Street, New York, NY 10001',
        ]);

        User::create([
            'name' => 'Manager Smith',
            'email' => 'manager@test.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'phone' => '+1-555-0102',
            'address' => '456 Manager Ave, New York, NY 10002',
        ]);

        User::create([
            'name' => 'Chef Johnson',
            'email' => 'kitchen@test.com',
            'password' => Hash::make('password'),
            'role' => 'kitchen',
            'phone' => '+1-555-0103',
            'address' => '789 Kitchen St, New York, NY 10003',
        ]);

        // Additional customers
        User::create([
            'name' => 'Alice Johnson',
            'email' => 'alice@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0104',
            'address' => '321 Oak Street, Los Angeles, CA 90210',
        ]);

        User::create([
            'name' => 'Bob Wilson',
            'email' => 'bob@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0105',
            'address' => '654 Pine Avenue, Chicago, IL 60601',
        ]);

        User::create([
            'name' => 'Carol Davis',
            'email' => 'carol@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0106',
            'address' => '987 Elm Drive, Houston, TX 77001',
        ]);

        User::create([
            'name' => 'David Brown',
            'email' => 'david@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0107',
            'address' => '147 Maple Lane, Phoenix, AZ 85001',
        ]);

        User::create([
            'name' => 'Emma Garcia',
            'email' => 'emma@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0108',
            'address' => '258 Cedar Road, Philadelphia, PA 19101',
        ]);

        User::create([
            'name' => 'Frank Miller',
            'email' => 'frank@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0109',
            'address' => '369 Birch Street, San Antonio, TX 78201',
        ]);

        User::create([
            'name' => 'Grace Lee',
            'email' => 'grace@example.com',
            'password' => Hash::make('password'),
            'role' => 'customer',
            'phone' => '+1-555-0110',
            'address' => '741 Spruce Avenue, San Diego, CA 92101',
        ]);
    }
}

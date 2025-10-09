<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Full system access and management capabilities',
                'permissions' => [
                    'users.view',
                    'users.create',
                    'users.edit',
                    'users.delete',
                    'products.view',
                    'products.create',
                    'products.edit',
                    'products.delete',
                    'categories.view',
                    'categories.create',
                    'categories.edit',
                    'categories.delete',
                    'orders.view',
                    'orders.create',
                    'orders.edit',
                    'orders.delete',
                    'orders.manage',
                    'inventory.view',
                    'inventory.edit',
                    'inventory.manage',
                    'suppliers.view',
                    'suppliers.create',
                    'suppliers.edit',
                    'suppliers.delete',
                    'roles.view',
                    'roles.create',
                    'roles.edit',
                    'roles.delete',
                    'reports.view',
                    'reports.sales',
                    'reports.inventory',
                ],
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'user',
                'display_name' => 'User',
                'description' => 'Can order food and manage their own orders',
                'permissions' => [
                    'orders.view',
                    'orders.create',
                    'products.view',
                ],
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 2,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }
    }
}
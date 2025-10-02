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
                'name' => 'customer',
                'display_name' => 'Customer',
                'description' => 'Can order food and manage their own orders',
                'permissions' => [
                    'dashboard.customer',
                    'orders.view',
                    'orders.create',
                    'products.view',
                ],
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'manager',
                'display_name' => 'Manager',
                'description' => 'Full system access and management capabilities',
                'permissions' => [
                    'dashboard.manager',
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
                'sort_order' => 2,
            ],
            [
                'name' => 'kitchen',
                'display_name' => 'Kitchen Staff',
                'description' => 'Handle food preparation and order status updates',
                'permissions' => [
                    'dashboard.kitchen',
                    'orders.view',
                    'orders.edit',
                    'products.view',
                ],
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'supplier',
                'display_name' => 'Supplier',
                'description' => 'Manage inventory orders and deliveries',
                'permissions' => [
                    'dashboard.supplier',
                    'orders.view',
                    'products.view',
                ],
                'is_active' => true,
                'is_system' => true,
                'sort_order' => 4,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::create($roleData);
        }

        // Create some custom roles
        $customRoles = [
            [
                'name' => 'cashier',
                'display_name' => 'Cashier',
                'description' => 'Handle payments and customer service',
                'permissions' => [
                    'dashboard.customer',
                    'orders.view',
                    'orders.edit',
                    'products.view',
                    'reports.view',
                ],
                'is_active' => true,
                'is_system' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'delivery_driver',
                'display_name' => 'Delivery Driver',
                'description' => 'Handle order deliveries and status updates',
                'permissions' => [
                    'orders.view',
                    'orders.edit',
                ],
                'is_active' => true,
                'is_system' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($customRoles as $roleData) {
            Role::create($roleData);
        }
    }
}
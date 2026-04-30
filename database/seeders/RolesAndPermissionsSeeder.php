<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Inventory
            'inventory.view',
            'inventory.create',
            'inventory.edit',
            'inventory.delete',

            // Customers
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',

            // Bookings
            'bookings.view',
            'bookings.create',
            'bookings.edit',
            'bookings.cancel',
            'bookings.confirm',
            'bookings.manage_status',

            // Payments
            'payments.view',
            'payments.create',

            // Employees
            'employees.view',
            'employees.create',
            'employees.edit',
            'employees.delete',
            'employees.manage_roles',

            // Expenses
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',

            // Reports
            'reports.view',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        }

        // Superadmin — all permissions, set via gate bypass (see config/permission.php)
        $superadmin = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);

        // Manager — most operational permissions
        $manager = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $manager->syncPermissions([
            'inventory.view', 'inventory.create', 'inventory.edit',
            'customers.view', 'customers.create', 'customers.edit',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.cancel', 'bookings.confirm', 'bookings.manage_status',
            'payments.view', 'payments.create',
            'employees.view',
            'expenses.view', 'expenses.create', 'expenses.edit', 'expenses.delete',
            'reports.view',
        ]);

        // Staff — front desk operations
        $staff = Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $staff->syncPermissions([
            'inventory.view',
            'customers.view', 'customers.create', 'customers.edit',
            'bookings.view', 'bookings.create', 'bookings.edit', 'bookings.cancel', 'bookings.confirm',
            'payments.view', 'payments.create',
            'expenses.view', 'expenses.create',
        ]);

        // Viewer — read only
        $viewer = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);
        $viewer->syncPermissions([
            'inventory.view',
            'customers.view',
            'bookings.view',
            'payments.view',
            'expenses.view',
            'reports.view',
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Role;
use App\Models\Module;
use App\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin Role (cannot be deleted or deactivated)
        $superAdminRole = Role::firstOrCreate(
            ['slug' => 'superadmin'],
            [
                'id' => Str::uuid(),
                'name' => 'Super Admin',
                'description' => 'Has full system access',
                'is_active' => true,
                'is_system' => true,
            ]
        );

        // 2. Default User Role
        $userRole = Role::firstOrCreate(
            ['slug' => 'user'],
            [
                'id' => Str::uuid(),
                'name' => 'User',
                'description' => 'Default role for all new users',
                'is_active' => true,
                'is_system' => false,
            ]
        );

        // 3. Payroll Module
        $payrollModule = Module::firstOrCreate(
            ['slug' => 'payroll'],
            [
                'id' => Str::uuid(),
                'name' => 'Payroll',
                'description' => 'Payroll management module',
                'is_active' => true,
            ]
        );

        // 4. Payroll Permissions
        $permissions = [
            ['name' => 'Create Payroll', 'slug' => 'create-payroll'],
            ['name' => 'Edit Payroll', 'slug' => 'edit-payroll'],
            ['name' => 'View Payroll', 'slug' => 'view-payroll'],
            ['name' => 'Deactivate Payroll', 'slug' => 'deactivate-payroll'],
        ];

        foreach ($permissions as $perm) {
            $permission = Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                [
                    'id' => Str::uuid(),
                    'module_id' => $payrollModule->id,
                    'name' => $perm['name'],
                    'is_active' => true,
                ]
            );

            // Attach all payroll permissions to Super Admin
            $superAdminRole->permissions()->syncWithoutDetaching([(string) $permission->id]);
        }

        // 5. Default Super Admin User
        $superAdminUser = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'id' => Str::uuid(),
                'full_name' => 'System Administrator',
                'password' => Hash::make('password123'), // Change in production
                'status' => true,
            ]
        );

        // Attach Super Admin role if not already
        if (!$superAdminUser->roles()->where('roles.id', $superAdminRole->id)->exists()) {
            $superAdminUser->roles()->attach($superAdminRole->id);
        }
    }
}

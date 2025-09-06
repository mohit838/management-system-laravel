<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Create permissions
        $manageUsers = Permission::firstOrCreate(['name' => 'manage users']);
        $viewReports = Permission::firstOrCreate(['name' => 'view reports']);

        // Create roles and assign permissions
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->givePermissionTo([$manageUsers, $viewReports]);

        $user = Role::firstOrCreate(['name' => 'user']);
        $user->givePermissionTo([$viewReports]);
    }
}

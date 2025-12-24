<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionWSeeder_2025_12_23000 extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions using "model:action" pattern
        $permissions = [
            // User permissions
            'user:view',
            'user:create',
            'user:update',
            'user:delete',

            // Script permissions
            'script:view',
            'script:create',
            'script:update',
            'script:delete',
            'script:execute',

            // Execution log permissions
            'execution_log:view',
            'execution_log:delete',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Admin role and assign all permissions
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Create Common role and assign limited permissions
        $commonRole = Role::firstOrCreate(['name' => 'Common']);
        $commonRole->givePermissionTo([
            'script:view',
            'script:execute',
            'execution_log:view',
        ]);
    }
}

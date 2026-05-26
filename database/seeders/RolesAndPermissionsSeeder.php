<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'manage_users',
            'view_assets',
            'create_assets',
            'edit_assets',
            'delete_assets',
            'assign_assets',
            'return_assets',
            'view_reports',
            'export_reports',
            'manage_maintenance',
            'view_audit_log',
            'manage_backups',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        $itStaff = Role::firstOrCreate([
            'name' => 'IT Staff',
            'guard_name' => 'web',
        ]);

        $viewer = Role::firstOrCreate([
            'name' => 'Viewer',
            'guard_name' => 'web',
        ]);

        $hr = Role::firstOrCreate([
            'name' => 'HR',
            'guard_name' => 'web',
        ]);

        $admin->syncPermissions($permissions);

        $itStaff->syncPermissions([
            'view_assets',
            'create_assets',
            'edit_assets',
            'assign_assets',
            'return_assets',
            'view_reports',
            'export_reports',
            'manage_maintenance',
        ]);

        $viewer->syncPermissions([
            'view_assets',
            'view_reports',
        ]);

        $hr->syncPermissions([
            'view_assets',
            'view_reports',
            'export_reports',
        ]);

        $firstUser = User::query()->orderBy('id')->first();

        if ($firstUser && ! $firstUser->hasRole('Admin')) {
            $firstUser->assignRole('Admin');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
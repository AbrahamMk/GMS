<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RbacSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'access portal',
            'view dashboard',
            'view members',
            'manage members',
            'view attendance',
            'manage attendance',
            'view memberships',
            'manage memberships',
            'manage membership plans',
            'view bookings',
            'manage bookings',
            'view inventory',
            'manage inventory',
            'view reports',
            'manage billing',
            'manage settings',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $roles = [
            'super-admin' => $permissions,
            'branch-manager' => [
                'access portal',
                'view dashboard',
                'view members',
                'manage members',
                'view attendance',
                'manage attendance',
                'view memberships',
                'manage memberships',
                'manage membership plans',
                'view bookings',
                'manage bookings',
                'view inventory',
                'manage inventory',
                'view reports',
                'manage billing',
            ],
            'receptionist' => [
                'access portal',
                'view dashboard',
                'view members',
                'view attendance',
                'manage attendance',
                'view memberships',
                'view bookings',
                'manage bookings',
            ],
            'trainer' => [
                'access portal',
                'view dashboard',
                'view members',
                'view attendance',
                'view bookings',
                'manage bookings',
            ],
            'accountant' => [
                'access portal',
                'view dashboard',
                'view memberships',
                'manage billing',
                'view reports',
            ],
            'inventory-manager' => [
                'access portal',
                'view dashboard',
                'view inventory',
                'manage inventory',
                'view reports',
            ],
            'member' => [
                'access portal',
                'view dashboard',
                'view bookings',
                'manage bookings',
                'view memberships',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view bookings',
            'create bookings',
            'edit bookings',
            'delete bookings',
            'view instructors',
            'create instructors',
            'edit instructors',
            'delete instructors',
            'view services',
            'create services',
            'edit services',
            'delete services',
            'view suburbs',
            'create suburbs',
            'edit suburbs',
            'delete suburbs',
            'view payments',
            'view reports',
            'manage settings',
            'view clients',
            'edit clients',
            'delete clients',
            'manage marketing',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions
        $role = Role::create(['name' => 'admin']);
        $role->givePermissionTo(Permission::all());

        $role = Role::create(['name' => 'instructor']);
        $role->givePermissionTo([
            'view bookings',
            'view services',
            'view suburbs',
        ]);

        $role = Role::create(['name' => 'client']);
        $role->givePermissionTo([
            'view bookings',
            'create bookings',
            'view services',
            'view instructors',
            'view suburbs',
        ]);
    }
}

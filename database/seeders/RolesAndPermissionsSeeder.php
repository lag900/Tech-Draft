<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $allPermissions = [
            'categories.view', 'categories.create', 'categories.edit', 'categories.delete',
            'item_types.view', 'item_types.create', 'item_types.edit', 'item_types.delete',
            'fit_types.view', 'fit_types.create', 'fit_types.edit', 'fit_types.delete',
            'fabrics.view', 'fabrics.create', 'fabrics.edit', 'fabrics.delete',
            'measurements.view', 'measurements.create', 'measurements.export',
            'clients.view', 'clients.create', 'clients.edit', 'clients.delete',
            'orders.view', 'orders.create', 'orders.edit', 'orders.delete', 'orders.approve', 'orders.export',
            'designs.view', 'designs.create', 'designs.edit', 'designs.delete', 'designs.approve', 'designs.export',
            'products.view', 'products.create', 'products.edit', 'products.delete',
            'team.view', 'team.create', 'team.edit', 'team.delete',
            'roles.view', 'roles.edit', 'roles.delete',
            'settings.view', 'settings.edit',
        ];

        $clientPermissions = [
            'categories.view',
            'item_types.view',
            'fit_types.view',
            'fabrics.view',
            'measurements.view',
            'orders.view',
            'orders.create',
            'orders.edit',
            'designs.view',
            'designs.create',
            'designs.edit',
            'products.view',
        ];

        Role::updateOrCreate(['slug' => 'admin'], [
            'name' => 'Administrator',
            'permissions' => $allPermissions,
            'color' => '#ef4444',
        ]);

        Role::updateOrCreate(['slug' => 'client'], [
            'name' => 'Client',
            'permissions' => $clientPermissions,
            'color' => '#0ea5e9',
        ]);
        
        Role::updateOrCreate(['slug' => 'superadmin'], [
            'name' => 'Super Admin',
            'permissions' => $allPermissions,
            'color' => '#7c3aed',
        ]);
        
        echo "Roles and permissions seeded successfully.\n";
    }
}

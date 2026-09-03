<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use App\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $groups = [
            'employees' => [
                'employees.view', 'employees.create', 'employees.edit', 'employees.delete',
            ],
            'departments' => [
                'departments.view', 'departments.create', 'departments.edit', 'departments.delete',
            ],
            'leaves' => [
                'leaves.view', 'leaves.apply', 'leaves.approve', 'leaves.reject',
            ],
            'attendance' => [
                'attendance.view', 'attendance.edit',
            ],
            'bills' => [
                'bills.view', 'bills.apply', 'bills.approve', 'bills.reject', 'bills.modify',
            ],
            'movement_passes' => [
                'movement-passes.view', 'movement-passes.apply', 'movement-passes.approve',
            ],
            'increments' => [
                'increments.view', 'increments.create', 'increments.approve',
            ],
            'promotions' => [
                'promotions.view', 'promotions.create', 'promotions.approve',
            ],
            'reports' => [
                'reports.view', 'reports.export',
            ],
            'users' => [
                'users.view', 'users.create', 'users.edit', 'users.delete',
            ],
            'roles' => [
                'roles.view', 'roles.create', 'roles.edit', 'roles.delete',
            ],
            'settings' => [
                'settings.view', 'settings.edit',
            ],
        ];

        foreach ($groups as $group => $perms) {
            foreach ($perms as $perm) {
                Permission::firstOrCreate(
                    ['name' => $perm, 'guard_name' => 'web'],
                    ['group_name' => $group]
                );
            }
        }

        // System roles (tenant_id = 0, shared globally)
        $admin = Role::firstOrCreate(
            ['name' => 'admin', 'guard_name' => 'web'],
            ['tenant_id' => 0]
        );
        $admin->syncPermissions(Permission::all());

        $hrManager = Role::firstOrCreate(
            ['name' => 'hr-manager', 'guard_name' => 'web'],
            ['tenant_id' => 0]
        );
        $hrManager->syncPermissions([
            'employees.view', 'employees.create', 'employees.edit',
            'departments.view',
            'leaves.view', 'leaves.approve', 'leaves.reject',
            'attendance.view', 'attendance.edit',
            'bills.view', 'bills.approve', 'bills.reject', 'bills.modify',
            'movement-passes.view', 'movement-passes.approve',
            'increments.view', 'increments.create',
            'promotions.view', 'promotions.create',
            'reports.view', 'reports.export',
        ]);

        $employee = Role::firstOrCreate(
            ['name' => 'employee', 'guard_name' => 'web'],
            ['tenant_id' => 0]
        );
        $employee->syncPermissions([
            'employees.view',
            'leaves.view', 'leaves.apply',
            'bills.view', 'bills.apply',
            'movement-passes.view', 'movement-passes.apply',
            'reports.view',
        ]);
    }
}

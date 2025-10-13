<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionsAndRolesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define all permissions grouped by feature
        $permissions = [
            // Documents
            'documents' => [
                'documents.send',
                'documents.view_routed',
                'documents.forward',
                'documents.download',
            ],
            // Storage
            'storage' => [
                'storage.upload',
                'storage.view_department',
                'storage.download',
                'storage.delete',
            ],
            // Incidents
            'incidents' => [
                'incidents.create',
                'incidents.view_department',
                'incidents.view_own',
                'incidents.resolve',
            ],
            // Messages/Announcements
            'messages' => [
                'messages.send_department',
                'messages.send_all_departments',
                'messages.send_individual',
                'messages.view_department',
                'messages.view_own',
            ],
            // Visitors
            'visitors' => [
                'visitors.checkin',
                'visitors.view_department',
            ],
            // Schedules
            'schedules' => [
                'schedules.manage_own',
                'schedules.manage_department',
                'schedules.view_department',
            ],
            // Roles Management
            'roles' => [
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
                'roles.assign',
            ],
            // Users Management
            'users' => [
                'users.create',
                'users.view_department',
                'users.update',
                'users.deactivate',
                'users.delete',
            ],
            // Analytics
            'analytics' => [
                'analytics.view_department',
                'analytics.view_own',
            ],
            // Audit Logs
            'audit_logs' => [
                'audit_logs.view_department',
                'audit_logs.view_own',
            ],
            // Export
            'export' => [
                'export.department_data',
            ],
            // System
            'system' => [
                'system.admin',
            ],
        ];

        // Create all permissions
        $flatPermissions = [];
        foreach ($permissions as $group => $perms) {
            foreach ($perms as $permission) {
                $flatPermissions[$permission] = Permission::firstOrCreate(
                    ['name' => $permission],
                    ['guard_name' => 'web']
                );
            }
        }

        // Define roles and their permissions
        $roles = [
            'super_admin' => array_keys($flatPermissions), // All permissions
            'admin' => array_filter(array_keys($flatPermissions), function ($perm) {
                return $perm !== 'system.admin'; // All except system.admin
            }),
            'dept_head' => [
                'documents.send',
                'documents.view_routed',
                'documents.forward',
                'documents.download',
                'storage.upload',
                'storage.view_department',
                'storage.download',
                'storage.delete',
                'incidents.create',
                'incidents.view_department',
                'incidents.resolve',
                'messages.send_department',
                'messages.send_all_departments',
                'messages.send_individual',
                'messages.view_department',
                'visitors.checkin',
                'visitors.view_department',
                'schedules.manage_own',
                'schedules.manage_department',
                'schedules.view_department',
                'roles.view',
                'roles.update',
                'roles.assign',
                'users.view_department',
                'users.update',
                'users.deactivate',
                'analytics.view_department',
                'audit_logs.view_department',
                'export.department_data',
            ],
            'deputy_dept_head' => [
                'documents.send',
                'documents.view_routed',
                'documents.forward',
                'documents.download',
                'storage.upload',
                'storage.view_department',
                'storage.download',
                'storage.delete',
                'incidents.create',
                'incidents.view_department',
                'incidents.resolve',
                'messages.send_department',
                'messages.send_all_departments',
                'messages.send_individual',
                'messages.view_department',
                'visitors.checkin',
                'visitors.view_department',
                'schedules.manage_own',
                'schedules.manage_department',
                'schedules.view_department',
                'roles.view',
                'roles.update',
                'roles.assign',
                'users.view_department',
                'users.update',
                'users.deactivate',
                'analytics.view_department',
                'audit_logs.view_department',
                'export.department_data',
            ],
            'secretary' => [
                'documents.send',
                'documents.view_routed',
                'documents.forward',
                'documents.download',
                'storage.upload',
                'storage.view_department',
                'storage.download',
                'incidents.create',
                'incidents.view_own',
                'messages.send_department',
                'messages.send_individual',
                'messages.view_own',
                'visitors.checkin',
                'visitors.view_department',
                'schedules.manage_own',
                'schedules.manage_department',
                'schedules.view_department',
                'analytics.view_own',
                'audit_logs.view_own',
                'export.department_data',
            ],
            'staff' => [
                'documents.send',
                'documents.view_routed',
                'documents.forward',
                'documents.download',
                'storage.upload',
                'storage.view_department',
                'storage.download',
                'incidents.create',
                'incidents.view_own',
                'messages.send_department',
                'messages.send_individual',
                'messages.view_own',
                'visitors.checkin',
                'schedules.manage_own',
                'analytics.view_own',
                'audit_logs.view_own',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $permissions) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Sync permissions for this role
            $permissionObjects = array_map(
                fn ($perm) => $flatPermissions[$perm],
                $permissions
            );

            $role->syncPermissions($permissionObjects);
        }

        $this->command->info('✅ Permissions and roles seeded successfully!');
    }
}

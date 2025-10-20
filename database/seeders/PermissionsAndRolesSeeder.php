<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class PermissionsAndRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Wrap everything in a transaction for data integrity
        DB::transaction(function () {
            $this->createPermissions();
            $this->createRolesWithPermissions();
        });

        $this->command->info('✅ Permissions and roles seeded successfully with inheritance!');
    }

    /**
     * Create all permissions from config file.
     */
    protected function createPermissions(): void
    {
        $permissions = config('permissions.permissions');

        $this->command->info('Creating permissions...');

        foreach ($permissions as $permissionData) {
            Permission::firstOrCreate(
                ['name' => $permissionData['name']],
                [
                    'guard_name' => 'web',
                    // Store metadata in permission object (optional, for reference)
                ]
            );
        }

        $this->command->info('✅ Created ' . count($permissions) . ' permissions');
    }

    /**
     * Create roles and assign permissions with inheritance support.
     */
    protected function createRolesWithPermissions(): void
    {
        $rolesConfig = config('role-permissions.roles');
        $roleMetadata = config('role-permissions.role_metadata');
        $allPermissions = config('permissions.permissions');

        $this->command->info('Creating roles with inherited permissions...');

        // Cache to store resolved permissions for each role
        $resolvedPermissions = [];

        foreach ($rolesConfig as $roleName => $roleConfig) {
            // Create the role
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                ['guard_name' => 'web']
            );

            // Get all permissions for this role (with inheritance)
            $permissions = $this->resolveRolePermissions(
                $roleName,
                $rolesConfig,
                $allPermissions,
                $resolvedPermissions
            );

            // Sync permissions to role
            $role->syncPermissions($permissions);

            $displayName = $roleMetadata[$roleName]['display_name'] ?? $roleName;
            $permissionCount = count($permissions);

            $this->command->info("✅ {$displayName}: {$permissionCount} permissions assigned");
        }
    }

    /**
     * Resolve all permissions for a role, including inherited ones.
     *
     * @param string $roleName
     * @param array $rolesConfig
     * @param array $allPermissions
     * @param array &$cache
     * @return array
     */
    protected function resolveRolePermissions(
        string $roleName,
        array $rolesConfig,
        array $allPermissions,
        array &$cache
    ): array {
        // Return cached result if already resolved
        if (isset($cache[$roleName])) {
            return $cache[$roleName];
        }

        $roleConfig = $rolesConfig[$roleName];
        $permissions = [];

        // Handle special case: super_admin gets ALL permissions
        if (isset($roleConfig['permissions']) && $roleConfig['permissions'] === 'all') {
            $permissions = collect($allPermissions)->pluck('name')->toArray();
            $cache[$roleName] = $permissions;
            return $permissions;
        }

        // If role inherits from another role, get parent permissions first
        if (!empty($roleConfig['inherits'])) {
            $parentRole = $roleConfig['inherits'];

            // Recursively resolve parent permissions
            $inheritedPermissions = $this->resolveRolePermissions(
                $parentRole,
                $rolesConfig,
                $allPermissions,
                $cache
            );

            $permissions = array_merge($permissions, $inheritedPermissions);
        }

        // Add this role's own permissions
        if (isset($roleConfig['permissions']) && is_array($roleConfig['permissions'])) {
            $permissions = array_merge($permissions, $roleConfig['permissions']);
        }

        // Remove duplicates and cache result
        $permissions = array_unique($permissions);
        $cache[$roleName] = $permissions;

        return $permissions;
    }

    /**
     * Optional: Validate that all permission names in role config exist in permissions config.
     * Uncomment to enable validation.
     */
    protected function validatePermissions(): void
    {
        $allPermissionNames = collect(config('permissions.permissions'))
            ->pluck('name')
            ->toArray();

        $rolesConfig = config('role-permissions.roles');

        foreach ($rolesConfig as $roleName => $roleConfig) {
            if (!isset($roleConfig['permissions']) || $roleConfig['permissions'] === 'all') {
                continue;
            }

            foreach ($roleConfig['permissions'] as $permissionName) {
                if (!in_array($permissionName, $allPermissionNames)) {
                    $this->command->error("⚠️  Permission '{$permissionName}' in role '{$roleName}' does not exist in permissions config!");
                }
            }
        }
    }
}

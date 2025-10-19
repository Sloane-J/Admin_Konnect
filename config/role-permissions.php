<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Role Permission Mappings
    |--------------------------------------------------------------------------
    |
    | Define which permissions each role should have.
    | Roles can inherit permissions from other roles using the 'inherits' key.
    |
    | Inheritance chain: staff -> secretary -> deputy_dept_head -> dept_head
    |
    */

    'roles' => [

        /*
        |--------------------------------------------------------------------------
        | Staff (Base Role)
        |--------------------------------------------------------------------------
        | Basic operational permissions for general staff members.
        | All other department roles inherit from this base.
        |
        */
        'staff' => [
            'inherits' => null, // Base role, no inheritance
            'permissions' => [
                // Documents
                'documents.route',
                'documents.view-routed',
                'documents.forward',
                'documents.download',

                // Storage
                'storage.upload',
                'storage.view-department',
                'storage.download',

                // Incidents
                'incidents.create',

                // Messages
                'messages.send-department',
                'messages.send-individual',

                // Visitors
                'visitors.check-in',

                // Schedules
                'schedules.manage-own',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Secretary
        |--------------------------------------------------------------------------
        | Inherits all staff permissions plus additional administrative capabilities.
        |
        */
        'secretary' => [
            'inherits' => 'staff',
            'permissions' => [
                // Documents (adds delete)
                'documents.delete',

                // Storage (adds edit and delete)
                'storage.edit',
                'storage.delete',

                // Incidents (adds view department)
                'incidents.view-department',

                // Messages (adds view department and announcements)
                'announcements.create',
                'messages.view-department',

                // Visitors (adds view department)
                'visitors.view-department',

                // Schedules (adds department management)
                'schedules.manage-department',
                'schedules.view-department',

                // Analytics
                'analytics.view-department',

                // Audit Logs
                'audit-logs.view-department',

                // Export
                'export.department-data',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Deputy Department Head
        |--------------------------------------------------------------------------
        | Inherits all secretary permissions plus incident management.
        | Typically has same permissions as dept_head.
        |
        */
        'deputy_dept_head' => [
            'inherits' => 'secretary',
            'permissions' => [
                // Incidents (adds assign and resolve)
                'incidents.assign',
                'incidents.resolve',

                // Messages (adds broadcast to all departments)
                'messages.send-all-departments',

                // Roles (adds view, update, assign)
                'roles.view',
                'roles.update',
                'roles.assign',

                // Users (adds view, update, deactivate)
                'users.view-department',
                'users.update',
                'users.deactivate',
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Department Head
        |--------------------------------------------------------------------------
        | Inherits all deputy permissions.
        | Same permission set as deputy - both are department leadership.
        |
        */
        'dept_head' => [
            'inherits' => 'deputy_dept_head',
            'permissions' => [
                // Department heads have same permissions as deputies
                // No additional permissions needed
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Admin (Organization-Level)
        |--------------------------------------------------------------------------
        | Has all permissions except system.admin.
        | Can manage entire organization across all departments.
        |
        */
        'admin' => [
            'inherits' => null, // Defined explicitly, not inherited
            'permissions' => [
                // Documents
                'documents.route',
                'documents.view-routed',
                'documents.forward',
                'documents.download',
                'documents.delete',

                // Storage
                'storage.upload',
                'storage.view-department',
                'storage.download',
                'storage.edit',
                'storage.delete',

                // Incidents
                'incidents.create',
                'incidents.view-department',
                'incidents.assign',
                'incidents.resolve',

                // Messages & Announcements
                'announcements.create',
                'messages.send-department',
                'messages.send-all-departments',
                'messages.send-individual',
                'messages.view-department',

                // Visitors
                'visitors.check-in',
                'visitors.view-department',

                // Schedules
                'schedules.manage-own',
                'schedules.manage-department',
                'schedules.view-department',

                // Roles
                'roles.view',
                'roles.create',
                'roles.update',
                'roles.delete',
                'roles.assign',

                // Users
                'users.create',
                'users.view-department',
                'users.update',
                'users.deactivate',
                'users.delete',

                // Analytics
                'analytics.view-department',

                // Audit Logs
                'audit-logs.view-department',

                // Export
                'export.department-data',

                // NOTE: Explicitly does NOT have 'system.admin'
            ],
        ],

        /*
        |--------------------------------------------------------------------------
        | Super Admin (God Mode)
        |--------------------------------------------------------------------------
        | Has ALL permissions including system administration.
        | Full access to every feature in the application.
        |
        */
        'super_admin' => [
            'inherits' => null, // Defined explicitly
            'permissions' => 'all', // Special keyword: grants all permissions
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Role Metadata
    |--------------------------------------------------------------------------
    | Display names and descriptions for each role.
    |
    */

    'role_metadata' => [
        'staff' => [
            'display_name' => 'Staff',
            'description' => 'General staff member with basic operational permissions',
        ],
        'secretary' => [
            'display_name' => 'Secretary',
            'description' => 'Administrative staff with enhanced permissions for department operations',
        ],
        'deputy_dept_head' => [
            'display_name' => 'Deputy Department Head',
            'description' => 'Deputy head with full department management capabilities',
        ],
        'dept_head' => [
            'display_name' => 'Department Head',
            'description' => 'Department head with full department management capabilities',
        ],
        'admin' => [
            'display_name' => 'Administrator',
            'description' => 'Organization-level administrator with cross-department access',
        ],
        'super_admin' => [
            'display_name' => 'Super Administrator',
            'description' => 'System administrator with unrestricted access to all features',
        ],
    ],

];

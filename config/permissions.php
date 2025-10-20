<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Permissions
    |--------------------------------------------------------------------------
    |
    | Define all permissions used in the application with their metadata.
    | Each permission should have: name, category, description, display_name
    |
    */

    'permissions' => [

        // Documents (Routing & Workflow)
        [
            'name' => 'documents.route',
            'category' => 'documents',
            'display_name' => 'Route Documents',
            'description' => 'Send documents to other users for review or action',
        ],
        [
            'name' => 'documents.view-routed',
            'category' => 'documents',
            'display_name' => 'View Routed Documents',
            'description' => 'View documents that have been routed to you',
        ],
        [
            'name' => 'documents.forward',
            'category' => 'documents',
            'display_name' => 'Forward Documents',
            'description' => 'Forward received documents to other users',
        ],
        [
            'name' => 'documents.download',
            'category' => 'documents',
            'display_name' => 'Download Documents',
            'description' => 'Download routed documents',
        ],
        [
            'name' => 'documents.delete',
            'category' => 'documents',
            'display_name' => 'Delete Documents',
            'description' => 'Delete routed documents from the system',
        ],

        // Storage (Document Repository)
        [
            'name' => 'storage.upload',
            'category' => 'storage',
            'display_name' => 'Upload to Storage',
            'description' => 'Upload documents to department storage repository',
        ],
        [
            'name' => 'storage.view-department',
            'category' => 'storage',
            'display_name' => 'View Department Storage',
            'description' => 'View documents stored in department repository',
        ],
        [
            'name' => 'storage.download',
            'category' => 'storage',
            'display_name' => 'Download from Storage',
            'description' => 'Download documents from storage repository',
        ],
        [
            'name' => 'storage.edit',
            'category' => 'storage',
            'display_name' => 'Edit Storage Metadata',
            'description' => 'Edit document metadata in storage (title, category, description)',
        ],
        [
            'name' => 'storage.delete',
            'category' => 'storage',
            'display_name' => 'Delete from Storage',
            'description' => 'Delete documents from storage repository',
        ],

        // Incidents
        [
            'name' => 'incidents.create',
            'category' => 'incidents',
            'display_name' => 'Report Incidents',
            'description' => 'Create and report new incidents',
        ],
        [
            'name' => 'incidents.view-department',
            'category' => 'incidents',
            'display_name' => 'View Department Incidents',
            'description' => 'View all incidents assigned to department',
        ],
        [
            'name' => 'incidents.assign',
            'category' => 'incidents',
            'display_name' => 'Assign Incidents',
            'description' => 'Assign incidents to staff members for resolution',
        ],
        [
            'name' => 'incidents.resolve',
            'category' => 'incidents',
            'display_name' => 'Resolve Incidents',
            'description' => 'Mark incidents as resolved and close them',
        ],

        // Messages & Announcements
        [
            'name' => 'announcements.create',
            'category' => 'announcements',
            'display_name' => 'Create Announcements',
            'description' => 'Create and broadcast announcements',
        ],
        [
            'name' => 'messages.send-department',
            'category' => 'messages',
            'display_name' => 'Send to Department',
            'description' => 'Send messages/announcements to own department',
        ],
        [
            'name' => 'messages.send-all-departments',
            'category' => 'messages',
            'display_name' => 'Broadcast to All Departments',
            'description' => 'Send announcements to all departments organization-wide',
        ],
        [
            'name' => 'messages.send-individual',
            'category' => 'messages',
            'display_name' => 'Send to Individual',
            'description' => 'Send direct messages to individual users',
        ],
        [
            'name' => 'messages.view-department',
            'category' => 'messages',
            'display_name' => 'View Department Messages',
            'description' => 'View all messages sent to department',
        ],

        // Visitors
        [
            'name' => 'visitors.check-in',
            'category' => 'visitors',
            'display_name' => 'Check-in Visitors',
            'description' => 'Check visitors in and out of the facility',
        ],
        [
            'name' => 'visitors.view-department',
            'category' => 'visitors',
            'display_name' => 'View Department Visitors',
            'description' => 'View visitor logs for department',
        ],

        // Schedules
        [
            'name' => 'schedules.manage-own',
            'category' => 'schedules',
            'display_name' => 'Manage Own Schedule',
            'description' => 'Create, edit, and delete own schedule entries',
        ],
        [
            'name' => 'schedules.manage-department',
            'category' => 'schedules',
            'display_name' => 'Manage Department Schedules',
            'description' => 'Create, edit, and delete schedules for department members',
        ],
        [
            'name' => 'schedules.view-department',
            'category' => 'schedules',
            'display_name' => 'View Department Schedules',
            'description' => 'View schedules for all department members',
        ],

        // Roles Management
        [
            'name' => 'roles.view',
            'category' => 'roles',
            'display_name' => 'View Roles',
            'description' => 'View roles and their permissions',
        ],
        [
            'name' => 'roles.create',
            'category' => 'roles',
            'display_name' => 'Create Roles',
            'description' => 'Create new roles in the system',
        ],
        [
            'name' => 'roles.update',
            'category' => 'roles',
            'display_name' => 'Update Roles',
            'description' => 'Modify existing roles and their permissions',
        ],
        [
            'name' => 'roles.delete',
            'category' => 'roles',
            'display_name' => 'Delete Roles',
            'description' => 'Remove roles from the system',
        ],
        [
            'name' => 'roles.assign',
            'category' => 'roles',
            'display_name' => 'Assign Roles',
            'description' => 'Assign roles to users',
        ],

        // Users Management
        [
            'name' => 'users.create',
            'category' => 'users',
            'display_name' => 'Create Users',
            'description' => 'Create new user accounts',
        ],
        [
            'name' => 'users.view-department',
            'category' => 'users',
            'display_name' => 'View Department Users',
            'description' => 'View user accounts in department',
        ],
        [
            'name' => 'users.update',
            'category' => 'users',
            'display_name' => 'Update Users',
            'description' => 'Edit user account information',
        ],
        [
            'name' => 'users.deactivate',
            'category' => 'users',
            'display_name' => 'Deactivate Users',
            'description' => 'Deactivate user accounts (suspend access)',
        ],
        [
            'name' => 'users.delete',
            'category' => 'users',
            'display_name' => 'Delete Users',
            'description' => 'Permanently delete user accounts',
        ],

        // Analytics
        [
            'name' => 'analytics.view-department',
            'category' => 'analytics',
            'display_name' => 'View Department Analytics',
            'description' => 'View analytics and reports for department',
        ],

        // Audit Logs
        [
            'name' => 'audit-logs.view-department',
            'category' => 'audit-logs',
            'display_name' => 'View Department Audit Logs',
            'description' => 'View audit logs for department activities',
        ],

        // Export
        [
            'name' => 'export.department-data',
            'category' => 'export',
            'display_name' => 'Export Department Data',
            'description' => 'Export department data to various formats',
        ],

        // System Administration
        [
            'name' => 'system.admin',
            'category' => 'system',
            'display_name' => 'System Administration',
            'description' => 'Full system administration access',
        ],
    ],

];

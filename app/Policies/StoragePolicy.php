<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VisitorVisit;
use Illuminate\Auth\Access\Response;

class VisitorPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any visitor records.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('visitors.view-department') || $user->can('visitors.check-in');
    }

    /**
     * Determine whether the user can view the visitor record.
     */
    public function view(User $user, VisitorVisit $visitorVisit): bool
    {
        // User is the host
        if ($visitorVisit->host_user_id === $user->id) {
            return true;
        }

        // Must have view department permission to see others' visitors
        if (!$user->can('visitors.view-department')) {
            return false;
        }

        // Admins can view all visitor records
        if ($user->hasRole('admin')) {
            return true;
        }

        // User can view visitors from their department
        return $visitorVisit->department_id === $user->department_id;
    }

    /**
     * Determine whether the user can check in visitors.
     */
    public function create(User $user): bool
    {
        return $user->can('visitors.check-in');
    }

    /**
     * Determine whether the user can check in a visitor for a specific host.
     */
    public function checkInFor(User $user, User $host): bool
    {
        // Must have check-in permission
        if (!$user->can('visitors.check-in')) {
            return false;
        }

        // Can always check in for themselves
        if ($user->id === $host->id) {
            return true;
        }

        // Admins can check in for anyone
        if ($user->hasRole('admin')) {
            return true;
        }

        // Can check in for users in same department
        return $user->department_id === $host->department_id;
    }

    /**
     * Determine whether the user can update the visitor record.
     */
    public function update(User $user, VisitorVisit $visitorVisit): bool
    {
        // Admins can update any visitor record
        if ($user->hasRole('admin')) {
            return true;
        }

        // Host can update their own visitor records
        if ($visitorVisit->host_user_id === $user->id) {
            return true;
        }

        // Dept heads/deputies can update records in their department
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            return $visitorVisit->department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can check out the visitor.
     */
    public function checkOut(User $user, VisitorVisit $visitorVisit): bool
    {
        // Must have check-in permission (same permission for check-out)
        if (!$user->can('visitors.check-in')) {
            return false;
        }

        // Cannot check out if already checked out
        if ($visitorVisit->check_out_time !== null) {
            return Response::deny('Visitor has already been checked out.');
        }

        // Admins can check out anyone
        if ($user->hasRole('admin')) {
            return true;
        }

        // Host can check out their own visitors
        if ($visitorVisit->host_user_id === $user->id) {
            return true;
        }

        // Users in same department can check out visitors
        return $visitorVisit->department_id === $user->department_id;
    }

    /**
     * Determine whether the user can delete the visitor record.
     */
    public function delete(User $user, VisitorVisit $visitorVisit): bool
    {
        // Only admins can delete visitor records
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Cannot delete visits that are currently active (not checked out)
        if ($visitorVisit->check_out_time === null) {
            return Response::deny('Cannot delete active visitor records. Check out the visitor first.');
        }

        return true;
    }

    /**
     * Determine whether the user can restore the visitor record.
     */
    public function restore(User $user, VisitorVisit $visitorVisit): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can permanently delete the visitor record.
     */
    public function forceDelete(User $user, VisitorVisit $visitorVisit): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can view department visitor statistics.
     */
    public function viewDepartmentStats(User $user): bool
    {
        // Must have view department permission
        if (!$user->can('visitors.view-department')) {
            return false;
        }

        // Secretary and above can view stats
        return $user->hasRole(['secretary', 'deputy_dept_head', 'dept_head', 'admin']);
    }

    /**
     * Determine whether the user can export visitor logs.
     */
    public function export(User $user): bool
    {
        // Must have both visitors view and export permissions
        return $user->can('visitors.view-department') && $user->can('export.department-data');
    }
}

<?php

namespace App\Policies;

use App\Models\Incident;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class IncidentPolicy
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
     * Determine whether the user can view any incidents.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('incidents.view-department') || $user->can('incidents.create');
    }

    /**
     * Determine whether the user can view the incident.
     */
    public function view(User $user, Incident $incident): bool
    {
        // User created this incident
        if ($incident->reported_by === $user->id) {
            return true;
        }

        // Must have department view permission to see others' incidents
        if (!$user->can('incidents.view-department')) {
            return false;
        }

        // Admins can view all incidents
        if ($user->hasRole('admin')) {
            return true;
        }

        // Incident is assigned to user's department
        if ($incident->assigned_department_id === $user->department_id) {
            return true;
        }

        // User is specifically assigned to this incident
        if ($incident->assigned_to === $user->id) {
            return true;
        }

        // Dept heads can view incidents from their department (reported by their staff)
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            $reporter = User::find($incident->reported_by);
            if ($reporter && $reporter->department_id === $user->department_id) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determine whether the user can create incidents.
     */
    public function create(User $user): bool
    {
        return $user->can('incidents.create');
    }

    /**
     * Determine whether the user can update the incident.
     */
    public function update(User $user, Incident $incident): bool
    {
        // Admins can update any incident
        if ($user->hasRole('admin')) {
            return true;
        }

        // User created the incident and it's still open
        if ($incident->reported_by === $user->id && $incident->status === 'open') {
            return true;
        }

        // Dept head/deputy of assigned department can update
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            if ($incident->assigned_department_id === $user->department_id) {
                return true;
            }
        }

        // User is specifically assigned to this incident
        if ($incident->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can assign the incident to staff.
     */
    public function assign(User $user, Incident $incident): bool
    {
        // Must have assign permission
        if (!$user->can('incidents.assign')) {
            return false;
        }

        // Admins can assign any incident
        if ($user->hasRole('admin')) {
            return true;
        }

        // Must be dept head/deputy of the assigned department
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            return $incident->assigned_department_id === $user->department_id;
        }

        return false;
    }

    /**
     * Determine whether the user can resolve the incident.
     */
    public function resolve(User $user, Incident $incident): bool
    {
        // Must have resolve permission
        if (!$user->can('incidents.resolve')) {
            return false;
        }

        // Admins can resolve any incident
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dept head/deputy of assigned department can resolve
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            if ($incident->assigned_department_id === $user->department_id) {
                return true;
            }
        }

        // User specifically assigned to this incident can resolve
        if ($incident->assigned_to === $user->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the incident.
     */
    public function delete(User $user, Incident $incident): bool
    {
        // Only admins can delete incidents
        if (!$user->hasRole('admin')) {
            return false;
        }

        // Cannot delete resolved incidents (business rule)
        if ($incident->status === 'resolved' || $incident->status === 'closed') {
            return Response::deny('Cannot delete resolved or closed incidents.');
        }

        return true;
    }

    /**
     * Determine whether the user can restore the incident.
     */
    public function restore(User $user, Incident $incident): bool
    {
        return $user->hasRole(['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can permanently delete the incident.
     */
    public function forceDelete(User $user, Incident $incident): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine whether the user can change incident status.
     */
    public function changeStatus(User $user, Incident $incident): bool
    {
        // Admins can change any status
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dept head/deputy of assigned department can change status
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            return $incident->assigned_department_id === $user->department_id;
        }

        // User assigned to incident can change status
        if ($incident->assigned_to === $user->id) {
            return true;
        }

        return false;
    }
}

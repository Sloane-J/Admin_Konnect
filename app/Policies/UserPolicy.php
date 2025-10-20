<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Define role hierarchy for comparison.
     * Lower number = lower privilege.
     */
    protected array $roleHierarchy = [
        'staff' => 1,
        'secretary' => 2,
        'deputy_dept_head' => 3,
        'dept_head' => 3, // Same level as deputy
        'admin' => 4,
        'super_admin' => 5,
    ];

    /**
     * Perform pre-authorization checks.
     * Super admins can do anything except delete themselves.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            // Super admins can do everything except delete themselves
            if ($ability === 'delete' || $ability === 'forceDelete') {
                return null; // Let the policy method handle self-deletion check
            }
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Admins can view all users
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dept heads and above can view users in their department
        return $user->can('users.view-department');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Users can always view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        // Must have view permission
        if (!$user->can('users.view-department')) {
            return false;
        }

        // Admins can view anyone
        if ($user->hasRole('admin')) {
            return true;
        }

        // Dept heads/deputies/secretaries can view users in their department
        return $user->department_id === $model->department_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admins and super admins can create users
        return $user->can('users.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Users cannot update themselves (use profile update for that)
        if ($user->id === $model->id) {
            return false;
        }

        // Must have update permission
        if (!$user->can('users.update')) {
            return false;
        }

        // Admins can update anyone (except higher roles)
        if ($user->hasRole('admin')) {
            return !$this->isHigherRole($model, $user);
        }

        // Must be same department
        if ($user->department_id !== $model->department_id) {
            return false;
        }

        // Cannot update users with higher or equal role
        return $this->isLowerRole($model, $user);
    }

    /**
     * Determine whether the user can deactivate the model.
     */
    public function deactivate(User $user, User $model): bool
    {
        // Users cannot deactivate themselves
        if ($user->id === $model->id) {
            return false;
        }

        // Must have deactivate permission
        if (!$user->can('users.deactivate')) {
            return false;
        }

        // Admins can deactivate anyone (except higher roles)
        if ($user->hasRole('admin')) {
            return !$this->isHigherRole($model, $user);
        }

        // Must be same department
        if ($user->department_id !== $model->department_id) {
            return false;
        }

        // Cannot deactivate users with higher or equal role
        return $this->isLowerRole($model, $user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Users cannot delete themselves
        if ($user->id === $model->id) {
            return Response::deny('You cannot delete your own account.');
        }

        // Must have delete permission (only admins and super admins)
        if (!$user->can('users.delete')) {
            return false;
        }

        // Cannot delete users with higher or equal role
        if (!$this->isLowerRole($model, $user)) {
            return Response::deny('You cannot delete users with equal or higher privileges.');
        }

        return true;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Same rules as delete
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Same rules as delete
        return $this->delete($user, $model);
    }

    /**
     * Determine whether the user can assign roles to the model.
     */
    public function assignRole(User $user, User $model, string $roleName): bool
    {
        // Users cannot change their own role
        if ($user->id === $model->id) {
            return false;
        }

        // Must have role assignment permission
        if (!$user->can('roles.assign')) {
            return false;
        }

        // Create a temporary user object to check role level
        $roleToAssign = new User();
        $roleToAssign->syncRoles([$roleName]);

        // Cannot assign a role higher than your own
        if ($this->getRoleLevel($roleToAssign) >= $this->getRoleLevel($user)) {
            return false;
        }

        // Dept heads can only assign roles within their department
        if ($user->hasRole(['dept_head', 'deputy_dept_head'])) {
            if ($user->department_id !== $model->department_id) {
                return false;
            }

            // Dept heads cannot create other dept heads, deputies, or admins
            $restrictedRoles = ['dept_head', 'deputy_dept_head', 'admin', 'super_admin'];
            if (in_array($roleName, $restrictedRoles)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if target user has a lower role than acting user.
     */
    protected function isLowerRole(User $target, User $actor): bool
    {
        return $this->getRoleLevel($target) < $this->getRoleLevel($actor);
    }

    /**
     * Check if target user has a higher role than acting user.
     */
    protected function isHigherRole(User $target, User $actor): bool
    {
        return $this->getRoleLevel($target) > $this->getRoleLevel($actor);
    }

    /**
     * Get the highest role level for a user.
     */
    protected function getRoleLevel(User $user): int
    {
        $userRoles = $user->getRoleNames();
        $maxLevel = 0;

        foreach ($userRoles as $roleName) {
            $level = $this->roleHierarchy[$roleName] ?? 0;
            if ($level > $maxLevel) {
                $maxLevel = $level;
            }
        }

        return $maxLevel;
    }
}

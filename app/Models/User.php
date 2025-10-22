<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'department_id',
        'is_active',
        'last_login_at',
        'email_verified_at',
        'email_notifications',
        'notification_settings',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'email_notifications' => 'boolean',
            'notification_settings' => 'array',
        ];
    }

    /**
     * Relationships
     */

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get documents created by this user.
     */
    public function documents()
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    /**
     * Get documents routed to this user.
     */
    public function routedDocuments()
    {
        return $this->hasManyThrough(
            Document::class,
            DocumentRouting::class,
            'to_user_id',
            'id',
            'id',
            'document_id'
        );
    }

    /**
     * Get incidents reported by this user.
     */
    public function reportedIncidents()
    {
        return $this->hasMany(Incident::class, 'reported_by');
    }

    /**
     * Get incidents assigned to this user.
     */
    public function assignedIncidents()
    {
        return $this->hasMany(Incident::class, 'assigned_to');
    }

    /**
     * Get visitor visits hosted by this user.
     */
    public function hostedVisits()
    {
        return $this->hasMany(VisitorVisit::class, 'host_user_id');
    }

    /**
     * Scopes
     */

    /**
     * Scope to only active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to only inactive users.
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope to users in a specific department.
     */
    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    /**
     * Scope to exclude super admins from queries.
     */
    public function scopeWithoutSuperAdmins($query)
    {
        return $query->whereDoesntHave('roles', function ($q) {
            $q->where('name', 'super_admin');
        });
    }

    /**
     * Accessors
     */

    /**
     * Check if user is a super admin.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is an admin.
     */
    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    /**
     * Check if user is a department head or deputy.
     */
    public function isDepartmentHead(): bool
    {
        return $this->hasRole(['dept_head', 'deputy_dept_head']);
    }
}

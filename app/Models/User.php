<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\CausesActivity;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles, LogsActivity, CausesActivity;

    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_photo_path',
        'department_id',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    protected $appends = [
        'avatar',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'department_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->dontLogIfAttributesChangedOnly(['last_login_at', 'remember_token'])
            ->setDescriptionForEvent(fn(string $eventName) => "User {$eventName}")
            ->useLogName('user');
    }

    public function getAvatarAttribute(): ?string
    {
        if ($this->profile_photo_path) {
            return Storage::url($this->profile_photo_path);
        }

        return null;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function createdDocuments(): HasMany
    {
        return $this->hasMany(Document::class, 'created_by');
    }

    public function routedDocuments(): HasMany
    {
        return $this->hasMany(DocumentRouting::class, 'to_user_id');
    }

    public function reportedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'reported_by');
    }

    public function assignedIncidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_to');
    }

    public function hostedVisits(): HasMany
    {
        return $this->hasMany(VisitorVisit::class, 'host_user_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function updateLastLogin(): void
    {
        $this->disableLogging();
        $this->update(['last_login_at' => now()]);
        $this->enableLogging();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Department extends Model
{
    use LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'code',
        'head_user_id',
        'deputy_head_user_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'description', 'code', 'head_user_id', 'deputy_head_user_id', 'is_active'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Department {$eventName}")
            ->useLogName('department');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function head(): BelongsTo
    {
        return $this->belongsTo(User::class, 'head_user_id');
    }

    public function deputyHead(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deputy_head_user_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'assigned_department_id');
    }

    public function visitorVisits(): HasMany
    {
        return $this->hasMany(VisitorVisit::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
}

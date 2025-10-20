<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Incident extends Model
{
    use LogsActivity;

    protected $fillable = [
        'title',
        'description',
        'location',
        'reported_by',
        'assigned_department_id',
        'assigned_to',
        'status',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'status', 'assigned_to', 'assigned_department_id', 'resolution_notes'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Incident {$eventName}")
            ->useLogName('incident');
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_by');
    }

    public function assignedDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'assigned_department_id');
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignTo(User $user): void
    {
        $this->update([
            'assigned_to' => $user->id,
            'status' => 'in_progress',
        ]);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'assigned_to_id' => $user->id,
                'assigned_to_name' => $user->name,
            ])
            ->event('assigned')
            ->log('Incident assigned to user');
    }

    public function resolve(string $resolutionNotes): void
    {
        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolution_notes' => $resolutionNotes,
        ]);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'resolution_notes' => $resolutionNotes,
            ])
            ->event('resolved')
            ->log('Incident resolved');
    }

    public function reopen(): void
    {
        $this->update([
            'status' => 'open',
            'resolved_at' => null,
        ]);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->event('reopened')
            ->log('Incident reopened');
    }

    public function close(): void
    {
        $this->update(['status' => 'closed']);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->event('closed')
            ->log('Incident closed');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('assigned_department_id', $departmentId);
    }

    public function scopeReportedBy($query, $userId)
    {
        return $query->where('reported_by', $userId);
    }
}

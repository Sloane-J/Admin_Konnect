<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VisitorVisit extends Model
{
    use LogsActivity;

    protected $fillable = [
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'visitor_company',
        'host_user_id',
        'department_id',
        'purpose',
        'notes',
        'check_in_time',
        'check_out_time',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['visitor_name', 'host_user_id', 'check_in_time', 'check_out_time'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => "Visitor visit {$eventName}")
            ->useLogName('visitor_visit');
    }

    public function host(): BelongsTo
    {
        return $this->belongsTo(User::class, 'host_user_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function checkIn(): void
    {
        $this->update(['check_in_time' => now()]);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'visitor_name' => $this->visitor_name,
                'host_id' => $this->host_user_id,
            ])
            ->event('checked_in')
            ->log('Visitor checked in');
    }

    public function checkOut(): void
    {
        $this->update(['check_out_time' => now()]);

        activity()
            ->performedOn($this)
            ->causedBy(auth()->user())
            ->withProperties([
                'visitor_name' => $this->visitor_name,
                'duration' => $this->getVisitDuration(),
            ])
            ->event('checked_out')
            ->log('Visitor checked out');
    }

    public function isCheckedIn(): bool
    {
        return !is_null($this->check_in_time) && is_null($this->check_out_time);
    }

    public function isCheckedOut(): bool
    {
        return !is_null($this->check_in_time) && !is_null($this->check_out_time);
    }

    public function getVisitDuration(): ?string
    {
        if ($this->check_in_time && $this->check_out_time) {
            $minutes = $this->check_in_time->diffInMinutes($this->check_out_time);

            if ($minutes < 60) {
                return $minutes . ' minutes';
            }

            $hours = floor($minutes / 60);
            $remainingMinutes = $minutes % 60;

            return $hours . ' hour' . ($hours > 1 ? 's' : '') .
                   ($remainingMinutes > 0 ? ' ' . $remainingMinutes . ' minutes' : '');
        }

        return null;
    }

    public function scopeCheckedIn($query)
    {
        return $query->whereNotNull('check_in_time')->whereNull('check_out_time');
    }

    public function scopeCheckedOut($query)
    {
        return $query->whereNotNull('check_in_time')->whereNotNull('check_out_time');
    }

    public function scopeForHost($query, $userId)
    {
        return $query->where('host_user_id', $userId);
    }

    public function scopeInDepartment($query, $departmentId)
    {
        return $query->where('department_id', $departmentId);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('check_in_time', today());
    }
}

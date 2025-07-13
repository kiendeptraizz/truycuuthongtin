<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadCareSchedule extends Model
{
    protected $fillable = [
        'lead_id',
        'title',
        'description',
        'scheduled_at',
        'type',
        'status',
        'result',
        'notification_sent',
        'completed_at',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'notification_sent' => 'boolean',
    ];

    /**
     * Relationship with lead
     */
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    /**
     * Scope for scheduled items
     */
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    /**
     * Scope for today's schedules
     */
    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    /**
     * Scope for overdue schedules
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<', now());
    }

    /**
     * Scope for upcoming schedules
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '>', now());
    }

    /**
     * Scope for schedules needing reminder
     */
    public function scopeNeedingReminder($query)
    {
        return $query->where('status', 'scheduled')
            ->where('notification_sent', false)
            ->where('scheduled_at', '<=', now()->addHour())
            ->where('scheduled_at', '>', now());
    }

    /**
     * Check if schedule is overdue
     */
    public function isOverdue(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at < now();
    }

    /**
     * Check if schedule needs reminder
     */
    public function needsReminder(): bool
    {
        return $this->status === 'scheduled'
            && !$this->notification_sent
            && $this->scheduled_at <= now()->addHour()
            && $this->scheduled_at > now();
    }

    /**
     * Mark as completed
     */
    public function markCompleted(string $result = null): bool
    {
        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'result' => $result,
        ]);
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'call' => 'Gọi điện',
            'message' => 'Nhắn tin',
            'email' => 'Email',
            'meeting' => 'Gặp mặt',
            'follow_up' => 'Theo dõi',
            'other' => 'Khác',
            default => 'Không xác định',
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'Đã lên lịch',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
            'missed' => 'Bỏ lỡ',
            default => 'Không xác định',
        };
    }

    /**
     * Get status color for UI
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'scheduled' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'missed' => 'warning',
            default => 'secondary',
        };
    }
}

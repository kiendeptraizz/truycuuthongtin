<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentPost extends Model
{
    protected $fillable = [
        'title',
        'content',
        'image_path',
        'image_url',
        'target_groups',
        'scheduled_at',
        'status',
        'notification_sent',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'target_groups' => 'array',
        'notification_sent' => 'boolean',
    ];

    // Scope để lấy các bài đăng đã lên lịch
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    // Scope để lấy các bài đăng cần nhắc nhở (trong vòng 1 giờ tới)
    public function scopeNeedingReminder($query)
    {
        return $query->where('status', 'scheduled')
            ->where('notification_sent', false)
            ->where('scheduled_at', '<=', now()->addHour())
            ->where('scheduled_at', '>', now());
    }

    // Scope để lấy các bài đăng quá hạn (đã qua giờ đăng nhưng chưa đăng)
    public function scopeOverdue($query)
    {
        return $query->where('status', 'scheduled')
            ->where('scheduled_at', '<', now());
    }

    // Kiểm tra xem bài đăng có cần nhắc nhở không
    public function needsReminder(): bool
    {
        return $this->status === 'scheduled'
            && !$this->notification_sent
            && $this->scheduled_at <= now()->addHour()
            && $this->scheduled_at > now();
    }

    // Kiểm tra xem bài đăng có quá hạn không
    public function isOverdue(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at < now();
    }

    // Lấy danh sách nhóm dưới dạng chuỗi
    public function getTargetGroupsStringAttribute(): string
    {
        return is_array($this->target_groups) ? implode(', ', $this->target_groups) : '';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadActivity extends Model
{
    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'notes',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    // Quan hệ với Lead
    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    // Quan hệ với User (người thực hiện)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Lấy tên loại hoạt động
    public function getTypeName(): string
    {
        return match ($this->type) {
            'call' => 'Gọi điện',
            'email' => 'Gửi email',
            'meeting' => 'Gặp mặt',
            'note' => 'Ghi chú',
            'quote' => 'Báo giá',
            'follow_up' => 'Theo dõi',
            'converted' => 'Chuyển đổi',
            'lost' => 'Mất lead',
            default => 'Khác'
        };
    }

    // Lấy icon cho loại hoạt động
    public function getTypeIcon(): string
    {
        return match ($this->type) {
            'call' => 'phone',
            'email' => 'envelope',
            'meeting' => 'users',
            'note' => 'sticky-note',
            'quote' => 'file-invoice-dollar',
            'follow_up' => 'clock',
            'converted' => 'check-circle',
            'lost' => 'times-circle',
            default => 'comment'
        };
    }

    // Lấy màu cho loại hoạt động
    public function getTypeColor(): string
    {
        return match ($this->type) {
            'call' => 'info',
            'email' => 'primary',
            'meeting' => 'success',
            'note' => 'secondary',
            'quote' => 'warning',
            'follow_up' => 'info',
            'converted' => 'success',
            'lost' => 'danger',
            default => 'secondary'
        };
    }
}

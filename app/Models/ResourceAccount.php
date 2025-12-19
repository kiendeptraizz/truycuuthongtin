<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class ResourceAccount extends Model
{
    protected $fillable = [
        'resource_category_id',
        'resource_subcategory_id',
        'name',
        'email',
        'username',
        'password',
        'two_factor_secret',
        'recovery_codes',
        'start_date',
        'end_date',
        'is_available',
        'status',
        'notes',
        'extra_fields',
    ];

    protected $casts = [
        'is_available' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
        'extra_fields' => 'array',
    ];

    /**
     * Relationship với ResourceCategory
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ResourceCategory::class, 'resource_category_id');
    }

    /**
     * Relationship với ResourceSubcategory (danh mục con)
     */
    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ResourceSubcategory::class, 'resource_subcategory_id');
    }

    /**
     * Scope: Chỉ lấy accounts khả dụng
     */
    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    /**
     * Scope: Lọc theo status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope: Accounts sắp hết hạn
     */
    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays($days)]);
    }

    /**
     * Scope: Accounts đã hết hạn
     */
    public function scopeExpired($query)
    {
        return $query->whereNotNull('end_date')
            ->where('end_date', '<', now());
    }

    /**
     * Kiểm tra còn hạn không
     */
    public function getIsValidAttribute(): bool
    {
        if (!$this->end_date) {
            return true;
        }
        return $this->end_date->isFuture() || $this->end_date->isToday();
    }

    /**
     * Số ngày còn lại
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) {
            return null;
        }
        return max(0, now()->diffInDays($this->end_date, false));
    }

    /**
     * Lấy trạng thái hiển thị
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Đang hoạt động',
            'expired' => 'Đã hết hạn',
            'sold' => 'Đã bán',
            'reserved' => 'Đã đặt trước',
            'suspended' => 'Tạm ngưng',
            default => 'Không xác định',
        };
    }

    /**
     * Lấy class badge cho trạng thái
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'sold' => 'bg-info',
            'reserved' => 'bg-warning',
            'suspended' => 'bg-secondary',
            default => 'bg-dark',
        };
    }

    /**
     * Tự động cập nhật trạng thái dựa trên ngày hết hạn
     */
    public function updateStatusBasedOnDate(): void
    {
        if ($this->end_date && $this->end_date->isPast() && $this->status === 'active') {
            $this->update(['status' => 'expired', 'is_available' => false]);
        }
    }
}

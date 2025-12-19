<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SharedAccountCredential extends Model
{
    protected $fillable = [
        'service_package_id',
        'email',
        'password',
        'two_factor_secret',
        'recovery_codes',
        'notes',
        'max_users',
        'start_date',
        'end_date',
        'is_active',
        'status',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_users' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Gói dịch vụ
     */
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    /**
     * Các dịch vụ khách hàng sử dụng tài khoản này
     */
    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class, 'shared_credential_id');
    }

    /**
     * Đếm số người đang dùng
     */
    public function getCurrentUsersCountAttribute(): int
    {
        return $this->customerServices()->where('status', 'active')->count();
    }

    /**
     * Số slot còn trống
     */
    public function getAvailableSlotsAttribute(): int
    {
        return max(0, $this->max_users - $this->current_users_count);
    }

    /**
     * Kiểm tra còn slot không
     */
    public function hasAvailableSlots(): bool
    {
        return $this->available_slots > 0;
    }

    /**
     * Scope: Chỉ lấy active
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope: Còn slot trống
     */
    public function scopeHasSlots($query)
    {
        return $query->whereRaw('(SELECT COUNT(*) FROM customer_services WHERE customer_services.shared_credential_id = shared_account_credentials.id AND customer_services.status = "active") < shared_account_credentials.max_users');
    }

    /**
     * Scope: Theo gói dịch vụ
     */
    public function scopeForPackage($query, $packageId)
    {
        return $query->where('service_package_id', $packageId);
    }

    /**
     * Lấy status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'active' => 'Đang hoạt động',
            'expired' => 'Hết hạn',
            'suspended' => 'Tạm ngưng',
            'full' => 'Đã đầy',
            default => 'Không xác định',
        };
    }

    /**
     * Lấy status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'suspended' => 'bg-warning',
            'full' => 'bg-info',
            default => 'bg-secondary',
        };
    }

    /**
     * Kiểm tra hết hạn
     */
    public function isExpired(): bool
    {
        return $this->end_date && $this->end_date->isPast();
    }

    /**
     * Số ngày còn lại
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (!$this->end_date) return null;
        return max(0, now()->diffInDays($this->end_date, false));
    }
}


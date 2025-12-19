<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FamilyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_name',
        'family_code',
        'service_package_id',
        'owner_email',
        'owner_name',
        'max_members',
        'current_members',
        'activated_at',
        'expires_at',
        'status',
        'family_notes',
        'internal_notes',
        'family_settings',
        'created_by',
        'managed_by',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'family_settings' => 'array',
        'max_members' => 'integer',
        'current_members' => 'integer',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($familyAccount) {
            if (empty($familyAccount->family_code)) {
                $familyAccount->family_code = 'FAM-' . strtoupper(Str::random(8));
            }

            // Tự động set activated_at nếu status là active và chưa có activated_at
            if ($familyAccount->status === 'active' && empty($familyAccount->activated_at)) {
                $familyAccount->activated_at = now();
            }
        });

        // Đồng bộ Customer Services khi cập nhật Family Account
        static::updated(function ($familyAccount) {
            // Chỉ đồng bộ khi các thông tin quan trọng thay đổi
            if ($familyAccount->wasChanged(['owner_email', 'expires_at', 'status'])) {
                $familyAccount->syncCustomerServices();
            }
        });
    }

    /**
     * Relationships
     */
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class)->where('status', 'active');
    }

    public function customerServices(): HasMany
    {
        return $this->hasMany(\App\Models\CustomerService::class, 'family_account_id');
    }

    public function activeCustomerServices(): HasMany
    {
        return $this->hasMany(\App\Models\CustomerService::class, 'family_account_id')
            ->where('status', 'active');
    }

    /**
     * Helper methods
     */

    // Cập nhật số thành viên hiện tại (đếm theo CustomerService - mỗi dịch vụ = 1 slot)
    public function updateCurrentMembers(): void
    {
        $this->current_members = $this->customerServices()->where('status', 'active')->count();
        $this->save();
    }

    // Accessor để luôn trả về số slot chính xác (đếm theo CustomerService - mỗi dịch vụ = 1 slot)
    public function getCurrentMembersAttribute($value): int
    {
        // Nếu có relation customerServices được load, tính toán từ collection
        if ($this->relationLoaded('customerServices')) {
            return $this->customerServices->where('status', 'active')->count();
        }

        // Nếu không có relation, trả về giá trị từ database hoặc tính toán trực tiếp
        return $value ?? $this->customerServices()->where('status', 'active')->count();
    }

    // Đồng bộ thông tin với Customer Services
    public function syncCustomerServices(): void
    {
        // Lấy tất cả customer IDs từ active members
        $customerIds = $this->members()->where('status', 'active')->pluck('customer_id');

        if ($customerIds->isEmpty()) {
            return;
        }

        // Tìm tất cả Customer Services của các thành viên family này
        // với service package loại "add family"
        $customerServices = \App\Models\CustomerService::whereIn('customer_id', $customerIds)
            ->whereHas('servicePackage', function ($query) {
                $query->where('account_type', 'Tài khoản add family');
            })
            ->get();

        foreach ($customerServices as $service) {
            $updated = false;

            // Cập nhật login_email nếu family owner_email đã thay đổi
            if ($this->wasChanged('owner_email') && $this->owner_email) {
                $service->login_email = $this->owner_email;
                $updated = true;
            }

            // Cập nhật expires_at nếu family expires_at đã thay đổi
            if ($this->wasChanged('expires_at') && $this->expires_at) {
                $service->expires_at = $this->expires_at;
                $updated = true;
            }

            // Cập nhật status nếu family status đã thay đổi
            if ($this->wasChanged('status')) {
                $newStatus = match ($this->status) {
                    'active' => 'active',
                    'expired' => 'expired',
                    'suspended' => 'cancelled',
                    default => $service->status
                };

                if ($service->status !== $newStatus) {
                    $service->status = $newStatus;
                    $updated = true;
                }
            }

            if ($updated) {
                $service->save();
            }
        }
    }

    // Lấy số ngày còn lại
    public function getDaysRemaining(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        // Sử dụng startOfDay() để so sánh theo ngày lịch, không phải giờ
        $today = now()->startOfDay();
        $expiryDate = $this->expires_at->startOfDay();

        // Nếu ngày hết hạn là trước ngày hôm nay (không bao gồm hôm nay)
        if ($expiryDate->lt($today)) {
            return 0;
        }

        // Tính số ngày còn lại từ hôm nay đến ngày hết hạn (theo ngày lịch)
        // Nếu hết hạn hôm nay thì còn 0 ngày, nếu hết hạn ngày mai thì còn 1 ngày
        return $today->diffInDays($expiryDate, false);
    }

    // Kiểm tra xem tài khoản đã hết hạn chưa
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Kiểm tra xem tài khoản có sắp hết hạn trong vòng X ngày không
    public function isExpiringSoon(int $days = 5): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining >= 0 && $daysRemaining <= $days;
    }
}

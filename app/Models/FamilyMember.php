<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_account_id',
        'customer_id',
        'member_name',
        'member_email',
        'member_role',
        'status',
        'permissions',
        'joined_at',
        'last_active_at',
        'removed_at',
        'usage_count',
        'first_usage_at',
        'last_usage_at',
        'member_notes',
        'expires_at',
        'start_date',
        'end_date',
        'internal_notes',
        'added_by',
        'removed_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'joined_at' => 'datetime',
        'last_active_at' => 'datetime',
        'removed_at' => 'datetime',
        'first_usage_at' => 'datetime',
        'last_usage_at' => 'datetime',
        'expires_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_count' => 'integer',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        // Cập nhật current_members khi tạo member mới
        static::created(function ($member) {
            if ($member->familyAccount) {
                $member->familyAccount->updateCurrentMembers();
            }
        });

        // Cập nhật current_members khi cập nhật status của member
        static::updated(function ($member) {
            if ($member->familyAccount) {
                // Cập nhật số thành viên nếu status thay đổi
                if ($member->wasChanged('status')) {
                    $member->familyAccount->updateCurrentMembers();
                }

                // Đồng bộ Customer Service nếu thông tin quan trọng thay đổi
                if ($member->wasChanged(['member_email', 'status', 'expires_at', 'start_date', 'end_date'])) {
                    $member->syncCustomerService();
                }
            }
        });

        // Cập nhật current_members khi xóa member
        static::deleted(function ($member) {
            if ($member->familyAccount) {
                $member->familyAccount->updateCurrentMembers();
            }
        });
    }

    /**
     * Relationships
     */
    public function familyAccount(): BelongsTo
    {
        return $this->belongsTo(FamilyAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'added_by');
    }

    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'removed_by');
    }

    /**
     * Đồng bộ thông tin member với Customer Service
     */
    public function syncCustomerService()
    {
        if (!$this->customer) {
            return;
        }

        // Lấy customer service có family_account_id trùng
        $customerService = \App\Models\CustomerService::where('customer_id', $this->customer_id)
            ->where('family_account_id', $this->family_account_id)
            ->first();

        if ($customerService) {
            $updated = false;

            // Đồng bộ email nếu có thay đổi
            if ($this->member_email && $customerService->login_email !== $this->member_email) {
                $customerService->login_email = $this->member_email;
                $updated = true;
            }

            // Đồng bộ ngày hết hạn
            if ($this->end_date && $customerService->expires_at !== $this->end_date) {
                $customerService->expires_at = $this->end_date;
                $updated = true;
            }

            // Đồng bộ status
            $serviceStatus = $this->status === 'active' ? 'active' : 'inactive';
            if ($customerService->status !== $serviceStatus) {
                $customerService->status = $serviceStatus;
                $updated = true;
            }

            if ($updated) {
                $customerService->save();
            }
        }
    }
}

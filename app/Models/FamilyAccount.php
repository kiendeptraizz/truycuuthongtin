<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'managed_by');
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeExpiringSoon($query, $days = 7)
    {
        return $query->where('expires_at', '<=', now()->addDays($days))
            ->where('status', 'active');
    }

    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now())
            ->where('status', '!=', 'expired');
    }

    /**
     * Accessors & Mutators
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expires_at < now();
    }

    public function getIsExpiringSoonAttribute(): bool
    {
        return $this->expires_at <= now()->addDays(7) && !$this->is_expired;
    }

    public function getAvailableSlotsAttribute(): int
    {
        return $this->max_members - $this->current_members;
    }

    public function getUsagePercentageAttribute(): float
    {
        if ($this->max_members == 0) return 0;
        return ($this->current_members / $this->max_members) * 100;
    }

    public function getDaysUntilExpiryAttribute(): int
    {
        return max(0, now()->diffInDays($this->expires_at, false));
    }

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
        });

        static::saved(function ($familyAccount) {
            // Update current_members count
            $familyAccount->updateMemberCount();
        });
    }

    /**
     * Helper methods
     */
    public function updateMemberCount(): void
    {
        $this->update([
            'current_members' => $this->activeMembers()->count()
        ]);
    }

    public function canAddMember(): bool
    {
        return $this->current_members < $this->max_members && $this->status === 'active';
    }

    public function addMember(Customer $customer, array $data = []): FamilyMember
    {
        if (!$this->canAddMember()) {
            throw new \Exception('Cannot add member: Family is full or inactive');
        }

        $member = $this->members()->create([
            'customer_id' => $customer->id,
            'member_email' => $data['member_email'] ?? $customer->email,
            'member_role' => $data['member_role'] ?? 'member',
            'added_by' => auth('admin')->id(),
            'member_notes' => $data['member_notes'] ?? null,
        ]);

        $this->updateMemberCount();

        return $member;
    }

    public function removeMember(FamilyMember $member, $reason = null): bool
    {
        $member->update([
            'status' => 'removed',
            'removed_at' => now(),
            'removed_by' => auth('admin')->id(),
            'internal_notes' => ($member->internal_notes ?? '') . "\nRemoved: " . ($reason ?? 'No reason provided') . ' at ' . now(),
        ]);

        $this->updateMemberCount();

        return true;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => '<span class="badge bg-success">Hoạt động</span>',
            'expired' => '<span class="badge bg-danger">Hết hạn</span>',
            'suspended' => '<span class="badge bg-warning">Tạm dừng</span>',
            'cancelled' => '<span class="badge bg-secondary">Đã hủy</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }

    public function getUsageBadgeAttribute(): string
    {
        $percentage = $this->usage_percentage;

        if ($percentage >= 90) {
            return '<span class="badge bg-danger">Gần đầy (' . round($percentage) . '%)</span>';
        } elseif ($percentage >= 70) {
            return '<span class="badge bg-warning">Cao (' . round($percentage) . '%)</span>';
        } elseif ($percentage >= 50) {
            return '<span class="badge bg-info">Trung bình (' . round($percentage) . '%)</span>';
        } else {
            return '<span class="badge bg-success">Thấp (' . round($percentage) . '%)</span>';
        }
    }
}

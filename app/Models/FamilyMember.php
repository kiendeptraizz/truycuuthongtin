<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_account_id',
        'customer_id',
        'member_email',
        'member_role',
        'status',
        'permissions',
        'last_active_at',
        'removed_at',
        'usage_count',
        'first_usage_at',
        'last_usage_at',
        'member_notes',
        'internal_notes',
        'added_by',
        'removed_by',
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'removed_at' => 'datetime',
        'first_usage_at' => 'datetime',
        'last_usage_at' => 'datetime',
        'permissions' => 'array',
        'usage_count' => 'integer',
    ];

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
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeRemoved($query)
    {
        return $query->where('status', 'removed');
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('member_role', $role);
    }

    /**
     * Accessors & Mutators
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active';
    }

    public function getIsOwnerAttribute(): bool
    {
        return $this->member_role === 'owner';
    }

    public function getIsAdminAttribute(): bool
    {
        return in_array($this->member_role, ['owner', 'admin']);
    }

    public function getDaysInFamilyAttribute(): int
    {
        return $this->created_at->diffInDays(now());
    }

    public function getDaysSinceLastActiveAttribute(): ?int
    {
        return $this->last_active_at ? $this->last_active_at->diffInDays(now()) : null;
    }

    public function getStatusBadgeAttribute(): string
    {
        $badges = [
            'active' => '<span class="badge bg-success">Hoạt động</span>',
            'inactive' => '<span class="badge bg-warning">Không hoạt động</span>',
            'removed' => '<span class="badge bg-danger">Đã xóa</span>',
            'suspended' => '<span class="badge bg-secondary">Tạm dừng</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }

    public function getRoleBadgeAttribute(): string
    {
        $badges = [
            'owner' => '<span class="badge bg-primary"><i class="fas fa-crown me-1"></i>Chủ gia đình</span>',
            'admin' => '<span class="badge bg-info"><i class="fas fa-user-shield me-1"></i>Quản trị viên</span>',
            'member' => '<span class="badge bg-secondary"><i class="fas fa-user me-1"></i>Thành viên</span>',
        ];

        return $badges[$this->member_role] ?? '<span class="badge bg-secondary">Không xác định</span>';
    }

    public function getUsageBadgeAttribute(): string
    {
        if ($this->usage_count == 0) {
            return '<span class="badge bg-secondary">Chưa sử dụng</span>';
        } elseif ($this->usage_count >= 100) {
            return '<span class="badge bg-success">Rất tích cực (' . $this->usage_count . ')</span>';
        } elseif ($this->usage_count >= 50) {
            return '<span class="badge bg-info">Tích cực (' . $this->usage_count . ')</span>';
        } elseif ($this->usage_count >= 10) {
            return '<span class="badge bg-warning">Ít sử dụng (' . $this->usage_count . ')</span>';
        } else {
            return '<span class="badge bg-light text-dark">Rất ít (' . $this->usage_count . ')</span>';
        }
    }

    /**
     * Helper methods
     */
    public function recordUsage(): void
    {
        $this->increment('usage_count');

        $now = now();

        if (!$this->first_usage_at) {
            $this->update(['first_usage_at' => $now]);
        }

        $this->update([
            'last_usage_at' => $now,
            'last_active_at' => $now,
        ]);
    }

    public function activate(): bool
    {
        if ($this->status === 'removed') {
            return false; // Cannot reactivate removed members
        }

        return $this->update([
            'status' => 'active',
            'last_active_at' => now(),
        ]);
    }

    public function deactivate($reason = null): bool
    {
        $notes = $this->internal_notes ?? '';
        if ($reason) {
            $notes .= "\nDeactivated: {$reason} at " . now();
        }

        return $this->update([
            'status' => 'inactive',
            'internal_notes' => $notes,
        ]);
    }

    public function hasPermission($permission): bool
    {
        if ($this->is_owner) {
            return true; // Owner has all permissions
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    public function grantPermission($permission): void
    {
        $permissions = $this->permissions ?? [];
        if (!in_array($permission, $permissions)) {
            $permissions[] = $permission;
            $this->update(['permissions' => $permissions]);
        }
    }

    public function revokePermission($permission): void
    {
        $permissions = $this->permissions ?? [];
        $permissions = array_filter($permissions, fn($p) => $p !== $permission);
        $this->update(['permissions' => array_values($permissions)]);
    }
}

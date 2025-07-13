<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'source',
        'status',
        'priority',
        'notes',
        'requirements',
        'estimated_value',
        'service_package_id',
        'assigned_to',
        'last_contact_at',
        'next_follow_up_at',
        'converted_at',
        'customer_id',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'last_contact_at' => 'datetime',
        'next_follow_up_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    // Quan hệ với ServicePackage
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    // Quan hệ với User (người phụ trách)
    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Quan hệ với Customer (nếu đã chuyển đổi)
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    // Quan hệ với các hoạt động
    public function activities(): HasMany
    {
        return $this->hasMany(LeadActivity::class)->orderBy('created_at', 'desc');
    }

    // Quan hệ với lịch chăm sóc (backward compatibility)
    public function careSchedules(): HasMany
    {
        return $this->hasMany(LeadCareSchedule::class);
    }

    // Scope cho các lead cần theo dõi hôm nay
    public function scopeNeedFollowUpToday($query)
    {
        return $query->whereDate('next_follow_up_at', today())
            ->whereNotIn('status', ['won', 'lost']);
    }

    // Scope cho các lead quá hạn chăm sóc
    public function scopeOverdueFollowUp($query)
    {
        return $query->where('next_follow_up_at', '<', now())
            ->whereNotIn('status', ['won', 'lost']);
    }

    // Scope cho lead theo độ ưu tiên
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Scope cho lead theo trạng thái
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope cho lead theo người phụ trách
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    // Lấy nhãn màu cho trạng thái
    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            'new' => 'bg-primary',
            'contacted' => 'bg-info',
            'interested' => 'bg-success',
            'quoted' => 'bg-warning',
            'negotiating' => 'bg-warning',
            'won' => 'bg-success',
            'lost' => 'bg-danger',
            'follow_up' => 'bg-secondary',
            default => 'bg-secondary'
        };
    }

    // Lấy nhãn màu cho độ ưu tiên
    public function getPriorityBadgeClass(): string
    {
        return match ($this->priority) {
            'low' => 'bg-secondary',
            'medium' => 'bg-info',
            'high' => 'bg-warning',
            'urgent' => 'bg-danger',
            default => 'bg-secondary'
        };
    }

    // Lấy tên trạng thái
    public function getStatusName(): string
    {
        return match ($this->status) {
            'new' => 'Mới',
            'contacted' => 'Đã liên hệ',
            'interested' => 'Quan tâm',
            'quoted' => 'Đã báo giá',
            'negotiating' => 'Đang đàm phán',
            'won' => 'Thành công',
            'lost' => 'Thất bại',
            'follow_up' => 'Cần theo dõi',
            default => 'Không xác định'
        };
    }

    // Lấy tên độ ưu tiên
    public function getPriorityName(): string
    {
        return match ($this->priority) {
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            'urgent' => 'Khẩn cấp',
            default => 'Trung bình'
        };
    }

    // Kiểm tra lead có quá hạn chăm sóc không
    public function isOverdue(): bool
    {
        return $this->next_follow_up_at &&
            $this->next_follow_up_at->isPast() &&
            !in_array($this->status, ['won', 'lost']);
    }

    // Lấy số ngày quá hạn
    public function getDaysOverdue(): int
    {
        if (!$this->isOverdue()) {
            return 0;
        }

        return $this->next_follow_up_at->diffInDays(now());
    }

    // Chuyển đổi lead thành customer
    public function convertToCustomer(array $customerData = []): Customer
    {
        $customer = Customer::create(array_merge([
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
        ], $customerData));

        $this->update([
            'status' => 'won',
            'converted_at' => now(),
            'customer_id' => $customer->id,
        ]);

        return $customer;
    }

    // Thêm hoạt động chăm sóc
    public function addActivity(string $type, string $notes, ?array $data = null, ?int $userId = null): LeadActivity
    {
        return $this->activities()->create([
            'type' => $type,
            'notes' => $notes,
            'data' => $data,
            'user_id' => $userId ?? (Auth::id() ?? 1),
        ]);
    }

    // Cập nhật lần liên hệ cuối
    public function updateLastContact(): void
    {
        $this->update(['last_contact_at' => now()]);
    }
}

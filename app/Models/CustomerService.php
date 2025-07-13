<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerService extends Model
{
    protected $fillable = [
        'customer_id',
        'service_package_id',
        'assigned_by',
        'supplier_id',
        'supplier_service_id',
        'login_email',
        'login_password',
        'activated_at',
        'expires_at',
        'status',
        'internal_notes',
        'reminder_sent',
        'reminder_sent_at',
        'reminder_count',
        'reminder_notes',
        // Các trường mới cho tài khoản dùng chung
        'two_factor_code',
        'recovery_codes',
        'shared_account_notes',
        'customer_instructions',
        'password_expires_at',
        'two_factor_updated_at',
        'is_password_shared',
        'shared_with_customers',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'reminder_count' => 'integer',
        // Casts cho các trường mới
        'password_expires_at' => 'datetime',
        'two_factor_updated_at' => 'datetime',
        'is_password_shared' => 'boolean',
        'recovery_codes' => 'array', // Tự động parse JSON
        'shared_with_customers' => 'array', // Tự động parse JSON
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function supplierService(): BelongsTo
    {
        return $this->belongsTo(SupplierProduct::class, 'supplier_service_id');
    }

    // Kiểm tra xem dịch vụ có sắp hết hạn không (trong vòng 5 ngày)
    public function isExpiringSoon(): bool
    {
        if (!$this->expires_at || $this->expires_at->isPast()) {
            return false;
        }

        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining > 0 && $daysRemaining <= 5;
    }

    // Lấy số ngày còn lại
    public function getDaysRemaining(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        if ($this->expires_at->isPast()) {
            return 0;
        }

        // Tính số ngày còn lại từ hôm nay đến ngày hết hạn
        return (int) now()->diffInDays($this->expires_at, false);
    }

    // Kiểm tra xem dịch vụ đã hết hạn chưa
    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    // Scope để lấy các dịch vụ đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope để lấy các dịch vụ sắp hết hạn (mặc định 5 ngày)
    public function scopeExpiringSoon($query, $days = 5)
    {
        return $query->where('status', 'active')
            ->where('expires_at', '>', now())
            ->where('expires_at', '<=', now()->addDays((int) $days));
    }

    // Scope để lấy các dịch vụ đã hết hạn
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<', now());
    }

    // Scope để lấy các dịch vụ sắp hết hạn chưa được nhắc nhở
    public function scopeExpiringSoonNotReminded($query, $days = 5)
    {
        return $query->expiringSoon($days)->where('reminder_sent', false);
    }

    // Scope để lấy các dịch vụ đã được nhắc nhở
    public function scopeReminded($query)
    {
        return $query->where('reminder_sent', true);
    }

    // Đánh dấu đã gửi nhắc nhở
    public function markAsReminded($notes = null): bool
    {
        $this->reminder_sent = true;
        $this->reminder_sent_at = now();
        $this->reminder_count = $this->reminder_count + 1;

        if ($notes) {
            $existingNotes = $this->reminder_notes ? $this->reminder_notes . "\n" : '';
            $this->reminder_notes = $existingNotes . '[' . now()->format('d/m/Y H:i') . '] ' . $notes;
        }

        return $this->save();
    }

    // Reset trạng thái nhắc nhở
    public function resetReminderStatus(): bool
    {
        $this->reminder_sent = false;
        $this->reminder_sent_at = null;

        return $this->save();
    }

    // Kiểm tra có cần nhắc nhở lại không (sau 1 ngày)
    public function needsReminderAgain(): bool
    {
        if (!$this->reminder_sent || !$this->reminder_sent_at) {
            return true;
        }

        // Nhắc lại sau 1 ngày nếu vẫn sắp hết hạn
        return $this->isExpiringSoon() &&
            $this->reminder_sent_at->diffInHours(now()) >= 24;
    }

    // Lấy trạng thái dịch vụ
    public function getStatus(): string
    {
        if (!$this->activated_at) {
            return 'inactive';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        if ($this->isExpiringSoon()) {
            return 'expiring';
        }

        return 'active';
    }
}

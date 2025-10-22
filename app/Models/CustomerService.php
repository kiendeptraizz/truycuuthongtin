<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class CustomerService extends Model
{
    protected $fillable = [
        'customer_id',
        'service_package_id',
        'assigned_by',

        'family_account_id',
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

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'assigned_by'); // Sử dụng assigned_by làm created_by
    }



    public function familyAccount(): BelongsTo
    {
        return $this->belongsTo(FamilyAccount::class);
    }

    public function profit(): HasOne
    {
        return $this->hasOne(Profit::class);
    }

    // Kiểm tra xem dịch vụ có sắp hết hạn không (trong vòng 5 ngày)
    public function isExpiringSoon(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        // Kiểm tra nếu đã hết hạn (qua hết ngày hết hạn)
        if ($this->isExpired()) {
            return false;
        }

        $daysRemaining = $this->getDaysRemaining();
        return $daysRemaining >= 0 && $daysRemaining <= 5;
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

    // Kiểm tra xem dịch vụ đã hết hạn chưa
    // Chỉ tính là hết hạn khi đã QUA HẾT ngày hết hạn (sau 23:59:59)
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        // So sánh theo ngày lịch: Chỉ hết hạn khi qua hết ngày đó
        $expiryDate = $this->expires_at->copy()->endOfDay();
        return $expiryDate->isPast();
    }

    // Scope để lấy các dịch vụ đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope để lấy các dịch vụ sắp hết hạn (mặc định 5 ngày)
    // Bao gồm cả dịch vụ hết hạn trong ngày hôm nay (vì khách vẫn còn cả ngày để dùng)
    public function scopeExpiringSoon($query, $days = 5)
    {
        $today = now()->startOfDay();
        $endDate = now()->addDays((int) $days)->endOfDay();

        return $query->where('status', 'active')
            ->where('expires_at', '>=', $today)
            ->where('expires_at', '<=', $endDate);
    }

    // Scope để lấy các dịch vụ đã hết hạn
    // Chỉ tính là hết hạn khi đã qua hết ngày hết hạn (sau 23:59:59 của ngày hôm qua)
    public function scopeExpired($query)
    {
        $yesterday = now()->subDay()->endOfDay();
        return $query->where('expires_at', '<=', $yesterday);
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

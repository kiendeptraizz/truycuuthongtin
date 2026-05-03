<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class CustomerService extends Model
{
    use SoftDeletes;

    protected static function booted(): void
    {
        $clearCache = function () {
            Cache::forget('dashboard_stats');
            Cache::forget('dashboard_popular_services');
            Cache::forget('revenue_general_stats');
        };

        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);

        // Auto-gen order_code khi tạo CS — đảm bảo mọi đơn đều có mã DH-YYMMDD-XXX
        static::creating(function (CustomerService $cs) {
            if (!empty($cs->order_code)) {
                return; // đã có (vd webhook copy từ PendingOrder)
            }
            // Nếu link với PendingOrder → copy luôn order_code
            if (!empty($cs->pending_order_id)) {
                $po = PendingOrder::find($cs->pending_order_id);
                if ($po && !empty($po->order_code)) {
                    $cs->order_code = $po->order_code;
                    return;
                }
            }
            // Còn lại — gen mới theo today (giống PendingOrder::generateOrderCode nhưng query
            // cả 2 bảng để tránh trùng)
            $cs->order_code = static::generateOrderCode();
        });
    }

    /**
     * Sinh mã đơn DH-yymmdd-XXX (XXX = 3 chữ số sequence trong ngày).
     * Race-safe nhờ UNIQUE constraint của order_code — collision sẽ bị retry.
     */
    public static function generateOrderCode(?\DateTimeInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'DH-' . $date->format('ymd') . '-';

        // Lấy seq lớn nhất trong ngày từ cả 2 bảng (cs + pending_orders)
        $maxFromCs = static::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('order_code')
            ->value('order_code');

        $maxFromPo = PendingOrder::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('order_code')
            ->value('order_code');

        $maxSeq = 0;
        foreach ([$maxFromCs, $maxFromPo] as $code) {
            if ($code && preg_match('/-(\d+)$/', $code, $m)) {
                $maxSeq = max($maxSeq, (int) $m[1]);
            }
        }
        return $prefix . str_pad((string) ($maxSeq + 1), 3, '0', STR_PAD_LEFT);
    }

    protected $fillable = [
        'order_code',
        'customer_id',
        'service_package_id',
        'assigned_by',
        'pending_order_id',

        'family_account_id',
        'shared_credential_id',
        'login_email',
        'login_password',
        'activated_at',
        'expires_at',
        'status',
        'duration_days',
        'warranty_days',
        'order_amount',
        'family_code',
        'cost_price',
        'price',
        'internal_notes',
        'refund_amount',
        'refunded_at',
        'refund_reason',
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
        'refunded_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'reminder_sent' => 'boolean',
        'reminder_count' => 'integer',
        'refund_amount' => 'integer',
        // Casts cho các trường mới
        'password_expires_at' => 'datetime',
        'two_factor_updated_at' => 'datetime',
        'is_password_shared' => 'boolean',
        'recovery_codes' => 'array',
        'shared_with_customers' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Lấy số ngày đã hết hạn
     */
    public function getDaysExpired(): int
    {
        if (!$this->expires_at || !$this->isExpired()) {
            return 0;
        }
        return $this->expires_at->startOfDay()->diffInDays(now()->startOfDay());
    }

    /**
     * Scope để lấy các dịch vụ hết hạn quá X ngày
     */
    public function scopeExpiredMoreThanDays($query, int $days)
    {
        $cutoffDate = now()->subDays($days)->endOfDay();
        return $query->where('status', 'expired')
            ->where('expires_at', '<=', $cutoffDate);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function familyAccount(): BelongsTo
    {
        return $this->belongsTo(FamilyAccount::class);
    }

    public function sharedCredential(): BelongsTo
    {
        return $this->belongsTo(SharedAccountCredential::class);
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
    // Đồng bộ với filter UI: dịch vụ hết hạn vào ngày X được coi là "đã hết hạn" từ 00:00 ngày X
    public function isExpired(): bool
    {
        if (!$this->expires_at) {
            return false;
        }

        // expires_at (chỉ ngày, bỏ qua giờ) ≤ hôm nay → đã hết hạn
        return $this->expires_at->copy()->startOfDay()->lessThanOrEqualTo(now()->startOfDay());
    }

    // Scope để lấy các dịch vụ đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope để lấy các dịch vụ sắp hết hạn (mặc định 5 ngày)
    // KHÔNG bao gồm dịch vụ hết hạn hôm nay (vì đã được tính vào "Đã hết hạn")
    // Chỉ lấy dịch vụ hết hạn từ ngày mai trở đi trong vòng 5 ngày
    public function scopeExpiringSoon($query, $days = 5)
    {
        // Dùng whereDate để tránh vấn đề timezone
        $tomorrow = now()->addDay()->format('Y-m-d');
        $endDate = now()->addDays((int) $days)->format('Y-m-d');

        return $query->where('status', 'active')
            ->whereDate('expires_at', '>=', $tomorrow)
            ->whereDate('expires_at', '<=', $endDate);
    }

    // Scope để lấy các dịch vụ đã hết hạn
    // Lọc theo status = 'expired' (đã được tự động cập nhật bởi scheduled command)
    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Scope để lấy các dịch vụ đã hết hạn theo thời gian (bất kể status)
    // Đồng bộ với filter UI và isExpired(): hết hạn từ 00:00 ngày `expires_at`
    public function scopeExpiredByDate($query)
    {
        return $query->whereDate('expires_at', '<=', now()->format('Y-m-d'));
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

<?php

namespace App\Models;

use App\Services\VietQrService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class PendingOrder extends Model
{
    protected $fillable = [
        'order_code',
        'group_code',
        'amount',
        'note',
        'status',
        'created_via',
        'is_paid',
        'paid_at',
        'paid_amount',
        'bank_transaction_id',
        'bank_raw_payload',
        'customer_id',
        'service_package_id',
        'customer_service_id',
        'created_by',
        'telegram_chat_id',
        'account_email',
        'family_code',
        'duration_days',
        'warranty_days',
        'profit_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'paid_amount' => 'integer',
        'duration_days' => 'integer',
        'warranty_days' => 'integer',
        'profit_amount' => 'integer',
    ];

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Sinh mã đơn dạng DH-yymmdd-XXX (XXX = sequence trong ngày, 3 chữ số).
     * Query CẢ 2 bảng pending_orders + customer_services để tránh va chạm
     * khi web tạo CS độc lập gen cùng số trong cùng ngày (bug đã từng xảy ra
     * 02/05/2026 khi CS web 023 conflict với PO bot 023).
     */
    public static function generateOrderCode(?\DateTimeInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'DH-' . $date->format('ymd') . '-';

        $maxFromPo = static::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('order_code')
            ->value('order_code');

        $maxFromCs = \App\Models\CustomerService::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('order_code')
            ->value('order_code');

        $maxSeq = 0;
        foreach ([$maxFromPo, $maxFromCs] as $code) {
            if ($code && preg_match('/-(\d+)$/', $code, $m)) {
                $maxSeq = max($maxSeq, (int) $m[1]);
            }
        }

        return $prefix . str_pad((string) ($maxSeq + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * URL VietQR để bot/web hiển thị mã thanh toán.
     */
    public function qrCodeUrl(): string
    {
        return app(VietQrService::class)->buildQrUrl(
            (int) $this->amount,
            $this->order_code
        );
    }

    /**
     * Sinh mã group GR-yymmdd-XXX cho lô đơn nhiều dịch vụ cùng lúc.
     * Sequence = max(seq trong ngày) + 1, query DISTINCT group_code để tránh
     * trùng (vì 1 lô có N PO cùng group_code, không thể count rows).
     */
    public static function generateGroupCode(?\DateTimeInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'GR-' . $date->format('ymd') . '-';

        $maxFromGroup = static::where('group_code', 'like', $prefix . '%')
            ->orderByDesc('group_code')
            ->value('group_code');

        $maxSeq = 0;
        if ($maxFromGroup && preg_match('/-(\d+)$/', $maxFromGroup, $m)) {
            $maxSeq = (int) $m[1];
        }

        return $prefix . str_pad((string) ($maxSeq + 1), 3, '0', STR_PAD_LEFT);
    }

    /**
     * Hỏi xem đơn này có thuộc 1 lô (group) không.
     */
    public function hasGroup(): bool
    {
        return !empty($this->group_code);
    }

    /**
     * Lấy tất cả PendingOrder cùng lô (kể cả chính nó). Trả về collection rỗng
     * nếu đơn không thuộc lô nào.
     */
    public function siblingsInGroup()
    {
        if (!$this->hasGroup()) {
            return collect();
        }
        return static::where('group_code', $this->group_code)->orderBy('order_code')->get();
    }

    /**
     * URL ảnh QR có background đẹp (dùng template "compact2" của VietQR).
     */
    public function qrImageUrl(): string
    {
        return $this->qrCodeUrl();
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}

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
        'amount',
        'note',
        'status',
        'created_via',
        'is_paid',
        'paid_at',
        'paid_amount',
        'bank_transaction_id',
        'bank_raw_payload',
        'customer_service_id',
        'created_by',
        'telegram_chat_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
        'paid_amount' => 'integer',
    ];

    public function isPaid(): bool
    {
        return $this->paid_at !== null;
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Sinh mã đơn dạng DH-yymmdd-XXX (XXX = sequence trong ngày, 3 chữ số).
     * Race-safe nhờ UNIQUE constraint của order_code — nếu trùng sẽ retry.
     */
    public static function generateOrderCode(?\DateTimeInterface $date = null): string
    {
        $date = $date ?? now();
        $prefix = 'DH-' . $date->format('ymd') . '-';

        // Lấy số đơn lớn nhất trong ngày để tăng tiếp
        $latest = static::where('order_code', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('order_code');

        $nextSeq = 1;
        if ($latest && preg_match('/-(\d+)$/', $latest, $m)) {
            $nextSeq = (int) $m[1] + 1;
        }

        return $prefix . str_pad((string) $nextSeq, 3, '0', STR_PAD_LEFT);
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

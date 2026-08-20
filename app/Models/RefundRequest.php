<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RefundRequest extends Model
{
    protected $fillable = [
        'code',
        'customer_name',
        'order_code',
        'bank_account',
        'qr_image_path',
        'status',
        'admin_note',
        'ip_address',
    ];

    protected static function booted(): void
    {
        // Tự sinh mã theo dõi RF-YYMMDD-XXXX khi tạo mới (nếu chưa có)
        static::creating(function (RefundRequest $req) {
            if (empty($req->code)) {
                do {
                    $code = 'RF-' . now()->format('ymd') . '-' . strtoupper(Str::random(4));
                } while (static::where('code', $code)->exists());
                $req->code = $code;
            }
        });
    }

    // URL công khai của ảnh QR (public disk đã storage:link)
    public function getQrUrlAttribute(): ?string
    {
        return $this->qr_image_path ? asset('storage/' . $this->qr_image_path) : null;
    }

    // Liên kết mềm tới đơn dịch vụ theo order_code (không FK cứng vì khách tự nhập)
    public function customerService()
    {
        return $this->belongsTo(CustomerService::class, 'order_code', 'order_code');
    }
}

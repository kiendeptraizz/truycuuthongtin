<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mã đánh dấu công việc cần xử lý (CV-XXXXXX). Admin tạo mã qua bot/web, dán vào
 * đoạn chat Zalo làm mốc, rồi xem lại + đánh dấu hoàn thành ở bot hoặc web.
 */
class WorkTask extends Model
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_DONE = 'done';

    protected $fillable = [
        'code',
        'note',
        'status',
        'created_via',
        'created_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeDone($query)
    {
        return $query->where('status', self::STATUS_DONE);
    }

    public function isDone(): bool
    {
        return $this->status === self::STATUS_DONE;
    }

    public function markDone(): void
    {
        $this->update(['status' => self::STATUS_DONE, 'completed_at' => now()]);
    }

    public function reopen(): void
    {
        $this->update(['status' => self::STATUS_PENDING, 'completed_at' => null]);
    }

    /**
     * Sinh mã CV-XXXXXX duy nhất. Bảng chữ tránh ký tự dễ nhầm (0/O/1/I/L)
     * để đọc/gõ lại từ Zalo không sai. Retry nếu trùng.
     */
    public static function generateCode(): string
    {
        $alphabet = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';
        $max = strlen($alphabet) - 1;
        for ($attempt = 0; $attempt < 20; $attempt++) {
            $suffix = '';
            for ($i = 0; $i < 6; $i++) {
                $suffix .= $alphabet[random_int(0, $max)];
            }
            $code = 'CV-' . $suffix;
            if (!self::where('code', $code)->exists()) {
                return $code;
            }
        }
        // Fallback cực hiếm (20 lần trùng) — thêm entropy theo microtime.
        return 'CV-' . strtoupper(base_convert((string) random_int(1000000, 9999999), 10, 36));
    }
}

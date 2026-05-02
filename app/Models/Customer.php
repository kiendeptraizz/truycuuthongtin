<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'name',
        'email',
        'phone',
        'notes',
        'is_collaborator',
    ];

    protected $casts = [
        'is_collaborator' => 'boolean',
    ];

    /**
     * Format tên khách hàng theo đúng định dạng Tiếng Việt
     * VD: "phùng văn đại" hoặc "PHÙNG VĂN ĐẠI" -> "Phùng Văn Đại"
     * Mutator: Tự động format khi lưu vào database
     * Accessor: Tự động format khi đọc từ database (backup)
     */
    protected function name(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->formatVietnameseName($value),
            set: fn ($value) => $this->formatVietnameseName($value),
        );
    }

    /**
     * Lấy tên gốc chưa format (để dùng khi cần)
     */
    public function getRawNameAttribute(): ?string
    {
        return $this->attributes['name'] ?? null;
    }

    /**
     * Format tên tiếng Việt - Viết hoa đầu mỗi từ
     */
    private function formatVietnameseName(?string $name): ?string
    {
        if (empty($name)) {
            return $name;
        }

        // Chuyển về lowercase trước, sau đó viết hoa đầu mỗi từ
        $name = mb_strtolower($name, 'UTF-8');
        
        // Tách các từ và viết hoa chữ cái đầu
        $words = explode(' ', $name);
        $formattedWords = array_map(function ($word) {
            return mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
        }, $words);

        return implode(' ', $formattedWords);
    }

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }

    public function familyMemberships(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function activeFamilyMembership(): HasOne
    {
        return $this->hasOne(FamilyMember::class)->where('status', 'active');
    }

    public function activeServices(): HasMany
    {
        return $this->hasMany(CustomerService::class)->where('status', 'active');
    }

    public function expiredServices(): HasMany
    {
        return $this->hasMany(CustomerService::class)->where('status', 'expired');
    }

    // Tự động tạo customer_code khi tạo mới
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {
            if (empty($customer->customer_code)) {
                $customer->customer_code = self::generateCustomerCode($customer->is_collaborator ?? false);
            }
        });
    }

    private static function generateCustomerCode(bool $isCollaborator = false): string
    {
        $prefix = $isCollaborator ? 'CTV' : 'KUN';
        $maxAttempts = 50;

        // Pre-check để giảm xác suất collision lần đầu insert; race window vẫn còn
        // (2 request có thể cùng pick chung 1 code chưa exists). UNIQUE ở DB chặn
        // double-insert; caller nên dùng Customer::createSafe() để retry tự động.
        for ($i = 0; $i < $maxAttempts; $i++) {
            $code = $prefix . str_pad(random_int(1, 99999), 5, '0', STR_PAD_LEFT);
            if (!self::where('customer_code', $code)->exists()) {
                return $code;
            }
        }

        return $prefix . substr((string) (microtime(true) * 1000), -5) . random_int(0, 9);
    }

    /**
     * Tạo Customer + retry tự động khi customer_code collision (race condition).
     *
     * Pattern check-then-insert trong boot::creating có race window: 2 caller cùng
     * gen 1 code chưa tồn tại → insert thứ 2 fail UNIQUE. createSafe bắt lỗi đó
     * và retry, đảm bảo caller luôn nhận về 1 Customer hợp lệ.
     */
    public static function createSafe(array $attrs): self
    {
        $maxRetries = 5;
        $lastException = null;

        for ($i = 0; $i < $maxRetries; $i++) {
            try {
                return static::create($attrs);
            } catch (\Illuminate\Database\QueryException $e) {
                $errorCode = $e->errorInfo[1] ?? null;
                $isUniqueViolation = $errorCode === 1062
                    || str_contains(strtolower($e->getMessage()), 'duplicate entry');
                $isCustomerCodeCollision = str_contains($e->getMessage(), 'customer_code');

                if ($isUniqueViolation && $isCustomerCodeCollision) {
                    $lastException = $e;
                    unset($attrs['customer_code']); // ép boot::creating regen
                    continue;
                }
                throw $e;
            }
        }

        throw $lastException ?? new \RuntimeException('Customer::createSafe: vượt số lần retry');
    }
}

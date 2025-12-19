<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ResourceCategory extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'color',
        'description',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    /**
     * Relationship với ResourceAccount
     */
    public function accounts(): HasMany
    {
        return $this->hasMany(ResourceAccount::class);
    }

    /**
     * Relationship với ResourceSubcategory (danh mục con)
     */
    public function subcategories(): HasMany
    {
        return $this->hasMany(ResourceSubcategory::class)->ordered();
    }

    /**
     * Scope: Chỉ lấy categories đang hoạt động
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Sắp xếp theo thứ tự
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Đếm số tài khoản khả dụng
     */
    public function getAvailableAccountsCountAttribute(): int
    {
        return $this->accounts()->where('is_available', true)->count();
    }

    /**
     * Đếm số tài khoản sắp hết hạn (trong 7 ngày)
     */
    public function getExpiringAccountsCountAttribute(): int
    {
        return $this->accounts()
            ->where('is_available', true)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [now(), now()->addDays(7)])
            ->count();
    }
}

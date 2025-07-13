<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServicePackage extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'account_type',
        'default_duration_days',
        'price',
        'cost_price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function customerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class);
    }

    public function activeCustomerServices(): HasMany
    {
        return $this->hasMany(CustomerService::class)->where('status', 'active');
    }

    // Scope để lấy các gói đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Tính lợi nhuận
    public function getProfit()
    {
        if (!$this->cost_price) {
            return 0;
        }
        return $this->price - $this->cost_price;
    }

    // Tính tỷ lệ lợi nhuận (%)
    public function getProfitMargin()
    {
        if (!$this->cost_price || $this->cost_price == 0) {
            return 0;
        }
        return round(($this->getProfit() / $this->cost_price) * 100, 2);
    }
}

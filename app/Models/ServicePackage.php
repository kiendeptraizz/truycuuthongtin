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
        'custom_duration',
        'price',
        'cost_price',
        'description',
        'detailed_notes',
        'warranty_type',
        'is_active',
        'is_renewable',
        'device_limit',
        'shared_users_limit',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_renewable' => 'boolean',
        'device_limit' => 'integer',
        'shared_users_limit' => 'integer',
    ];

    // Accessor for backward compatibility
    public function getDurationMonthsAttribute()
    {
        return round($this->default_duration_days / 30);
    }

    // Accessor for package name compatibility
    public function getPackageNameAttribute()
    {
        return $this->name;
    }

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

    public function familyAccounts(): HasMany
    {
        return $this->hasMany(FamilyAccount::class);
    }

    public function activeFamilyAccounts(): HasMany
    {
        return $this->hasMany(FamilyAccount::class)->where('status', 'active');
    }

    public function sharedCredentials(): HasMany
    {
        return $this->hasMany(SharedAccountCredential::class);
    }

    public function activeSharedCredentials(): HasMany
    {
        return $this->hasMany(SharedAccountCredential::class)->where('is_active', true)->where('status', 'active');
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

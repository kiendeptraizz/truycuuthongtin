<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

        do {
            $code = $prefix . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
        } while (self::where('customer_code', $code)->exists());

        return $code;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PotentialSupplier extends Model
{
    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'website',
        'notes',
        'reason_potential',
        'priority',
        'expected_cooperation_date',
    ];

    protected $casts = [
        'expected_cooperation_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($supplier) {
            if (empty($supplier->supplier_code)) {
                $supplier->supplier_code = self::generateSupplierCode();
            }
        });
    }

    public static function generateSupplierCode(): string
    {
        $lastSupplier = self::orderBy('id', 'desc')->first();
        $number = $lastSupplier ? (int) substr($lastSupplier->supplier_code, 3) + 1 : 1;
        return 'PSU' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function services(): HasMany
    {
        return $this->hasMany(PotentialSupplierService::class);
    }

    public function getTotalEstimatedValueAttribute(): float
    {
        return $this->services->sum('estimated_price');
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'Thấp',
            'medium' => 'Trung bình',
            'high' => 'Cao',
            default => 'Trung bình'
        };
    }

    public function getPriorityBadgeClassAttribute(): string
    {
        return match ($this->priority) {
            'low' => 'badge-secondary',
            'medium' => 'badge-warning',
            'high' => 'badge-danger',
            default => 'badge-warning'
        };
    }
}

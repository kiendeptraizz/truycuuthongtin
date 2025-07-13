<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_code',
        'supplier_name',
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
        return 'SUP' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function products(): HasMany
    {
        return $this->hasMany(SupplierProduct::class);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->products->sum('price');
    }

    public function getProductCountAttribute(): int
    {
        return $this->products->count();
    }
}

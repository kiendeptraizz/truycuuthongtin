<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierProduct extends Model
{
    protected $fillable = [
        'supplier_id',
        'product_name',
        'price',
        'warranty_days',
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 0, '.', ',') . ' VND';
    }
}

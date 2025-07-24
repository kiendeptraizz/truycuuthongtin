<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PotentialSupplierService extends Model
{
    protected $fillable = [
        'potential_supplier_id',
        'service_name',
        'estimated_price',
        'description',
        'unit',
        'warranty_days',
        'notes',
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
    ];

    public function potentialSupplier(): BelongsTo
    {
        return $this->belongsTo(PotentialSupplier::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->estimated_price, 0, '.', ',') . ' VND';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CollaboratorService extends Model
{
    protected $fillable = [
        'collaborator_id',
        'service_name',
        'price',
        'quantity',
        'warranty_period',
        'status',
        'description'
    ];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public function collaborator(): BelongsTo
    {
        return $this->belongsTo(Collaborator::class);
    }

    public function accounts(): HasMany
    {
        return $this->hasMany(CollaboratorServiceAccount::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return formatCurrency($this->price);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->price * $this->quantity;
    }

    public function getFormattedTotalValueAttribute(): string
    {
        return formatCurrency($this->total_value);
    }

    public function getActiveAccountsCountAttribute(): int
    {
        return $this->accounts->where('status', 'active')->count();
    }

    public function getExpiredAccountsCountAttribute(): int
    {
        return $this->accounts->where('status', 'expired')->count();
    }
}

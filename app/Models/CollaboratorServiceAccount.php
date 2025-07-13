<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CollaboratorServiceAccount extends Model
{
    protected $fillable = [
        'collaborator_service_id',
        'account_info',
        'provided_date',
        'expiry_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'provided_date' => 'date',
        'expiry_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($account) {
            // Auto set status based on expiry date
            if ($account->expiry_date && $account->expiry_date->isPast()) {
                $account->status = 'expired';
            }
        });

        static::updating(function ($account) {
            // Auto update status if expiry date changes
            if ($account->expiry_date && $account->expiry_date->isPast() && $account->status === 'active') {
                $account->status = 'expired';
            }
        });
    }

    public function collaboratorService(): BelongsTo
    {
        return $this->belongsTo(CollaboratorService::class);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (!$this->expiry_date) return null;

        return Carbon::now()->diffInDays($this->expiry_date, false);
    }

    public function getFormattedProvidedDateAttribute(): string
    {
        return $this->provided_date ? $this->provided_date->format('d/m/Y') : '';
    }

    public function getFormattedExpiryDateAttribute(): string
    {
        return $this->expiry_date ? $this->expiry_date->format('d/m/Y') : '';
    }

    public function getRemainingDaysAttribute(): string
    {
        if (!$this->expiry_date) return 'Không giới hạn';

        $days = $this->days_until_expiry;

        if ($days < 0) {
            return 'Đã hết hạn ' . abs($days) . ' ngày';
        } elseif ($days === 0) {
            return 'Hết hạn hôm nay';
        } else {
            return 'Còn ' . $days . ' ngày';
        }
    }
}

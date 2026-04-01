<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Cache;

class Profit extends Model
{
    protected $fillable = [
        'customer_service_id',
        'profit_amount',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'profit_amount' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        $clearCache = function () {
            Cache::forget('revenue_general_stats');
        };
        static::created($clearCache);
        static::updated($clearCache);
        static::deleted($clearCache);
    }

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    // Scope để lấy profits theo ngày
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('created_at', $date);
    }

    // Scope để lấy profits trong khoảng thời gian
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

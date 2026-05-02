<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HomeSettings extends Model
{
    protected $table = 'home_settings';

    protected $fillable = [
        'customers_override',
        'services_override',
        'packages_override',
    ];

    protected $casts = [
        'customers_override' => 'integer',
        'services_override' => 'integer',
        'packages_override' => 'integer',
    ];

    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1], []);
    }

    protected static function booted(): void
    {
        $clear = function () {
            Cache::forget('home_stats');
        };

        static::created($clear);
        static::updated($clear);
    }
}

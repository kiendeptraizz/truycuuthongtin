<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Ánh xạ product_id bên bot Python → ServicePackage bên CRM.
 * Tạo tự động khi ingest gặp product mới (lazy). Xem BotSaleIngestService.
 */
class BotProductMap extends Model
{
    protected $table = 'bot_product_map';

    protected $fillable = [
        'bot_product_id',
        'service_package_id',
        'last_name',
    ];

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerServiceWarranty extends Model
{
    public $timestamps = false; // chỉ created_at

    protected $fillable = [
        'customer_service_id',
        'replacement_email',
        'replacement_password',
        'extended_days',
        'note',
        'actor_type',
        'actor_id',
        'actor_label',
    ];

    protected $casts = [
        'extended_days' => 'integer',
        'created_at' => 'datetime',
    ];

    public function customerService(): BelongsTo
    {
        return $this->belongsTo(CustomerService::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    /**
     * Type label (vd: "Đổi TK", "Gia hạn 30 ngày", "Ghi chú")
     */
    public function getTypeLabel(): string
    {
        $parts = [];
        if (!empty($this->replacement_email)) {
            $parts[] = 'Đổi TK';
        }
        if (!empty($this->extended_days)) {
            $parts[] = "Gia hạn +{$this->extended_days} ngày";
        }
        if (empty($parts)) {
            $parts[] = 'Ghi chú';
        }
        return implode(' · ', $parts);
    }
}

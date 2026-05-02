<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerServiceAudit extends Model
{
    public $timestamps = false; // chỉ có created_at, không cần updated_at

    protected $fillable = [
        'customer_service_id',
        'event',
        'old_values',
        'new_values',
        'changed_fields',
        'actor_type',
        'actor_id',
        'actor_label',
        'ip_address',
        'user_agent',
        'note',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'changed_fields' => 'array',
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
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_account_id',
        'customer_id',
        'member_name',
        'member_email',
        'member_role',
        'status',
        'permissions',
        'joined_at',
        'last_active_at',
        'removed_at',
        'usage_count',
        'first_usage_at',
        'last_usage_at',
        'member_notes',
        'expires_at',
        'start_date',
        'end_date',
        'internal_notes',
        'added_by',
        'removed_by',
    ];

    protected $casts = [
        'permissions' => 'array',
        'joined_at' => 'datetime',
        'last_active_at' => 'datetime',
        'removed_at' => 'datetime',
        'first_usage_at' => 'datetime',
        'last_usage_at' => 'datetime',
        'expires_at' => 'datetime',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_count' => 'integer',
    ];

    /**
     * Relationships
     */
    public function familyAccount(): BelongsTo
    {
        return $this->belongsTo(FamilyAccount::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'added_by');
    }

    public function removedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'removed_by');
    }
}

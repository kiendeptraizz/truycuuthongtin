<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FamilyAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'family_name',
        'family_code',
        'service_package_id',
        'owner_email',
        'owner_name',
        'max_members',
        'current_members',
        'activated_at',
        'expires_at',
        'status',
        'family_notes',
        'internal_notes',
        'family_settings',
        'created_by',
        'managed_by',
    ];

    protected $casts = [
        'activated_at' => 'datetime',
        'expires_at' => 'datetime',
        'family_settings' => 'array',
        'max_members' => 'integer',
        'current_members' => 'integer',
    ];

    /**
     * Boot method
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($familyAccount) {
            if (empty($familyAccount->family_code)) {
                $familyAccount->family_code = 'FAM-' . strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Relationships
     */
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(FamilyMember::class);
    }

    public function activeMembers(): HasMany
    {
        return $this->hasMany(FamilyMember::class)->where('status', 'active');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function managedBy(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'managed_by');
    }
}

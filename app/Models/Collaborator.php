<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collaborator extends Model
{
    protected $fillable = [
        'collaborator_code',
        'name',
        'email',
        'phone',
        'address',
        'status',
        'notes',
        'commission_rate',
    ];

    protected $casts = [
        'commission_rate' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($collaborator) {
            if (empty($collaborator->collaborator_code)) {
                $collaborator->collaborator_code = self::generateCollaboratorCode();
            }
        });
    }

    public static function generateCollaboratorCode(): string
    {
        $lastCollaborator = self::orderBy('id', 'desc')->first();
        $number = $lastCollaborator ? (int) substr($lastCollaborator->collaborator_code, 4) + 1 : 1;
        return 'COLL' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function services(): HasMany
    {
        return $this->hasMany(CollaboratorService::class);
    }

    public function getTotalValueAttribute(): float
    {
        return $this->services->sum(function ($service) {
            return $service->price * $service->quantity;
        });
    }

    public function getActiveServicesCountAttribute(): int
    {
        return $this->services->where('status', 'active')->count();
    }

    public function getTotalAccountsAttribute(): int
    {
        return $this->services->sum(function ($service) {
            return $service->accounts->count();
        });
    }
}

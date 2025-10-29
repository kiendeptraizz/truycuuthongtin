<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TargetGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'group_name',
        'group_link',
        'group_id',
        'topic',
        'total_members',
        'opening_date',
        'group_type',
        'status',
        'last_scanned_at',
        'description',
    ];

    protected $casts = [
        'opening_date' => 'date',
        'last_scanned_at' => 'datetime',
        'total_members' => 'integer',
    ];

    /**
     * Get group members
     */
    public function members()
    {
        return $this->hasMany(GroupMember::class);
    }

    /**
     * Get new members (not yet contacted)
     */
    public function newMembers()
    {
        return $this->hasMany(GroupMember::class)->where('status', 'new');
    }

    /**
     * Get contacted members
     */
    public function contactedMembers()
    {
        return $this->hasMany(GroupMember::class)->where('status', 'contacted');
    }

    /**
     * Get converted members
     */
    public function convertedMembers()
    {
        return $this->hasMany(GroupMember::class)->where('status', 'converted');
    }

    /**
     * Get campaigns targeting this group
     */
    public function campaigns()
    {
        return $this->hasMany(MessageCampaign::class, 'target_group_id');
    }

    /**
     * Get campaigns where this is the own group
     */
    public function ownGroupCampaigns()
    {
        return $this->hasMany(MessageCampaign::class, 'own_group_id');
    }

    /**
     * Get conversions to this group (if it's an own group)
     */
    public function conversions()
    {
        return $this->hasMany(ConversionLog::class, 'own_group_id');
    }

    /**
     * Update total members count
     */
    public function updateMembersCount(): void
    {
        $this->total_members = $this->members()->count();
        $this->save();
    }

    /**
     * Check if group is owned by us
     */
    public function isOwnGroup(): bool
    {
        return $this->group_type === 'own';
    }

    /**
     * Check if group is competitor's
     */
    public function isCompetitorGroup(): bool
    {
        return $this->group_type === 'competitor';
    }
}


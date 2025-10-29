<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GroupMember extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'target_group_id',
        'zalo_id',
        'display_name',
        'phone_number',
        'avatar_url',
        'status',
        'joined_at',
        'last_contacted_at',
        'contact_count',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'last_contacted_at' => 'datetime',
        'contact_count' => 'integer',
    ];

    /**
     * Get the target group
     */
    public function targetGroup()
    {
        return $this->belongsTo(TargetGroup::class);
    }

    /**
     * Get message logs
     */
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    /**
     * Get conversion log
     */
    public function conversionLog()
    {
        return $this->hasOne(ConversionLog::class);
    }

    /**
     * Check if member has been contacted
     */
    public function hasBeenContacted(): bool
    {
        return in_array($this->status, ['contacted', 'converted']);
    }

    /**
     * Check if member has converted
     */
    public function hasConverted(): bool
    {
        return $this->status === 'converted';
    }

    /**
     * Mark as contacted
     */
    public function markAsContacted(): void
    {
        $this->status = 'contacted';
        $this->last_contacted_at = now();
        $this->increment('contact_count');
    }

    /**
     * Mark as converted
     */
    public function markAsConverted(): void
    {
        $this->status = 'converted';
        $this->save();
    }

    /**
     * Get last message sent
     */
    public function lastMessage()
    {
        return $this->hasOne(MessageLog::class)->latestOfMany();
    }
}


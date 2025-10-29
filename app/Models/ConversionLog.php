<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'group_member_id',
        'message_log_id',
        'own_group_id',
        'joined_at',
        'days_to_convert',
        'notes',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'days_to_convert' => 'integer',
    ];

    /**
     * Boot method to calculate days to convert
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($conversion) {
            if ($conversion->message_log_id) {
                $messageLog = MessageLog::find($conversion->message_log_id);
                if ($messageLog && $messageLog->sent_at) {
                    $conversion->days_to_convert = $messageLog->sent_at->diffInDays($conversion->joined_at);
                }
            }
        });
    }

    /**
     * Get campaign
     */
    public function campaign()
    {
        return $this->belongsTo(MessageCampaign::class);
    }

    /**
     * Get group member
     */
    public function groupMember()
    {
        return $this->belongsTo(GroupMember::class);
    }

    /**
     * Get message log
     */
    public function messageLog()
    {
        return $this->belongsTo(MessageLog::class);
    }

    /**
     * Get own group
     */
    public function ownGroup()
    {
        return $this->belongsTo(TargetGroup::class, 'own_group_id');
    }
}


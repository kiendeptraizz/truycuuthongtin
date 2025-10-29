<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MessageCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'campaign_name',
        'target_group_id',
        'own_group_id',
        'message_template',
        'start_date',
        'end_date',
        'daily_target',
        'status',
        'total_sent',
        'total_delivered',
        'total_failed',
        'total_converted',
        'conversion_rate',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'daily_target' => 'integer',
        'total_sent' => 'integer',
        'total_delivered' => 'integer',
        'total_failed' => 'integer',
        'total_converted' => 'integer',
        'conversion_rate' => 'decimal:2',
    ];

    /**
     * Get target group
     */
    public function targetGroup()
    {
        return $this->belongsTo(TargetGroup::class);
    }

    /**
     * Get own group
     */
    public function ownGroup()
    {
        return $this->belongsTo(TargetGroup::class, 'own_group_id');
    }

    /**
     * Get message logs
     */
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class, 'campaign_id');
    }

    /**
     * Get today's message logs
     */
    public function todayMessageLogs()
    {
        return $this->hasMany(MessageLog::class, 'campaign_id')
            ->whereDate('sent_at', today());
    }

    /**
     * Get conversion logs
     */
    public function conversions()
    {
        return $this->hasMany(ConversionLog::class, 'campaign_id');
    }

    /**
     * Calculate and update conversion rate
     */
    public function updateConversionRate(): void
    {
        $this->total_converted = $this->conversions()->count();
        
        if ($this->total_delivered > 0) {
            $this->conversion_rate = ($this->total_converted / $this->total_delivered) * 100;
        } else {
            $this->conversion_rate = 0;
        }
        
        $this->save();
    }

    /**
     * Update statistics
     */
    public function updateStatistics(): void
    {
        $this->total_sent = $this->messageLogs()->count();
        $this->total_delivered = $this->messageLogs()->where('status', 'delivered')->count();
        $this->total_failed = $this->messageLogs()->where('status', 'failed')->count();
        $this->updateConversionRate();
    }

    /**
     * Get messages sent today count
     */
    public function getTodayMessageCountAttribute(): int
    {
        return $this->todayMessageLogs()->count();
    }

    /**
     * Check if campaign is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if daily target is reached
     */
    public function isDailyTargetReached(): bool
    {
        return $this->today_message_count >= $this->daily_target;
    }

    /**
     * Get remaining messages for today
     */
    public function getRemainingTodayAttribute(): int
    {
        return max(0, $this->daily_target - $this->today_message_count);
    }
}


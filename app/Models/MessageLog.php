<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessageLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'campaign_id',
        'zalo_account_id',
        'group_member_id',
        'message_content',
        'status',
        'error_message',
        'sent_at',
        'delivered_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    /**
     * Get campaign
     */
    public function campaign()
    {
        return $this->belongsTo(MessageCampaign::class);
    }

    /**
     * Get Zalo account
     */
    public function zaloAccount()
    {
        return $this->belongsTo(ZaloAccount::class);
    }

    /**
     * Get group member
     */
    public function groupMember()
    {
        return $this->belongsTo(GroupMember::class);
    }

    /**
     * Get conversion log if exists
     */
    public function conversion()
    {
        return $this->hasOne(ConversionLog::class);
    }

    /**
     * Mark as sent
     */
    public function markAsSent(): void
    {
        $this->status = 'sent';
        $this->sent_at = now();
        $this->save();
    }

    /**
     * Mark as delivered
     */
    public function markAsDelivered(): void
    {
        $this->status = 'delivered';
        $this->delivered_at = now();
        $this->save();
    }

    /**
     * Mark as failed
     */
    public function markAsFailed(string $errorMessage = null): void
    {
        $this->status = 'failed';
        $this->error_message = $errorMessage;
        $this->save();
    }

    /**
     * Check if message was successful
     */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['sent', 'delivered']);
    }
}


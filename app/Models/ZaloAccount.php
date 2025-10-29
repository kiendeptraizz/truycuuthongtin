<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Crypt;

class ZaloAccount extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'account_name',
        'email_or_phone',
        'password',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'status',
        'daily_message_limit',
        'messages_sent_today',
        'last_message_date',
        'notes',
    ];

    protected $casts = [
        'token_expires_at' => 'datetime',
        'last_message_date' => 'date',
        'daily_message_limit' => 'integer',
        'messages_sent_today' => 'integer',
    ];

    protected $hidden = [
        'password',
        'access_token',
        'refresh_token',
    ];

    /**
     * Set encrypted password
     */
    public function setPasswordAttribute($value)
    {
        if ($value) {
            $this->attributes['password'] = Crypt::encryptString($value);
        }
    }

    /**
     * Get decrypted password
     */
    public function getPasswordAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Set encrypted access token
     */
    public function setAccessTokenAttribute($value)
    {
        if ($value) {
            $this->attributes['access_token'] = Crypt::encryptString($value);
        }
    }

    /**
     * Get decrypted access token
     */
    public function getAccessTokenAttribute($value)
    {
        if ($value) {
            try {
                return Crypt::decryptString($value);
            } catch (\Exception $e) {
                return null;
            }
        }
        return null;
    }

    /**
     * Check if account can send messages today
     */
    public function canSendMessage(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Reset counter if it's a new day
        if ($this->last_message_date && $this->last_message_date->isToday() === false) {
            $this->messages_sent_today = 0;
            $this->save();
        }

        return $this->messages_sent_today < $this->daily_message_limit;
    }

    /**
     * Increment message count
     */
    public function incrementMessageCount(): void
    {
        $this->increment('messages_sent_today');
        $this->last_message_date = now()->toDateString();
        $this->save();
    }

    /**
     * Get message logs
     */
    public function messageLogs()
    {
        return $this->hasMany(MessageLog::class);
    }

    /**
     * Get today's message logs
     */
    public function todayMessageLogs()
    {
        return $this->hasMany(MessageLog::class)
            ->whereDate('sent_at', today());
    }

    /**
     * Get remaining messages for today
     */
    public function getRemainingMessagesAttribute(): int
    {
        return max(0, $this->daily_message_limit - $this->messages_sent_today);
    }
}


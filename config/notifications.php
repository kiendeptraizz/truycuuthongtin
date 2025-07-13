<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Notification Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for various notification channels
    | used by the application for sending reminders and alerts.
    |
    */

    'email' => [
        'enabled' => env('NOTIFICATION_EMAIL_ENABLED', false),
        'recipient' => env('NOTIFICATION_EMAIL_RECIPIENT', 'admin@example.com'),
        'from_name' => env('NOTIFICATION_EMAIL_FROM_NAME', 'Content Scheduler'),
        'from_email' => env('NOTIFICATION_EMAIL_FROM', 'noreply@example.com'),
    ],

    'telegram' => [
        'enabled' => env('NOTIFICATION_TELEGRAM_ENABLED', false),
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id' => env('TELEGRAM_CHAT_ID'),
    ],

    'zalo' => [
        'enabled' => env('NOTIFICATION_ZALO_ENABLED', false),
        'app_id' => env('ZALO_APP_ID'),
        'app_secret' => env('ZALO_APP_SECRET'),
        'access_token' => env('ZALO_ACCESS_TOKEN'),
        'phone_number' => env('ZALO_PHONE_NUMBER'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Reminder Settings
    |--------------------------------------------------------------------------
    |
    | Configure when and how reminders should be sent
    |
    */

    'reminder' => [
        'hours_before' => env('REMINDER_HOURS_BEFORE', 1), // Send reminder X hours before scheduled time
        'max_attempts' => env('REMINDER_MAX_ATTEMPTS', 3), // Maximum retry attempts
        'retry_delay' => env('REMINDER_RETRY_DELAY', 5), // Minutes between retries
    ],
];

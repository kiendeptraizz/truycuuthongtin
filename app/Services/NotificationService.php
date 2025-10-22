<?php

namespace App\Services;

use App\Models\ContentPost;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send notification for content reminder
     */
    public function sendContentReminder(ContentPost $post): bool
    {
        try {
            $message = $this->buildReminderMessage($post);

            // Log the notification
            Log::info('Content Reminder Notification', [
                'post_id' => $post->id,
                'title' => $post->title,
                'scheduled_at' => $post->scheduled_at,
                'message' => $message
            ]);

            // Send notifications through available channels
            $results = [];

            if (config('notifications.email.enabled', false)) {
                $results['email'] = $this->sendEmailNotification($message, $post);
            }

            if (config('notifications.zalo.enabled', false)) {
                $results['zalo'] = $this->sendZaloNotification($message, $post);
            }

            // If no notification channels are configured, just log
            if (empty($results)) {
                Log::info('No notification channels configured, reminder logged only');
                return true;
            }

            // Check if at least one notification was successful
            return in_array(true, $results);
        } catch (\Exception $e) {
            Log::error('Notification Service Error', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Build reminder message
     */
    private function buildReminderMessage(ContentPost $post): string
    {
        $timeUntil = $post->scheduled_at->diffForHumans();
        $groups = implode(', ', $post->target_groups);

        return "🔔 Nhắc nhở đăng bài\n\n" .
            "📝 Tiêu đề: {$post->title}\n" .
            "⏰ Thời gian: {$post->scheduled_at->format('d/m/Y H:i')} ({$timeUntil})\n" .
            "👥 Nhóm: {$groups}\n\n" .
            "📄 Nội dung:\n{$post->content}";
    }

    /**
     * Send email notification
     */
    private function sendEmailNotification(string $message, ContentPost $post): bool
    {
        try {
            // TODO: Implement email sending
            // This is a placeholder for actual email implementation
            Log::info('Email notification would be sent', [
                'to' => config('notifications.email.recipient'),
                'subject' => "Nhắc nhở đăng bài: {$post->title}",
                'message' => $message
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Email notification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send Zalo notification
     */
    private function sendZaloNotification(string $message, ContentPost $post): bool
    {
        try {
            // TODO: Implement Zalo API integration
            // This is a placeholder for actual Zalo implementation
            Log::info('Zalo notification would be sent', [
                'message' => $message,
                'post_id' => $post->id
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Zalo notification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}

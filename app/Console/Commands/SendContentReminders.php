<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentPost;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class SendContentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminders for content posts that are scheduled to be posted soon';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for content posts that need reminders...');

        $postsNeedingReminder = ContentPost::needingReminder()->get();

        if ($postsNeedingReminder->isEmpty()) {
            $this->info('No posts need reminders at this time.');
            return 0;
        }

        $this->info("Found {$postsNeedingReminder->count()} posts that need reminders.");

        foreach ($postsNeedingReminder as $post) {
            $this->sendReminder($post);
        }

        $this->info('Reminder process completed.');
        return 0;
    }

    /**
     * Send reminder for a specific post
     */
    private function sendReminder(ContentPost $post)
    {
        try {
            $notificationService = new NotificationService();
            $success = $notificationService->sendContentReminder($post);

            if ($success) {
                // Mark notification as sent
                $post->update(['notification_sent' => true]);
                $this->line("✓ Reminder sent for: {$post->title}");
            } else {
                $this->error("✗ Failed to send reminder for: {$post->title}");
            }
        } catch (\Exception $e) {
            $this->error("Failed to send reminder for post {$post->id}: {$e->getMessage()}");
            Log::error('Content Reminder Failed', [
                'post_id' => $post->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}

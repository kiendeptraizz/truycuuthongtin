<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ContentPost;
use Illuminate\Support\Facades\Log;

class CheckContentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'content:check-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for content posts that need reminders and mark overdue posts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking content reminders...');

        // Get posts that need reminders (within 1 hour and not notified yet)
        $reminderPosts = ContentPost::needingReminder()->get();

        foreach ($reminderPosts as $post) {
            $this->sendReminder($post);
            $post->update(['notification_sent' => true]);
            $this->info("Reminder sent for: {$post->title}");
        }

        // Mark overdue posts
        $overduePosts = ContentPost::overdue()->get();

        foreach ($overduePosts as $post) {
            $post->update(['status' => 'overdue']);
            $this->warn("Marked as overdue: {$post->title}");
        }

        $this->info("Processed {$reminderPosts->count()} reminders and {$overduePosts->count()} overdue posts.");

        return Command::SUCCESS;
    }

    /**
     * Send reminder notification
     */
    private function sendReminder(ContentPost $post)
    {
        // Log the reminder (in a real app, you might send email, Slack notification, etc.)
        Log::info("Content reminder: {$post->title} scheduled for {$post->scheduled_at->format('Y-m-d H:i')}");

        // Here you could implement:
        // - Email notifications
        // - Slack/Discord webhooks
        // - Browser notifications
        // - SMS notifications

        // Example: Simple browser notification (would need frontend implementation)
        // You could store these in a notifications table and display them in the admin panel
    }
}

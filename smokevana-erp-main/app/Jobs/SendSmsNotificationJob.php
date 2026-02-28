<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Log;

class SendSmsNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $notifiable;
    protected $notificationId;

    /**
     * Create a new job instance.
     *
     * @param $notifiable
     * @param $notificationId
     */
    public function __construct($notifiable, $notificationId)
    {
        $this->notifiable = $notifiable;
        $this->notificationId = $notificationId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $notification = DatabaseNotification::find($this->notificationId);
        if (!$notification || $notification->read_at) {
            // Already read or doesn't exist
            return;
        }
        $data = $notification->data;
        // Send SMS (replace with your SMS provider logic)
        // Example: SmsService::send($this->notifiable->mobile, $data['body'] ?? 'You have a new notification.');
        // For demo, just log it
        Log::info('SMS sent to ' . ($this->notifiable->mobile ?? 'N/A') . ': ' . ($data['body'] ?? 'You have a new notification.'));
    }
}

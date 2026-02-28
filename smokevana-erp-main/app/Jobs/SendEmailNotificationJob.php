<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Mail;

class SendEmailNotificationJob implements ShouldQueue
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
        // Send email (you may want to use a Mailable class for real implementation)
        Mail::raw($data['body'] ?? 'You have a new notification.', function ($message) use ($data) {
            $message->to($this->notifiable->email)
                ->subject($data['title'] ?? 'Notification');
        });
    }
}

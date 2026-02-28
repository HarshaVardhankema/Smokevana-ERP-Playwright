<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Notifications\DatabaseNotification;
use App\Services\FireBaseServices;

class SendFcmNotificationJob implements ShouldQueue
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
        // Send FCM notification
        $fcmService = new FireBaseServices();
        $data = $notification->data;
        // You may need to adjust payload structure as per your FCM logic
        $fcmService->sendNotification([
            'to' => $this->notifiable->fcmToken,
            'notification' => [
                'title' => $data['title'] ?? 'Notification',
                'body' => $data['body'] ?? '',
            ],
            'data' => $data,
            'priority' => 'high',
        ]);
    }
}

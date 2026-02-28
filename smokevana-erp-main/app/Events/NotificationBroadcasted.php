<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationBroadcasted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $notifiable;
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param $notifiable
     * @param $notification
     * @return void
     */
    public function __construct($notifiable, $notification)
    {
        $this->notifiable = $notifiable;
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Support both User and Contact
        $type = class_basename($this->notifiable);
        return [
            new PrivateChannel("App.{$type}.{$this->notifiable->id}")
        ];
    }

    /**
     * The data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'notification' => $this->notification,
        ];
    }
}

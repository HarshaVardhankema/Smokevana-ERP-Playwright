<?php

namespace App\Events;

use App\TicketActivity;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TicketMessageEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $activity;
    public $ticketId;
    public $messageData;

    /**
     * Create a new event instance.
     *
     * @param TicketActivity $activity
     * @return void
     */
    public function __construct(TicketActivity $activity)
    {
        $this->activity = $activity;
        $this->ticketId = $activity->ticket_id;
        
        // Prepare message data for broadcasting
        $this->messageData = [
            'id' => $activity->id,
            'ticket_id' => $activity->ticket_id,
            'user_id' => $activity->user_id,
            'activity_type' => $activity->activity_type,
            'activity_details' => $activity->activity_details,
            'attachment' => $activity->attachment,
            'file_url' => $activity->file_url,
            'created_at' => $activity->created_at->toISOString(),
            'user' => [
                'id' => $activity->user->id,
                'name' => $activity->user->first_name . ' ' . $activity->user->last_name,
                'first_name' => $activity->user->first_name,
                'last_name' => $activity->user->last_name,
            ],
            'is_image' => $activity->isImage(),
            'file_extension' => $activity->getFileExtension(),
        ];
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        // Use a private channel for each ticket to ensure only authorized users can listen
        return new PrivateChannel('ticket.' . $this->ticketId);
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'activity' => $this->messageData,
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'ticket.message';
    }
}


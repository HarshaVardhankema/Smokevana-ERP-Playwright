<?php

namespace App\Notifications;

use App\Lead;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $lead;
    protected $creator;
    protected $assignedUser;

    /**
     * Create a new notification instance.
     *
     * @param Lead $lead
     * @param User $creator
     * @param User|null $assignedUser
     */
    public function __construct(Lead $lead, User $creator, User $assignedUser = null)
    {
        $this->lead = $lead;
        $this->creator = $creator;
        $this->assignedUser = $assignedUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $mailMessage = (new MailMessage)
            ->subject('New Lead Created - ' . $this->lead->store_name)
            ->greeting('Hello!')
            ->line('A new lead has been created in the system.')
            ->line('**Lead Details:**')
            ->line('Store Name: ' . $this->lead->store_name)
            ->line('Contact: ' . ($this->lead->contact_name ?: 'N/A'))
            ->line('Phone: ' . ($this->lead->contact_phone ?: 'N/A'))
            ->line('Location: ' . $this->lead->full_address)
            ->line('Created by: ' . $this->creator->first_name . ' ' . $this->creator->last_name);

        if ($this->assignedUser) {
            $mailMessage->line('Assigned to: ' . $this->assignedUser->first_name . ' ' . $this->assignedUser->last_name);
        }

        if ($this->lead->estimated_value) {
            $mailMessage->line('Estimated Value: ' . $this->lead->currency . ' ' . number_format($this->lead->estimated_value, 2));
        }

        $mailMessage->action('View Lead', url('/leads'))
            ->line('Thank you for using our application!');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->store_name,
            'creator_id' => $this->creator->id,
            'creator_name' => $this->creator->first_name . ' ' . $this->creator->last_name,
            'assigned_user_id' => $this->assignedUser ? $this->assignedUser->id : null,
            'assigned_user_name' => $this->assignedUser ? $this->assignedUser->first_name . ' ' . $this->assignedUser->last_name : null,
            'message' => 'New lead "' . $this->lead->store_name . '" has been created' . 
                        ($this->assignedUser ? ' and assigned to ' . $this->assignedUser->first_name . ' ' . $this->assignedUser->last_name : ''),
            'type' => 'lead_created',
            'priority' => $this->lead->priority,
            'status' => $this->lead->lead_status,
            'created_at' => now()
        ];
    }
}

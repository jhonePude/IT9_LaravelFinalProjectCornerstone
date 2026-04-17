<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEventPublished extends Notification
{
    use Queueable;

    protected $event;

    /**
     * Create a new notification instance.
     */
    public function __construct($event)
    {
        $this->event = $event;
    }

    /**
     * Get the notification's delivery channels.
     * We use 'database' so it shows up in the portal.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     * This is what gets saved in the "data" column of your notifications table.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->Event_Id,
            'title'    => $this->event->Title,
            'message'  => 'A new event "' . $this->event->Title . '" has been scheduled!',
            'date'     => $this->event->Event_Date,
        ];
    }
}

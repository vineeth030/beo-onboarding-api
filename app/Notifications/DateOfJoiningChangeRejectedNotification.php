<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DateOfJoiningChangeRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }
    
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Date of joining changed rejected.',
            'message' => 'Date of joining changed rejected!',
            'employee_id' => $notifiable->id
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Date of Joining Change Request - Rejected')
            ->greeting(' ')
            ->line('Hi,')
            ->line("Your request to change the Date of Joining has been rejected by the HR team.")
            ->line('Please contact the HR team for more information.')
            ->line('')
            ->line('')
            ->line('Thanks,')
            ->line('HR Team')
            ->line('BEO Software')
            ->salutation(' ');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

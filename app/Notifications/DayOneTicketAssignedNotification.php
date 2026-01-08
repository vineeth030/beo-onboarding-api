<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DayOneTicketAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Employee $employee)
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
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Day one ticket assigned')
            ->greeting(' ')
            ->line("Hi {$this->employee->first_name},")
            ->line("A day one ticket has been assigned to you. Please login to the portal and check the day one ticket.")
            ->line('Please contact the HR team for more information.')
            ->line('')
            ->line('')
            ->line('Thanks,')
            ->line('HR Team')
            ->line('BEO Software')
            ->salutation(' ');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'A day one ticket has been assigned to you.',
            'message' => 'A day one ticket has been assigned to you!',
            'employee_id' => $notifiable->id
        ];
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

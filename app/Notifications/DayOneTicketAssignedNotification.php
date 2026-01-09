<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DayOneTicketAssignedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName)
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
            ->subject('Day One Ticket Assigned')
            ->greeting(' ')
            ->line("Hi $this->employeeName,")
            ->line("A day one ticket has been assigned to you.")
            ->line('Please log in to the onboarding portal to view the ticket details.')
            ->line('If you have any questions, please contact the HR team.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>HR Team<br>BEO Software'))
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

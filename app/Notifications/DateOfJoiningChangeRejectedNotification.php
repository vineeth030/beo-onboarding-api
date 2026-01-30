<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DateOfJoiningChangeRejectedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName, public string $requestedDateOfJoining)
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
            ->line("Hello $this->employeeName,")
            ->line("Your request to change the Date of Joining to $this->requestedDateOfJoining has been reviewed and is not approved at this time.")
            ->line('Please continue with the originally scheduled Date of Joining as communicated earlier.')
            ->line('If you need further clarification, feel free to contact the HR team.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
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

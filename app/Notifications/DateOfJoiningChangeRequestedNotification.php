<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DateOfJoiningChangeRequestedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
            public string $requestedDateOfJoining,
            public string $requestedEmployeeName
        )
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
            ->subject('Request to change Date of Joining from candidate')
            ->greeting(" ")
            ->line('Hello,')
            ->line(new HtmlString(
                "The candidate, <strong>{$this->requestedEmployeeName}</strong> has requested a change to their Date of Joining to <strong>{$this->requestedDateOfJoining}</strong>."
            ))
            ->line('Please log in to the onboarding portal to review the request and take the necessary action.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
            ->salutation(' ');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => 'A change in Date of joining is requested.',
            'message' => "There has been a change in Date of joining request from $this->requestedEmployeeName. He wants to change the date to $this->requestedDateOfJoining.",
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

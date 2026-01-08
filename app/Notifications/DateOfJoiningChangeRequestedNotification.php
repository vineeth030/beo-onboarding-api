<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

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
            ->greeting("Date of Joining change request")
            ->line("There has been a request from candidate named $this->requestedEmployeeName to change the Date of Joining to $this->requestedDateOfJoining.")
            ->line('Please contact the candidate for more information.')
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

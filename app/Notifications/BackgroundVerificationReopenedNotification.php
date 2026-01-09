<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class BackgroundVerificationReopenedNotification extends Notification
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

    public function toDatabase($notifiable)
    {
        return [
            'title' => "Backgroud verification form has been reopened.",
            'message' => "Backgroud verification form has been reopened for updates.",
            'employee_id' => $notifiable->id
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Background Verification Form - Reopened')
            ->greeting(' ')
            ->line("Hi $this->employeeName,")
            ->line("Your background verification form has been reopened for updates.")
            ->line('Please log in to the onboarding portal and resubmit the form with the required changes.')
            ->line('If you have any questions, please contact the HR team.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>HR Team<br>BEO Software'))
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

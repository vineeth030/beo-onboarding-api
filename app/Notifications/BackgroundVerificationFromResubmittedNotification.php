<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class BackgroundVerificationFromResubmittedNotification extends Notification
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
        return ['database','mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Background Verification Form - Resubmitted')
            ->greeting(" ")
            ->line('Hi Team,')
            ->line("The candidate, $this->employeeName has resubmitted the background verification form after making the requested changes.")
            ->line('Please log in to the onboarding portal to review the updated information and continue the verification process.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO ONBOARDING'))
            ->salutation(' ');
    }

    public function toDatabase($notifiable)
    {
        return [
            'title' => "Background verification form has been resubmitted by $this->employeeName.",
            'message' => "Background verification form has been resubmitted by $this->employeeName.",
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

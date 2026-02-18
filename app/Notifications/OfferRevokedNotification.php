<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OfferRevokedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName, public ?string $reason = null)
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
        $message = (new MailMessage)
            ->subject('Offer Revoked')
            ->greeting(' ')
            ->line("Hello $this->employeeName,")
            ->line('We regret to inform you that your offer has been revoked by HR.');

        if ($this->reason) {
            $message->line("Reason: $this->reason");
        }

        $message->line('If you have any questions or concerns, please contact the HR team.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
            ->salutation(' ');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Your offer has been revoked',
            'message' => 'Your offer has been revoked by HR.'.($this->reason ? " Reason: $this->reason" : ''),
            'employee_id' => $notifiable->id,
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

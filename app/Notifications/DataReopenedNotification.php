<?php

namespace App\Notifications;

use App\Enums\ResubmissionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class DataReopenedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName, public ResubmissionType $type)
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
        $typeLabel = $this->type->getLabel();

        return (new MailMessage)
            ->subject('Data Reopened for Review - '.ucfirst($typeLabel))
            ->greeting(' ')
            ->line("Hello {$this->employeeName},")
            ->line("Your {$typeLabel} has been reopened for review by the HR team.")
            ->line('This means additional information or corrections may be required. Please log in to the onboarding portal to review any comments and make the necessary updates.')
            ->line('If you have any questions, please contact the HR team.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
            ->salutation(' ');
    }

    public function toDatabase($notifiable): array
    {
        $typeLabel = $this->type->getLabel();

        return [
            'title' => "Your {$typeLabel} has been reopened for review.",
            'message' => "Your {$typeLabel} has been reopened by the HR team. Please review and make necessary updates.",
            'employee_id' => $notifiable->id ?? null,
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

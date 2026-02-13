<?php

namespace App\Notifications;

use App\Enums\ResubmissionType;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class CandidateDataResubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public string $employeeName, public ResubmissionType $resubmissionType)
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
        $typeLabel = $this->resubmissionType->getLabel();

        return (new MailMessage)
            ->subject('Candidate Data Resubmitted - '.ucfirst($typeLabel))
            ->greeting(' ')
            ->line('Hello,')
            ->line("The candidate, {$this->employeeName} has resubmitted their {$typeLabel} after making the requested changes.")
            ->line('Please log in to the onboarding portal to review the updated information and continue the verification process.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
            ->salutation(' ');
    }

    public function toDatabase($notifiable): array
    {
        $typeLabel = $this->resubmissionType->getLabel();

        return [
            'title' => "{$this->employeeName} has resubmitted their {$typeLabel}.",
            'message' => "{$this->employeeName} has resubmitted their {$typeLabel}.",
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

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
    public function __construct(
            public string $employeeName, 
            public string $requestedDateOfJoining, 
            public bool $isProposedDate = false
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
        return ['mail', 'database'];
    }
    
    public function toDatabase($notifiable): array
    {
        return $this->isProposedDate ? [
            'title' => 'Proposed date of joining - Rejected',
            'message' => "We regret to inform you that your proposed date of joining, $this->requestedDateOfJoining has been declined. ",
            'employee_id' => $notifiable->id
        ] : [
            'title' => 'Request to change date of joining - Rejected',
            'message' => "We regret to inform you that your request to change the date of joining to $this->requestedDateOfJoining has been declined. ",
            'employee_id' => $notifiable->id
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return $this->isProposedDate ? $this->mailMessageForProposedDate() : $this->mailMessageForRequestedDate();
    }

    private function mailMessageForProposedDate(): MailMessage{

        return (new MailMessage)
            ->subject('Proposed date of joining - Rejected')
            ->greeting(" ")
            ->line("Hello $this->employeeName,")
            ->line("We regret to inform you that your proposed date of joining, $this->requestedDateOfJoining has been declined. ")
            ->line('Kindly reach out to your POC (Recruiter) for further assistance.')
            ->line('')
            ->line('')
            ->line(new HtmlString('Thanks,<br>BEO HR Team'))
            ->salutation(' ');
    }

    private function mailMessageForRequestedDate(): MailMessage{

        return (new MailMessage)
            ->subject('Request to change date of joining - Rejected')
            ->greeting(" ")
            ->line("Hello $this->employeeName,")
            ->line("We regret to inform you that your request to change the date of joining to $this->requestedDateOfJoining has been declined. ")
            ->line('Kindly reach out to your POC (Recruiter) for further assistance.')
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

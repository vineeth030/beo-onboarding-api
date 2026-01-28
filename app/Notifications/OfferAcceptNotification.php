<?php

namespace App\Notifications;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class OfferAcceptNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public Employee $employee)
    {
        $employee->loadMissing('designation');
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

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'Offer accepted.',
            'message' => 'Offer accepted by employee!',
            'employee_id' => $notifiable->id
        ];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        if (str_contains($this->employee->joining_date, "month")) {
            $line = "The proposed joining date is " . $this->employee->joining_date . ". Should there be any change to this date, the candidate will inform us in advance."; 
        }else{
            $line = "The joining date will be " . $this->employee->joining_date . "."; 
        }
        
        return (new MailMessage)
            ->subject('Offer accepted by candidate')
            ->greeting(' ')
            ->line("Hello,")
            ->line("This is to inform you that the candidate, " . $this->employee->full_name . ", has accepted the offer for the position of " . $this->employee->designation?->name . ".")
            ->line($line)
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

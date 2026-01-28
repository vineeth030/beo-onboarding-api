<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OfferLetterSend extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        private string $offerLetterFilePath = "",
        private bool $isClient = false,
        private string $content = "",
        public ?Employee $employee
    ){}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Offer Letter Send',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.offers.send',
            with: [
                'isClient' => $this->isClient,
                'content' => $this->content,
                'employee' => $this->employee
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment> | null
     */
    public function attachments(): ?array
    {
        if ($this->hasAttachments()) {
            return [
                Attachment::fromPath($this->offerLetterFilePath)
                    ->withMime('application/pdf')
            ];
        }

        return null;
    }

    /**
     * Check if there are any attachments.
     *
     * @return bool
     */
    private function hasAttachments(): bool
    {
        // Check if the offer letter file path is set
        return !empty($this->offerLetterFilePath);
    }
}

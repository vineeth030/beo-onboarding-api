<?php

namespace App\Mail;

use App\Models\Employee;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SendDocumentsToAccountManagersMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(public Employee $employee)
    {
        $employee->loadMissing('documents');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "New employee onboarded — {$this->employee->full_name}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.employees.documents-to-account-managers',
            with: [
                'employee' => $this->employee,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return $this->employee->documents
            ->filter(function ($document): bool {
                $relativePath = $this->relativePath($document->file_path);

                return ! empty($relativePath) && Storage::disk('public')->exists($relativePath);
            })
            ->map(function ($document): Attachment {
                $relativePath = $this->relativePath($document->file_path);
                $extension = pathinfo($relativePath, PATHINFO_EXTENSION);

                return Attachment::fromStorageDisk('public', $relativePath)
                    ->as($extension ? "{$document->type}.{$extension}" : $document->type);
            })
            ->values()
            ->all();
    }

    /**
     * Convert a stored public path (e.g. "/storage/documents/1/file.pdf")
     * into a path relative to the public disk root.
     */
    private function relativePath(?string $filePath): string
    {
        return ltrim(Str::after((string) $filePath, '/storage/'), '/');
    }
}

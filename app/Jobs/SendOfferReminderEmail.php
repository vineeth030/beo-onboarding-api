<?php

namespace App\Jobs;

use App\Mail\OfferReminderMail;
use App\Models\Offer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOfferReminderEmail implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(public int $offerId)
    {
        // Pass only the ID, not the full model, to avoid serialization issues
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $offer = Offer::with(['employee'])->find($this->offerId);

        if (! $offer) {
            Log::warning("Offer #{$this->offerId} not found. Skipping reminder.");
            return;
        }
        
        if ($offer->is_accepted || $offer->is_declined || $offer->is_revoked) {
            Log::info("Offer #{$this->offerId} is no longer pending. Skipping reminder.");
            return;
        }

        if (! $offer->employee || ! $offer->employee->email) {
            Log::error("Offer #{$this->offerId} has no valid employee email. Skipping reminder.");
            return;
        }

        try {
            Mail::to($offer->employee->email)->send(new OfferReminderMail($offer->employee));

            $offer->update([
                'last_reminder_sent_at' => now(),
            ]);

            Log::info("Reminder sent successfully for Offer #{$this->offerId} to {$offer->employee->email}");
        } catch (\Exception $e) {
            // Log error and allow job to retry (based on $tries)
            Log::error("Failed to send reminder for Offer #{$this->offerId}: {$e->getMessage()}");

            throw $e;
        }
    }
}

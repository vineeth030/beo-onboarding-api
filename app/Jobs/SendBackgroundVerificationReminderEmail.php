<?php

namespace App\Jobs;

use App\Mail\BackgroundVerificationReminderMail;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\Offer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendBackgroundVerificationReminderEmail implements ShouldQueue
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
    public function __construct(public Offer $offer)
    {
        // Pass only the ID, not the full model, to avoid serialization issues
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employee = Employee::with(['designation', 'activeOffer'])->find($this->offer->employee->id);

        if ($this->offer->is_revoked || $this->offer->is_declined) {
            Log::info("Employee #{$employee->id} offer revoked/declined. Skipping reminder.");

            return;
        }

        try {
            Mail::to($employee->email)->send(new BackgroundVerificationReminderMail($employee));

            $this->offer->update([
                'last_background_verification_reminder_sent_at' => now(),
            ]);

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => 0,
                'user_type' => 'hr',
                'type' => 'update.backgroundverification.reminder.sent',
                'title' => "A background verification reminder email has been sent to $employee->full_name.",
            ]);

            Log::info("BV reminder sent successfully for Employee #{$employee->id} to {$employee->email}");
        } catch (\Exception $e) {
            // Log error and allow job to retry (based on $tries)
            Log::error("Failed to send BV reminder for Employee #{$employee->id}: {$e->getMessage()}");

            throw $e;
        }
    }
}

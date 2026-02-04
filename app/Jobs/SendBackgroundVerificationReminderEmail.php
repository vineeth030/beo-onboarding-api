<?php

namespace App\Jobs;

use App\Mail\BackgroundVerificationReminderMail;
use App\Models\Employee;
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
    public function __construct(public int $employeeId)
    {
        // Pass only the ID, not the full model, to avoid serialization issues
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $employee = Employee::with(['designation', 'activeOffer'])->find($this->employeeId);

        if ($employee->activeOffer && ($employee->activeOffer->is_revoked || $employee->activeOffer->is_declined)) {
            Log::info("Employee #{$this->employeeId} offer revoked/declined. Skipping reminder.");

            return;
        }

        try {
            Mail::to($employee->email)->send(new BackgroundVerificationReminderMail($employee));

            $employee->update([
                'last_background_verification_reminder_sent_at' => now(),
            ]);

            Log::info("BV reminder sent successfully for Employee #{$this->employeeId} to {$employee->email}");
        } catch (\Exception $e) {
            // Log error and allow job to retry (based on $tries)
            Log::error("Failed to send BV reminder for Employee #{$this->employeeId}: {$e->getMessage()}");

            throw $e;
        }
    }
}

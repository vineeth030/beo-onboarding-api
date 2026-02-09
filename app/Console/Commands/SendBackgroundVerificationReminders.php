<?php

namespace App\Console\Commands;

use App\Jobs\SendBackgroundVerificationReminderEmail;
use App\Models\Employee;
use App\Models\Offer;
use Illuminate\Console\Command;

class SendBackgroundVerificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'background-verification:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to candidates who haven\'t submitted their background verification forms (every 4 days)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting background verification reminder process...');

        $fourDaysAgo = now()->subDays(4);

        $offers = Offer::query()
            ->where('is_revoked', false)
            ->where('is_declined', false)
            ->where('status', 2)
            ->where(function ($query) use ($fourDaysAgo) {
                $query->where('last_background_verification_reminder_sent_at', '<=', $fourDaysAgo)
                    ->orWhereNull('last_background_verification_reminder_sent_at');
            })
            ->get();

        if ($offers->isEmpty()) {
            $this->info('No offers require reminders at this time.');

            return Command::SUCCESS;
        }


        $dispatchedCount = 0;
        foreach ($offers as $offer) {

            SendBackgroundVerificationReminderEmail::dispatch($offer->employee?->id);
            $dispatchedCount++;

            $this->line("- Queued reminder for Employee #{$offer->employee?->id} ({$offer->employee?->full_name})");
        }

        $this->info("Successfully dispatched {$dispatchedCount} reminder email(s) to the queue.");

        return Command::SUCCESS;
    }
}

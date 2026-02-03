<?php

namespace App\Console\Commands;

use App\Jobs\SendOfferReminderEmail;
use App\Models\Offer;
use Illuminate\Console\Command;

class SendOfferReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'offers:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send reminder emails to candidates who haven\'t accepted their offers (every 2 days)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting offer reminder process...');

        $twoDaysAgo = now()->subDays(2);

        $offers = Offer::query()
            ->with(['employee']) // Eager load employee for job processing
            ->where('is_accepted', false)
            ->where('is_declined', false)
            ->where('is_revoked', false)
            ->where(function ($query) use ($twoDaysAgo) {
                // Never sent a reminder, but offer is at least 2 days old
                $query->whereNull('last_reminder_sent_at')
                    ->where('created_at', '<=', $twoDaysAgo);
            })
            ->orWhere(function ($query) use ($twoDaysAgo) {
                // Last reminder was sent 2+ days ago
                $query->where('is_accepted', false)
                    ->where('is_declined', false)
                    ->where('is_revoked', false)
                    ->whereNotNull('last_reminder_sent_at')
                    ->where('last_reminder_sent_at', '<=', $twoDaysAgo);
            })
            ->get();

        if ($offers->isEmpty()) {
            $this->info('No offers require reminders at this time.');

            return Command::SUCCESS;
        }

        $this->info("Found {$offers->count()} offer(s) requiring reminders.");

        $dispatchedCount = 0;
        foreach ($offers as $offer) {

            SendOfferReminderEmail::dispatch($offer->id);
            $dispatchedCount++;

            $this->line("- Queued reminder for Offer #{$offer->id} (Employee: {$offer->employee->full_name})");
        }

        $this->info("Successfully dispatched {$dispatchedCount} reminder email(s) to the queue.");

        return Command::SUCCESS;
    }
}

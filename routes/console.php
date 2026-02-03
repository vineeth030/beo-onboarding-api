<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule offer reminder emails to run daily at 9:00 AM
// The command itself filters offers that need reminders (every 2 days)
Schedule::command('offers:send-reminders')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->onOneServer() // Ensures job runs on only one server in multi-server setup
    ->runInBackground();

<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\BackgroundVerificationFromSubmittedNotification;

class BackgroundVerificationFormSubmittedAction
{
    public function execute(Employee $employee): void
    {
        $employee->update(['status' => 2]);

        $hrEmails = config('app.hr_emails', []);

        User::whereIn('email', $hrEmails)->each(function ($admin) use ($employee) {
            $admin->notify(
                new BackgroundVerificationFromSubmittedNotification($employee->first_name.' '.$employee->last_name)
            );
        });

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'update.backgroundverification.submitted',
            'title' => "Backgroud verification form has been submitted by $employee->full_name",
        ]);
    }
}

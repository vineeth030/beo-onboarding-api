<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\BackgroundVerificationFromResubmittedNotification;

class BackgroundVerificationFormResubmittedAction
{
    public function execute(Employee $employee): void
    {
        //$employee->update(['is_open' => 1]);

        User::where('role', 'admin')->each(function ($admin) use ($employee) {
            $admin->notify(
                new BackgroundVerificationFromResubmittedNotification($employee->first_name . ' ' . $employee->last_name)
            );
        });

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'update.backgroundverification.resubmitted',
            'title' => "Backgroud verification form has been resubmitted by $employee->first_name $employee->last_name",
        ]);
    }
}

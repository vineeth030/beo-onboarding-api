<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;
use App\Notifications\BackgroundVerificationReopenedNotification;

class BackgroundVerificationReopenedAction
{
    public function execute(Employee $employee): void
    {
        $employee->update(['is_open' => 1]);

        $employee->user->notify(
            new BackgroundVerificationReopenedNotification($employee->first_name . ' ' . $employee->last_name)
        );

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'update.backgroundverification.reopened',
            'title' => "Backgroud verification of $employee->fullname has been reopened by " . auth()->user()->name,
        ]);
    }
}

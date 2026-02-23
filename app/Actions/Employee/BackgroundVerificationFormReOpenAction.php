<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;

class BackgroundVerificationFormReOpenAction
{
    public function execute(Employee $employee): void
    {
        $employee->update(['status' => 1]);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => 'update.backgroundverification.submitted',
            'title' => "Backgroud verification form reopened for $employee->full_name",
        ]);
    }
}

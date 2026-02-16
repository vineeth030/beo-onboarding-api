<?php

namespace App\Actions\Employee;

use App\Enums\ResubmissionType;
use App\Models\Activity;
use App\Models\Employee;
use App\Notifications\DataReopenedNotification;

class NotifyCandidateOnReopenAction
{
    public function execute(Employee $employee, ResubmissionType $type): void
    {
        $employee->user->notify(
            new DataReopenedNotification($employee->first_name, $type)
        );

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => $type->getReopenActivityType(),
            'title' => ucfirst($type->getLabel())." has been reopened for {$employee->full_name} by ".auth()->user()->name,
        ]);
    }
}

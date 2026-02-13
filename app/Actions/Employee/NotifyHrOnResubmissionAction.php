<?php

namespace App\Actions\Employee;

use App\Enums\ResubmissionType;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\CandidateDataResubmittedNotification;

class NotifyHrOnResubmissionAction
{
    public function execute(Employee $employee, ResubmissionType $type): void
    {
        $hrEmails = config('app.hr_emails', []);
        User::whereIn('email', $hrEmails)->each(function ($hrUser) use ($employee, $type) {
            $hrUser->notify(
                new CandidateDataResubmittedNotification($employee->full_name, $type)
            );
        });

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'candidate',
            'type' => $type->getActivityType(),
            'title' => ucfirst($type->getLabel())." has been resubmitted by {$employee->full_name}",
        ]);
    }
}

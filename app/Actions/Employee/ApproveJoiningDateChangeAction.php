<?php

namespace App\Actions\Employee;

use App\Models\Activity;
use App\Models\Employee;
use App\Notifications\DateOfJoiningChangeApprovedNotification;
use App\Notifications\DateOfJoiningChangeRejectedNotification;

class ApproveJoiningDateChangeAction
{
    public function execute(Employee $employee, bool $isJoiningDateUpdateApproved, ?string $updatedJoiningDate = null): void
    {
        $employee->update([
            'is_joining_date_update_approved' => $isJoiningDateUpdateApproved,
            'updated_joining_date' => $updatedJoiningDate,
            'requested_joining_date' => null,
        ]);

        if ($isJoiningDateUpdateApproved) {

            $employee->user->notify(
                new DateOfJoiningChangeApprovedNotification(
                    employeeName: $employee->first_name . ' ' . $employee->last_name,
                    updatedDateOfJoining: $updatedJoiningDate)
            );

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'hr',
                'type' => 'update.dateofjoiningchange.approved',
                'title' => "Request to change the Date of Joining of $employee->name to $updatedJoiningDate has been approved",
            ]);

        } else {
            $employee->user->notify(
                new DateOfJoiningChangeRejectedNotification(
                    employeeName: $employee->first_name . ' ' . $employee->last_name,
                    requestedDateOfJoining: $updatedJoiningDate
                )
            );

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'hr',
                'type' => 'update.dateofjoiningchange.rejected',
                'title' => "Request to change the Date of Joining of $employee->name to $updatedJoiningDate has been rejected",
            ]);
        }
    }
}

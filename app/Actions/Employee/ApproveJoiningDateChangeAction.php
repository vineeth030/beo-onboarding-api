<?php

namespace App\Actions\Employee;

use App\Mail\JoiningDateChangeApprovedMail;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\DateOfJoiningChangeApprovedNotification;
use App\Notifications\DateOfJoiningChangeRejectedNotification;
use Illuminate\Support\Facades\Mail;

class ApproveJoiningDateChangeAction
{
    public function execute(Employee $employee, bool $isJoiningDateUpdateApproved, ?string $updatedJoiningDate = null, ?string $requestedJoiningDate = null): void
    {
        if ($isJoiningDateUpdateApproved) {

            $employee->user->notify(
                new DateOfJoiningChangeApprovedNotification(
                    employeeName: $employee->first_name.' '.$employee->last_name,
                    updatedDateOfJoining: $updatedJoiningDate)
            );

            $employee->update([
                'is_joining_date_update_approved' => $isJoiningDateUpdateApproved,
                'updated_joining_date' => $updatedJoiningDate,
                'requested_joining_date' => null,
            ]);

            $clientEmailIds = $employee->department?->emails->pluck('email')->toArray() ?? [];

            $hrEmailIds = config('app.hr_emails');

            // Send email notification to hr & client email ids.
            Mail::to([...$hrEmailIds, ...$clientEmailIds])->send(new JoiningDateChangeApprovedMail(employee: $employee, updatedJoiningDate: $updatedJoiningDate));

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'hr',
                'type' => 'update.dateofjoiningchange.approved',
                'title' => "Request to change the Date of Joining of $employee->full_name to $updatedJoiningDate has been approved",
            ]);

        } else {
            $employee->user->notify(
                new DateOfJoiningChangeRejectedNotification(
                    employeeName: $employee->first_name.' '.$employee->last_name,
                    requestedDateOfJoining: $requestedJoiningDate
                )
            );

            $employee->update([
                'is_joining_date_update_approved' => $isJoiningDateUpdateApproved,
                'updated_joining_date' => null,
                'requested_joining_date' => $requestedJoiningDate,
            ]);

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'hr',
                'type' => 'update.dateofjoiningchange.rejected',
                'title' => "Request to change the Date of Joining of $employee->full_name to $updatedJoiningDate has been rejected",
            ]);
        }
    }
}

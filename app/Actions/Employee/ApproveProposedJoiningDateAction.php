<?php

namespace App\Actions\Employee;

use App\Mail\WelcomeCandidateMail;
use App\Models\Activity;
use App\Models\Employee;
use App\Notifications\DateOfJoiningChangeRejectedNotification;
use Illuminate\Support\Facades\Mail;

class ApproveProposedJoiningDateAction
{
    public function execute(Employee $employee, bool $isJoiningDateUpdateApproved, ?string $updatedJoiningDate = null, ?string $requestedJoiningDate = null): void
    {
        if ($isJoiningDateUpdateApproved) {

            $employee->update([
                'is_joining_date_update_approved' => $isJoiningDateUpdateApproved,
                'updated_joining_date' => $updatedJoiningDate,
                'requested_joining_date' => null,
            ]);

            $clientEmailIds = $employee->department?->emails->pluck('email')->toArray() ?? [];

            $hrEmailIds = config('app.hr_emails');

            Mail::to($employee->email)
                ->cc([...$hrEmailIds, ...$clientEmailIds])
                ->send(new WelcomeCandidateMail(employee: $employee));

            Activity::create([
                'employee_id' => $employee->id,
                'performed_by_user_id' => auth()->user()->id,
                'user_type' => 'hr',
                'type' => 'update.dateofjoiningchange.approved',
                'title' => "Proposed Date of Joining of $updatedJoiningDate by $employee->full_name has been approved.",
            ]);

        } else {
            $employee->user->notify(
                new DateOfJoiningChangeRejectedNotification(
                    employeeName: $employee->first_name.' '.$employee->last_name,
                    requestedDateOfJoining: $requestedJoiningDate,
                    isProposedDate: true
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
                'title' => "Proposed Date of Joining of $updatedJoiningDate by $employee->full_name has been rejected",
            ]);
        }
    }
}

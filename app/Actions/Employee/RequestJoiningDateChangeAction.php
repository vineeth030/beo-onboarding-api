<?php

namespace App\Actions\Employee;

use App\Mail\JoiningDateChangeRequestMail;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\DateOfJoiningChangeRequestedNotification;
use Illuminate\Support\Facades\Mail;

class RequestJoiningDateChangeAction
{
    public function execute(Employee $employee, string $requestedJoiningDate, bool $isProposedDate = false): void
    {
        $employee->update([
            'is_joining_date_update_approved' => null,
            'requested_joining_date' => $requestedJoiningDate,
        ]);

        // When candidate proposes a joining date, no need to send email from here.
        // Email will be send when candidate accepts offer with proposed joining date.
        if($isProposedDate) return;

        if ($requestedJoiningDate) {
            $hrEmailIds = $employee->activeOffer->beo_emails;
            
            // User::whereIn('email', $hrEmailIds)->each(fn ($admin) => 
            //     $admin->notify(
            //         new DateOfJoiningChangeRequestedNotification(
            //             requestedDateOfJoining: $requestedJoiningDate,
            //             requestedEmployeeName: auth()->user()->name,
            //         )
            //     )
            // );

            Mail::to($hrEmailIds)->send(new JoiningDateChangeRequestMail(
                employee: $employee,
                requestedJoiningDate: $requestedJoiningDate
            ));
        }
    }
}

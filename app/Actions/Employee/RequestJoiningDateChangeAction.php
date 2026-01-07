<?php

namespace App\Actions\Employee;

use App\Models\Employee;
use App\Models\User;
use App\Notifications\DateOfJoiningChangeApprovedNotification;
use App\Notifications\DateOfJoiningChangeRejectedNotification;
use App\Notifications\DateOfJoiningChangeRequestedNotification;

class RequestJoiningDateChangeAction
{
    public function execute(Employee $employee, string $requestedJoiningDate): void
    {
        $employee->update([
            'is_joining_date_update_approved' => null,
            'requested_joining_date' => $requestedJoiningDate,
        ]);

        if ($requestedJoiningDate) {
            
            User::where('role', 'admin')->each(function ($admin) use ($requestedJoiningDate) {
                $admin->notify(
                    new DateOfJoiningChangeRequestedNotification(
                        $requestedJoiningDate,
                        auth()->user()->name
                    )
                );
            });
        }
    }
}

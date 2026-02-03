<?php

namespace App\Actions\Employee;

use App\Enums\OfferStatus;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\PreJoiningFormDownloadedNotification;

class PreJoiningFormDownloadedNotificationAction
{
    public function execute(Employee $employee): void
    {
        $employee->update(['is_pre_joining_form_downloaded' => 1]);

        $employee->activeOffer()->update(['status' => OfferStatus::COMPLETED_PRE_JOINING]);

        User::where('role', 'admin')->each(function ($admin) use ($employee) {
            $admin->notify(
                new PreJoiningFormDownloadedNotification(
                    $employee->first_name.' '.$employee->last_name
                )
            );
        });
    }
}

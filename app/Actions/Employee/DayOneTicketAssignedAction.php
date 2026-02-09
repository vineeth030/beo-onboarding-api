<?php

namespace App\Actions\Employee;

use App\Enums\OfferStatus;
use App\Models\Activity;
use App\Models\Employee;
use App\Notifications\DayOneTicketAssignedNotification;

class DayOneTicketAssignedAction
{
    public function execute(Employee $employee): void
    {
        $employee->update(['is_day_one_ticket_assigned' => 1]);

        $employee->user->notify(
            new DayOneTicketAssignedNotification(employeeName: $employee->first_name.' '.$employee->last_name)
        );

        $employee->activeOffer()->update(['status' => OfferStatus::DAY_ONE_TICKET_ISSUED]);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'update.dayoneticket.assigned',
            'title' => "A day one ticket has been assigned to $employee->fullname by ".auth()->user()->name,
        ]);
    }
}

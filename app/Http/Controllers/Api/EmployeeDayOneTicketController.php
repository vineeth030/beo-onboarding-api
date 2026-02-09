<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\DayOneTicketAssignedAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Response;

class EmployeeDayOneTicketController extends Controller
{
    public function assign(Employee $employee, DayOneTicketAssignedAction $action): Response
    {
        $action->execute(employee: $employee);

        return response()->noContent();
    }
}

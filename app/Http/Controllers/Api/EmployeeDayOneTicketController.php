<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\DayOneTicketAssignedAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class EmployeeDayOneTicketController extends Controller
{
    public function assign(Employee $employee, DayOneTicketAssignedAction $action): Response
    {
        Gate::authorize('adminOnly', Employee::class);
        
        $action->execute(employee: $employee);

        return response()->noContent();
    }
}

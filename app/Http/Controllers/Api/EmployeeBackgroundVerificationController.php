<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\Response;

class EmployeeBackgroundVerificationController extends Controller
{
    public function submit(Employee $employee, BackgroundVerificationFormSubmittedAction $action): Response
    {
        $action->execute(employee: $employee);

        return response()->noContent();
    }
}

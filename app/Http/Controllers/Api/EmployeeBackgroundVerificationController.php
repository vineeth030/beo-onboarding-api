<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\BackgroundVerificationFormResubmittedAction;
use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Actions\Employee\BackgroundVerificationReopenedAction;
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

    public function resubmit(Employee $employee, BackgroundVerificationFormResubmittedAction $action): Response
    {
        $action->execute(employee: $employee);

        return response()->noContent();
    }

    public function reopen(Employee $employee, BackgroundVerificationReopenedAction $action): Response
    {
        $action->execute(employee: $employee);

        return response()->noContent();
    }
}

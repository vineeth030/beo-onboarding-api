<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\ApproveJoiningDateChangeAction;
use App\Actions\Employee\RequestJoiningDateChangeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApproveJoiningDateRequest;
use App\Http\Requests\RejectJoiningDateRequest;
use App\Http\Requests\RequestJoiningDateRequest;
use App\Models\Employee;
use Illuminate\Http\Response;

class EmployeeJoiningDateController extends Controller
{
    public function request(RequestJoiningDateRequest $request, Employee $employee, RequestJoiningDateChangeAction $action): Response
    {
        $action->execute(
            employee: $employee,
            requestedJoiningDate: $request->validated('requested_joining_date')
        );

        return response()->noContent();
    }

    public function approve(ApproveJoiningDateRequest $request, Employee $employee, ApproveJoiningDateChangeAction $action): Response
    {
        $action->execute(
            employee: $employee,
            isJoiningDateUpdateApproved: 1,
            updatedJoiningDate: $request->validated('updated_joining_date'),
            requestedJoiningDate: $request->validated('requested_joining_date')
        );

        return response()->noContent();
    }

    public function reject(RejectJoiningDateRequest $request, Employee $employee, ApproveJoiningDateChangeAction $action): Response
    {
        $action->execute(
            employee: $employee,
            isJoiningDateUpdateApproved: 0,
            updatedJoiningDate: $request->validated('updated_joining_date'),
            requestedJoiningDate: $request->validated('requested_joining_date')
        );

        return response()->noContent();
    }
}

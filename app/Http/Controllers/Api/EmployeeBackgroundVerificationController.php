<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\BackgroundVerificationFormReOpenAction;
use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;

class EmployeeBackgroundVerificationController extends Controller
{
    public function submit(Employee $employee, BackgroundVerificationFormSubmittedAction $action): JsonResponse
    {
        $action->execute(employee: $employee);

        return response()->json([
            'message' => 'Background verification form submitted successfully.'
        ], 200);
    }

    public function reopen(Employee $employee, BackgroundVerificationFormReOpenAction $action) : JsonResponse 
    {
        if ($employee->status !== 2) {
            return response()->json([
                'message' => 'The status of this background verification form is not completed.'
            ], 422);
        }

        $action->execute(employee: $employee);

        return response()->json([
            'message' => 'Background verification form reopened successfully.'
        ], 200);
    }
}

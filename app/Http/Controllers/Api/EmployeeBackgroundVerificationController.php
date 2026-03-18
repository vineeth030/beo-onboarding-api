<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\BackgroundVerificationFormReOpenAction;
use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Http\Controllers\Controller;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class EmployeeBackgroundVerificationController extends Controller
{
    public function start(Employee $employee, BackgroundVerificationFormSubmittedAction $action): JsonResponse
    {
        Gate::authorize('view', $employee);
        
        $action->execute(employee: $employee, bvgStatus: 1);

        return response()->json([
            'message' => 'Background verification form submitted successfully.'
        ], 200);
    }

    public function submit(Employee $employee, BackgroundVerificationFormSubmittedAction $action): JsonResponse
    {
        Gate::authorize('update', $employee);

        $action->execute(employee: $employee, bvgStatus: 2);

        return response()->json([
            'message' => 'Background verification form submitted successfully.'
        ], 200);
    }

    public function reopen(Employee $employee, BackgroundVerificationFormReOpenAction $action) : JsonResponse 
    {
        Gate::authorize('adminOnly', Employee::class);
        
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

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeBankDetailRequest;
use App\Http\Requests\UpdateEmployeeBankDetailRequest;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class EmployeeBankDetailController extends Controller
{
    public function show(Employee $employee): JsonResponse
    {
        Gate::authorize('view', $employee);

        $bankDetail = $employee->bankDetail;

        if (! $bankDetail) {
            return response()->json(null, 404);
        }

        return response()->json($bankDetail);
    }

    public function store(StoreEmployeeBankDetailRequest $request, Employee $employee): JsonResponse
    {
        Gate::authorize('update', $employee);

        if ($employee->bankDetail()->exists()) {
            return response()->json(['message' => 'Bank detail already exists for this employee.'], 409);
        }

        $path = $request->file('proof_document')->store("bank-details/{$employee->id}", 'public');

        $bankDetail = $employee->bankDetail()->create(
            Arr::except($request->validated(), ['proof_document']) + ['proof_document_path' => '/storage/'.$path]
        );

        return response()->json($bankDetail, 201);
    }

    public function update(UpdateEmployeeBankDetailRequest $request, Employee $employee): JsonResponse
    {
        Gate::authorize('update', $employee);

        $bankDetail = $employee->bankDetail;

        if (! $bankDetail) {
            return response()->json(null, 404);
        }

        $attributes = Arr::except($request->validated(), ['proof_document']);

        if ($request->hasFile('proof_document')) {
            $path = $request->file('proof_document')->store("bank-details/{$employee->id}", 'public');
            $attributes['proof_document_path'] = '/storage/'.$path;
        }

        $bankDetail->update($attributes);

        return response()->json($bankDetail);
    }

    public function destroy(Employee $employee): JsonResponse
    {
        Gate::authorize('update', $employee);

        $bankDetail = $employee->bankDetail;

        if (! $bankDetail) {
            return response()->json(null, 404);
        }

        $bankDetail->delete();

        return response()->json(null, 204);
    }
}

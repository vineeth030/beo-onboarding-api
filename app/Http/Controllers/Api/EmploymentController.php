<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmploymentRequest;
use App\Http\Requests\UpdateEmploymentRequest;
use App\Models\Employee;
use App\Models\Employment;

class EmploymentController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->employments;
    }

    public function store(StoreEmploymentRequest $request, Employee $employee)
    {
        $employment = $employee->employments()->create($request->validated());
        return response()->json($employment, 201);
    }

    public function show(Employee $employee, Employment $employment)
    {
        return $employment;
    }

    public function update(UpdateEmploymentRequest $request, Employee $employee, Employment $employment)
    {
        $employment->update($request->validated());
        return response()->json($employment);
    }

    public function destroy(Employee $employee, Employment $employment)
    {
        $employment->delete();
        return response()->json(null, 204);
    }
}

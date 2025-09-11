<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Models\Education;
use App\Models\Employee;

class EducationController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->educations;
    }

    public function store(StoreEducationRequest $request, Employee $employee)
    {
        $education = $employee->educations()->create($request->validated());
        return response()->json($education, 201);
    }

    public function show(Employee $employee, Education $education)
    {
        return $education;
    }

    public function update(UpdateEducationRequest $request, Employee $employee, Education $education)
    {
        $education->update($request->validated());
        return response()->json($education);
    }

    public function destroy(Employee $employee, Education $education)
    {
        $education->delete();
        return response()->json(null, 204);
    }
}

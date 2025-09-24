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
        $educations = [];

        foreach ($request->validated()['educations'] as $educationData) {
            $file = $educationData['file'];
            unset($educationData['file']);

            $path = $file->store("documents/{$employee->id}", 'public');

            $education = $employee->educations()->create(
                $educationData + ['certificate_path' => $path]
            );

            $educations[] = $education;
        }

        return response()->json($educations, 201);
    }

    public function show(Employee $employee, Education $education)
    {
        return $education;
    }

    public function update(UpdateEducationRequest $request, Employee $employee, Education $education)
    {
        $path = $request->file('file')->store("documents/{$employee->id}", 'public');

        $education->update($request->validated() + ['certificate_path' + $path]);

        return response()->json($education);
    }

    public function destroy(Employee $employee, Education $education)
    {
        $education->delete();
        return response()->json(null, 204);
    }
}

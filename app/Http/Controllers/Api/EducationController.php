<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEducationRequest;
use App\Http\Requests\UpdateEducationRequest;
use App\Models\Education;
use App\Models\Employee;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;

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

    public function update(UpdateEducationRequest $request, $employee_id)
    {
        $employee = Employee::where('id', $employee_id)->first();

        $employee->educations()->delete();

        $educations = [];

        foreach ($request->validated()['educations'] as $educationData) {
            $file = $educationData['file'] ?? null;
            unset($educationData['file']);

            $certificatePath = null;

            if ($file instanceof UploadedFile) {
                $path = $file->store("documents/{$employee->id}", 'public');
                $certificatePath = '/storage/' . $path;
            }elseif (is_string($file) && str_starts_with($file, '/storage/')) {
                $certificatePath = $file;
            }

            $education = $employee->educations()->create(
                $educationData + ['employee_id' => $employee->id] + ($certificatePath ? ['certificate_path' => $certificatePath] : [])
            );

            $educations[] = $education;
        }

        return response()->json($educations);
    }

    public function destroy(Employee $employee, Education $education)
    {
        $education->delete();
        return response()->json(null, 204);
    }
}

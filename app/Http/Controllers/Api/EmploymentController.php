<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\NotifyHrOnResubmissionAction;
use App\Enums\ResubmissionType;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmploymentRequest;
use App\Http\Requests\UpdateEmploymentRequest;
use App\Models\Employee;
use App\Models\Employment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmploymentController extends Controller
{
    public function index(Employee $employee)
    {
        return $employee->employments()->with('salarySlips')->get();
    }

    public function store(StoreEmploymentRequest $request, Employee $employee)
    {
        $validatedData = $request->validated();
        $employments = [];

        foreach ($validatedData['employments'] as $index => $employmentData) {
            $salarySlipFiles = $employmentData['salary_slips'] ?? [];
            unset($employmentData['salary_slips']);

            if ($request->hasFile("employments.{$index}.resignation_acceptance_letter_file")) {
                $employmentData['resignation_acceptance_letter_file'] = $request->file("employments.{$index}.resignation_acceptance_letter_file")
                    ->storeAs("documents/{$employee->id}", uniqid().'.'.$request->file("employments.{$index}.resignation_acceptance_letter_file")->getClientOriginalExtension(), 'public');
                $employmentData['resignation_acceptance_letter_preview_url'] = Storage::url($employmentData['resignation_acceptance_letter_file']);
            }

            if ($request->hasFile("employments.{$index}.experience_letter_file")) {
                $employmentData['experience_letter_file'] = $request->file("employments.{$index}.experience_letter_file")
                    ->storeAs("documents/{$employee->id}", uniqid().'.'.$request->file("employments.{$index}.experience_letter_file")->getClientOriginalExtension(), 'public');
                $employmentData['experience_letter_preview_url'] = Storage::url($employmentData['experience_letter_file']);
            }

            $employment = $employee->employments()->create($employmentData);

            foreach ($salarySlipFiles as $salarySlipFile) {
                $filePath = $salarySlipFile->storeAs("documents/{$employee->id}/salary_slips", uniqid().'.'.$salarySlipFile->getClientOriginalExtension(), 'public');

                $employment->salarySlips()->create([
                    'file_path' => $filePath,
                    'preview_url' => Storage::url($filePath),
                ]);
            }

            $employments[] = $employment->load('salarySlips');
        }

        return response()->json($employments, 201);
    }

    public function show(Employee $employee, Employment $employment)
    {
        return $employment->load('salarySlips');
    }

    public function update(UpdateEmploymentRequest $request, $employee_id)
    {
        $employee = Employee::where('id', $employee_id)->first();

        $wasAnyEmploymentOpen = $employee->employments()->where('is_open', 1)->exists();

        $employee->employments()->delete();

        $employments = [];

        foreach ($request->validated()['employments'] as $index => $employmentData) {
            $salarySlipFiles = $employmentData['salary_slips'] ?? [];
            unset($employmentData['salary_slips']);

            if ($request->hasFile("employments.{$index}.resignation_acceptance_letter_file")) {
                $employmentData['resignation_acceptance_letter_file'] = $request->file("employments.{$index}.resignation_acceptance_letter_file")
                    ->storeAs("documents/{$employee->id}", uniqid().'.'.$request->file("employments.{$index}.resignation_acceptance_letter_file")->getClientOriginalExtension(), 'public');
                $employmentData['resignation_acceptance_letter_preview_url'] = Storage::url($employmentData['resignation_acceptance_letter_file']);
            } elseif (isset($employmentData['resignation_acceptance_letter_file']) && is_string($employmentData['resignation_acceptance_letter_file']) && str_starts_with($employmentData['resignation_acceptance_letter_file'], '/storage/')) {
                $employmentData['resignation_acceptance_letter_preview_url'] = $employmentData['resignation_acceptance_letter_file'];
                $employmentData['resignation_acceptance_letter_file'] = str_replace('/storage/', '', $employmentData['resignation_acceptance_letter_file']);
            }

            if ($request->hasFile("employments.{$index}.experience_letter_file")) {
                $employmentData['experience_letter_file'] = $request->file("employments.{$index}.experience_letter_file")
                    ->storeAs("documents/{$employee->id}", uniqid().'.'.$request->file("employments.{$index}.experience_letter_file")->getClientOriginalExtension(), 'public');
                $employmentData['experience_letter_preview_url'] = Storage::url($employmentData['experience_letter_file']);
            } elseif (isset($employmentData['experience_letter_file']) && is_string($employmentData['experience_letter_file']) && str_starts_with($employmentData['experience_letter_file'], '/storage/')) {
                $employmentData['experience_letter_preview_url'] = $employmentData['experience_letter_file'];
                $employmentData['experience_letter_file'] = str_replace('/storage/', '', $employmentData['experience_letter_file']);
            }

            $employment = $employee->employments()->create($employmentData + ['employee_id' => $employee->id]);

            foreach ($salarySlipFiles as $salarySlipFile) {

                if (is_file($salarySlipFile)) {
                    $filePath = $salarySlipFile->storeAs("documents/{$employee->id}/salary_slips", uniqid().'.'.$salarySlipFile->getClientOriginalExtension(), 'public');
                } elseif (isset($salarySlipFile) && is_string($salarySlipFile) && str_starts_with($salarySlipFile, '/storage/')) {
                    $filePath = str_replace('/storage/', '', $salarySlipFile);
                }

                $employment->salarySlips()->create([
                    'file_path' => $filePath,
                    'preview_url' => Storage::url($filePath),
                ]);
            }

            $employments[] = $employment->load('salarySlips');
        }

        if ($wasAnyEmploymentOpen && auth()->user()->role == 'candidate') {
            $employee->employments()->update(['is_open' => 0]);
            app(NotifyHrOnResubmissionAction::class)->execute(
                employee: $employee,
                type: ResubmissionType::Employment
            );
        }

        return response()->json($employments);
    }

    public function verify(Employment $employment, Request $request): JsonResponse
    {

        $validated = $request->validate([
            'is_verified' => ['required', 'boolean'],
        ]);

        $employment->update(['is_verified' => $validated['is_verified']]);

        return response()->json(null, 200);
    }

    public function open(Employment $employment, Request $request): JsonResponse
    {

        $validated = $request->validate([
            'is_open' => ['required', 'boolean'],
        ]);

        $employment->update(['is_open' => $validated['is_open']]);

        return response()->json(null, 200);
    }

    public function destroy(Employee $employee, Employment $employment)
    {
        $employment->delete();

        return response()->json(null, 204);
    }
}

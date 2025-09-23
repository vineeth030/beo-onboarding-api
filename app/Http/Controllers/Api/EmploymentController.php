<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmploymentRequest;
use App\Http\Requests\UpdateEmploymentRequest;
use App\Models\Employee;
use App\Models\Employment;
use App\Models\SalarySlip;
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
        $salarySlipFiles = $validatedData['salary_slips'] ?? [];
        unset($validatedData['salary_slips']);

        if ($request->hasFile('resignation_acceptance_letter_file')) {
            $validatedData['resignation_acceptance_letter_file'] = $request->file('resignation_acceptance_letter_file')
                ->storeAs("documents/{$employee->id}", uniqid() . '.' . $request->file('resignation_acceptance_letter_file')->getClientOriginalExtension(), 'public');
            $validatedData['resignation_acceptance_letter_preview_url'] = Storage::url($validatedData['resignation_acceptance_letter_file']);
        }

        if ($request->hasFile('experience_letter_file')) {
            $validatedData['experience_letter_file'] = $request->file('experience_letter_file')
                ->storeAs("documents/{$employee->id}", uniqid() . '.' . $request->file('experience_letter_file')->getClientOriginalExtension(), 'public');
            $validatedData['experience_letter_preview_url'] = Storage::url($validatedData['experience_letter_file']);
        }

        $employment = $employee->employments()->create($validatedData);

        foreach ($salarySlipFiles as $salarySlipFile) {
            $filePath = $salarySlipFile->storeAs("documents/{$employee->id}/salary_slips",uniqid() . '.' . $salarySlipFile->getClientOriginalExtension(),'public');

            $employment->salarySlips()->create([
                'file_path' => $filePath,
                'preview_url' => Storage::url($filePath),
            ]);
        }

        return response()->json($employment->load('salarySlips'), 201);
    }

    public function show(Employee $employee, Employment $employment)
    {
        return $employment->load('salarySlips');
    }

    public function update(UpdateEmploymentRequest $request, Employee $employee, Employment $employment)
    {
        $validatedData = $request->validated();
        $salarySlipFiles = $validatedData['salary_slips'] ?? [];
        unset($validatedData['salary_slips']);

        if ($request->hasFile('resignation_acceptance_letter_file')) {

            if ($employment->resignation_acceptance_letter_file) {
                Storage::disk('public')->delete($employment->resignation_acceptance_letter_file);
            }

            $validatedData['resignation_acceptance_letter_file'] = $request->file('resignation_acceptance_letter_file')
                ->storeAs("documents/{$employee->id}", uniqid() . '.' . $request->file('resignation_acceptance_letter_file')->getClientOriginalExtension(), 'public');
            $validatedData['resignation_acceptance_letter_preview_url'] = Storage::url($validatedData['resignation_acceptance_letter_file']);
        }

        if ($request->hasFile('experience_letter_file')) {

            if ($employment->experience_letter_file) {
                Storage::disk('public')->delete($employment->experience_letter_file);
            }
            
            $validatedData['experience_letter_file'] = $request->file('experience_letter_file')
                ->storeAs("documents/{$employee->id}", uniqid() . '.' . $request->file('experience_letter_file')->getClientOriginalExtension(), 'public');
            $validatedData['experience_letter_preview_url'] = Storage::url($validatedData['experience_letter_file']);
        }

        $employment->update($validatedData);
        
        foreach ($salarySlipFiles as $salarySlipFile) {
            $filePath = $salarySlipFile->storeAs("documents/{$employee->id}/salary_slips",uniqid() . '.' . $salarySlipFile->getClientOriginalExtension(),'public');

            $employment->salarySlips()->create([
                'file_path' => $filePath,
                'preview_url' => Storage::url($filePath),
            ]);
        }

        return response()->json($employment->load('salarySlips'));
    }

    public function destroy(Employee $employee, Employment $employment)
    {
        $employment->delete();
        return response()->json(null, 204);
    }
}

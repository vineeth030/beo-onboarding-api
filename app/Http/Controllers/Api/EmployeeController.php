<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Employee::with(['addresses', 'documents', 'educations', 'employments'])->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $randomPassword = Str::random(8);

        $employeeUser = User::create([
            'name' => $request->get('first_name') . ' ' . $request->get('last_name'), 
            'email' => $request->get('email'), 
            'password' => Hash::make($randomPassword),
            'role' => 'candidate'
        ]);

        $employee = Employee::create($request->validated() + [
            'user_id' => $employeeUser->id, 
            'password' => $randomPassword,
            'client_id' => $request->get('client_id')
        ]);
        
        return response()->json($employee, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return $employee->load(['office', 'addresses', 'documents', 'educations', 'employments.salarySlips', 'offers']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store("documents/{$employee->id}", 'public');
            $employee->update($request->validated() + ['photo_path' => '/storage/' . $path]);
        }else{
            $employee->update($request->validated());
        }

        return response()->json($employee);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();
        return response()->json(null, 204);
    }

    /**
     * buddy_is is the employee_id in beo_employees table.
     */
    public function assignBuddy(Employee $employee, Request $request) : JsonResponse {

        $employee->update([
            'buddy_id' => $request->get('beo_employee_id')
        ]);

        return response()->json(null, 200);
    }

    /**
     * buddy_is is the employee_id in beo_employees table.
     */
    public function assignPocs(Employee $employee, Request $request) : JsonResponse {

        $employee->update([
            'poc_1_id' => $request->get('beo_employee_1_id'),
            'poc_2_id' => $request->get('beo_employee_2_id')
        ]);

        return response()->json(null, 200);
    }
}

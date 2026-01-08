<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\ApproveJoiningDateChangeAction;
use App\Actions\Employee\BackgroundVerificationFormResubmittedAction;
use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Actions\Employee\BackgroundVerificationReopenedAction;
use App\Actions\Employee\PreJoiningFormDownloadedNotificationAction;
use App\Actions\Employee\RequestJoiningDateChangeAction;
use App\Actions\Employee\UpdateEmployeeAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\AssignBuddyNotification;
use App\Notifications\AssignPocNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            'department_id' => $request->get('department_id')
        ]);

        Activity::create([
            'employee_id' => $employeeUser->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'add.candidate',
            'title' => 'New candidate ' . $employeeUser->name . ' added by ' . auth()->user()->name,
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
    public function update(UpdateEmployeeRequest $request, Employee $employee, UpdateEmployeeAction $updateEmployeeAction)
    {
        $dataForEmployeeUpdate = Arr::except(
            $request->validated(),
            ['is_joining_date_update_approved','updated_joining_date','requested_joining_date', 
            'is_open', 'is_pre_joining_form_downloaded']
        );

        $updatedEmployee = $updateEmployeeAction->execute(
            employee: $employee, data: $dataForEmployeeUpdate, file: $request->file('file')
        );

        if ($request->has('status') && $request->status == 2 && auth()->user()->role == 'candidate') {
            app(BackgroundVerificationFormSubmittedAction::class)->execute(employee: $employee);
        }

        if ($employee->is_open == 1 && auth()->user()->role == 'candidate') {
            app(BackgroundVerificationFormResubmittedAction::class)->execute(employee: $employee);
        }

        if ($request->has('is_open') && $request->is_open && auth()->user()->role !== 'candidate') {
            app(BackgroundVerificationReopenedAction::class)->execute(employee: $employee);
        }

        if ($request->has('is_joining_date_update_approved') && auth()->user()->role !== 'candidate') {
            app(ApproveJoiningDateChangeAction::class)->execute(
                employee: $employee,
                isJoiningDateUpdateApproved: $request->is_joining_date_update_approved,
                updatedJoiningDate: $request->updated_joining_date
            );
        }

        if ($request->has('requested_joining_date') && is_null($request->get('is_joining_date_update_approved')) && auth()->user()->role == 'candidate') {
            app(RequestJoiningDateChangeAction::class)->execute(
                employee: $employee,
                requestedJoiningDate: $request->requested_joining_date
            );
        }

        if($request->has('is_pre_joining_form_downloaded') && $request->get('is_pre_joining_form_downloaded')) {
            app(PreJoiningFormDownloadedNotificationAction::class)->execute(employee: $employee);
        }

        return response()->json($updatedEmployee);
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

        $employee->user->notify(new AssignBuddyNotification());

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'assign.buddy.candidate',
            'title' => 'Assigned buddy to candidate ' . $employee->name . ' by ' . auth()->user()->name,
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

        $employee->user->notify(new AssignPocNotification());

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'assign.pocs.candidate',
            'title' => 'Assigned POCs to candidate ' . $employee->name . ' by ' . auth()->user()->name,
        ]);

        return response()->json(null, 200);
    }
}

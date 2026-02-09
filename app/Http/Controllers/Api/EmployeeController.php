<?php

namespace App\Http\Controllers\Api;

use App\Actions\Employee\ApproveJoiningDateChangeAction;
use App\Actions\Employee\BackgroundVerificationFormResubmittedAction;
use App\Actions\Employee\BackgroundVerificationFormSubmittedAction;
use App\Actions\Employee\BackgroundVerificationReopenedAction;
use App\Actions\Employee\DayOneTicketAssignedAction;
use App\Actions\Employee\PreJoiningFormDownloadedNotificationAction;
use App\Actions\Employee\RequestJoiningDateChangeAction;
use App\Actions\Employee\UpdateEmployeeAction;
use App\Enums\OfferStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Activity;
use App\Models\Employee;
use App\Models\User;
use App\Notifications\AssignBuddyNotification;
use App\Notifications\AssignPocNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with(['offers', 'department', 'activeOffer'])
            ->orderBy('id', 'desc')
            ->get();

        return EmployeeResource::collection($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $randomPassword = Str::random(8);

        $employeeUser = User::create([
            'name' => $request->get('first_name').' '.$request->get('last_name'),
            'email' => $request->get('email'),
            'password' => Hash::make($randomPassword),
            'role' => 'candidate',
        ]);

        $employee = Employee::create($request->validated() + [
            'user_id' => $employeeUser->id,
            'password' => $randomPassword,
            'department_id' => $request->get('department_id'),
        ]);

        Activity::create([
            'employee_id' => $employeeUser->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'add.candidate',
            'title' => 'New candidate '.$employeeUser->name.' added by '.auth()->user()->name,
        ]);

        return response()->json($employee, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load([
            'activeOffer',
            'office',
            'addresses',
            'documents',
            'educations',
            'employments.salarySlips',
            'offers',
        ]);

        return new EmployeeResource($employee);
    }

    /**
     * Update the specified resource in storage.
     *
     * @deprecated Workflow flags (status, is_open, requested_joining_date, is_joining_date_update_approved,
     *             is_day_one_ticket_assigned, is_onboarded) will be removed. Use dedicated endpoints instead:
     *             - POST /employees/{employee}/background-verification/submit
     *             - POST /employees/{employee}/background-verification/resubmit
     *             - POST /employees/{employee}/background-verification/reopen
     *             - POST /employees/{employee}/joining-date/request
     *             - POST /employees/{employee}/joining-date/approve
     *             - POST /employees/{employee}/joining-date/reject
     *             - POST /employees/{employee}/day-one-ticket/assign
     *             - POST /employees/{employee}/onboard
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee, UpdateEmployeeAction $updateEmployeeAction)
    {
        $dataForEmployeeUpdate = Arr::except(
            $request->validated(),
            ['is_joining_date_update_approved', 'updated_joining_date', 'requested_joining_date',
                'is_open', 'is_pre_joining_form_downloaded', 'is_day_one_ticket_assigned', 'is_onboarded']
        );

        $updatedEmployee = $updateEmployeeAction->execute(
            employee: $employee, data: $dataForEmployeeUpdate, file: $request->file('file')
        );

        // TODO: Remove these workflow handlers once React team switches to dedicated endpoints
        // Background verification: submit
        if ($request->has('status') && $request->status == 2 && auth()->user()->role == 'candidate') {
            app(BackgroundVerificationFormSubmittedAction::class)->execute(employee: $employee);
        }

        // Background verification: resubmit
        if ($employee->is_open == 1 && auth()->user()->role == 'candidate') {
            app(BackgroundVerificationFormResubmittedAction::class)->execute(employee: $employee);
        }

        // Background verification: reopen
        if ($request->has('is_open') && $request->is_open && auth()->user()->role !== 'candidate') {
            app(BackgroundVerificationReopenedAction::class)->execute(employee: $employee);
        }

        // Joining date: approve/reject
        if ($request->has('is_joining_date_update_approved') && auth()->user()->role !== 'candidate') {
            app(ApproveJoiningDateChangeAction::class)->execute(
                employee: $employee,
                isJoiningDateUpdateApproved: $request->is_joining_date_update_approved,
                updatedJoiningDate: $request->updated_joining_date,
                requestedJoiningDate: $request->requested_joining_date
            );
        }

        // Joining date: request
        if ($request->has('requested_joining_date') && is_null($request->get('is_joining_date_update_approved')) && auth()->user()->role == 'candidate') {
            app(RequestJoiningDateChangeAction::class)->execute(
                employee: $employee,
                requestedJoiningDate: $request->requested_joining_date
            );
        }

        // Pre-joining form downloaded (keeping as is, not extracted to new endpoint)
        if ($request->has('is_pre_joining_form_downloaded') && $request->get('is_pre_joining_form_downloaded')) {
            app(PreJoiningFormDownloadedNotificationAction::class)->execute(employee: $employee);
        }

        // Day one ticket: assign
        if ($request->has('is_day_one_ticket_assigned') && $request->get('is_day_one_ticket_assigned')) {
            app(DayOneTicketAssignedAction::class)->execute(employee: $employee);
        }

        // Onboarding: mark as onboarded
        if ($request->has('is_onboarded') && $request->get('is_onboarded')) {
            $employee->activeOffer()->update(['status' => OfferStatus::REGISTERED_EMPLOYEE]);
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
    public function assignBuddy(Employee $employee, Request $request): JsonResponse
    {

        $employee->update([
            'buddy_id' => $request->get('beo_employee_id'),
        ]);

        $employee->user->notify(new AssignBuddyNotification);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'assign.buddy.candidate',
            'title' => 'Assigned buddy to candidate '.$employee->fullname.' by '.auth()->user()->name,
        ]);

        return response()->json(null, 200);
    }

    /**
     * buddy_is is the employee_id in beo_employees table.
     */
    public function assignPocs(Employee $employee, Request $request): JsonResponse
    {

        $employee->update([
            'poc_1_id' => $request->get('beo_employee_1_id'),
            'poc_2_id' => $request->get('beo_employee_2_id'),
        ]);

        $employee->user->notify(new AssignPocNotification);

        Activity::create([
            'employee_id' => $employee->id,
            'performed_by_user_id' => auth()->user()->id,
            'user_type' => 'hr',
            'type' => 'assign.pocs.candidate',
            'title' => 'Assigned POCs to candidate '.$employee->fullname.' by '.auth()->user()->name,
        ]);

        return response()->json(null, 200);
    }
}

<?php

use App\Http\Controllers\Api\ActivityController;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BEOSystemController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ClientEmailController;
use App\Http\Controllers\Api\DepartmentController;
use App\Http\Controllers\Api\DesignationController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\EmployeeBackgroundVerificationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmployeeDayOneTicketController;
use App\Http\Controllers\Api\EmployeeJoiningDateController;
use App\Http\Controllers\Api\EmployeeOnboardingController;
use App\Http\Controllers\Api\EmploymentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SalaryComponentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Authentication routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/test', function () {
    return response()->json(['Success! API works!']);
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Employee routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('employees.addresses', AddressController::class)->shallow();
    Route::apiResource('employees.documents', DocumentController::class)->shallow();
    Route::apiResource('employees.educations', EducationController::class)->shallow();
    Route::apiResource('employees.employments', EmploymentController::class)->shallow();

    // Employee workflow endpoints
    Route::post('employees/{employee}/background-verification/submit', [EmployeeBackgroundVerificationController::class, 'submit']);
    // Route::post('employees/{employee}/background-verification/resubmit', [EmployeeBackgroundVerificationController::class, 'resubmit']);
    // Route::post('employees/{employee}/background-verification/reopen', [EmployeeBackgroundVerificationController::class, 'reopen']);

    Route::post('employees/{employee}/joining-date/request', [EmployeeJoiningDateController::class, 'request']);
    Route::post('employees/{employee}/joining-date/approve', [EmployeeJoiningDateController::class, 'approve']);
    Route::post('employees/{employee}/joining-date/reject', [EmployeeJoiningDateController::class, 'reject']);

    Route::post('employees/{employee}/day-one-ticket/assign', [EmployeeDayOneTicketController::class, 'assign']);

    Route::post('employees/{employee}/onboard', [EmployeeOnboardingController::class, 'onboard']);

    Route::post('employees/{employee}', [EmployeeController::class, 'verify']);
    Route::post('employments/{employment}/verify', [EmploymentController::class, 'verify']);
    Route::post('educations/{education}/verify', [EducationController::class, 'verify']);
    Route::post('profile/{employee}/open', [EmployeeController::class, 'open']);
    Route::post('documents/{document}/open', [DocumentController::class, 'open']);
    Route::post('employments/{employment}/open', [EmploymentController::class, 'open']);
    Route::post('educations/{education}/open', [EducationController::class, 'open']);

    // Client routes
    Route::apiResource('clients', ClientController::class);
    Route::post('/clients/{id}/emails', [ClientEmailController::class, 'store']);
    Route::delete('/client-emails/{id}', [ClientEmailController::class, 'destroy']);

    // Department and Designation routes
    Route::apiResource('departments', DepartmentController::class);
    Route::apiResource('designations', DesignationController::class);
    Route::post('designations-sync', [DesignationController::class, 'sync']);

    Route::get('offices', [OfferController::class, 'index']);
    Route::get('offices/{office}', [OfferController::class, 'show']);

    // Offers routes
    Route::apiResource('offers', OfferController::class);

    // Activity routes
    Route::apiResource('activities', ActivityController::class);

    Route::get('/reports', [ReportController::class, 'index']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/read-notifications', [NotificationController::class, 'readAll']);

    Route::get('/countries', [BEOSystemController::class, 'countries']);
    Route::get('/states', [BEOSystemController::class, 'states']);

    Route::post('/register-employees-to-beo-system', [BEOSystemController::class, 'store']);
    Route::get('/get-registered-employee-from-beo-system', [BEOSystemController::class, 'show']);

    Route::post('/store-beo-employees-to-onboarding', [BEOSystemController::class, 'storeBEOEmployeesToOnboarding']);
    Route::post('/store-departments-to-onboarding', [BEOSystemController::class, 'storeDepartmentsToOnboarding']);
    Route::post('/store-designations-to-onboarding', [BEOSystemController::class, 'storeDesignationsToOnboarding']);

    Route::get('/get-all-beo-employees', [BEOSystemController::class, 'getBEOEmployees']);
    Route::get('/get-single-beo-employee/{employee_id}', [BEOSystemController::class, 'getSingleBEOEmployee']);
    Route::post('/employees/{employee}/assign-buddy-to-employee', [EmployeeController::class, 'assignBuddy']);
    Route::post('/employees/{employee}/assign-pocs-to-employee', [EmployeeController::class, 'assignPocs']);

    Route::get('/salary-components', [SalaryComponentController::class, 'show']);
    Route::post('/salary-components', [SalaryComponentController::class, 'store']);
    Route::put('/salary-components', [SalaryComponentController::class, 'update']);
});

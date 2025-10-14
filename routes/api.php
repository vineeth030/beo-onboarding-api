<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BEOSystemController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmploymentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ClientEmailController;
use App\Http\Controllers\Api\OfferController;
use App\Http\Controllers\OfficeController;
use App\Models\Office;

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
Route::get('/test', function(){
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

    Route::post('employees/{employee}', [EmployeeController::class, 'verify']);
    Route::post('employments/{employment}/verify', [EmploymentController::class, 'verify']);
    Route::post('educations/{education}/verify', [EducationController::class, 'verify']);
    Route::post('employments/{employment}/open', [EmploymentController::class, 'open']);
    Route::post('educations/{education}/open', [EducationController::class, 'open']);

    // Client routes
    Route::apiResource('clients', ClientController::class);
    Route::post('/clients/{id}/emails', [ClientEmailController::class, 'store']);
    Route::delete('/client-emails/{id}', [ClientEmailController::class, 'destroy']);

    Route::get('offices', [OfficeController::class, 'index']);
    Route::get('offices/{office}', [OfficeController::class, 'show']);

    // Offers routes
    Route::apiResource('offers', OfferController::class);

    Route::get('/countries', [BEOSystemController::class, 'countries']);
    Route::get('/states', [BEOSystemController::class, 'states']);

    Route::post('/store-beo-employees-to-onboarding', [BEOSystemController::class, 'storeBEOEmployeesToOnboarding']);
    Route::get('/get-all-beo-employees', [BEOSystemController::class, 'getBEOEmployees']);
    Route::get('/get-single-beo-employee/{employee_id}', [BEOSystemController::class, 'getSingleBEOEmployee']);
    Route::post('/employees/{employee}/assign-buddy-to-employee', [EmployeeController::class, 'assignBuddy']);
    Route::post('/employees/{employee}/assign-pocs-to-employee', [EmployeeController::class, 'assignPocs']);
});
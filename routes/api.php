<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AddressController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\EmploymentController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\ClientEmailController;

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
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Employee routes
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('employees', EmployeeController::class);
    Route::apiResource('employees.addresses', AddressController::class)->shallow();
    Route::apiResource('employees.documents', DocumentController::class)->shallow();
    Route::apiResource('employees.educations', EducationController::class)->shallow();
    Route::apiResource('employees.employments', EmploymentController::class)->shallow();

    // Client routes
    Route::apiResource('clients', ClientController::class);
    Route::post('/clients/{id}/emails', [ClientEmailController::class, 'store']);
    Route::delete('/client-emails/{id}', [ClientEmailController::class, 'destroy']);
});
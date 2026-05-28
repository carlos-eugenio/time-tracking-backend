<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\TimeEntryController;
use App\Http\Controllers\Api\Reports\TimeEntryReportController;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('employees', [EmployeeController::class, 'index']);
    Route::post('employees', [EmployeeController::class, 'create']);
    Route::get('employees/{employee}', [EmployeeController::class, 'show']);
    Route::match(['put', 'patch'], 'employees/{employee}', [EmployeeController::class, 'update']);
    Route::delete('employees/{employee}', [EmployeeController::class, 'delete']);
    Route::patch('employees/{employee}/activate', [EmployeeController::class, 'activate']);
    Route::patch('employees/{employee}/deactivate', [EmployeeController::class, 'deactivate']);

    Route::get('time-entries', [TimeEntryController::class, 'index']);
    Route::post('time-entries', [TimeEntryController::class, 'create']);
    Route::get('time-entries/{timeEntry}', [TimeEntryController::class, 'show']);
    Route::match(['put', 'patch'], 'time-entries/{timeEntry}', [TimeEntryController::class, 'update']);
    Route::delete('time-entries/{timeEntry}', [TimeEntryController::class, 'delete']);

    Route::get('reports/time-entries', [TimeEntryReportController::class, 'index']);
    Route::get('reports/time-entries/export', [TimeEntryReportController::class, 'export']);
});

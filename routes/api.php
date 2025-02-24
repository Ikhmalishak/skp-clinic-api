<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExcelImportController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// ✅ Employees (Patients) can register for queue WITHOUT LOGIN
Route::post('/queue/register', [QueueController::class, 'register']);
Route::post('/queue/getcurrentserving', [QueueController::class, 'getCurrentServing']);
Route::post('/changepassword', [AuthController::class, 'changePassword']);

// ✅ Employees (Patients) can check queue status WITHOUT LOGIN
Route::get('/queue', [QueueController::class, 'index']);

Route::middleware(EnsureFrontendRequestsAreStateful::class)->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
});

// ✅ Admin & Doctor Routes (Require Login)
Route::middleware('auth:sanctum')->group(function () {
    
    //employees
    Route::apiResource('employees', EmployeeController::class); // Admin manages employees
    Route::post('/employees/import', [ExcelImportController::class, 'import']);

    //queue
    Route::patch('/queue/{id}/status', [QueueController::class, 'updateStatus']); // Doctor updates queue status 
    Route::get('/queue/statistic', [QueueController::class, 'getStats']);//api to get the data statistic
    Route::get('/queue/weeklystatistic', [QueueController::class, 'getWeeklyStats']);//api to get the data statistic

    //doctor
    Route::apiResource('doctors', DoctorController::class); // Admin manage doctor
});



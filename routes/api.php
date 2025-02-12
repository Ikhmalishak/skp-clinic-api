<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\AuthController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

// ✅ Employees (Patients) can register for queue WITHOUT LOGIN
Route::post('/queue/register', [QueueController::class, 'register']);

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

    //queue
    Route::patch('/queue/{id}/status', [QueueController::class, 'updateStatus']); // Doctor updates queue status 
});
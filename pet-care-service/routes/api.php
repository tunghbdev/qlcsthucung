<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ServiceController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\PetController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ServiceRequestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group.
|
*/

// Public routes
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::get('/services', [ServiceController::class, 'index']);
Route::get('/services/{id}', [ServiceController::class, 'show']);

// Protected routes (using custom token authentication)
Route::get('/auth/profile', [AuthController::class, 'profile']);

// Dashboard
Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

// Appointments
Route::get('/appointments', [AppointmentController::class, 'index']);
Route::post('/appointments', [AppointmentController::class, 'store']);
Route::put('/appointments/{id}', [AppointmentController::class, 'update']);
Route::delete('/appointments/{id}', [AppointmentController::class, 'destroy']);

// Pets
Route::get('/pets', [PetController::class, 'index']);
Route::post('/pets', [PetController::class, 'store']);
Route::put('/pets/{id}', [PetController::class, 'update']);
Route::delete('/pets/{id}', [PetController::class, 'destroy']);

// Invoices
Route::get('/invoices', [InvoiceController::class, 'index']);
Route::get('/invoices/{id}', [InvoiceController::class, 'show']);
Route::post('/invoices', [InvoiceController::class, 'store']);

// Payments
Route::get('/payments', [PaymentController::class, 'index']);
Route::post('/payments', [PaymentController::class, 'store']);

// Service Requests
Route::get('/service-requests', [ServiceRequestController::class, 'index']);
Route::post('/service-requests', [ServiceRequestController::class, 'store']);
Route::get('/service-requests/{id}', [ServiceRequestController::class, 'show']);
Route::put('/service-requests/{id}', [ServiceRequestController::class, 'update']);
Route::delete('/service-requests/{id}', [ServiceRequestController::class, 'destroy']);
Route::post('/service-requests/{id}/approve', [ServiceRequestController::class, 'approve']);
Route::post('/service-requests/{id}/reject', [ServiceRequestController::class, 'reject']);

Route::get('/user', function (Request $request) {
    return $request->user();
});

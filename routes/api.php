<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\LoanController;
use App\Models\LoanApplication;

Route::get('/health', function (Request $request) {
    return response()->json(['status' => 'ok']);
});

// Token-based API auth (mobile/partner use-case)
Route::post('/token', [AuthTokenController::class, 'store']);
Route::post('/login', [AuthTokenController::class, 'store']);
Route::delete('/token', [AuthTokenController::class, 'destroy'])->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'api.otp', 'throttle:api'])->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'id' => $user?->id,
            'name' => $user?->name,
            'email' => $user?->email,
            'role' => $user?->role?->name,
        ]);
    });

    // Example: borrower can fetch their own applications via API
    Route::get('/loans', function (Request $request) {
        $user = $request->user();

        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        $query = LoanApplication::query()->with('assignedOfficer');

        if ($user->isUser()) {
            $query->where('user_id', $user->id);
        }

        if ($user->isLoanOfficer()) {
            $query->where('assigned_officer_id', $user->id);
        }

        return response()->json($query->latest()->limit(20)->get());
    });

    // Production-ready endpoints
    Route::post('/loans', [LoanController::class, 'store']);
    Route::get('/loans/{loan}', [LoanController::class, 'show']);
    Route::get('/loans/{loan}/documents/{filename}', [LoanController::class, 'downloadDocument'])
        ->where('filename', '.*');
});

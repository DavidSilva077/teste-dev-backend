<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    UserController,
    JobController,
    CandidateController,
    ImportController,
    AuthController
};

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['api', 'auth:sanctum'])->group(function () {
    // Users
    Route::apiResource('users', UserController::class);
    Route::post('/users/bulk-delete', [UserController::class, 'bulkDelete']);

    // Jobs
    Route::apiResource('jobs', JobController::class);
    Route::post('/jobs/bulk-delete', [JobController::class, 'bulkDelete']);
    Route::post('jobs/{job}/subscribe/{candidate}', [JobController::class, 'subscribe']);

    // Candidates
    Route::apiResource('candidates', CandidateController::class);
    Route::post('/candidates/bulk-delete', [CandidateController::class, 'bulkDelete']);

    // Importação
    Route::post('import', [ImportController::class, 'import']);
    Route::get('import/analysis', [ImportController::class, 'analysis']);

    // Auth 
    Route::post('/logout', [AuthController::class, 'logout']);
});

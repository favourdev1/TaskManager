<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API route for updating task status via drag and drop - using match for multiple HTTP methods
Route::middleware(['auth:sanctum', 'web'])->group(function() {
    Route::match(['post', 'patch'], '/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->name('api.tasks.update-status');
});

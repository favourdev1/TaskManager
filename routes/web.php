<?php

use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Task management routes
    Route::resource('tasks', TaskController::class);
    Route::get('/kanban', [TaskController::class, 'kanban'])->name('tasks.kanban');
    Route::patch('/tasks/{task}/complete', [TaskController::class, 'complete'])->name('tasks.complete');
    
    // Fix for the Kanban board drag and drop
    Route::match(['post', 'patch'], '/tasks/{task}/update-status', [TaskController::class, 'updateStatus'])
        ->name('tasks.update-status')
        ->middleware('web');
});

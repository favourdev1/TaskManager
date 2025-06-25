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


Route::get('/run-migrate', function () {
    Artisan::call('migrate');
    $msg = Artisan::output();

    return response()->json([
        'migrations' => $msg,
    ]);
});

Route::get('/run-migrate-rollback', function () {
    Artisan::call('migrate:rollback');
    $msg = Artisan::output();

    return response()->json([
        'migrations' => $msg,
    ]);
});

Route::get('/run-seed', function () {
    Artisan::call('db:seed');
    $msg = Artisan::output();

    return response()->json([
        'migrations' => $msg,
    ]);
});

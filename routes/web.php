<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserSettingsController;
use App\Http\Controllers\UserStatsController;
use App\Http\Controllers\FocusedSessionController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('isAuthenticated', [HomeController::class, 'isAuthenticated'])->name('isAuthenticated');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
    Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    Route::post('/projects/{project}/tasks', [TaskController::class, 'store'])->name('tasks.store');
    Route::patch('/tasks/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

    // Routes for Focused Session
    Route::post('/focused-sessions', [FocusedSessionController::class, 'store'])->name('focused-sessions.store');
    Route::patch('/focused-sessions/current', [FocusedSessionController::class, 'update'])->name('focused-sessions.update');
    Route::get('/focused-sessions', [FocusedSessionController::class, 'index'])->name('focused-sessions.index');

    // Routes for User Stats
    Route::get('/user-stats', [UserStatsController::class, 'index'])->name('user-stats.index');
    Route::get('/user-stats/calendar/{year}', [UserStatsController::class, 'getCalendarData']);
    Route::get('/user-stats/calendar/{year}/{month}', [UserStatsController::class, 'getCalendarData']);
    Route::get('/user-stats/calendar/{year}/{month}/{day}', [UserStatsController::class, 'getCalendarData']);

    // Routes for User Settings
    Route::get('/user-settings', [UserSettingsController::class, 'index'])->name('user-settings.index');
    Route::patch('/user-settings', [UserSettingsController::class, 'update'])->name('user-settings.update');
    Route::get('/background', [UserSettingsController::class, 'getBackground'])->name('user-settings.getBackground');


});

require __DIR__.'/auth.php';

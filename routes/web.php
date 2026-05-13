<?php

use App\Http\Controllers\Admin\BadgeController as AdminBadgeController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\HabitController as AdminHabitController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\User\BadgeController as UserBadgeController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\HabitController as UserHabitController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (Auth::check()) {
        return Auth::user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }

    return redirect()->route('login');
})->name('welcome');

Auth::routes();

Route::middleware(['auth', 'user.role'])->prefix('app')->name('user.')->group(function (): void {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
    Route::resource('habits', UserHabitController::class)->except(['show']);
    Route::post('/habits/{habit}/log', [UserHabitController::class, 'saveLog'])->name('habits.log');
    Route::get('/badges', [UserBadgeController::class, 'index'])->name('badges.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', AdminUserController::class)->except(['show']);
    Route::resource('habits', AdminHabitController::class)->except(['show']);
    Route::resource('badges', AdminBadgeController::class)->except(['show']);
});

Route::get('/home', function () {
    return Auth::user()?->isAdmin()
        ? redirect()->route('admin.dashboard')
        : redirect()->route('user.dashboard');
})->middleware('auth')->name('home');

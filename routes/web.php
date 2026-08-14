<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->hasRole('member')) {
            return Redirect::route('member.dashboard');
        }
        return Redirect::route('portal.dashboard');
    }

    return Inertia::render('Welcome');
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
Route::post('/login', [AuthenticatedSessionController::class, 'store']);
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

Route::get('/verify-email', function () {
    return Inertia::render('Auth/VerifyEmail');
})->middleware('auth')->name('verification.notice');

require __DIR__.'/feature.php';

Route::middleware(['auth'])->prefix('member')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\MemberAppController::class, 'dashboard'])->name('member.dashboard');
    Route::get('/classes', [\App\Http\Controllers\MemberAppController::class, 'classes'])->name('member.classes');
    Route::get('/workouts', [\App\Http\Controllers\MemberAppController::class, 'workouts'])->name('member.workouts');
    Route::get('/profile', [\App\Http\Controllers\MemberAppController::class, 'profile'])->name('member.profile');
    Route::put('/profile', [\App\Http\Controllers\MemberAppController::class, 'updateProfile'])->name('member.profile.update');
});

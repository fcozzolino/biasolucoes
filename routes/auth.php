<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\SocialAuthController;

Route::middleware('guest')->group(function () {
    // Login routes
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::post('login/phone', [LoginController::class, 'loginWithPhone'])->name('login.phone');

    // Register routes
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);

    // OTP routes
    Route::post('otp/send', [OtpController::class, 'send'])->name('otp.send');
    Route::post('otp/verify', [OtpController::class, 'verify'])->name('otp.verify');

    // Social login routes
    Route::get('auth/{provider}', [SocialAuthController::class, 'redirect'])->name('social.login');
    Route::get('auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

Route::middleware('auth')->group(function () {
    // Logout
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Email verification
    Route::post('email/resend', [RegisterController::class, 'resendVerification'])->name('verification.resend');

    // Company setup (for business accounts)
    Route::post('register/company', [RegisterController::class, 'setupCompany'])->name('register.company');
    Route::post('register/modules', [RegisterController::class, 'selectModules'])->name('register.modules');

    // 2FA routes
    Route::prefix('2fa')->group(function () {
        Route::post('enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
        Route::post('confirm', [TwoFactorController::class, 'confirmEnable'])->name('2fa.confirm');
        Route::post('disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
        Route::post('recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.recovery');
    });

    // Social account management
    Route::delete('auth/{provider}', [SocialAuthController::class, 'unlink'])->name('social.unlink');
});

// 2FA verification (special case - user logged but needs 2FA)
Route::post('2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify')->middleware('web');

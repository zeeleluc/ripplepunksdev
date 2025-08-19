<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    WelcomeController,
    GiveawayController,
    AboutController,
    XamanController,
    HolderController,
    LogController,
    BadgeController,
    ClaimController,
    ProfileController
};
use App\Http\Controllers\Admin\LogEntryController;
use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\UserController;

Route::group(['middleware' => 'web'], function () {

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('login');
    Route::get('/profile/{wallet?}', [ProfileController::class, 'index'])
        ->name('profile');
    Route::get('/about-cto', [AboutController::class, 'showCtoPage'])->name('about.cto');
    Route::get('/holders', [HolderController::class, 'index'])->name('holders.index');
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/badges', [BadgeController::class, 'index'])->name('badges');
    Route::get('/shoutboard', function () {
        return view('shoutboard');
    })->name('shoutboard');
    Route::get('/rewards', [ClaimController::class, 'index'])->name('claim');

    // Giveaway routes
    Route::prefix('giveaway')->group(function () {
        Route::get('/', [GiveawayController::class, 'index'])->name('giveaway.index');
        Route::post('/{type}', [GiveawayController::class, 'store'])->name('giveaway.store');
        Route::post('/{id}/decline', [GiveawayController::class, 'decline'])->name('giveaway.decline');
        Route::post('/{id}/approve', [GiveawayController::class, 'approve'])->name('giveaway.approve');
    });

    // Xaman authenticatie routes
    Route::get('/login', [XamanController::class, 'showLoginQr'])->name('xaman.login');
    Route::prefix('xaman')->group(function () {
        Route::get('/login-check', [XamanController::class, 'loginCheck'])->name('xaman.loginCheck');
        Route::get('/callback', [XamanController::class, 'handleCallback'])->name('xaman.callback');
        Route::post('/webhook', [XamanController::class, 'handleWebhook'])->name('xaman.webhook');
        Route::post('/login-finalize', [XamanController::class, 'loginFinalize'])->name('xaman.loginFinalize');
    });

    // Logout route (met auth middleware)
    Route::post('/logout', [XamanController::class, 'logout'])
        ->middleware('auth')
        ->name('xaman.logout');

    Route::middleware(['auth', 'isAdmin'])->group(function () {
        Route::get('/admin/log-entry', [LogEntryController::class, 'index'])->name('admin.log-entry');
        Route::get('/admin/claims', [AdminClaimController::class, 'index'])->name('admin.claims');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    });
});

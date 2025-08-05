<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    WelcomeController,
    GiveawayController,
    AboutController,
    XamanController,
    HolderController
};
use App\Http\Controllers\Admin\LogEntryController;

Route::group(['middleware' => 'web'], function () {

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('login');
    Route::get('/about-cto', [AboutController::class, 'showCtoPage'])->name('about.cto');
    Route::get('/holders', [HolderController::class, 'index'])->name('holders.index');

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
    });
});

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
    PunksController,
    LaunchpadController,
    PulseController,
    CleanupController,
    TestController
};
use App\Http\Controllers\Admin\LogEntryController;
use App\Http\Controllers\Admin\ClaimController as AdminClaimController;
use App\Http\Controllers\Admin\UserController;

Route::group(['middleware' => 'web'], function () {

    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/test', [TestController::class, 'index'])->name('test');
    Route::get('/welcome', [WelcomeController::class, 'index'])->name('login');
    Route::get('/punks', [PunksController::class, 'index'])->name('punks');
    Route::get('/punk/{id}', [PunksController::class, 'show'])
        ->whereNumber('id')
        ->where('id', '^(?:[0-9]|[1-9][0-9]{0,3}|1[0-9]{4}|1999[0-9])$')
        ->name('punks.show');
    Route::get('/cleanup', [CleanupController::class, 'index'])->name('cleanup');
    Route::get('/pulse', [PulseController::class, 'index'])->name('pulse');
    Route::get('/launchpad', [LaunchpadController::class, 'index'])->name('launchpad.index');
    Route::get('/holder/{wallet}', [HolderController::class, 'show'])->name('holder');
    Route::get('/about-cto', [AboutController::class, 'showCtoPage'])->name('about.cto');
    Route::get('/holders', [HolderController::class, 'index'])->name('holders.index');
    Route::get('/logs', [LogController::class, 'index'])->name('logs');
    Route::get('/badges/{wallet?}', [BadgeController::class, 'index'])->name('badges');
    Route::get('/shoutboard', function () {
        return view('shoutboard');
    })->name('shoutboard');
    Route::get('/rewards', [ClaimController::class, 'index'])->name('claim');

    Route::get('/buy-modal', function () {
        return redirect()->back()->with('showBuyModal', true);
    })->name('showBuyModal');

    // Giveaway routes
    Route::prefix('giveaway')->group(function () {
        Route::get('/', [GiveawayController::class, 'index'])->name('giveaway.index');
        Route::post('/{type}', [GiveawayController::class, 'store'])->name('giveaway.store');
        Route::post('/{id}/decline', [GiveawayController::class, 'decline'])->name('giveaway.decline');
        Route::post('/{id}/approve', [GiveawayController::class, 'approve'])->name('giveaway.approve');
    });

    // Xaman authentication and payment routes
    Route::get('/login', [XamanController::class, 'showLoginQr'])->name('xaman.login');
    Route::prefix('xaman')->group(function () {
        Route::get('/login-check', [XamanController::class, 'loginCheck'])->name('xaman.loginCheck');
        Route::post('/login-store', [XamanController::class, 'loginStore'])->name('xaman.loginStore');
        Route::get('/callback', [XamanController::class, 'handleCallback'])->name('xaman.callback');
        Route::post('/webhook', [XamanController::class, 'handleWebhook'])->name('xaman.webhook');
        Route::post('/login-finalize', [XamanController::class, 'loginFinalize'])->name('xaman.loginFinalize');
    });

    // Logout route (with auth middleware)
    Route::post('/logout', [XamanController::class, 'logout'])
        ->middleware('auth')
        ->name('xaman.logout');

    Route::middleware(['auth', 'isAdmin'])->group(function () {
        Route::get('/admin/log-entry', [LogEntryController::class, 'index'])->name('admin.log-entry');
        Route::get('/admin/claims', [AdminClaimController::class, 'index'])->name('admin.claims');
        Route::get('/admin/users', [UserController::class, 'index'])->name('admin.users');
    });
});



use App\Http\Controllers\ChecksumController;

Route::get('/checksum/{checksum}', [ChecksumController::class, 'check']);

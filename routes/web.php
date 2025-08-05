<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\{GiveawayController, AboutController, XamanController};

Route::group(['middleware' => 'web'], function () {
    // Sessie routes
    Route::get('/set', function () {
        Session::put('foo', 'bar');
        return 'Set';
    });

    Route::get('/get', fn() => 'Get: ' . Session::get('foo', 'not found'));
    // Welcome pagina
    Route::get('/', fn() => view('welcome', [
        'totalItems' => 10000,
        'bar1Count' => 10000,
        'bar2Count' => 110,
        'colors' => [
            'bar1' => ['#006EFF', '#006EFF'],
            'bar2' => ['#006EFF', '#006EFF'],
        ]
    ]))->name('welcome');

    // About pagina
    Route::get('/about-cto', [AboutController::class, 'showCtoPage'])->name('about.cto');

    // Giveaway routes
    Route::prefix('giveaway')->group(function () {
        Route::get('/{type}', [GiveawayController::class, 'index'])->name('giveaway.index');
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
});

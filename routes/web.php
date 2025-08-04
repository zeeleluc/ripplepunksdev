<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiveawayController;
use App\Http\Controllers\AboutController;

use App\Http\Controllers\XamanController;

Route::get('/login', [XamanController::class, 'showLoginQr']);
Route::get('/xaman/login-check', [XamanController::class, 'loginCheck']);
Route::get('/xaman/callback', [XamanController::class, 'handleCallback'])->name('xaman.callback');
Route::post('/xaman/webhook', [XamanController::class, 'handleWebhook'])->name('xaman.webhook');
Route::post('/xaman/login-finalize', [XamanController::class, 'loginFinalize'])->name('xaman.loginFinalize');

Route::get('/', function () {
    $totalItems = 10000;

    // Example progress counts for each bar
    $bar1Count = 10000;
    $bar2Count = 100;

    // Colors for bars: 2 colors per bar (gradient)
    $colors = [
        'bar1' => ['#006EFF', '#006EFF'],
        'bar2' => ['#006EFF', '#006EFF'],
    ];

    return view('welcome', compact('totalItems', 'bar1Count', 'bar2Count', 'colors'));
})->name('welcome');

Route::get('/about-cto', [AboutController::class, 'showCtoPage']);

Route::get('/giveaway/{type}', [GiveawayController::class, 'index']);
Route::post('/giveaway/{type}', [GiveawayController::class, 'store']);
Route::post('/giveaway/{id}/decline', [GiveawayController::class, 'decline']);
Route::post('/giveaway/{id}/approve', [GiveawayController::class, 'approve']);

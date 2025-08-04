<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GiveawayController;
use App\Http\Controllers\AboutController;

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
});

Route::get('/about-cto', [AboutController::class, 'showCtoPage']);

Route::get('/giveaway/{type}', [GiveawayController::class, 'index']);
Route::post('/giveaway/{type}', [GiveawayController::class, 'store']);
Route::post('/giveaway/{id}/decline', [GiveawayController::class, 'decline']);
Route::post('/giveaway/{id}/approve', [GiveawayController::class, 'approve']);

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChecksumController;

Route::post('/check-punk', [ChecksumController::class, 'checkPunk']);


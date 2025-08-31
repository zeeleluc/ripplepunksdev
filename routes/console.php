<?php

use Illuminate\Support\Facades\Schedule;

// ========== Production ==========
if (app()->environment('prod')) {
    Schedule::command('nfts:sync', [
        '--issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
        '--taxon'  => '604',
    ])->everyThirtyMinutes();

    Schedule::command('holders:sync')->everyTenMinutes();
    Schedule::command('xrp:fetch-price')->everyMinute();
    Schedule::command('xrpl:fetch-sales')->everyMinute();

    // Daily AM
    Schedule::call(function () {
        (new \App\Services\XPost())->tweetGm();
    })->timezone('America/New_York')->dailyAt('06:00');

    // Daily AM
    Schedule::call(function () {
        (new \App\Services\XPost())->tweetXRPTrendImage();
    })->timezone('America/New_York')->dailyAt('00:05');

    // Daily midnight
    Schedule::call(function () {
        (new \App\Services\XPost())->tweetMarketplacePieChart();
    })->timezone('America/New_York')->dailyAt('14:07');

    // Weekly
    Schedule::call(function () { // Saturday
        (new \App\Services\XPost())->tweetLeftRight();
    })->timezone('America/New_York')->weeklyOn(6, '09:30');
}

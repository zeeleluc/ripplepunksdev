<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Artisan;

// ========== Production ==========
if (app()->environment('prod')) {
    Schedule::command('nfts:sync', [
        '--issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
        '--taxon'  => '604',
    ])->everyThirtyMinutes();

    Schedule::command('holders:sync')->everyTenMinutes();
    Schedule::command('xrp:fetch-price')->everyMinute();
    Schedule::command('xrpl:fetch-sales')->everyMinute();

    Schedule::call(function () {
        Artisan::call('nfts:fill-skin');
        Artisan::call('nfts:generate-checksums');
    })->everyTenMinutes();

    // Daily
    Schedule::call(function () {
        (new \App\Services\XPost())->tweetRandomFourImages();
    })->timezone('America/New_York')->dailyAt('03:03');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetGm();
    })->timezone('America/New_York')->dailyAt('06:00');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetRepostPinned();
    })->timezone('America/New_York')->dailyAt('10:11');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetMarketplacePieChart();
    })->timezone('America/New_York')->dailyAt('14:07');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetRandomImage();
    })->timezone('America/New_York')->dailyAt('19:15');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetTopWallets();
    })->timezone('America/New_York')->dailyAt('20:58');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetXrpPrice();
    })->timezone('America/New_York')->dailyAt('23:23');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetXRPTrendImage();
    })->timezone('America/New_York')->dailyAt('00:18');


    // Weekly
    Schedule::call(function () { // Friday
        (new \App\Services\XPost())->tweetWebsiteAdImage();
    })->timezone('America/New_York')->weeklyOn(5, '10:22');

    Schedule::call(function () { // Saturday
        (new \App\Services\XPost())->tweetLeftRight();
    })->timezone('America/New_York')->weeklyOn(6, '09:30');
}

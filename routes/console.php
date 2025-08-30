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

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetGm();
    })->dailyAt('06:00')->timezone('America/New_York');

    Schedule::call(function () {
        (new \App\Services\XPost())->tweetLeftRight();
    })->weeklyOn(6, '09:30')->timezone('America/New_York');



    Schedule::call(function () {
        $xPost = new \App\Services\XPost();
        $xPost->tweetXRPTrendImage();
    })->timezone('America/New_York')
        ->dailyAt('00:20');

}

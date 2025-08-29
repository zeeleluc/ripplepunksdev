<?php

use Illuminate\Support\Facades\Schedule;

// ========== Production ==========
if (app()->environment('prod')) {
    Schedule::command('nfts:sync', [
        '--issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
        '--taxon'  => '604',
    ])->everyThirtyMinutes();

    Schedule::command('holders:sync')->everyTenMinutes();
    Schedule::command('xrp:fetch-price')->everyFifteenSeconds();
    Schedule::command('xrpl:fetch-sales')->everyMinute();
}

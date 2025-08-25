<?php

use Illuminate\Support\Facades\Schedule;

// ========== Production ==========
if (app()->environment('prod')) {
    Schedule::command('nfts:sync', [
        '--issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
        '--taxon'  => '604',
    ])->everyFifteenMinutes();

    Schedule::command('holders:sync')->everyMinute();
    Schedule::command('nfts:sync-images')->everyThirtyMinutes();
}

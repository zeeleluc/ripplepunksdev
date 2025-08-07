<?php

use Illuminate\Console\Scheduling\Schedule;

$schedule = app(Schedule::class);

// ========== Production ==========
if (app()->environment('prod')) {
    $schedule->command('nfts:sync', [
        '--issuer' => 'r3SvAe5197xnXvPHKnyptu3EjX5BG8f2mS',
        '--taxon'  => '604',
    ])->cron('*/20 * * * *'); // runs every 20 minutes
}

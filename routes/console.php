<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel unpaid orders every hour
Schedule::command('orders:auto-cancel')->hourly();

// Auto-complete delivered orders every 6 hours
Schedule::command('orders:auto-complete')->everySixHours();

<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Automatically release student sessions when a PC loses its heartbeat.
Schedule::command('app:expire-disconnected-student-sessions')
    ->everyMinute()
    ->withoutOverlapping();
<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Programar revisión de pendientes (Ejecutar todos los días a las 8:00 AM)
Schedule::command('pendientes:reminders')->dailyAt('08:00');

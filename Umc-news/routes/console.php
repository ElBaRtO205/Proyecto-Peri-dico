<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Convierte las noticias programadas en publicadas en cuanto llega su hora.
Schedule::command('noticias:publicar-programadas')
    ->everyMinute()
    ->withoutOverlapping();

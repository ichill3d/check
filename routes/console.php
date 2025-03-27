<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('team:send-pending-invitations')->everyMinute();
Schedule::command('team:send-checklist-items-summaries')->everyFiveMinutes();

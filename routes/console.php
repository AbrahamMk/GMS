<?php

use App\Console\Commands\DispatchRenewalReminders;
use App\Console\Commands\ProcessMembershipExpiry;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command(ProcessMembershipExpiry::class)->hourly();
Schedule::command(DispatchRenewalReminders::class)->dailyAt('09:00');

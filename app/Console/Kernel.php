<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Jobs\BookingReminderNotification;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        $schedule->job(new BookingReminderNotification)->everyMinute();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
    }
}

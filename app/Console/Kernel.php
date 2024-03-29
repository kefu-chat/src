<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cancel:subscription-expired-enterprise')->dailyAt('00:00')->withoutOverlapping();
        $schedule->command('clear:empty-visitor')->dailyAt('01:00')->withoutOverlapping();
        $schedule->command('terminate:timeout-conversation')->everyMinute()->withoutOverlapping(300);
        $schedule->command('conversation:noreply')->everyMinute()->withoutOverlapping(300);
        // $schedule->command('visitor:timeout')->hourly()->withoutOverlapping(3300);
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

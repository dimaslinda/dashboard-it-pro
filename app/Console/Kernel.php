<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Check for expiring domains and hosting every day at 9:00 AM
        $schedule->command('expiry:check --days=3')
                 ->dailyAt('09:00')
                 ->description('Check for domains and hosting expiring in 3 days');
        
        // Additional check at 2:00 PM for same day expiry
        $schedule->command('expiry:check --days=0')
                 ->dailyAt('14:00')
                 ->description('Check for domains and hosting expiring today');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
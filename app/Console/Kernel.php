<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('telescope:prune --hours=12')->everyFiveMinutes();
        $schedule->command('send:summary')->dailyAt('21:00')->timezone('Asia/Hong_Kong');
        $schedule->command('bank:balance')->dailyAt('21:00')->timezone('Asia/Hong_Kong');
        $schedule->command('backup:run')->dailyAt('06:00')->timezone('Asia/Hong_Kong');
        // $schedule->command('stock:alert')->weekly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
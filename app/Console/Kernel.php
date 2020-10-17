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
        Commands\GetPermits::class,
        Commands\GetWells::class,
        Commands\GetLandtracLeases::class,
        Commands\GetLegalLeases::class,
        Commands\DailyReport::class,
        Commands\DetermineProduction::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('process:DailyReport')->dailyAt(7)->timezone('America/New_York');
        $schedule->command('process:getPermits')->dailyAt(6)->timezone('America/New_York');
      //  $schedule->command('process:getWells')->dailyAt(23)->timezone('America/New_York');
        $schedule->command('determine:production')->dailyAt(8)->timezone('America/New_York');
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

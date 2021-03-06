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
        Commands\TruncateData::class,
        Commands\RefreshDatabase::class,
        Commands\RefreshProfiles::class,
        Commands\FakeGraph::class,
        Commands\ListNoData::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Check if new data is present every minute
        $schedule->command('database:refresh')->everyMinute();
        $schedule->command('database:refresh --towz --force')->everyMinute();

        // Refresh profile's information every day
        $schedule->command('profiles:refresh')->daily();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}

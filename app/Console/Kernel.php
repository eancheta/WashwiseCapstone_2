<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Register your test command here
        \App\Console\Commands\TestCloudinaryUpload::class,
    ];

    protected function schedule(\Illuminate\Console\Scheduling\Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

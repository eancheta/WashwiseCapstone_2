<?php

namespace App\Console;

use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\ShopBookingController;

use Illuminate\Console\Scheduling\Schedule;
class Kernel extends ConsoleKernel
{
    protected $commands = [
        // Register your test command here
        \App\Console\Commands\TestCloudinaryUpload::class,
    ];

    protected function schedule(Schedule $schedule)
    {
                $schedule->call(function () {
            app(ShopBookingController::class)->sendBookingReminders();
        })->everyMinute();
        // $schedule->command('inspire')->hourly();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

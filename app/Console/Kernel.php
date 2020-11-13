<?php

namespace App\Console;

use App\Console\Commands\ExpressSync;
use App\Console\Commands\OrderTimeOut;
use App\Console\Commands\SendAgentBirthday;
use App\Console\Commands\SendMemberBirthday;
use App\Console\Commands\DevReset;
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
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        //消息提醒
        $schedule->command ('notice:send')->everyMinute ();
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

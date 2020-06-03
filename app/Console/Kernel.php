<?php

namespace App\Console;

use App\Console\Commands\ExpireLeaveApplication;
use App\Console\Commands\ExpireLeaveApplications;
use App\Console\Commands\LoadLeaveBalances;
use App\Console\Commands\ResignEmployees;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ExpireLeaveApplications::class,
        ExpireLeaveApplication::class,
        LoadLeaveBalances::class,
        ResignEmployees::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('leave_applications:expire')->daily();
        $schedule->command('employees:resign')->daily();
    }
}

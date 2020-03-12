<?php

namespace App\Console;

use App\Jobs\DailyTaskForReports;
use App\Jobs\DailyTasks;
use App\Jobs\MinuteTasks;
use App\Jobs\MonthlyTask;
use App\Jobs\WeeklyTask;
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
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //For this to work we need to set a minute crone
        // * * * * * php artisan schedule:run >> /dev/null 2>&1

        /* Enable this code to test email reminders in a minute base
           Should be commented before merging to master though. */
        /*$schedule->call(function () {
            dispatch(new DailyTasks());
        })->name('DailyTasks2')->everyMinute();*/
        if (\App::environment(['local'])) {
            $schedule->call(function () {
                dispatch(new MinuteTasks());
            })->name('MinuteTask')->everyMinute()->withoutOverlapping();
            $schedule->call(function () {
                dispatch(new DailyTasks());
            })->name('DailyTasks')->everyFiveMinutes()->withoutOverlapping();
            $schedule->call(function () {
                dispatch(new WeeklyTask());
            })->name('WeeklyTasks')->everyTenMinutes()->withoutOverlapping();
            $schedule->call(function () {
                dispatch(new MonthlyTask());
            })->name('MonthlyTasks')->everyThirtyMinutes()->withoutOverlapping();
            $schedule->call(function () {
                dispatch(new DailyTaskForReports());
            })->name('DailyTaskForReports')->everyMinute()->timezone('America/Denver')->withoutOverlapping();
            return;
        }

        $schedule->call(function () {
            dispatch(new MinuteTasks());
        })->name('MinuteTask')->everyMinute();
        $schedule->call(function () {
            dispatch(new DailyTasks());
        })->name('DailyTasks')->daily();
        $schedule->call(function () {
            dispatch(new WeeklyTask());
        })->name('WeeklyTasks')->weekly();
        $schedule->call(function () {
            dispatch(new MonthlyTask());
        })->name('MonthlyTasks')->monthly();
        $schedule->call(function () {
            dispatch(new DailyTaskForReports());
        })->name('DailyTaskForReports')->dailyAt('17:54')->timezone('America/Denver');
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

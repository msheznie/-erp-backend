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
        'App\Console\Commands\QueueWork'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('command:newPR')->daily()->withoutOverlapping();
        $schedule->command('command:queuework')->everyMinute()->withoutOverlapping();
        $schedule->command('invoiceDueReminder')->daily()->withoutOverlapping();
        $schedule->command('notification_service')->daily()->withoutOverlapping();
        $schedule->command('leave_accrual_schedule')->daily()->withoutOverlapping();
        $schedule->command('financialPeriodActivation')->daily()->withoutOverlapping();
        $schedule->command('itemWACAmountPost')->daily()->withoutOverlapping();
        $schedule->command('command:recurringVoucher')->daily()->withoutOverlapping();
        $schedule->command('command:reversePoAccrual')->daily()->withoutOverlapping();
        $schedule->command('command:delegationActive')->daily()->withoutOverlapping();

        $schedule->command('pull-attendance')
        ->timezone('Asia/Muscat')
        ->dailyAt('00:30')
        ->withoutOverlapping();

        $schedule->command('command:forgotToPunchIn')
            ->timezone('Asia/Muscat')
            ->hourly()
            ->between('8:00', '13:00')
            ->withoutOverlapping();

        $schedule->command('command:forgotToPunchOut')
            ->timezone('Asia/Muscat')
            ->dailyAt('07:00')
            ->withoutOverlapping();

        $schedule->command('command:attendanceDailySummary')
            ->timezone('Asia/Muscat')
            ->dailyAt('07:00')
            ->withoutOverlapping();

        $schedule->command('command:attendanceWeeklySummary')
            ->timezone('Asia/Muscat')
            ->weeklyOn(5, '09:00')
            ->withoutOverlapping();

        $schedule->command('command:birthday_wish_schedule')
            ->timezone('Asia/Muscat')
            ->dailyAt('02:00')
            ->withoutOverlapping(); 

        $schedule->command('command:leaveCarryForwardComputationSchedule')
            ->timezone('Asia/Muscat')
            ->dailyAt('21:00')
            ->withoutOverlapping();
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

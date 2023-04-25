<?php namespace Jakten\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Jakten\Console\Commands\{CancelReservation,
    DatabaseReset,
    DeliverCourses,
    ExtendExpiresAtKlarnaOrder,
    DeliverGiftCards,
    FetchLatLng,
    GetStats,
    ResetLoyaltyLevel,
    SendEmails,
    SendReminderPromoteEmails,
    SendTestMail,
    SetCourseGeolocation,
    Sitemap,
    TransferData,
    UpdateCoursesLatAndLng};

/**
 * Class Kernel
 * @package Jakten\Console
 */
class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        DatabaseReset::class,
        FetchLatLng::class,
        SendEmails::class,
        SendReminderPromoteEmails::class,
        SendTestMail::class,
        TransferData::class,
        DeliverCourses::class,
        ExtendExpiresAtKlarnaOrder::class,
        SetCourseGeolocation::class,
        CancelReservation::class,
        Sitemap::class,
        GetStats::class,
        DeliverGiftCards::class,
        ResetLoyaltyLevel::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('school:statistics')->dailyAt('08:00');
        $schedule->command('school:statistics')->dailyAt('12:00');
        $schedule->command('school:statistics')->dailyAt('16:00');
        $schedule->command('kkj:deliver_courses')->dailyAt('05:00');
        $schedule->command('kkj:deliver_courses')->dailyAt('17:00');
        $schedule->command('kkj:deliver_gift_cards')->dailyAt('06:00');
        $schedule->command('kkj:sitemap')->daily();
        $schedule->command('orders:extend')->hourly();
        $schedule->command('kkj:reset_loyalty_level')->cron('0 0 1 5 *');
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

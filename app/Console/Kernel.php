<?php
namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;

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
        // DB::table('headers')->where('id', '=', 1)->update(['user_id' => 10]);
        // $schedule->call(function () {
        //     DB::table('headers')->where('id', '=', 1)->update(['user_id' => 1]);
        //     $hc = new \App\Http\Controllers\Api\V1\HomeController();
        //     $hc->test();
        // })->everyMinute();
        $schedule->call('\App\Http\Controllers\Api\V1\HomeController@test')->daily();
    }
}
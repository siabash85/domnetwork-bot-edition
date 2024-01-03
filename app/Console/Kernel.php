<?php

namespace App\Console;

use Illuminate\Support\Facades\Http;
use Modules\Setting\Entities\Setting;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            $res = Http::get("http://api.navasan.tech/latest/?api_key=freeMmtSq8Pcj2uIGyG9f2ZZq3TSAPj7");
            $obj = json_decode($res->body());
            Setting::query()->updateOrCreate([
                'name' => "usd_amount"
            ], [
                'value' => $obj->usd->value
            ]);
        })->everySixHours();
        //everySixHours
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Artisan komutlarını kaydet
     */
    protected $commands = [
        // Buraya kendi komutunu ekleyebilirsin
        \App\Console\Commands\SendDailyReport::class,
    ];

    /**
     * Scheduler ayarları
     */
    protected function schedule(Schedule $schedule)
    {
        // Her gün saat 20:00'de çalıştır
        $schedule->command('report:daily')->dailyAt('20:00');
    }

    /**
     * Komutları yükle
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

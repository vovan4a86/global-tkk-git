<?php namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel {

	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		'App\Console\Commands\Inspire',
		'App\Console\Commands\Test',
		Commands\SitemapCommand::class,
		Commands\ImportProductsCommand::class,
		Commands\ExportProductsCommand::class,
	];

	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{

        $schedule->command('import:products')->everyMinute()->withoutOverlapping()->sendOutputTo(
            storage_path() . '/logs/queue-jobs.log');

        $schedule->command('export:products')->dailyAt('05:00')->withoutOverlapping()
            ->sendOutputTo(storage_path() . '/logs/queue-jobs.log');

        $schedule->command('sitemap')->dailyAt('01:15');
	}
	//в крон прописать - php artisan schedule:run
}

<?php namespace App\Console\Commands;

use Illuminate\Console\Command;

class Test extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $signature = 'test';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Display an inspiring quote';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
        return 0;
	}

}

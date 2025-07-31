<?php

namespace App\Console\Commands;

use App\Imports\ProductsImport;
use Fanky\Admin\Models\AdminLog;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class ImportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products from file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = resource_path('price.xlsx');
        if (!File::exists($file)) {
            $this->info('Updating file not exist');
            return 0;
        }

        $this->info('Price file found');
        $last_update = Carbon::createFromTimestamp(0);
        $last_update_file = resource_path('.last_update');
        if (File::exists($last_update_file)) {
            $last_update = Carbon::createFromTimestamp(File::get($last_update_file));
        }
        $file_modify = Carbon::createFromTimestamp(File::lastModified($file));
        if ($file_modify->greaterThan($last_update)) {
            AdminLog::$processLog = false;

            $this->output->title('Starting import');
            (new ProductsImport())->withOutput($this->output)->import($file);
            $this->output->success('Import successful');

            File::put($last_update_file, $file_modify->timestamp);
            AdminLog::$processLog = true;
            AdminLog::add('Каталог был успешно обновлен.');
        }
    }
}

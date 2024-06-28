<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class CheckBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check today s backup';

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
        $exist = false;
        $files = Storage::disk('local')->allFiles('1ztUHrxOTcyCT9e6FRsNN3QHD2n5ViI1m');
        foreach ($files as $file) {
            $today = date('Y-m-d');
            if (str_contains($file, $today)) {
                $exist = true;
            }
        }
        if (!$exist) {
            Artisan::call('backup:run');
        }
        return 0;
    }
}
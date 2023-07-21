<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Http\Controllers\Admin\TinyPNGController;
class ConvertImageToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertimagetowebp:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Image To WEBP by Tiny PNG';

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
        $tinyPNGController = new TinyPNGController();
        $msg = $tinyPNGController->convertImageToWebp();
        \Log::info("Convert Image To Webp Cron is working fine!: {$msg}");
        return 0;
    }
}

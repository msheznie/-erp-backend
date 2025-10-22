<?php

namespace App\Console\Commands;

use App\Jobs\CleanExpiredSignedPdfUrls;
use Illuminate\Console\Command;

class CleanSignedPdfCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:clean-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean expired signed PDF URL cache entries';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            CleanExpiredSignedPdfUrls::dispatch();
        } catch (\Exception $e) {
            $this->error('✗ Cache cleanup failed: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

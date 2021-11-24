<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ReOrderItemPR;
use Illuminate\Support\Facades\Auth;
class ReOrderItemPRScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:newPR';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reorder level PR';

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
        ReOrderItemPR::dispatch(Auth::user());
    }
}

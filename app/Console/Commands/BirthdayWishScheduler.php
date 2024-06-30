<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use Illuminate\Console\Command;
use App\Jobs\BirthdayWishInitiate;
use Illuminate\Support\Facades\Log;


class BirthdayWishScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:birthday_wish_schedule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Birth day wish scheduler';

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
        Log::useFiles( CommonJobService::get_specific_log_file('birthday-wishes') );
        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
        }

        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;

                . __CLASS__ . " \tline no :" . __LINE__);

            BirthdayWishInitiate::dispatch($tenant_database);
        }
    }
}

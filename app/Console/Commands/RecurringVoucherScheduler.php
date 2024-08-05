<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Jobs\CreateRecurringVoucherDocument;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RecurringVoucherScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:recurringVoucher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recurring Voucher Generate';

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
        Log::useFiles( CommonJobService::get_specific_log_file('recurring-voucher') );


        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return;
        }

        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;
            $res = CreateRecurringVoucherDocument::dispatch($tenantDb);
        }
    }
}

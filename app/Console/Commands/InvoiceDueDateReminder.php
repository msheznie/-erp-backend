<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Jobs\InvoiceDueReminderJob;

class InvoiceDueDateReminder extends Command
{
    private $customerInvoiceRepoRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoiceDueReminder';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Invoice Due Reminder';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceRepo)
    {
        parent::__construct();
        $this->customerInvoiceRepoRepository = $customerInvoiceRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Customer Invoice Due Reminder'.now());

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            Log::info("Tenant details not found. \t on file: " . __CLASS__ ." \tline no :".__LINE__);
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;

            Log::info("{$tenant_database} DB added to queue for invoice due reminder . \t on file: " . __CLASS__ ." \tline no :".__LINE__);

            InvoiceDueReminderJob::dispatch($tenant_database);
        }        
    }
}

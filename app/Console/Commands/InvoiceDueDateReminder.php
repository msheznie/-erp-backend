<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Support\Facades\Log;

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

        $this->customerInvoiceRepoRepository->croneJobCustomerInvoiceReminder();
    }
}

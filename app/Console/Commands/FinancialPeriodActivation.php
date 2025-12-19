<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CompanyFinanceYearRepository;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Jobs\FinancePeriodActivationJob;

class FinancialPeriodActivation extends Command
{
    private $companyFinanceYearRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'financialPeriodActivation';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Financial Period Activation ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(CompanyFinanceYearRepository $companyFinanceRepo)
    {
        parent::__construct();
        $this->companyFinanceYearRepository = $companyFinanceRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
        }


        foreach ($tenants as $tenant){
            $tenant_database = $tenant->database;


            FinancePeriodActivationJob::dispatch($tenant_database);
        }        
    }
}

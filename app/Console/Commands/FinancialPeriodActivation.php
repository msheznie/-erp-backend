<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Repositories\CompanyFinanceYearRepository;
use Illuminate\Support\Facades\Log;

class FinancialPeriodActivation extends Command
{
    private $CompanyFinanceYearRepository;
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
        $this->CompanyFinanceYearRepository = $companyFinanceRepo;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('Financial Period Activation'.now());

        $this->CompanyFinanceYearRepository->croneJobFinancialPeriodActivation();
    }
}

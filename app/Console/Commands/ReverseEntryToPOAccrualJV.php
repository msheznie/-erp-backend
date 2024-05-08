<?php

namespace App\Console\Commands;

use App\helper\CommonJobService;
use App\Http\Controllers\API\JvMasterAPIController;
use App\Jobs\ReversePOAccrual;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\DocumentApproved;
use App\Models\DocumentMaster;
use App\Models\JvDetail;
use App\Services\UserTypeService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use App\Models\JvMaster;
use Illuminate\Support\Facades\Log;

class ReverseEntryToPOAccrualJV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:reversePoAccrual';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command reverse po accrual entry';

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

        $tenants = CommonJobService::tenant_list();
        if(count($tenants) == 0){
            return;
        }

        foreach ($tenants as $tenant){
            $tenantDb = $tenant->database;
            $res = ReversePOAccrual::dispatch($tenantDb);
        }



    }
}

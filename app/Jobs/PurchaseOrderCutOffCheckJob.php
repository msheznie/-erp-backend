<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\Helper;
use App\helper\CommonJobService;
use App\Models\ProcumentOrder;
use App\Models\PoCutoffJobData;
use App\Models\PoCutoffJob;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetMaster;
use App\Models\GRVDetails;
use App\helper\BudgetConsumptionService;
use App\Jobs\CompanyWiseCutOffNotificationJob;
use App\helper\NotificationService;
use App\Services\BudgetCutOffNotificationService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PurchaseOrderCutOffCheckJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $daysData;
    public $valueData;
    public $typeData;
    public $emailData;
    public $companyIDFromScenarios;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $typeData, $daysData, $valueData, $emailData, $companyIDFromScenarios)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->typeData = $typeData;
        $this->daysData = $daysData;
        $this->valueData = $valueData;
        $this->emailData = $emailData;
        $this->companyIDFromScenarios = $companyIDFromScenarios;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/budget-cutoff-po.log');  
        $db = $this->dispatch_db;
        $days = $this->daysData;
        $type = $this->typeData;
        $value = $this->valueData;
        $emails = $this->emailData;
        $companyIDFromScenario = $this->companyIDFromScenarios;
        CommonJobService::db_switch($db);

        $now = Carbon::now();
        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
                                    ->where('companySystemID', $value['companySystemID'])
                                    ->first();

        if ($checkBudget && $checkBudget->isYesNO) {
            $budgetConsumedData = BudgetConsumptionService::getBudgetIdsByConsumption($value['documentSystemID'], $value['purchaseOrderID']);

            if (count($budgetConsumedData['budgetmasterIDs']) > 0) {
                $budgetIds = array_unique($budgetConsumedData['budgetmasterIDs']);

                foreach ($budgetIds as $key1 => $value1) {
                    $budgetMaster = BudgetMaster::with(['finance_year_by', 'segment_by'])->find($value1);

                    if ($budgetMaster && $budgetMaster->finance_year_by) {
                        $cutOffDate = Carbon::parse($budgetMaster->finance_year_by->endingDate)->addMonths($budgetMaster->cutOffPeriod);

                        $diff = $now->diffInDays($cutOffDate);
                        $temp = [];
                        $temp['documentCode'] = $value['purchaseOrderCode'];
                        $temp['segment'] = ($budgetMaster && $budgetMaster->segment_by) ? $budgetMaster->segment_by->ServiceLineCode." - ".$budgetMaster->segment_by->ServiceLineDes : "";
                        $temp['currency'] = ($value['currency']) ? $value['currency']['CurrencyCode'] : "";
                        $temp['documentValue'] = ($value['currency']) ? number_format($value['poTotalSupplierTransactionCurrency'], $value['currency']['DecimalPlaces']) : number_format($value['poTotalSupplierTransactionCurrency'], 2);
                        $temp['cutOffDate'] = $cutOffDate->format('Y-m-d');

                        if (($cutOffDate > $now) && $diff > 0) {
                            if ($type == 1 && ($diff == $days)) { // Before 
                               $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                               $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);

                               PoCutoffJobData::create($temp);
                            } 
                        } else if ($cutOffDate < $now && $diff > 0) {
                            if ($type == 2 && ($diff == $days)) { // After
                               $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                               $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);
                               PoCutoffJobData::create($temp);
                            }
                        } else {
                            if ($type == 0 && ($diff == 0)) { // Same Day
                               $recivedValue = ($value['grvRecieved'] == 1)  ? GRVDetails::selectRaw('SUM(erp_grvdetails.GRVcostPerUnitSupTransCur*erp_grvdetails.noQty) as total')->where('purchaseOrderMastertID', $value['purchaseOrderID'])->first()->total : 0;
                               $temp['remainingValue'] = ($value['currency']) ? number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), $value['currency']['DecimalPlaces']) : number_format(($value['poTotalSupplierTransactionCurrency'] - $recivedValue), 2);
                               PoCutoffJobData::create($temp);
                            } 
                        }
                    }
                }
            }
        }

        $poCutOffData = PoCutoffJob::first();

        if ($poCutOffData) {
            $poCutOffData->jobCount = $poCutOffData->jobCount + 1;
            $poCutOffData->save();
            if ($poCutOffData->jobCount == $poCutOffData->poCount) {
                $cutOffPos = PoCutoffJobData::all();

                if (count($cutOffPos) > 0) {
                    $subject = "Open Purchase Order/s Reaching budget cutoff period";
                    foreach ($emails as $key => $notificationUserVal) {
                        $emailContent = BudgetCutOffNotificationService::getEmailContent($cutOffPos, $notificationUserVal[$key]['empName']);

                        $sendEmail = NotificationService::emailNotification($companyIDFromScenario, $subject, $notificationUserVal[$key]['empEmail'], $emailContent);

                        if (!$sendEmail["success"]) {
                            Log::error($sendEmail["message"]);
                        }
                    }

                }
                PoCutoffJob::truncate();
                PoCutoffJobData::truncate();
            }
        } else {
            PoCutoffJobData::truncate();
        }
    }
}

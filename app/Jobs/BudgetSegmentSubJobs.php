<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateDetails;
use App\Models\SegmentMaster;
use App\Models\UploadBudgets;
use App\Services\WebPushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class BudgetSegmentSubJobs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $subData;


    public function __construct($db, $subData)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->db = $db;
        $this->subData = $subData;
    }


    public function handle()
    {
        $subData = $this->subData;

        $segment = $subData['segment'];
        $template = $subData['template'];
        $financeYear = $subData['financeYear'];
        $year = $subData['year'];
        $month = $subData['month'];
        $employee = $subData['employee'];
        $notification = $subData['notification'];
        $uploadBudget = $subData['uploadBudget'];
        $result = $subData['result'];
        $currency = $subData['currency'];
        $totalSegments = $subData['totalSegments'];
        CommonJobService::db_switch($this->db);

        Log::useFiles(storage_path().'/logs/budget_segment_bulk_insert.log');


        DB::beginTransaction();
        try {
            $uploadBudgetCounter = UploadBudgets::find($uploadBudget->id);
            $segmentCount = $uploadBudgetCounter->counter;


            $segmentMaster = SegmentMaster::where('ServiceLineDes', $segment)->first();

            if ($segmentMaster) {
                $budgetArray = array(
                    'documentSystemID' => 65,
                    'documentID' => 'BUD',
                    'companySystemID' => $template->companySystemID,
                    'companyID' => $template->companyID,
                    'companyFinanceYearID' => $financeYear->companyFinanceYearID,
                    'serviceLineSystemID' => $segmentMaster->serviceLineSystemID,
                    'serviceLineCode' => $segmentMaster->ServiceLineCode,
                    'templateMasterID' => $template->companyReportTemplateID,
                    'Year' => $year,
                    'month' => 1,
                    'generateStatus' => 100,
                    'createdByUserSystemID' => $employee->employeeSystemID,
                    'createdByUserID' => $employee->empID,
                    'createdDateTime' => \Helper::currentDateTime(),
                    'sentNotificationAt' => $notification,
                    'cutOffPeriod' => 3,
                    'budgetUploadID' => $uploadBudget->id
                );

                $budget = BudgetMaster::create($budgetArray);

                foreach ($result as $value) {

                    $templateDetail = ReportTemplateDetails::where('description', $value['Template Description 2'])
                        ->where('companyReportTemplateID', $budget->templateMasterID)
                        ->whereHas('gllink', function ($query) use ($value) {
                            $query->where('glCode', $value['GL Code']);
                        })
                        ->first();

                    if (!empty($templateDetail)) {


                        $chartOfAccount = ChartOfAccount::where('AccountCode', $value['GL Code'])->first();

                        $currencyMaster = CurrencyMaster::where('CurrencyCode', $currency)->first();

                        $segmentMaster = SegmentMaster::find($budget->serviceLineSystemID);
                        $currencyConvection = \Helper::currencyConversion($budget->companySystemID, $currencyMaster->currencyID, $currencyMaster->currencyID, $value[$segmentMaster->ServiceLineDes]);

                        $localAmount = \Helper::roundValue($currencyConvection['localAmount']);

                        $companyMaster = Company::find($budget->companySystemID);
                        $currencyMasterLocal = CurrencyMaster::where('currencyID', $companyMaster->localCurrencyID)->first();

                        $budjetAmtLocal = 0;
                        $budjetAmtRpt = 0;
                        $counter = 1;
                        $startMonth = $month;
                        $startYear = $year;
                        for ($i = 1; $i <= 12; $i++) {
                            if($startMonth == 13){
                                $startMonth = 1;
                                $startYear = $startYear + 1;
                            }


                            if($counter == 12){
                                $budgetDetailsArray = array(
                                    'budgetmasterID' => $budget->budgetmasterID,
                                    'companySystemID' => $budget->companySystemID,
                                    'companyId' => $budget->companyID,
                                    'companyFinanceYearID' => $budget->companyFinanceYearID,
                                    'serviceLineSystemID' => $budget->serviceLineSystemID,
                                    'serviceLine' => $segmentMaster->ServiceLineCode,
                                    'templateDetailID' => isset($templateDetail->detID) ? $templateDetail->detID : null,
                                    'chartOfAccountID' => isset($chartOfAccount->chartOfAccountSystemID) ? $chartOfAccount->chartOfAccountSystemID : null,
                                    'glCode' => $value['GL Code'],
                                    'glCodeType' => isset($chartOfAccount->controlAccounts) ? $chartOfAccount->controlAccounts : null,
                                    'Year' => $startYear,
                                    'month' => $startMonth,
                                    'budjetAmtLocal' => round(round($localAmount, $currencyMasterLocal->DecimalPlaces) - $budjetAmtLocal,$currencyMasterLocal->DecimalPlaces),
                                    'budjetAmtRpt' => round(round($value[$segmentMaster->ServiceLineDes], $currencyMaster->DecimalPlaces) - $budjetAmtRpt,$currencyMaster->DecimalPlaces),
                                    'createdByUserSystemID' => $employee->employeeSystemID,
                                    'createdByUserID' => $employee->empID,
                                    'createdDateTime' => \Helper::currentDateTime(),
                                );
                            }
                            else {
                                $budgetDetailsArray = array(
                                    'budgetmasterID' => $budget->budgetmasterID,
                                    'companySystemID' => $budget->companySystemID,
                                    'companyId' => $budget->companyID,
                                    'companyFinanceYearID' => $budget->companyFinanceYearID,
                                    'serviceLineSystemID' => $budget->serviceLineSystemID,
                                    'serviceLine' => $segmentMaster->ServiceLineCode,
                                    'templateDetailID' => isset($templateDetail->detID) ? $templateDetail->detID : null,
                                    'chartOfAccountID' => isset($chartOfAccount->chartOfAccountSystemID) ? $chartOfAccount->chartOfAccountSystemID : null,
                                    'glCode' => $value['GL Code'],
                                    'glCodeType' => isset($chartOfAccount->controlAccounts) ? $chartOfAccount->controlAccounts : null,
                                    'Year' => $startYear,
                                    'month' => $startMonth,
                                    'budjetAmtLocal' => round($localAmount / 12, $currencyMasterLocal->DecimalPlaces),
                                    'budjetAmtRpt' => round($value[$segmentMaster->ServiceLineDes] / 12, $currencyMaster->DecimalPlaces),
                                    'createdByUserSystemID' => $employee->employeeSystemID,
                                    'createdByUserID' => $employee->empID,
                                    'createdDateTime' => \Helper::currentDateTime(),
                                );

                                $budjetAmtLocal = $budjetAmtLocal + round($localAmount / 12, $currencyMasterLocal->DecimalPlaces);
                                $budjetAmtRpt = $budjetAmtRpt + round($value[$segmentMaster->ServiceLineDes] / 12, $currencyMaster->DecimalPlaces);
                            }

                            $counter++;
                            $startMonth++;
                            $budgetDetails = Budjetdetails::create($budgetDetailsArray);

                        }

                    }
                }
            }

            $uploadBudgetCounter->increment('counter');

            $uploadBudgetCounter->save();

            $newCounterValue = $uploadBudgetCounter->counter;

            if ($newCounterValue == $totalSegments) {
                $webPushData = [
                    'title' => "Upload Budget Successfully Completed",
                    'body' => "",
                    'url' => "general-ledger/budget-upload",
                    'path' => "",
                ];

               WebPushNotificationService::sendNotification($webPushData, 2, [$employee->employeeSystemID], $this->db);
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 1]);
            }

            DB::commit();

        }
        catch (\Exception $e){
            DB::rollback();
            Log::error($this->failed($e));

           //  $webPushData = [
           //      'title' => "Upload Budget Failed",
           //      'body' => "",
           //      'url' => "general-ledger/budget-upload",
           //      'path' => "",
           //  ];

           // WebPushNotificationService::sendNotification($webPushData, 2, [$employee->employeeSystemID], $this->db);
            try {
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 0]);
                $this->logUpdate($template->companySystemID,$uploadBudget->id, $e->getMessage(),$e->getLine());
                DB::commit();
            } catch (\Exception $e){
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 0]);
                $this->logUpdate($template->companySystemID,$uploadBudget->id, $e->getMessage(),$e->getLine());
  
                DB::commit();
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }

    public function logUpdate($comapny,$budgetID,$msg,$line)
    {
    
        $uploadLogArray = array(
            'companySystemID' => $comapny,
            'bugdet_upload_id' => $budgetID,
            'is_failed' => 1,
            'error_line' => $line,
            'log_message' => $msg
        );

        $logUploadBudget= logUploadBudget::create($uploadLogArray);

      return true;
    }
}

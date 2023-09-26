<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\CurrencyMaster;
use App\Models\ReportTemplateDetails;
use App\Models\SegmentMaster;
use App\Models\UploadBudgets;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SegmentBulkInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $financeYear;
    protected $segments;
    protected $template;
    protected $year;
    protected $month;
    protected $notification;
    protected $uploadBudget;
    protected $result;
    protected $currency;
    protected $employee;
    public function __construct($db, $financeYear, $segments, $template, $year, $month, $notification, $uploadBudget, $result, $currency, $employee)
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
        $this->financeYear = $financeYear;
        $this->segments = $segments;
        $this->template = $template;
        $this->year = $year;
        $this->month = $month;
        $this->notification = $notification;
        $this->uploadBudget = $uploadBudget;
        $this->result = $result;
        $this->currency = $currency;
        $this->employee = $employee;
    }

    public function handle()
    {

        $financeYear = $this->financeYear;
        $segments = $this->segments;
        $template = $this->template;
        $year = $this->year;
        $month = $this->month;
        $notification = $this->notification;
        $uploadBudget = $this->uploadBudget;
        $result = $this->result;
        $currency = $this->currency;
        $employee = $this->employee;



        DB::beginTransaction();
        try {

            foreach ($segments as $segment) {

                $segmentMaster = SegmentMaster::where('ServiceLineDes', $segment)->first();
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
                    'month' => $month,
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

                        Log::info("Template Description: " . $value['Template Description 2']);
                        Log::info("Template Detail: " . $templateDetail);


                        $chartOfAccount = ChartOfAccount::where('AccountCode', $value['GL Code'])->first();

                        $currencyMaster = CurrencyMaster::where('CurrencyCode', $currency)->first();

                        $segmentMaster = SegmentMaster::find($budget->serviceLineSystemID);
                        $currencyConvection = \Helper::currencyConversion($budget->companySystemID, $currencyMaster->currencyID, $currencyMaster->currencyID, $value[$segmentMaster->ServiceLineDes]);

                        $localAmount = \Helper::roundValue($currencyConvection['localAmount']);

                        for ($i = 1; $i <= 12; $i++) {
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
                                'Year' => $year,
                                'month' => $i,
                                'budjetAmtLocal' => $localAmount / 12,
                                'budjetAmtRpt' => $value[$segmentMaster->ServiceLineDes] / 12,
                                'createdByUserSystemID' => $employee->employeeSystemID,
                                'createdByUserID' => $employee->empID,
                                'createdDateTime' => \Helper::currentDateTime(),
                            );

                            $budgetDetails = Budjetdetails::create($budgetDetailsArray);
                        }
                        Log::info("Budget Detail: " . $budgetDetails);

                    }
                }
            }
            UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 1]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
            UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 0]);
            DB::commit();
        }
    }
}

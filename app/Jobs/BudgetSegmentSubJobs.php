<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
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
        $segmentCount = $subData['segmentCount'];
        $totalSegments = $subData['totalSegments'];

        CommonJobService::db_switch($this->db);
        Log::useFiles(storage_path().'/logs/budget_segment_bulk_insert.log');

        DB::beginTransaction();
        try {
            Log::info($segment.' count - '. $segmentCount);

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

                }
            }

            if ($segmentCount == $totalSegments) {
                $webPushData = [
                    'title' => "Upload Budget Successfully Completed",
                    'body' => "",
                    'url' => "",
                    'path' => "",
                ];
                Log::info('Budget Segment Bulk Insert Completed Successfully '. $totalSegments);

//            WebPushNotificationService::sendNotification($webPushData, 3, $employee->employeeSystemID);
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 1]);
            }
            Log::info($segment . ' Completed Successfully. Count- ' . $segmentCount);

            DB::commit();

        }
        catch (\Exception $e){
            DB::rollback();
            Log::error($this->failed($e));
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- Budget Segment Sub Job End with Error-----' . date('H:i:s'));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

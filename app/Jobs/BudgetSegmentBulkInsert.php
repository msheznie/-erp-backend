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


class BudgetSegmentBulkInsert implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $db;
    protected $uploadData;
    protected $employee;
    protected $decodeFile;

    public function __construct($db, $uploadData)
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
        $this->uploadData = $uploadData;
    }

    public function handle()
    {
        $uploadData = $this->uploadData;

        CommonJobService::db_switch($this->db);

        DB::beginTransaction();
        try {
            Log::useFiles(storage_path().'/logs/budget_segment_bulk_insert.log');

            $uploadBudget = $uploadData['uploadBudget'];
            $employee = $uploadData['employee'];
            $objPHPExcel = $uploadData['objPHPExcel'];



            $worksheet = $objPHPExcel->getActiveSheet();

            $header = $worksheet->toArray();

            $templateName = $header[0][1];
            $financialYear = $header[0][3];
            $currency = $header[1][1];
            $notification = $header[1][3];
            $segments = $header[6];
            $segments = array_slice($segments, 4);

            $dates = explode(" - ", $financialYear);

            $startDate = $dates[0];
            $endDate = $dates[1];

            list($day, $month, $year) = explode("/", $startDate);
            $mysqlFormattedStartDate = "{$year}-{$month}-{$day}";

            list($day, $month, $year) = explode("/", $endDate);
            $mysqlFormattedEndDate = "{$year}-{$month}-{$day}";

            list($startMonth, $startDay, $startYear) = explode("/", $startDate);

            $year = $startYear;
            $month = $startMonth;

            $template = ReportTemplate::where('description', $templateName)->first();

            $worksheet->removeRow(1, 6);

            $data = $worksheet->toArray();

            $data = array_filter(collect($data)->toArray());

            $keys = $data[0];

            array_shift($data);

            $result = [];

            foreach ($data as $row) {
                $rowAssoc = array_combine($keys, $row);
                $result[] = $rowAssoc;
            }

            $financeYear = CompanyFinanceYear::where('companySystemID', $template->companySystemID)->where('bigginingDate', "<=", $mysqlFormattedStartDate)->where('endingDate', ">=", $mysqlFormattedEndDate)->first();

            $budgetExists = BudgetMaster::where('templateMasterID', $template->companyReportTemplateID)->where('companyFinanceYearID', $financeYear->companyFinanceYearID)->get();


            $segmentDes = [];
            foreach ($budgetExists as $budgetExist) {
                $segmentMaster = SegmentMaster::find($budgetExist->serviceLineSystemID);

                $segmentDes[] = $segmentMaster->ServiceLineDes;
            }

            $segments = array_filter($segments, function ($segment) use ($segmentDes) {
                return !in_array($segment, $segmentDes);
            });



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
            }

            $webPushData = [
                'title' => "Upload Budget Successfully Completed",
                'body' => "",
                'url' => "",
                'path' => "",
            ];

//            WebPushNotificationService::sendNotification($webPushData, 3, $employee->employeeSystemID);
            UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 1]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- Budget Segment Bulk Insert Error-----' . date('H:i:s'));
            DB::beginTransaction();
            $webPushData = [
                'title' => "Upload Budget Failed",
                'body' => "",
                'url' => "",
                'path' => "",
            ];

//            WebPushNotificationService::sendNotification($webPushData, 3, $employee->employeeSystemID);
            try {
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 0]);
                DB::commit();
            } catch (\Exception $e){
                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 0]);
                DB::commit();
            }
        }
    }
}

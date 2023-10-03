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
        ini_set('max_execution_time', 21600);
        ini_set('memory_limit', -1);
        $uploadData = $this->uploadData;
        $db = $this->db;

        CommonJobService::db_switch($db);
        Log::useFiles(storage_path().'/logs/budget_segment_bulk_insert.log');

        DB::beginTransaction();
        try {
            Log::info('Budget Segment Bulk Insert Started');

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

            $segmentCount = 1;
            $totalSegments = count($segments);
            Log::info('Total Segments: ' . $totalSegments);

            foreach ($segments as $segment) {
                $subData = ['segment' => $segment,
                        'template' => $template,
                        'employee' => $employee,
                        'result' => $result,
                        'financeYear' => $financeYear,
                        'year' => $year,
                        'month' => $month,
                        'notification' => $notification,
                        'uploadBudget' => $uploadBudget,
                        'currency' => $currency,
                        'segmentCount' => $segmentCount,
                        'totalSegments' => $totalSegments
                ];
                BudgetSegmentSubJobs::dispatch($db,$subData);
                $segmentCount++;
            }

            if($totalSegments == 0){
                Log::info('Zero segments available');

                UploadBudgets::where('id', $uploadBudget->id)->update(['uploadStatus' => 1]);

            }

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

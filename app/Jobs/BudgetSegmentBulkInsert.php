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
use App\Models\logUploadBudget;
use Exception;
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

            $uploadBudget = $uploadData['uploadBudget'];
            $employee = $uploadData['employee'];
            $objPHPExcel = $uploadData['objPHPExcel'];
            $uploadedCompany = $uploadData['uploadedCompany'];

            $worksheet = $objPHPExcel->getActiveSheet();
            $highestRow = $worksheet->getHighestRow();
            $header = $worksheet->toArray();

            $templateName = $header[0][1];
            $financialYear = $header[0][3];
            $currency = $header[1][1];
            $notification = $header[1][3];
            $segments = $header[7];
            $segments = array_slice($segments, 4);

            $dates = explode(" - ", $financialYear);

            $startDate = $dates[0];
            $endDate = $dates[1];

            list($day, $month, $year) = explode("/", $startDate);
            $mysqlFormattedStartDate = "{$year}-{$month}-{$day}";

            list($day, $month, $year) = explode("/", $endDate);
            $mysqlFormattedEndDate = "{$year}-{$month}-{$day}";

            list($startDay, $startMonth, $startYear) = explode("/", $startDate);

            $year = $startYear;
            $month = $startMonth;
            $nonEmptyRowCount = 0;
            foreach ($header as $row) {
                if ($this->isRowNotEmpty($row)) {
                    $nonEmptyRowCount++;
                }
            }


            $notValidSegment = true;
            foreach($segments as $segment) {
                $segemntInfo =  SegmentMaster::where('ServiceLineDes',$segment)->first();
                if (!isset($segemntInfo)) 
                {
                    $notValidSegment = false;
                    $result = $this->failed($uploadedCompany,$uploadBudget->id,"The segment " .$segment." does not exist",8);
                    if($result)
                    {
                        continue;
                    }
                }
               
            }



            $row1 = 9;
            $notValidCode = true;
            for ($row1 = 9; $row1 <= $highestRow; $row1++) {
                $cellValue = $worksheet->getCell('C' . $row1)->getValue();
                $gl_codes =  ChartOfAccount::where('AccountCode',$cellValue)->first();
                if (!isset($gl_codes)) 
                {
                    $notValidCode = false;
                    $result = $this->failed($uploadedCompany,$uploadBudget->id,"The GL code " .$cellValue." is not available in the COA",$row1);
                    if($result)
                    {
                        continue;
                    }
                }
               
            }
            $notValidTemplate= true;
            $template = ReportTemplate::where('description', $templateName)->where('companySystemID', $uploadedCompany)->first();
            if (!isset($template)) {
                $notValidTemplate= false;
                $this->failed($uploadedCompany,$uploadBudget->id,"Template name is not matching with existing templates",1);
            }


            $notValidLine= true;
            if($notValidTemplate)
            {   
                $glCOdes = ReportTemplateDetails::with(['gllink'  => function ($query) {
                    $query->orderBy('sortOrder', 'asc');
                  }])
                    ->where('companySystemID', $uploadedCompany)
                    ->where('companyReportTemplateID', $template->companyReportTemplateID)
                    ->orderBy('sortOrder', 'asc')
                    ->get();
    
                    $glCOdesSorted = collect($glCOdes);
                    $count = 0;
                foreach($glCOdesSorted->values()->all() as $det)
                {
                    $count = $count + count($det->gllink);
        
                }
    
                 $assetCount = $count + 6;
                 if($nonEmptyRowCount != $assetCount)
                 {
                    $notValidLine= false;
                    $this->failed($uploadedCompany,$uploadBudget->id,"Some rows have been deleted from the template",$highestRow);
                 }
            }
  

             $notValidFinance = true;
             $financeYear = CompanyFinanceYear::where('companySystemID', $uploadedCompany)->whereDate('bigginingDate', '=', $mysqlFormattedStartDate)->whereDate('endingDate', '=', $mysqlFormattedEndDate)->first();
             if (!isset($financeYear)) 
             {
                 $notValidFinance = false;
                 $finDate = $mysqlFormattedStartDate.' - '.$mysqlFormattedEndDate;
                 $msg = 'The financial year '.$finDate.' is not active/format not correct';
                 $this->failed($uploadedCompany,$uploadBudget->id,$msg,1);
             }


            if($notValidCode && $notValidSegment && $notValidTemplate && $notValidLine && $notValidFinance)
            {           
                     
                $worksheet->removeRow(1, 7);

                $data = $worksheet->toArray();
    
                $data = array_filter(collect($data)->toArray());
    
                $keys = $data[0];
    
                array_shift($data);
    
                $result = [];
    
                foreach ($data as $row) {
                    $rowAssoc = array_combine($keys, $row);
                    $result[] = $rowAssoc;
                }
    
    
                $budgetExists = BudgetMaster::where('templateMasterID', $template->companyReportTemplateID)->where('companyFinanceYearID', $financeYear->companyFinanceYearID)->get();
    
                $segmentDes = [];
                foreach ($budgetExists as $budgetExist) {
                    $segmentMaster = SegmentMaster::find($budgetExist->serviceLineSystemID);
    
                    $segmentDes[] = $segmentMaster->ServiceLineDes;
                }
    
                $segments = array_filter($segments, function ($segment) use ($segmentDes) {
                    return !in_array($segment, $segmentDes);
                });
    
                $totalSegments = count($segments);
    
                if($uploadedCompany != $template->companySystemID){
                    Log::error('Uploaded company is different from the template company');
    
                    $webPushData = [
                        'title' => "Upload Budget Failed",
                        'body' => "",
                        'url' => "general-ledger/budget-upload",
                        'path' => "",
                    ];
    
                    WebPushNotificationService::sendNotification($webPushData, 2, [$employee->employeeSystemID], $db);
                    $this->failed($uploadedCompany,$uploadBudget->id,"Uploaded company is different from the template company","");
                
                } else {
    
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
                            'totalSegments' => $totalSegments
                        ];
                        BudgetSegmentSubJobs::dispatch($db, $subData)->onQueue('single');
    
                    }
                }
    
                if($totalSegments == 0){
                    Log::error('Zero segments available');
    
                    $webPushData = [
                        'title' => "Upload Budget Failed",
                        'body' => "",
                        'url' => "general-ledger/budget-upload",
                        'path' => "",
                    ];
    
                    WebPushNotificationService::sendNotification($webPushData, 2, [$employee->employeeSystemID], $db);
                    $this->failed($uploadedCompany,$uploadBudget->id,"Zero segments available","");
    
                }                           
        
            }
  

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e->getMessage());
            DB::beginTransaction();
            $webPushData = [
                'title' => "Upload Budget Failed",
                'body' => "",
                'url' => "general-ledger/budget-upload",
                'path' => "",
            ];

           WebPushNotificationService::sendNotification($webPushData, 2, [$employee->employeeSystemID], $db);
            try {
             
                $this->failed($uploadedCompany,$uploadBudget->id,$e->getMessage(),$e->getLine());
                DB::commit();
            } catch (\Exception $e){
                $this->failed($uploadedCompany,$uploadBudget->id,$e->getMessage(),$e->getLine());
                DB::commit();
            }
        }
    }


    public function failed($comapny,$budgetID,$msg,$line)
    {
        UploadBudgets::where('id', $budgetID)->update(['uploadStatus' => 0]);

        $uploadLogArray = array(
            'companySystemID' => $comapny,
            'bugdet_upload_id' => $budgetID,
            'is_failed' => 1,
            'error_line' => $line,
            'log_message' => $msg
        );

        $logUploadBudget= logUploadBudget::create($uploadLogArray);

      return true;;
    }

        private function isRowNotEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if (!is_null($cell) && trim($cell) !== '') {
                return true;
            }
        }
        return false;
    }
}

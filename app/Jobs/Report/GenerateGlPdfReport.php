<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\Models\CurrencyMaster;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;

class GenerateGlPdfReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $reportCount;
    public $requestData;
    public $userIds;
    public $outputChunkData;
    public $outputData;
    public $rootPath;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $reportCount, $userId, $outputData, $outputChunkData, $rootPath)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->reportCount = $reportCount;
        $this->userIds = $userId;
        $this->outputChunkData = $outputChunkData;
        $this->outputData = $outputData;
        $this->rootPath = $rootPath;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        $db = $this->dispatch_db;
        $request = $this->requestData;
        $outputChunkCount = $this->outputChunkData;
        $output = $this->outputData;
        $rootPaths = $this->rootPath;

        $count = $this->reportCount;
        CommonJobService::db_switch($db);
        

        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $checkIsGroup = Company::find($request->companySystemID);
        $data = array();

        $decimalPlace = array();
        $currencyIdLocal = 1;
        $currencyIdRpt = 2;

        $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
        $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);

        $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
        $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);


        if (!empty($decimalPlaceUniqueLocal)) {
            $currencyIdLocal = $decimalPlaceUniqueLocal[0];
        }

        if (!empty($decimalPlaceUniqueRpt)) {
            $currencyIdRpt = $decimalPlaceUniqueRpt[0];
        }

        $extraColumns = [];
        if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
            $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
        }

        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

        $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

        $currencyLocal = $requestCurrencyLocal->CurrencyCode;
        $currencyRpt = $requestCurrencyRpt->CurrencyCode;

        $totaldocumentLocalAmountDebit = array_sum(collect($output)->pluck('localDebit')->toArray());
        $totaldocumentLocalAmountCredit = array_sum(collect($output)->pluck('localCredit')->toArray());
        $totaldocumentRptAmountDebit = array_sum(collect($output)->pluck('rptDebit')->toArray());
        $totaldocumentRptAmountCredit = array_sum(collect($output)->pluck('rptCredit')->toArray());


        $finalData = array();
        foreach ($output as $val) {
            $finalData[$val->glCode . ' - ' . $val->AccountDescription][] = $val;
        }

        $dataArr = array(
            'reportData' => $finalData,
            'extraColumns' => $extraColumns,
            'companyName' => $checkIsGroup->CompanyName,
            'isGroup' => $checkIsGroup->isGroup,
            'currencyDecimalPlace' => !empty($decimalPlace) ? $decimalPlace[0] : 2,
            'currencyLocal' => $currencyLocal,
            'decimalPlaceLocal' => $decimalPlaceLocal,
            'decimalPlaceRpt' => $decimalPlaceRpt,
            'currencyRpt' => $currencyRpt,
            'reportDate' => date('d/m/Y H:i:s A'),
            'fromDate' => \Helper::dateFormat($request->fromDate),
            'toDate' => \Helper::dateFormat($request->toDate),
            'totaldocumentLocalAmountDebit' =>  round((isset($totaldocumentLocalAmountDebit) ? $totaldocumentLocalAmountDebit : 0), 3),
            'totaldocumentLocalAmountCredit' => round((isset($totaldocumentLocalAmountCredit) ? $totaldocumentLocalAmountCredit : 0), 3),
            'totaldocumentRptAmountDebit' => round((isset($totaldocumentRptAmountDebit) ? $totaldocumentRptAmountDebit : 0), 3),
            'totaldocumentRptAmountCredit' => round((isset($totaldocumentRptAmountCredit) ? $totaldocumentRptAmountCredit : 0), 3),
        );


        $html = view('print.report_general_ledger', $dataArr);

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        $pdf_content =  $pdf->setPaper('a4', 'landscape')->setWarnings(false)->output();

        $fileName = 'general_ledger_'.strtotime(date("Y-m-d H:i:s")).'_Part_'.$count.'.pdf';
        $path = $rootPaths.'/'.$fileName;

        $result = Storage::disk('local_public')->put($path, $pdf_content);

        $files = File::files(public_path($rootPaths));
        if (count($files) == $outputChunkCount) {
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');

            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');

            $companyCode = isset($checkIsGroup->CompanyID)?$checkIsGroup->CompanyID:'common';


            $zip = new ZipArchive;
            $fileName = $companyCode.'_'.'general_ledger_report_('.$fromDate.'_'.$toDate.')_'.strtotime(date("Y-m-d H:i:s")).'.zip';
            if ($zip->open(public_path($fileName), ZipArchive::CREATE) === TRUE)
            {
                foreach($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    $zip->addFile($value, $relativeNameInZipFile);
                }
                $zip->close();
            }

            $contents = Storage::disk('local_public')->get($fileName);
            $zipPath = $companyCode."/general-ledger/repots/".$fileName;
            $fileMoved = Storage::disk('s3')->put($zipPath, $contents);

            if ($fileMoved) {
                if ($exists = Storage::disk('local_public')->exists($fileName)) {
                    $fileDeleted = Storage::disk('local_public')->delete($fileName);
                }
            }

            $reportTitle = "general_ledger_report_pdf_generated";

            $webPushData = [
                'title' => $reportTitle,
                'body' => 'Period : '.$fromDate.' - '.$toDate,
                'url' => "",
                'path' => $zipPath,
            ];

            WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds);

            Storage::disk('local_public')->deleteDirectory('general-ledger-pdf');
        }

        return true;
    }
}

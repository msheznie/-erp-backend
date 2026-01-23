<?php

namespace App\Jobs\Report;

use App\helper\Helper;
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

class GenerateBankLedgerPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $reportCount;
    public $requestData;
    public $userIds;
    public $outputChunkData;
    public $outputData;
    public $rootPath;
    public $languageCode;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $request, $reportCount, $userId, $outputData, $outputChunkData, $rootPath, $languageCode)
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
        $this->languageCode = $languageCode;
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
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);
        $count = $this->reportCount;
        CommonJobService::db_switch($db);

        $checkIsGroup = Company::find($request->companySystemID);
        $requestCurrencyRpt = CurrencyMaster::where('currencyID', $checkIsGroup->reportingCurrency)->first();
        $requestCurrencyLocal = CurrencyMaster::where('currencyID', $checkIsGroup->localCurrencyID)->first();

        if ($request->currencyID == 2) {
            $decimalPlace = $requestCurrencyRpt ? $requestCurrencyRpt->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyRpt ? $requestCurrencyRpt->CurrencyCode : "";
        } else if ($request->currencyID == 3) {
            $decimalPlace = $requestCurrencyLocal ? $requestCurrencyLocal->DecimalPlaces : 2;
            $currencyCode = $requestCurrencyLocal ? $requestCurrencyLocal->CurrencyCode : "";
        } else {
            $decimalPlace = 2;
            $currencyCode = "";
        }

        $extraColumns = [];
        if (isset($request->extraColoumns) && count($request->extraColoumns) > 0) {
            $extraColumns = collect($request->extraColoumns)->pluck('id')->toArray();
        }

        
        $totaldocumentLocalAmountDebit = array_sum(collect($output)->pluck('localDebit')->toArray());
        $totaldocumentLocalAmountCredit = array_sum(collect($output)->pluck('localCredit')->toArray());
        $totaldocumentRptAmountDebit = array_sum(collect($output)->pluck('rptDebit')->toArray());
        $totaldocumentRptAmountCredit = array_sum(collect($output)->pluck('rptCredit')->toArray());

        $finalData = array();
        foreach ($output as $val) {
            $finalData[$val->bankName . ' - ' . $val->AccountNo][] = $val;
        }

        $dataArr = array(
            'reportData' => $finalData,
            'extraColumns' => $extraColumns,
            'companyName' => $checkIsGroup->CompanyName,
            'isGroup' => $checkIsGroup->isGroup,
            'currencyDecimalPlace' => $decimalPlace,
            'currencyID' => $request->currencyID,
            'accBalanceShow' => (count($request->accounts) == 1) ? true : false,
            'currencyCode' => $currencyCode,
            'reportDate' => date('d/m/Y H:i:s A'),
            'fromDate' => Helper::dateFormat($request->fromDate),
            'toDate' => Helper::dateFormat($request->toDate),
            'totaldocumentLocalAmountDebit' =>  round((isset($totaldocumentLocalAmountDebit) ? $totaldocumentLocalAmountDebit : 0), $decimalPlace),
            'totaldocumentLocalAmountCredit' => round((isset($totaldocumentLocalAmountCredit) ? $totaldocumentLocalAmountCredit : 0), $decimalPlace),
            'totaldocumentRptAmountDebit' => round((isset($totaldocumentRptAmountDebit) ? $totaldocumentRptAmountDebit : 0), $decimalPlace),
            'totaldocumentRptAmountCredit' => round((isset($totaldocumentRptAmountCredit) ? $totaldocumentRptAmountCredit : 0), $decimalPlace),
            'lang' => $languageCode,
        );

        // Check if Arabic language for RTL support
        $isRTL = ($languageCode === 'ar');

        // Configure mPDF for RTL support if Arabic
        $mpdfConfig = Helper::getMpdfConfig([
            'tempDir' => public_path('tmp'), 
            'mode' => 'utf-8', 
            'format' => 'A4-L', 
            'setAutoTopMargin' => 'stretch', 
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 0,
            'margin_header' => 9,
            'margin_footer' => 9
        ], $languageCode);
        
        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl';
        }

        $html = view('print.report_bank_ledger', $dataArr);

        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->SetHTMLFooter('<table style="width: 100%; font-size: 10px;">
        <tr>
            <td style="text-align: left; width: 50%;">' . trans('custom.printed_date') . ' : ' . date("d-M-y", strtotime(now())) . '</td>
            <td style="text-align: right; width: 50%;">' . trans('custom.page') . ' {PAGENO}</td>
        </tr>
        </table>');
        $mpdf->WriteHTML($html);
        $pdf_content = $mpdf->Output('', 'S');

        $fileName = trans('custom.bank_ledger').'_'.strtotime(date("Y-m-d H:i:s")).'_Part_'.$count.'.pdf';
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
            $fileName = $companyCode.'_'.trans('custom.bank_ledger_report').'_('.$fromDate.'_'.$toDate.')_'.strtotime(date("Y-m-d H:i:s")).'.zip';
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

            $reportTitle = "bank_ledger_report_pdf_generated";

            $webPushData = [
                'title' => $reportTitle,
                'body' => trans('custom.period') . ' : ' . $fromDate . ' - ' . $toDate,
                'url' => "",
                'path' => $zipPath,
            ];

            WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds);

            Storage::disk('local_public')->deleteDirectory('bank-ledger-pdf');
        }

        return true;
    }
}

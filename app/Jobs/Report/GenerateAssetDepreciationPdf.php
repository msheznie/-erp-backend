<?php

namespace App\Jobs\Report;

use App\helper\Helper;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\FixedAssetDepreciationMaster;
use Illuminate\Support\Facades\Storage;
use App\Services\WebPushNotificationService;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\Log;
use ZipArchive;
use File;

class GenerateAssetDepreciationPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $reportCount;
    public $depMasterAutoID;
    public $userIds;
    public $outputChunkData;
    public $outputData;
    public $rootPath;
    public $languageCode;
    public $totalRecords;
    public $grandTotalDepAmountLocal;
    public $grandTotalDepAmountRpt;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $depMasterAutoID, $reportCount, $userId, $outputData, $outputChunkData, $rootPath, $languageCode, $totalRecords, $grandTotalDepAmountLocal = null, $grandTotalDepAmountRpt = null)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->depMasterAutoID = $depMasterAutoID;
        $this->reportCount = $reportCount;
        $this->userIds = $userId;
        $this->outputChunkData = $outputChunkData;
        $this->outputData = $outputData;
        $this->rootPath = $rootPath;
        $this->languageCode = $languageCode;
        $this->totalRecords = $totalRecords;
        $this->grandTotalDepAmountLocal = $grandTotalDepAmountLocal;
        $this->grandTotalDepAmountRpt = $grandTotalDepAmountRpt;
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
        $depMasterAutoID = $this->depMasterAutoID;
        $output = $this->outputData;
        $rootPaths = $this->rootPath;
        $languageCode = $this->languageCode;
        $totalOriginalRecords = $this->totalRecords;
        app()->setLocale($languageCode);
        $count = $this->reportCount;
        CommonJobService::db_switch($db);

        /** @var FixedAssetDepreciationMaster $assetDepreciation */
        $assetDepreciation = FixedAssetDepreciationMaster::with([
            'created_by',
            'confirmed_by',
            'company',
            'financeperiod_by',
            'approved_by' => function ($query) {
                $query->with('employee')
                    ->where('rejectedYN', 0)
                    ->where('documentSystemID', 23);
            }
        ])->find($depMasterAutoID);

        if (empty($assetDepreciation)) {
            Log::error("Asset Depreciation Master not found: {$depMasterAutoID}");
            return false;
        }

        if ($assetDepreciation->depLocalCur) {
            $assetDepreciation->localcurrency = CurrencyMaster::find($assetDepreciation->depLocalCur);
        }
        if ($assetDepreciation->depRptCur) {
            $assetDepreciation->rptcurrency = CurrencyMaster::find($assetDepreciation->depRptCur);
        }
        if ($assetDepreciation->companyFinanceYearID) {
            $assetDepreciation->financeYear = CompanyFinanceYear::find($assetDepreciation->companyFinanceYearID);
        }

        $detailsCollection = collect($output)->map(function($item) {
            $period = (object) $item;
            if (isset($item['maincategory_by']) && is_array($item['maincategory_by'])) {
                $period->maincategory_by = (object) $item['maincategory_by'];
            }
            if (isset($item['financecategory_by']) && is_array($item['financecategory_by'])) {
                $period->financecategory_by = (object) $item['financecategory_by'];
            }
            if (isset($item['serviceline_by']) && is_array($item['serviceline_by'])) {
                $period->serviceline_by = (object) $item['serviceline_by'];
            }

            return $period;
        });

        $assetDepreciation->details = $detailsCollection;

        // Calculate totals for this chunk
        $recordsPerPdf = 300;
        $startingRowNumber = (($count - 1) * $recordsPerPdf) + 1;

        $totalPdfParts = ceil($totalOriginalRecords / $recordsPerPdf);
        $isFirstPdfPart = ($count == 1);
        $isLastPdfPart = ($count == $totalPdfParts);

        // Calculate totals - use grand totals if available and this is the last part, otherwise use chunk totals
        if ($isLastPdfPart && $this->grandTotalDepAmountLocal !== null && $this->grandTotalDepAmountRpt !== null) {
            $assetDepreciation->totalDepAmountLocal = $this->grandTotalDepAmountLocal;
            $assetDepreciation->totalDepAmountRpt = $this->grandTotalDepAmountRpt;
        } else {
            $assetDepreciation->totalDepAmountLocal = $detailsCollection->sum('depAmountLocal');
            $assetDepreciation->totalDepAmountRpt = $detailsCollection->sum('depAmountRpt');
        }
        $isRTL = ($languageCode === 'ar');

        $mpdfConfig = Helper::getMpdfConfig([
            'tempDir' => public_path('tmp'),
            'mode' => 'utf-8',
            'format' => 'A4-P',
            'setAutoTopMargin' => 'stretch',
            'autoMarginPadding' => -10
        ], $languageCode);

        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl';
        }

        $array = array(
            'dbdata' => $assetDepreciation,
            'showFooterDetails' => $isLastPdfPart,
            'showHeader' => $isFirstPdfPart,
            'startingRowNumber' => $startingRowNumber,
            'totalFromJob' => true
        );

        $html = view('print.asset_depreciation', $array);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);

        $pdf_content = $mpdf->Output('', 'S');
        $translatedTitle = trans('custom.asset_depreciation');
        $time = strtotime("now");
        $fileName = $translatedTitle . '_' . $depMasterAutoID . '_' . $time . '_' . $count . '.pdf';
        $path = $rootPaths.'/'.$fileName;

        $result = Storage::disk('local_public')->put($path, $pdf_content);

        $expectedTotalPdfParts = ceil($totalOriginalRecords / $recordsPerPdf);

        $files = File::files(public_path($rootPaths));
        $fileCount = count($files);
        if ($fileCount >= $expectedTotalPdfParts) {
            $company = Company::find($assetDepreciation->companySystemID);
            $companyCode = isset($company->CompanyID) ? $company->CompanyID : 'common';

            $zip = new ZipArchive;
            // Sanitize depCode to remove backslashes and other invalid characters for filename
            $originalDepCode = $assetDepreciation->depCode ?? '';
            $sanitizedDepCode = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', $originalDepCode);
            $sanitizedDepCode = str_replace('\\', '_', $sanitizedDepCode);
            $translatedTitle = trans('custom.asset_depreciation');
            $translatedTitle = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', $translatedTitle);

            $zipFileName = $companyCode.'_'.$translatedTitle.'_('.$sanitizedDepCode.')_'.strtotime(date("Y-m-d H:i:s")).'.zip';
            $zipFileName = str_replace(['\\', '/', ':', '*', '?', '"', '<', '>', '|'], '_', $zipFileName);
            $zipFullPath = public_path($zipFileName);

            if ($zip->open(public_path($zipFileName), ZipArchive::CREATE) === TRUE)
            {
                foreach($files as $key => $value) {
                    $relativeNameInZipFile = basename($value);
                    $zip->addFile($value, $relativeNameInZipFile);
                }
                $zip->close();
            }else {
                Log::error("Failed to create ZIP file: {$zipFullPath}");
            }

            if (file_exists($zipFullPath)) {
                // Read file directly from public path since we created it there
                $contents = file_get_contents($zipFullPath);
                if ($contents === false) {
                    Log::error("Failed to read ZIP file contents: {$zipFullPath}");
                }
                $zipPath = $companyCode."/asset-depreciation/reports/".$zipFileName;

                if ($contents !== false) {
                    $fileMoved = Storage::disk('s3')->put($zipPath, $contents);

                    if ($fileMoved) {
                        Log::info("ZIP file uploaded to S3 successfully: {$zipPath}");
                        // Delete the local ZIP file from public directory
                        if (file_exists($zipFullPath)) {
                            $fileDeleted = @unlink($zipFullPath);
                            if ($fileDeleted) {
                                Log::info("Local ZIP file deleted: {$zipFileName}");
                            } else {
                                Log::warning("Failed to delete local ZIP file: {$zipFullPath}");
                            }
                        }
                    } else {
                        Log::error("Failed to upload ZIP file to S3: {$zipPath}");
                    }
                } else {
                    Log::error("Cannot upload to S3: ZIP file contents could not be read");
                }

                $webPushData = [
                    'title' => trans('custom.asset_depreciation_report_pdf_generated'),
                    'body' => trans('custom.doc_code') . ' : ' . $assetDepreciation->depCode,
                    'url' => "",
                    'path' => $zipPath,
                ];

                $notificationResult = WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds, $db);
                Log::info("Send report to user id: " . json_encode((array) $this->userIds));
                if (Storage::disk('local_public')->exists($rootPaths)) {
                    Storage::disk('local_public')->deleteDirectory($rootPaths);
                    Log::info("Temporary folder deleted: $rootPaths");
                }
                Log::info("Report sent");
            } else {
                Log::error("ZIP file does not exist after creation: {$zipFullPath}");
            }
        }
        return true;
    }
}

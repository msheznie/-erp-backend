<?php

namespace App\Jobs\Report;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use App\helper\CreateExcel;
use App\helper\Helper;
use App\Services\WebPushNotificationService;
use App\Http\Controllers\API\AssetManagementReportAPIController;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Services\AssetManagementService;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportVatDetailReportService;
use App\Exports\AssetManagement\AssetRegister\AssetRegisterDetail2;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use App\Mail\EmailForQueuing;

class AssetRegisterExportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $dispatch_db;
    public $requestData;
    public $userIds;
    public $languageCode;

   
    public function __construct($dispatch_db, $request, $userId, $languageCode)
    {
        if (env('QUEUE_DRIVER_CHANGE','database') == 'database') {
            if (env('IS_MULTI_TENANCY',false)) {
                self::onConnection('database_main');
            }
            else {
                self::onConnection('database');
            }
        }
        else {
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }
        $this->dispatch_db = $dispatch_db;
        $this->requestData = $request;
        $this->userIds = $userId;
        $this->languageCode = $languageCode;
    }

   
    public function handle()
    {
        ini_set('max_execution_time', config('app.report_max_execution_limit'));
        ini_set('memory_limit', -1);
        $db = $this->dispatch_db;
        CommonJobService::db_switch($db);
        $languageCode = $this->languageCode;
        app()->setLocale($languageCode);

        try {
            $controller = new AssetManagementReportAPIController();
            $service = new ExportVatDetailReportService();
            $assetManagementService = new AssetManagementService();
            $request = (object) $this->requestData;
            $reportTypeID = $request->reportTypeID;
            $type = 'xls';
            $basePath = '';

            if ($reportTypeID == 'ARD') {
                $basePath = $this->processARD($controller, $service, $assetManagementService, $request);
            } elseif ($reportTypeID == 'ARD3') {
                $basePath = $this->processARD3($controller, $request);
            } elseif ($reportTypeID == 'ARD2') {
                $basePath = $this->processARD2($controller, $service, $request);
            } elseif ($reportTypeID == 'ARS') {
                $basePath = $this->processARS($controller, $service, $request);
            } elseif ($reportTypeID == 'ARGD') {
                $basePath = $this->processARGD($controller, $service, $request);
            }

            if (!empty($basePath)) {
                $webPushData = [
                    'title' => trans('custom.asset_register_report_generated'),
                    'body' => trans('custom.report_type') . ' : ' . $reportTypeID,
                    'url' => '',
                    'path' => $basePath,
                ];
                WebPushNotificationService::sendNotification($webPushData, 3, $this->userIds, $db);
            }

            if (!empty($basePath)) {
                try {
                    $user = \App\Models\User::where('employee_id', $this->userIds)->first();

                    if ($user && !empty($user->email)) {
                        $request = (object) $this->requestData;
                        $storagePath = $basePath;
                        $path = parse_url($basePath, PHP_URL_PATH);
                        $bucket = config('filesystems.disks.s3.bucket');
                        if ($path && $bucket) {
                            $prefix = '/' . $bucket . '/';
                            if (strpos($path, $prefix) === 0) {
                                $storagePath = ltrim(substr($path, strlen($prefix)), '/');
                            }
                        }
                        $storagePath = rawurldecode($storagePath);

                        $reportTypeLabels = [
                            'ARD'  => trans('custom.asset_register_detail_report'),
                            'ARD2' => trans('custom.asset_register_detail2_report'),
                            'ARD3' => trans('custom.asset_register_detail_3'),
                            'ARS'  => trans('custom.asset_register_summary_report'),
                            'ARGD' => trans('custom.asset_register_grouped_detail_report'),
                        ];
                        $reportTypeName = $reportTypeLabels[$reportTypeID] ?? $reportTypeID;
                        $companyMaster = Company::find($request->companySystemID ?? null);
                        $companyName = $companyMaster ? ($companyMaster->CompanyName ?? '') : '';

                        if ($reportTypeID === 'ARD2' && isset($request->fromDate) && isset($request->toDate)) {
                            $fromDate = Carbon::parse($request->fromDate)->format('d-m-Y');
                            $toDate = Carbon::parse($request->toDate)->format('d-m-Y');
                            $body = '<p>' . trans('custom.asset_register_email_dear_user') . '</p>'
                                . '<p>' . trans('custom.asset_register_email_body_with_dates', ['reportTypeName' => $reportTypeName, 'fromDate' => $fromDate, 'toDate' => $toDate]) . '</p>'
                                . '<p>' . trans('custom.asset_register_email_regards') . '</p>'
                                . '<p>' . $companyName . '</p>';
                        } else {
                            $body = '<p>' . trans('custom.asset_register_email_dear_user') . '</p>'
                                . '<p>' . trans('custom.asset_register_email_body', ['reportTypeName' => $reportTypeName]) . '</p>'
                                . '<p>' . trans('custom.asset_register_email_regards') . '</p>'
                                . '<p>' . $companyName . '</p>';
                        }

                        $subject = trans('custom.asset_register_report_generated');

                        Mail::to($user->email)->send(
                            new EmailForQueuing($subject, $body, $storagePath, [], '#C23C32', 'GEARS', 'GEARS', $this->languageCode)
                        );
                    }
                } catch (\Exception $emailException) {
                   sendError('AssetRegisterExportJob: failed to send email', [
                    'error' => $emailException->getMessage(),
                    'trace' => $emailException->getTraceAsString(),
                   ]);
                }
            }

        } catch (\Exception $e) {
            throw $e;
        }
    }

   
    private function processARD($controller, $service, $assetManagementService, $request)
    {
        $request = (object)$controller->convertArrayToSelectedValue((array)$request, array('typeID'));
        $output = $controller->getAssetRegisterDetail($request);
        $data = $assetManagementService->generateDataToExport($request, $output);
        $companyMaster = Company::find(isset($request->companySystemID) ? $request->companySystemID : null);
        $companyCode = isset($companyMaster->CompanyID) ? $companyMaster->CompanyID : 'common';
        $excelColumnFormat = [
            'K' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00,
            'L' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'N' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
        $title = trans('custom.asset_register_detail_report');
        $fileName = trans('custom.asset_register_detail');
        $path = 'asset_register/report/excel/';

        $exportToExcel = $service
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName("")
            ->setFromDate("")
            ->setToDate("")
            ->setType('xls')
            ->setReportType(2)
            ->setCurrency("")
            ->setExcelFormat($excelColumnFormat)
            ->setData($data)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if (!$exportToExcel['success']) {
            throw new \Exception(trans('custom.unable_to_export_excel'));
        }

        return $exportToExcel['data'];
    }

   
    private function processARD3($controller, $request)
    {
        $request = (object)$controller->convertArrayToSelectedValue((array)$request, array('typeID', 'currencyID'));
        $output = $controller->getAssetRegisterDetail3($request);
        $x = 2;
        $totAcq = 0;
        $totDepPed = 0;
        $totDisVal = 0;
        $totNBV = 0;
        $totDisPro = 0;
        $data = [];

        $companyData = Helper::companyCurrency($request->companySystemID);
        $decimalPlaces = ($request->currencyID == 3) ? $companyData->reportingcurrency->DecimalPlaces : $companyData->localcurrency->DecimalPlaces;
        $currencyCode = ($request->currencyID == 3) ? $companyData->reportingcurrency->CurrencyCode : $companyData->localcurrency->CurrencyCode;

        if (!empty($output)) {
            foreach ($output as $key => $value) {
                $datetime = Carbon::parse($value->dateAQ);
                $datetime2 = Carbon::parse($value->dateDEP);

                $data[$x][trans('custom.fixed_asset_code')] = $value->faCode;
                $data[$x][trans('custom.asset_description')] = $value->assetDescription;
                $data[$x][trans('custom.account_code')] = $value->COSTGLCODE;
                $data[$x][trans('custom.asset_class')] = is_string($value->financeCatDescription) ? htmlspecialchars_decode($value->financeCatDescription) : $value->financeCatDescription;
                $data[$x][trans('custom.serial_number')] = $value->faUnitSerialNo;
                $data[$x][trans('custom.location')] = $value->locationName;
                $data[$x][trans('custom.sub_location')] = $value->ServiceLineDes;
                $data[$x][trans('custom.acquisition_date')] = ($value->dateAQ) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($value->dateAQ)) : null;
                $data[$x][trans('custom.supplier_name')] = $value->supplierName;
                $data[$x][trans('custom.acquisition_cost')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($request->currencyID == 3) ? $value->costUnitRpt : $value->COSTUNIT, $decimalPlaces));
                $data[$x][trans('custom.place_in_service_date')] = ($value->dateDEP) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($value->dateDEP)) : null;
                $data[$x][trans('custom.useful_life')] = $value->depMonth;
                $data[$x][trans('custom.remaining_life')] = $value->depMonth - $value->depreciatedMonths;
                $data[$x][trans('custom.depr_type')] = "SL";
                $data[$x][trans('custom.depreciation_percentage')] = $value->DEPpercentage;
                $data[$x][trans('custom.depreciation_for_the_period')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($request->currencyID == 3) ? $value->depAmountRpt : $value->depAmountLocal, $decimalPlaces));
                $data[$x][trans('custom.accumulated_depreciation')."  (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($request->currencyID == 3) ? $value->acDepAmountRpt : $value->adDepAmountLocal, $decimalPlaces));
                $data[$x][trans('custom.nbv')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($request->currencyID == 3) ? floatval($value->costUnitRpt) - floatval($value->depAmountRpt) : floatval($value->COSTUNIT) - floatval($value->depAmountLocal), $decimalPlaces));
                $data[$x][trans('custom.additions')] = 0;
                $data[$x][trans('custom.revaluations')] = 0;

                $disposalValue = $request->currencyID == 3 ? $value->costUnitRpt : $value->COSTUNIT;
                $data[$x][trans('custom.disposals')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round((($value->DIPOSED == -1) ? $disposalValue : 0), $decimalPlaces));
                $disposalProfit = $request->currencyID == 3 ? (floatval($value->sellingPriceRpt) - (floatval($value->costUnitRpt) - floatval($value->acDepAmountRpt))) : (floatval($value->sellingPriceLocal) - (floatval($value->COSTUNIT) - floatval($value->adDepAmountLocal)));
                $data[$x][trans('custom.profit_loss_on_disposal')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($value->DIPOSED == -1 && $request->typeID == 1 && $value->disposalType == 6) ? $disposalProfit : 0, $decimalPlaces));
                $data[$x][trans('custom.impairment')] = 0;
                $data[$x][trans('custom.write_offs')] = 0;

                $totAcq += ($request->currencyID == 3) ? $value->costUnitRpt : $value->COSTUNIT;
                $totDepPed += ($request->currencyID == 3) ? $value->depAmountRpt : $value->depAmountLocal;
                $totNBV += ($request->currencyID == 3) ? floatval($value->costUnitRpt) - floatval($value->depAmountRpt) : floatval($value->COSTUNIT) - floatval($value->depAmountLocal);

                if ($value->DIPOSED == -1) {
                    $totDisVal += $request->currencyID == 3 ? $value->costUnitRpt : $value->COSTUNIT;
                }
                if ($value->DIPOSED == -1 && $request->typeID == 1 && $value->disposalType == 6) {
                    $totDisPro += $request->currencyID == 3 ? (floatval($value->sellingPriceRpt) - (floatval($value->costUnitRpt) - floatval($value->acDepAmountRpt))) : (floatval($value->sellingPriceLocal) - (floatval($value->COSTUNIT) - floatval($value->adDepAmountLocal)));
                }

                $x++;
            }

            $data[$x][0] = "";
            $data[$x][1] = "";
            $data[$x][2] = "";
            $data[$x][3] = "";
            $data[$x][4] = "";
            $data[$x][5] = "";
            $data[$x][6] = "";
            $data[$x][7] = "";
            $data[$x][8] = trans('custom.total');
            $data[$x][9] = CurrencyService::convertNumberFormatToNumber(round($totAcq, $decimalPlaces));
            $data[$x][10] = "";
            $data[$x][11] = "";
            $data[$x][12] = "";
            $data[$x][13] = "";
            $data[$x][14] = "";
            $data[$x][15] = CurrencyService::convertNumberFormatToNumber(round($totDepPed, $decimalPlaces));
            $data[$x][16] = CurrencyService::convertNumberFormatToNumber(round($totDepPed, $decimalPlaces));
            $data[$x][17] = CurrencyService::convertNumberFormatToNumber(round($totNBV, $decimalPlaces));
            $data[$x][18] = 0;
            $data[$x][19] = 0;
            $data[$x][20] = CurrencyService::convertNumberFormatToNumber(round($totDisVal, $decimalPlaces));
            $data[$x][21] = CurrencyService::convertNumberFormatToNumber(round($totDisPro, $decimalPlaces));
            $data[$x][22] = 0;
            $data[$x][23] = 0;
        }

        $companyCode = isset($companyData->CompanyID) ? $companyData->CompanyID : 'common';
        $excelColumnFormat = [
            'H' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'K' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'J' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];

        $detail_array = array(
            'company_code' => $companyCode,
            'excelFormat' => $excelColumnFormat
        );

        $fileName = trans('custom.asset_register_detail_3');
        $path = 'asset_register/report/excel/';
        $basePath = CreateExcel::process($data, 'xls', $fileName, $path, $detail_array);

        if ($basePath == '') {
            throw new \Exception(trans('custom.unable_to_export_excel'));
        }

        return $basePath;
    }

   
    private function processARD2($controller, $service, $request)
    {
        $request = (object)$controller->convertArrayToSelectedValue((array)$request, array('currencyID', 'typeID', 'excelType'));
        $output = $controller->getAssetRegisterDetail2($request);
        $companyCurrency = Helper::companyCurrency($request->companySystemID);
        if ($request->currencyID == 2) {
            $currencyDecimalPlace = $companyCurrency->localcurrency->DecimalPlaces;
            $currencyCode = $companyCurrency->localcurrency->CurrencyCode;
        } else {
            $currencyDecimalPlace = $companyCurrency->reportingcurrency->DecimalPlaces;
            $currencyCode = $companyCurrency->reportingcurrency->CurrencyCode;
        }

        $dataArray = array();
        $year = Carbon::parse($request->fromDate)->format('Y');
        $companyMaster = Company::find(isset($request->companySystemID) ? $request->companySystemID : null);
        $companyCode = isset($companyMaster->CompanyID) ? $companyMaster->CompanyID : 'common';

        if (empty($dataArray)) {
            $assetRegisterDetail2Header = new AssetRegisterDetail2();
            $allHeaders = collect($assetRegisterDetail2Header->getHeader())->toArray();

            $monthHeaders = [
                trans('custom.jan'), trans('custom.feb'), trans('custom.mar'),
                trans('custom.apr'), trans('custom.may'), trans('custom.jun'),
                trans('custom.jul'), trans('custom.aug'), trans('custom.sep'),
                trans('custom.oct'), trans('custom.nov'), trans('custom.dec')
            ];

            $monthStartIndex = -1;
            foreach ($allHeaders as $index => $header) {
                if (in_array($header, $monthHeaders)) {
                    $monthStartIndex = $index;
                    break;
                }
            }

            $headers = array_slice($allHeaders, 0, $monthStartIndex);

            $monthMap = [
                'Jan' => trans('custom.jan'), 'Feb' => trans('custom.feb'),
                'Mar' => trans('custom.mar'), 'Apr' => trans('custom.apr'),
                'May' => trans('custom.may'), 'Jun' => trans('custom.jun'),
                'Jul' => trans('custom.jul'), 'Aug' => trans('custom.aug'),
                'Sep' => trans('custom.sep'), 'Oct' => trans('custom.oct'),
                'Nov' => trans('custom.nov'), 'Dec' => trans('custom.dec')
            ];

            foreach ($output['period'] as $period) {
                $periodParts = explode('-', $period);
                if (isset($periodParts[0]) && isset($monthMap[$periodParts[0]])) {
                    $headers[] = $period;
                }
            }

            array_push($dataArray, $headers);
        }

        if ($output['data']) {
            foreach ($output['data'] as $val) {
                $financialData = new AssetRegisterDetail2();
                $datetime = Carbon::parse($val->postedDate);
                $datetime2 = Carbon::parse($val->dateDEP);
                $financialData->setGlCode($val->COSTGLCODE);
                $financialData->setCategory($val->catDescription);
                $financialData->setFaCode($val->faCode);
                $financialData->setGroupedFaCode($val->group_to);
                $financialData->setPostingDateOfFA($datetime->toDateString());
                $financialData->setDepStartDate($datetime2->toDateString());
                $financialData->setDepPercentage($val->DEPpercentage);
                $financialData->setServiceLine($val->ServiceLineDes);
                $financialData->setGrvDate($val->dateAQ);
                $financialData->setGrvNumber($val->docOrigin);
                $financialData->setSupplierName($val->supplierName);
                $financialData->setOpeningCost(round($val->opening, $currencyDecimalPlace));
                $financialData->setAdditionCost(round($val->addition, $currencyDecimalPlace));
                $financialData->setDisposalCost(round($val->disposed, $currencyDecimalPlace));
                $financialData->setClosingCost(round($val->costClosing, $currencyDecimalPlace));
                $financialData->setOpeningDep(round($val->openingDep, $currencyDecimalPlace));

                $sumPeriod = 0;
                foreach ($output['period'] as $val2) {
                    $sumPeriod += $val->$val2;
                }

                $financialData->setChargeDuringTheYear(round($sumPeriod, $currencyDecimalPlace));

                if ($val->DIPOSED == 0) {
                    $financialData->setChargeOnDisposal(round($val->disposedDep, $currencyDecimalPlace));
                } elseif ($val->DIPOSED != 0) {
                    $financialData->setChargeOnDisposal(round($val->openingDep + $sumPeriod, $currencyDecimalPlace));
                }

                if ($val->DIPOSED == 0) {
                    $financialData->setClosingDep(round($val->openingDep + $sumPeriod - $val->disposedDep, $currencyDecimalPlace));
                } elseif ($val->DIPOSED != 0) {
                    $financialData->setClosingDep(round($val->openingDep + $sumPeriod - ($val->openingDep + $sumPeriod), $currencyDecimalPlace));
                }

                if ($val->DIPOSED == 0) {
                    $financialData->setNbv(round($val->costClosing - ($val->openingDep + $sumPeriod - $val->disposedDep), $currencyDecimalPlace));
                } elseif ($val->DIPOSED != 0) {
                    $financialData->setNbv(round($val->costClosing - ($val->openingDep + $sumPeriod - ($val->openingDep + $sumPeriod)), $currencyDecimalPlace));
                }

                $rowData = [
                    $financialData->glCode,
                    $financialData->category,
                    $financialData->faCode,
                    $financialData->groupedFaCode,
                    $financialData->postingDateOfFA,
                    $financialData->depStartDate,
                    $financialData->depPercentage,
                    $financialData->serviceLine,
                    $financialData->grvDate,
                    $financialData->grvNumber,
                    $financialData->supplierName,
                    $financialData->openingCost,
                    $financialData->additionCost,
                    $financialData->disposalCost,
                    $financialData->closingCost,
                    $financialData->openingDep,
                    $financialData->chargeDuringTheYear,
                    $financialData->chargeOnDisposal,
                    $financialData->closingDep,
                    $financialData->nbv,
                ];

                for ($i = 0; $i < count($output['period']); $i++) {
                    $propertyName = $output['period'][$i];
                    $rowData[] = round($val->{$propertyName}, $currencyDecimalPlace);
                }

                array_push($dataArray, $rowData);
            }
        }

        $excelColumnFormat = [
            'L' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
        $title = trans('custom.asset_register_detail2_report');
        $fileName = trans('custom.asset_register_detail2_report');
        $path = 'asset_register/report/excel/';

        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');

        $exportToExcel = $service
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName("")
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setType('xls')
            ->setReportType(2)
            ->setCurrency("")
            ->setExcelFormat($excelColumnFormat)
            ->setData($dataArray)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if (!$exportToExcel['success']) {
            throw new \Exception(trans('custom.unable_to_export_excel'));
        }

        return $exportToExcel['data'];
    }

    
    private function processARS($controller, $service, $request)
    {
        $request = (object)$controller->convertArrayToSelectedValue((array)$request, array('currencyID', 'year', 'month', 'typeID', 'excelType'));
        $financePeriod = CompanyFinancePeriod::find($request->financePeriod);
        $financeYear = CompanyFinanceYear::find($request->financeYear);
        $beginingFinancialYear = Carbon::parse($financeYear->bigginingDate)->format('d-M-Y');
        $output = $controller->getAssetRegisterSummaryQRY($request);
        $assetCategory = $request->assetCategory;
        $costTotal = [];
        $depTotal = [];
        $nbv = [];
        $nbvEnd = [];
        $depQry = collect($output['depQry']);
        $costQry = collect($output['costQry']);

        $filteredCostQry = $costQry->firstWhere('description', $beginingFinancialYear);
        $filteredDepQry = $depQry->firstWhere('description', $beginingFinancialYear);

        if (count($assetCategory) > 0) {
            foreach ($assetCategory as $val) {
                $depTotal[$val['financeCatDescription']] = $depQry->sum($val['financeCatDescription']);
                $costTotal[$val['financeCatDescription']] = $costQry->sum($val['financeCatDescription']);
                $nbv[$val['financeCatDescription']] = $filteredCostQry[$val['financeCatDescription']] - $filteredDepQry[$val['financeCatDescription']];
                $nbvEnd[$val['financeCatDescription']] = $costQry->sum($val['financeCatDescription']) - $depQry->sum($val['financeCatDescription']);
            }
        }

        $selectedMonthYear = Carbon::parse($financePeriod->dateTo)->format('Y/M');

        $costTotal['total'] = collect($costTotal)->values()->sum();
        $costTotal['description'] = trans('custom.as_at_end_of') . ' ' . $selectedMonthYear;
        $depTotal['total'] = collect($depTotal)->values()->sum();
        $depTotal['description'] = trans('custom.as_at_end_of') . ' ' . $selectedMonthYear;
        $output['depQry'] = collect($output['depQry'])->toArray();
        $output['costQry'] = collect($output['costQry'])->toArray();
        $output['depQry'][] = $depTotal;
        $output['costQry'][] = $costTotal;

        $nbv['total'] = collect($nbv)->values()->sum();
        $nbvEnd['total'] = collect($nbvEnd)->values()->sum();
        $nbv['description'] = $beginingFinancialYear;
        $nbvEnd['description'] = trans('custom.as_at_end_of') . ' ' . $selectedMonthYear;
        $output['nbvQry'][] = $nbv;
        $output['nbvQry'][] = $nbvEnd;

        $currencyCode = '';
        $currencyDecimalPlace = 2;

        $companyCurrency = Helper::companyCurrency($request->companySystemID);
        if ($request->currencyID == 2) {
            $currencyDecimalPlace = $companyCurrency->localcurrency->DecimalPlaces;
            $currencyCode = $companyCurrency->localcurrency->CurrencyCode;
        } else {
            $currencyDecimalPlace = $companyCurrency->reportingcurrency->DecimalPlaces;
            $currencyCode = $companyCurrency->reportingcurrency->CurrencyCode;
        }

        
        $data = array();

        
        $headerRow = array();
        $headerRow[trans('custom.description')] = trans('custom.description');
        if (count($assetCategory) > 0) {
            foreach ($assetCategory as $val2) {
                $headerRow[$val2['financeCatDescription']] = $val2['financeCatDescription'];
            }
        }
        $headerRow[trans('custom.total')] = trans('custom.total');
        $data[] = $headerRow;

        
        $costSectionRow = array_fill_keys(array_keys($headerRow), '');
        $costSectionRow[trans('custom.description')] = 'Cost(' . $currencyCode . ')';
        $data[] = $costSectionRow;

        foreach ($output['costQry'] as $val) {
            $row = array();
            $row[trans('custom.description')] = data_get($val, 'description', '');
            if (count($assetCategory) > 0) {
                foreach ($assetCategory as $val2) {
                    $key = $val2['financeCatDescription'];
                    $row[$key] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, $key, 0), $currencyDecimalPlace));
                }
            }
            $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, 'total', 0), $currencyDecimalPlace));
            $data[] = $row;
        }

        
        $depSectionRow = array_fill_keys(array_keys($headerRow), '');
        $depSectionRow[trans('custom.description')] = 'Depreciation(' . $currencyCode . ')';
        $data[] = $depSectionRow;

        foreach ($output['depQry'] as $val) {
            $row = array();
            $row[trans('custom.description')] = data_get($val, 'description', '');
            if (count($assetCategory) > 0) {
                foreach ($assetCategory as $val2) {
                    $key = $val2['financeCatDescription'];
                    $row[$key] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, $key, 0), $currencyDecimalPlace));
                }
            }
            $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, 'total', 0), $currencyDecimalPlace));
            $data[] = $row;
        }

        
        $nbvSectionRow = array_fill_keys(array_keys($headerRow), '');
        $nbvSectionRow[trans('custom.description')] = 'Net Book Value(' . $currencyCode . ')';
        $data[] = $nbvSectionRow;

        foreach ($output['nbvQry'] as $val) {
            $row = array();
            $row[trans('custom.description')] = data_get($val, 'description', '');
            if (count($assetCategory) > 0) {
                foreach ($assetCategory as $val2) {
                    $key = $val2['financeCatDescription'];
                    $row[$key] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, $key, 0), $currencyDecimalPlace));
                }
            }
            $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round((float) data_get($val, 'total', 0), $currencyDecimalPlace));
            $data[] = $row;
        }

        $excelColumnFormat = [
            'B' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'C' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'D' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'E' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'G' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'V' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'X' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Y' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Z' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];

        $companyMaster = Company::find(isset($request->companySystemID) ? $request->companySystemID : null);
        $companyCode = isset($companyMaster->CompanyID) ? $companyMaster->CompanyID : 'common';

        $title = trans('custom.asset_register_summary_report');
        $fileName = trans('custom.asset_register_summary_report');
        $path = 'asset_register/report/excel/';

        $exportToExcel = $service
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName("")
            ->setFromDate("")
            ->setToDate("")
            ->setType("xls")
            ->setReportType(2)
            ->setCurrency("")
            ->setExcelFormat($excelColumnFormat)
            ->setData($data)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if (!$exportToExcel['success']) {
            throw new \Exception(trans('custom.unable_to_export_excel'));
        }

        return $exportToExcel['data'];
    }

   
    private function processARGD($controller, $service, $request)
    {
        $request = (object)$controller->convertArrayToSelectedValue((array)$request, array('typeID'));
        $output = $controller->getAssetRegisterDetail($request);
        $companyCurrency = Helper::companyCurrency($request->companySystemID);

        $final = $controller->getAssetRegisterGroupedDetailFinalArray($output, $companyCurrency);
        $outputArr = $final['reportData'];

        $x = 0;
        $data = [];
        if (!empty($outputArr)) {

            foreach ($outputArr as $masterKey => $masterVal) {
                $data[$x][trans('custom.cost_gl')] = $masterKey;
                $data[$x][trans('custom.acc_dep_gl')] = '';
                $data[$x][trans('custom.e_type')] = '';
                $data[$x][trans('custom.e_segment')] = '';
                $data[$x][trans('custom.category')] = '';
                $data[$x][trans('custom.fa_code')] = '';
                $data[$x][trans('custom.grouped_yn')] = '';
                $data[$x][trans('custom.serial_number')] = '';
                $data[$x][trans('custom.asset_description')] = '';
                $data[$x][trans('custom.dep_percentage')] = '';
                $data[$x][trans('custom.date_acquired')] = '';
                $data[$x][trans('custom.dep_start_date')] = '';
                $data[$x][trans('custom.local_amount_unitcost')] = '';
                $data[$x][trans('custom.local_amount_accdep')] = '';
                $data[$x][trans('custom.local_amount_net_value')] = '';
                $data[$x][trans('custom.rpt_amount_unit_cost')] = '';
                $data[$x][trans('custom.rpt_amount_acc_dep')] = '';
                $data[$x][trans('custom.rpt_amount_acc_net_value')] = '';

                $x++;

                $data[$x][trans('custom.cost_gl')] = trans('custom.cost_gl');
                $data[$x][trans('custom.acc_dep_gl')] = trans('custom.acc_dep_gl');
                $data[$x][trans('custom.e_type')] = trans('custom.e_type');
                $data[$x][trans('custom.e_segment')] = trans('custom.e_segment');
                $data[$x][trans('custom.category')] = trans('custom.finance_category');
                $data[$x][trans('custom.fa_code')] = trans('custom.fa_code');
                $data[$x][trans('custom.grouped_yn')] = trans('custom.grouped_fa_code');
                $data[$x][trans('custom.serial_number')] = trans('custom.serial_number');
                $data[$x][trans('custom.asset_description')] = trans('custom.asset_description');
                $data[$x][trans('custom.dep_percentage')] = trans('custom.dep_percentage');
                $data[$x][trans('custom.date_acquired')] = trans('custom.date_acquired');
                $data[$x][trans('custom.dep_start_date')] = trans('custom.dep_start_date');
                $data[$x][trans('custom.local_amount_unitcost')] = '';
                $data[$x][trans('custom.local_amount_accdep')] = '';
                $data[$x][trans('custom.local_amount_net_value')] = trans('custom.local_amount');
                $data[$x][trans('custom.rpt_amount_unit_cost')] = '';
                $data[$x][trans('custom.rpt_amount_acc_dep')] = trans('custom.rpt_amount');
                $data[$x][trans('custom.rpt_amount_acc_net_value')] = '';

                $x++;

                $data[$x][trans('custom.cost_gl')] = '';
                $data[$x][trans('custom.acc_dep_gl')] = '';
                $data[$x][trans('custom.e_type')] = '';
                $data[$x][trans('custom.e_segment')] = '';
                $data[$x][trans('custom.category')] = '';
                $data[$x][trans('custom.fa_code')] = '';
                $data[$x][trans('custom.grouped_yn')] = '';
                $data[$x][trans('custom.serial_number')] = '';
                $data[$x][trans('custom.asset_description')] = '';
                $data[$x][trans('custom.dep_percentage')] = '';
                $data[$x][trans('custom.date_acquired')] = '';
                $data[$x][trans('custom.dep_start_date')] = '';
                $data[$x][trans('custom.local_amount_unitcost')] = trans('custom.unit_cost');
                $data[$x][trans('custom.local_amount_accdep')] = trans('custom.accdep_amount');
                $data[$x][trans('custom.local_amount_net_value')] = trans('custom.net_book_value');
                $data[$x][trans('custom.rpt_amount_unit_cost')] = trans('custom.unit_cost');
                $data[$x][trans('custom.rpt_amount_acc_dep')] = trans('custom.accdep_amount');
                $data[$x][trans('custom.rpt_amount_acc_net_value')] = trans('custom.net_book_value');

                $x++;

                $COSTUNIT = 0;
                $depAmountLocal = 0;
                $localnbv = 0;
                $costUnitRpt = 0;
                $depAmountRpt = 0;
                $rptnbv = 0;

                $localDecimalPlace = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces : 3;
                $rptDecimalPlace = isset($companyCurrency->reportingcurrency->DecimalPlaces) ? $companyCurrency->reportingcurrency->DecimalPlaces : 2;

                foreach ($masterVal as $mainAsset => $assetArray) {
                    foreach ($assetArray as $value) {
                        $x++;
                        $datetime = Carbon::parse($value->postedDate);
                        $datetime2 = Carbon::parse($value->dateDEP);
                        $data[$x][trans('custom.cost_gl')] = $value->COSTGLCODE;
                        $data[$x][trans('custom.acc_dep_gl')] = $value->ACCDEPGLCODE;
                        $data[$x][trans('custom.e_type')] = $value->typeDes;
                        $data[$x][trans('custom.e_segment')] = $value->ServiceLineDes;
                        $data[$x][trans('custom.category')] = $masterKey;
                        $data[$x][trans('custom.fa_code')] = $value->faCode;
                        $data[$x][trans('custom.grouped_yn')] = $value->groupbydesc;
                        $data[$x][trans('custom.serial_number')] = $value->faUnitSerialNo;
                        $data[$x][trans('custom.asset_description')] = $value->assetDescription;
                        $data[$x][trans('custom.dep_percentage')] = round($value->DEPpercentage, 2);
                        $data[$x][trans('custom.date_acquired')] = ($value->postedDate) ? $datetime->toDateString() : null;
                        $data[$x][trans('custom.dep_start_date')] = ($value->dateDEP) ? $datetime2->toDateString() : null;
                        $data[$x][trans('custom.local_amount_unitcost')] = CurrencyService::convertNumberFormatToNumber(round($value->COSTUNIT, $localDecimalPlace));
                        $data[$x][trans('custom.local_amount_accdep')] = CurrencyService::convertNumberFormatToNumber(round($value->depAmountLocal, $localDecimalPlace));
                        $data[$x][trans('custom.local_amount_net_value')] = CurrencyService::convertNumberFormatToNumber(round($value->localnbv, $localDecimalPlace));
                        $data[$x][trans('custom.rpt_amount_unit_cost')] = CurrencyService::convertNumberFormatToNumber(round($value->costUnitRpt, $rptDecimalPlace));
                        $data[$x][trans('custom.rpt_amount_acc_dep')] = CurrencyService::convertNumberFormatToNumber(round($value->depAmountRpt, $rptDecimalPlace));
                        $data[$x][trans('custom.rpt_amount_acc_net_value')] = CurrencyService::convertNumberFormatToNumber(round($value->rptnbv, $rptDecimalPlace));

                        if (!$value->isHeader) {
                            $COSTUNIT += $value->COSTUNIT;
                            $depAmountLocal += $value->depAmountLocal;
                            $localnbv += $value->localnbv;
                            $costUnitRpt += $value->costUnitRpt;
                            $depAmountRpt += $value->depAmountRpt;
                            $rptnbv += $value->rptnbv;
                        }
                    }

                    $x++;
                    $data[$x][trans('custom.cost_gl')] = '';
                    $data[$x][trans('custom.acc_dep_gl')] = '';
                    $data[$x][trans('custom.e_type')] = '';
                    $data[$x][trans('custom.e_segment')] = '';
                    $data[$x][trans('custom.category')] = '';
                    $data[$x][trans('custom.fa_code')] = '';
                    $data[$x][trans('custom.grouped_yn')] = '';
                    $data[$x][trans('custom.serial_number')] = '';
                    $data[$x][trans('custom.asset_description')] = '';
                    $data[$x][trans('custom.dep_percentage')] = '';
                    $data[$x][trans('custom.date_acquired')] = '';
                    $data[$x][trans('custom.dep_start_date')] = '';
                    $data[$x][trans('custom.local_amount_unitcost')] = '';
                    $data[$x][trans('custom.local_amount_accdep')] = '';
                    $data[$x][trans('custom.local_amount_net_value')] = '';
                    $data[$x][trans('custom.rpt_amount_unit_cost')] = '';
                    $data[$x][trans('custom.rpt_amount_acc_dep')] = '';
                    $data[$x][trans('custom.rpt_amount_acc_net_value')] = '';
                }
                $x++;

                $data[$x][trans('custom.cost_gl')] = '';
                $data[$x][trans('custom.acc_dep_gl')] = '';
                $data[$x][trans('custom.e_type')] = '';
                $data[$x][trans('custom.e_segment')] = '';
                $data[$x][trans('custom.category')] = '';
                $data[$x][trans('custom.fa_code')] = '';
                $data[$x][trans('custom.grouped_yn')] = '';
                $data[$x][trans('custom.serial_number')] = '';
                $data[$x][trans('custom.asset_description')] = '';
                $data[$x][trans('custom.dep_percentage')] = '';
                $data[$x][trans('custom.date_acquired')] = '';
                $data[$x][trans('custom.dep_start_date')] = trans('custom.sub_total');
                $data[$x][trans('custom.local_amount_unitcost')] = $COSTUNIT;
                $data[$x][trans('custom.local_amount_accdep')] = $depAmountLocal;
                $data[$x][trans('custom.local_amount_net_value')] = $localnbv;
                $data[$x][trans('custom.rpt_amount_unit_cost')] = $costUnitRpt;
                $data[$x][trans('custom.rpt_amount_acc_dep')] = $depAmountRpt;
                $data[$x][trans('custom.rpt_amount_acc_net_value')] = $rptnbv;

                $x++;
            }

            $x++;

            $data[$x][trans('custom.cost_gl')] = '';
            $data[$x][trans('custom.acc_dep_gl')] = '';
            $data[$x][trans('custom.e_type')] = '';
            $data[$x][trans('custom.e_segment')] = '';
            $data[$x][trans('custom.category')] = '';
            $data[$x][trans('custom.fa_code')] = '';
            $data[$x][trans('custom.grouped_yn')] = '';
            $data[$x][trans('custom.serial_number')] = '';
            $data[$x][trans('custom.asset_description')] = '';
            $data[$x][trans('custom.dep_percentage')] = '';
            $data[$x][trans('custom.date_acquired')] = '';
            $data[$x][trans('custom.dep_start_date')] = trans('custom.total');
            $data[$x][trans('custom.local_amount_unitcost')] = CurrencyService::convertNumberFormatToNumber($final['COSTUNIT']);
            $data[$x][trans('custom.local_amount_accdep')] = CurrencyService::convertNumberFormatToNumber($final['depAmountLocal']);
            $data[$x][trans('custom.local_amount_net_value')] = CurrencyService::convertNumberFormatToNumber($final['localnbv']);
            $data[$x][trans('custom.rpt_amount_unit_cost')] = CurrencyService::convertNumberFormatToNumber($final['costUnitRpt']);
            $data[$x][trans('custom.rpt_amount_acc_dep')] = CurrencyService::convertNumberFormatToNumber($final['depAmountRpt']);
            $data[$x][trans('custom.rpt_amount_acc_net_value')] = CurrencyService::convertNumberFormatToNumber($final['rptnbv']);
        }

        $companyMaster = Company::find(isset($request->companySystemID) ? $request->companySystemID : null);
        $companyCode = isset($companyMaster->CompanyID) ? $companyMaster->CompanyID : 'common';

        $excelColumnFormat = [
            'K' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'L' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_DATE_DDMMYYYY,
            'M' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => \PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];
        $title = trans('custom.asset_register_grouped_detail_report');
        $fileName = trans('custom.asset_register_grouped_detail');
        $path = 'asset_register/report/excel/';

        $exportToExcel = $service
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName("")
            ->setFromDate("")
            ->setToDate("")
            ->setType("xls")
            ->setReportType(2)
            ->setCurrency("")
            ->setExcelFormat($excelColumnFormat)
            ->setData($data)
            ->setDateType(1)
            ->setDetails()
            ->generateExcel();

        if (!$exportToExcel['success']) {
            throw new \Exception(trans('custom.unable_to_export_excel'));
        }

        return $exportToExcel['data'];
    }
}

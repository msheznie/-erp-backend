<?php

namespace App\Services\AssetManagement\AssetRegister;

use App\Exports\AssetManagement\AssetRegister\AssetRegisterDetail2;
use App\helper\CreateExcel;
use App\helper\Helper;
use App\helper\email as EmailHelper;
use App\Models\Company;
use App\Models\Employee;
use App\Services\WebPushNotificationService;
use App\Services\Excel\ExportVatDetailReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExportAssetRegisterDetail2ExcelService
{
    private array $input;
    private string $userLang;
    private ?int $userId = null;

    public function __construct($input, string $userLang = 'en')
    {
        $this->input = is_array($input) ? $input : [];
        $this->userLang = $userLang ?: 'en';
        $this->userId = isset($this->input['userId']) ? (int) $this->input['userId'] : null;

        app()->setLocale($this->userLang);
    }

    public function exportAndNotify(): void
    {
        $basePath = $this->generateExcel();
        if (!$basePath) {
            return;
        }

        
        if ($this->userId) {
            $webPushData = [
                'title' => 'asset_register_detail2_excel_generated',
                'body' => '',
                'url' => '',
                'path' => $basePath,
            ];
            WebPushNotificationService::sendNotification($webPushData, 3, [$this->userId]);
        }

        
        $this->sendEmail($basePath);
    }

    /**
     * Generate Excel and upload it to S3.
     * Returns the S3 presigned URL (CreateExcel::process returns that).
     */
    public function generateExcel(): string
    {
        $input = $this->convertArrayToSelectedValue($this->input, ['currencyID', 'typeID', 'excelType']);
        $request = (object) $input;

        $type = 'xls';

        $output = $this->getAssetRegisterDetail2($request);
        $companyCurrency = Helper::companyCurrency($request->companySystemID);

        if ($request->currencyID == 2) {
            $currencyDecimalPlace = $companyCurrency->localcurrency->DecimalPlaces;
        } else {
            $currencyDecimalPlace = $companyCurrency->reportingcurrency->DecimalPlaces;
        }

        $dataArray = [];

        $companyMaster = Company::find(isset($request->companySystemID) ? $request->companySystemID : null);
        $companyCode = isset($companyMaster->CompanyID) ? $companyMaster->CompanyID : 'common';

        // Build headers 
        $assetRegisterDetail2Header = new AssetRegisterDetail2();
        $allHeaders = collect($assetRegisterDetail2Header->getHeader())->toArray();

        $monthHeaders = [
            trans('custom.jan'),
            trans('custom.feb'),
            trans('custom.mar'),
            trans('custom.apr'),
            trans('custom.may'),
            trans('custom.jun'),
            trans('custom.jul'),
            trans('custom.aug'),
            trans('custom.sep'),
            trans('custom.oct'),
            trans('custom.nov'),
            trans('custom.dec'),
        ];

        $monthStartIndex = -1;
        foreach ($allHeaders as $index => $header) {
            if (in_array($header, $monthHeaders, true)) {
                $monthStartIndex = $index;
                break;
            }
        }

        $headers = array_slice($allHeaders, 0, $monthStartIndex);

        $monthMap = [
            'Jan' => trans('custom.jan'),
            'Feb' => trans('custom.feb'),
            'Mar' => trans('custom.mar'),
            'Apr' => trans('custom.apr'),
            'May' => trans('custom.may'),
            'Jun' => trans('custom.jun'),
            'Jul' => trans('custom.jul'),
            'Aug' => trans('custom.aug'),
            'Sep' => trans('custom.sep'),
            'Oct' => trans('custom.oct'),
            'Nov' => trans('custom.nov'),
            'Dec' => trans('custom.dec'),
        ];

        foreach ($output['period'] as $period) {
            $periodParts = explode('-', $period);
            if (isset($periodParts[0]) && isset($monthMap[$periodParts[0]])) {
                $headers[] = $period;
            }
        }

        $dataArray[] = $headers;

        
        $chunkSize = 500;
        $period = $output['period'] ?? [];
        $rows = $output['data'] ?? [];

        collect($rows)->chunk($chunkSize)->each(function ($chunk) use (&$dataArray, $period, $currencyDecimalPlace) {
            foreach ($chunk as $val) {
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
                foreach ($period as $val2) {
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
                    $financialData->setNbv(round(
                        $val->costClosing - ($val->openingDep + $sumPeriod - $val->disposedDep),
                        $currencyDecimalPlace
                    ));
                } elseif ($val->DIPOSED != 0) {
                    $financialData->setNbv(round(
                        $val->costClosing - ($val->openingDep + $sumPeriod - ($val->openingDep + $sumPeriod)),
                        $currencyDecimalPlace
                    ));
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

                for ($i = 0; $i < count($period); $i++) {
                    $propertyName = $period[$i];
                    $rowData[] = round($val->{$propertyName}, $currencyDecimalPlace);
                }

                $dataArray[] = $rowData;
            }
        });

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

        $service = new ExportVatDetailReportService();
        $exportToExcel = $service
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName("")
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setType($type)
            ->setReportType(2)
            ->setCurrency("")
            ->setExcelFormat($excelColumnFormat)
            ->setData($dataArray)
            ->setDateType(2)
            ->setDetails()
            ->generateExcel();

        if (!$exportToExcel['success']) {
            return '';
        }

        return (string) $exportToExcel['data'];
    }

    private function sendEmail(string $basePath): void
    {
        if (!$this->userId) {
            return;
        }

        try {
            $employee = Employee::find($this->userId);
            if (!$employee || !$employee->empEmail) {
                return;
            }

            $companySystemID = $this->input['companySystemID'] ?? null;
            $companyName = $companySystemID ? (optional(Company::find($companySystemID))->CompanyName ?? '') : '';

            $fromDate = isset($this->input['fromDate']) ? Carbon::parse($this->input['fromDate'])->format('d/m/Y') : '';
            $toDate = isset($this->input['toDate']) ? Carbon::parse($this->input['toDate'])->format('d/m/Y') : '';

            $subject = trans('custom.asset_register_detail2_email_subject');
            $body = trans('custom.asset_register_detail2_email_greeting') . '<br /><br />' .
                trans('custom.asset_register_detail2_email_body', [
                    'fromDate' => $fromDate,
                    'toDate' => $toDate
                ]) . '<br /><br />' .
                trans('custom.asset_register_detail2_email_regards') . '<br />' .
                $companyName;

            $attachmentList = [
                'asset_register_detail2_report.xls' => $basePath,
            ];

            $dataEmail = [
                'empEmail' => $employee->empEmail,
                'companySystemID' => $companySystemID,
                'attachmentFileName' => '',
                'attachmentList' => $attachmentList,
                'emailAlertMessage' => $body,
                'alertMessage' => $subject,
            ];

            EmailHelper::sendEmailErp($dataEmail);
        } catch (\Exception $e) {
            Log::channel('asset_register_detail2_excel_export')->error('Email send failed.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function convertArrayToSelectedValue($input, $params): array
    {
        foreach ($input as $key => $value) {
            if (in_array($key, $params, true)) {
                if (is_array($input[$key])) {
                    if (count($input[$key]) > 0) {
                        $input[$key] = $input[$key][0];
                    }
                }
            }
        }

        return $input;
    }

    /**
     * Copied from AssetManagementReportAPIController::getAssetRegisterDetail2()
     */
    private function getAssetRegisterDetail2($request)
    {
        $typeID = $request->typeID;
        $fromDate = Carbon::parse($request->fromDate)->format('Y-m-d');
        $toDate = Carbon::parse($request->toDate)->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();

        $currencyColumn = '';
        $currencyColumnDep = '';
        if ($request->currencyID == 2) {
            $currencyColumn = 'erp_fa_asset_master.COSTUNIT';
            $currencyColumnDep = 'erp_fa_assetdepreciationperiods.depAmountLocal';
        } else {
            $currencyColumn = 'erp_fa_asset_master.costUnitRpt';
            $currencyColumnDep = 'erp_fa_assetdepreciationperiods.depAmountRpt';
        }

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [(int)$request->companySystemID];
        }

        $periodQry = "";
        $periodQry2 = "";
        $periodArr = [];
        $period = CarbonPeriod::create($fromDate, '1 month', $toDate);
        foreach ($period as $val) {
            $periodQry .= 'IFNULL(SUM(IF(DATE_FORMAT(depForFYperiodEndDate,"%Y-%m") = "' . $val->format('Y-m') . '",' . $currencyColumnDep . ',0)),0) AS `' . $val->format('M-Y') . '`,';
            $periodQry2 .= 'IFNULL(`' . $val->format('M-Y') . '`,0) as `' . $val->format('M-Y') . '`,';
            $periodArr[] = $val->format('M-Y');
        }

        $toDateNew = Carbon::createFromFormat('Y-m-d', $toDate)->addDay()->toDateString();
        $query = 'SELECT 
                ' . $periodQry2 . '
                COSTGLCODE,
                faCode,
                    postedDate,
                    dateDEP,
                    DEPpercentage,
                    opening,
                    addition,
                    dateAQ,
                    docOrigin,
                    faID,
                    serviceLineSystemID,
                    supplierIDRentedAsset,
                    groupTO,
                    faCatID,
                    DIPOSED,
                    disposedDate,
                    ServiceLineDes,
                    supplierName,
                    group_to,
                    catDescription,
                    disposed,
                    costClosing,
                    IFNULL(openingDep,0) as openingDep,
                    IFNULL(additionDep,0) as additionDep,
                    IFNULL(disposedDep,0) as disposedDep,
                    IFNULL(closingDep,0) as closingDep
                    FROM (SELECT
                    erp_fa_asset_master.COSTGLCODE,
                    erp_fa_asset_master.faCode,
                    erp_fa_asset_master.postedDate,
                    erp_fa_asset_master.dateDEP,
                    erp_fa_asset_master.DEPpercentage,
                    0 AS opening,
                    ' . $currencyColumn . ' AS addition,
                    erp_fa_asset_master.dateAQ,
                    erp_fa_asset_master.docOrigin,
                    erp_fa_asset_master.faID,
                    erp_fa_asset_master.serviceLineSystemID,
                    erp_fa_asset_master.supplierIDRentedAsset,
                    erp_fa_asset_master.groupTO,
                    erp_fa_asset_master.faCatID,
                    erp_fa_asset_master.DIPOSED,
                    erp_fa_asset_master.disposedDate,
                    serviceline.ServiceLineDes,
                    suppliermaster.supplierName,
                    a2.faCode as group_to,
                    erp_fa_category.catDescription,
                IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( "' . $fromDate . '" < erp_fa_asset_master.disposedDate && "' . $toDate . '" > erp_fa_asset_master.disposedDate ), ' . $currencyColumn . ', 0 ) AS disposed,
                    (0 + ' . $currencyColumn . ' - IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( "' . $fromDate . '" < erp_fa_asset_master.disposedDate && "' . $toDate . '" >  erp_fa_asset_master.disposedDate ), ' . $currencyColumn . ', 0 )) as costClosing,
                    dep1.*,
                    dep2.* 
                FROM
                    erp_fa_asset_master
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = erp_fa_asset_master.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '" OR erp_fa_assetdepreciationperiods.depForFYperiodStartDate IS NULL)
                    AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" OR erp_fa_assetdepreciationperiods.depForFYperiodEndDate IS NULL) GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
                    LEFT JOIN ( 
                    SELECT 0 as openingDep,IFNULL(SUM(' . $currencyColumnDep . '),0) as additionDep,SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 )) AS disposedDep,
                    (0 + IFNULL(SUM(' . $currencyColumnDep . '),0) - SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 ))) as closingDep,erp_fa_assetdepreciationperiods.faID as faID3 
                    FROM erp_fa_assetdepreciationperiods
                    INNER JOIN erp_fa_asset_master ON  erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID
                    INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID 
                    AND erp_fa_depmaster.approved = -1 AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '" OR erp_fa_assetdepreciationperiods.depForFYperiodStartDate IS NULL)
                    AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" OR erp_fa_assetdepreciationperiods.depForFYperiodEndDate IS NULL) GROUP BY erp_fa_assetdepreciationperiods.faID 
                    ) dep2 ON dep2.faID3 = erp_fa_asset_master.faID
                    WHERE DATE(erp_fa_asset_master.postedDate) BETWEEN "' . $fromDate . '" 
                    AND "' . $toDate . '" AND erp_fa_asset_master.AUDITCATOGARY IN (' . join(',', $assetCategory) . ') 
                    AND erp_fa_asset_master.approved = -1 
                    AND erp_fa_asset_master.assetType = "'. $typeID . '"
                    AND erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
                    GROUP BY erp_fa_asset_master.faID
                    
                    UNION
                    
                    SELECT
                    erp_fa_asset_master.COSTGLCODE,
                    erp_fa_asset_master.faCode,
                    erp_fa_asset_master.postedDate,
                    erp_fa_asset_master.dateDEP,
                    erp_fa_asset_master.DEPpercentage,
                    ' . $currencyColumn . ' as opening,
                    0 as addition,
                    erp_fa_asset_master.dateAQ,
                    erp_fa_asset_master.docOrigin,
                    erp_fa_asset_master.faID,
                    erp_fa_asset_master.serviceLineSystemID,
                    erp_fa_asset_master.supplierIDRentedAsset,
                    erp_fa_asset_master.groupTO,
                    erp_fa_asset_master.faCatID,
                    erp_fa_asset_master.DIPOSED,
                    erp_fa_asset_master.disposedDate,
                    serviceline.ServiceLineDes,
                    suppliermaster.supplierName,
                    a2.faCode as group_to,
                    erp_fa_category.catDescription,
                     if(erp_fa_asset_master.DIPOSED = -1 && ("' . $fromDate . '" < erp_fa_asset_master.disposedDate  && "' . $toDate . '" >  erp_fa_asset_master.disposedDate),' . $currencyColumn . ',0) as disposed,
                     (' . $currencyColumn . '+ 0 - if(erp_fa_asset_master.DIPOSED = -1 && ("' . $fromDate . '" < erp_fa_asset_master.disposedDate  && "' . $toDate . '" >  erp_fa_asset_master.disposedDate),' . $currencyColumn . ',0)) as costClosing,
                     dep1.*,
                    dep2.* 
                FROM
                    erp_fa_asset_master
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = erp_fa_asset_master.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '" OR erp_fa_assetdepreciationperiods.depForFYperiodStartDate IS NULL)
                    AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" OR erp_fa_assetdepreciationperiods.depForFYperiodEndDate IS NULL) GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
                    LEFT JOIN ( 
                    SELECT IFNULL(SUM(' . $currencyColumnDep . '),0) as openingDep,0 as additionDep, SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 )) AS disposedDep,
                    (IFNULL(SUM(' . $currencyColumnDep . '),0)+ 0 - SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 ))) as closingDep,erp_fa_assetdepreciationperiods.faID as faID3 
                      FROM erp_fa_assetdepreciationperiods 
                      INNER JOIN erp_fa_asset_master ON  erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID
                      INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND erp_fa_depmaster.approved = -1 AND DATE(depDate) < "' . $fromDate . '" 
                    GROUP BY erp_fa_assetdepreciationperiods.faID ) dep2 ON dep2.faID3 = erp_fa_asset_master.faID
                    WHERE DATE(erp_fa_asset_master.disposedDate) > "' . $fromDate . '" 
                    AND erp_fa_asset_master.AUDITCATOGARY IN (' . join(',', $assetCategory) . ') 
                    AND erp_fa_asset_master.approved = -1 
                    AND erp_fa_asset_master.DIPOSED = -1 
                    AND erp_fa_asset_master.assetType = "'. $typeID . '"
                    AND erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
                    GROUP BY	erp_fa_asset_master.faID
                    
                    UNION 
                    
                    SELECT
                    erp_fa_asset_master.COSTGLCODE,
                    erp_fa_asset_master.faCode,
                    erp_fa_asset_master.postedDate,
                    erp_fa_asset_master.dateDEP,
                    erp_fa_asset_master.DEPpercentage,
                    ' . $currencyColumn . ' as opening,
                    0 as addition,
                    erp_fa_asset_master.dateAQ,
                    erp_fa_asset_master.docOrigin,
                    erp_fa_asset_master.faID,
                    erp_fa_asset_master.serviceLineSystemID,
                    erp_fa_asset_master.supplierIDRentedAsset,
                    erp_fa_asset_master.groupTO,
                    erp_fa_asset_master.faCatID,
                    erp_fa_asset_master.DIPOSED,
                    erp_fa_asset_master.disposedDate,
                    serviceline.ServiceLineDes,
                    suppliermaster.supplierName,
                    a2.faCode as group_to,
                    erp_fa_category.catDescription,
                    0 as disposed,
                    (' . $currencyColumn . '+0-0) as costClosing,
                    dep1.*,
                    dep2.* 
                FROM
                    erp_fa_asset_master
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = erp_fa_asset_master.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '" OR erp_fa_assetdepreciationperiods.depForFYperiodStartDate IS NULL)
                    AND (DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" OR erp_fa_assetdepreciationperiods.depForFYperiodEndDate IS NULL) GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
                    LEFT JOIN ( SELECT IFNULL(SUM(' . $currencyColumnDep . '),0) as openingDep,0 as additionDep,0 as disposedDep,(IFNULL(SUM(' . $currencyColumnDep . '),0)+ 0 - 0) as closingDep, faID as faID3 
                    FROM erp_fa_assetdepreciationperiods 
                    INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND DATE(depDate) < "' . $fromDate . '" 
                    GROUP BY faID ) dep2 ON dep2.faID3 = erp_fa_asset_master.faID
                    WHERE DATE(erp_fa_asset_master.postedDate) < "' . $fromDate . '" 
                    AND erp_fa_asset_master.AUDITCATOGARY IN (' . join(',', $assetCategory) . ') 
                    AND erp_fa_asset_master.approved = -1 
                    AND erp_fa_asset_master.DIPOSED = 0 
                    AND erp_fa_asset_master.assetType = "'. $typeID . '"
                    AND erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
                     GROUP BY	erp_fa_asset_master.faID
                    ) a GROUP BY faID';

        $output = \DB::select($query);
        return ['data' => $output, 'period' => $periodArr];
    }
}


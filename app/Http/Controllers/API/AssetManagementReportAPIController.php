<?php
/**
 * =============================================
 * -- File Name : AssetManagementReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Mubashir
 * -- Create date : 12 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- REVISION HISTORY
 * -- Date: 12-july 2018 By: Mubashir Description: Added new functions named as getFilterData(),validateReport(),generateReport(),exportReport()
 * -- Date: 12-july 2018 By: Nazir Description: Added new functions named as getAssetAdditionsQRY()
 * -- Date: 12-july 2018 By: Fayas Description: Added new functions named as getAssetDisposal()
 * -- Date: 06-feb 2019 By: Mubashir Description: Added new functions named as getAssetRegisterSummaryQRY(),getAssetRegisterSummaryDrillDownQRY()
 * -- Date: 07-feb 2018 By: Mubashir Description: Added new functions named as getAssetCWIPQRY(),assetCWIPDrillDown()
 */

namespace App\Http\Controllers\API;

use App\Exports\AssetManagement\AssetRegister\AssetRegisterDetail;
use App\Exports\AssetManagement\AssetRegister\AssetRegisterDetail2;
use App\Exports\AssetManagement\AssetRegister\AssetRegisterSummary;
use App\helper\Helper;
use App\Models\AssetFinanceCategory;
use App\Models\Company;
use App\Models\AssetDisposalMaster;
use App\Models\FixedAssetCategory;
use App\Models\FixedAssetCost;
use App\Models\ItemAssigned;
use App\Models\CompanyFinancePeriod;
use App\Models\ExpenseAssetAllocation;
use App\Models\CompanyFinanceYear;
use App\Models\ChartOfAccountsAssigned;
use App\Models\FixedAssetDepreciationPeriod;
use App\Models\FixedAssetMaster;
use App\Models\GRVMaster;
use App\Models\Months;
use App\Models\Year;
use App\Models\AssetType;
use App\Scopes\ActiveScope;
use App\Services\AssetManagementService;
use App\Services\Currency\CurrencyService;
use App\Services\Excel\ExportReportToExcelService;
use App\Services\Excel\ExportVatDetailReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DateTime;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\helper\CreateExcel;
use App\Models\BookInvSuppDet;
use App\Models\BookInvSuppMaster;
use PhpOffice\PhpSpreadsheet\Shared\Date;


class AssetManagementReportAPIController extends AppBaseController
{
    public function getFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $assetCategory = AssetFinanceCategory::all();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,bigginingDate,endingDate"))->whereIN('companySystemID', $companiesByGroup)->groupBy('bigginingDate')->orderBy('bigginingDate', 'DESC')->get();

        $financePeriod = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod,companyFinanceYearID"))->whereIN('companySystemID', $companiesByGroup)->groupBy('dateFrom')->where('departmentSystemID', 9)->get();

        $years = Year::all();
        $months = Months::all();

        /*load asset type dropdown*/
        $aasetType = AssetType::all();

        $assets = [];
        $expenseGL = [];
        $mainCategory = [];

        if ((isset($request['reportID']) && $request['reportID'] == "AEA") || (isset($request['reportID']) && $request['reportID'] == "ATR")) {
            $expenseGL = ChartOfAccountsAssigned::where('companySystemID', $selectedCompanyId)
                ->where('isAssigned', -1)
                ->where('isActive', 1)
                ->get();

            if (isset($request['reportID']) && $request['reportID'] == "ATR") {
                $mainCategory = FixedAssetCategory::withoutGlobalScope(ActiveScope::class)->where('companySystemID', $selectedCompanyId)->get();
            }
            else {
                $assets = FixedAssetMaster::where('confirmedYN',1)->where('approved',-1)->where('companySystemID',$selectedCompanyId)->get();
            }
        }

        $output = array(
            'companyFinanceYear' => $companyFinanceYear,
            'assetCategory' => $assetCategory,
            'financePeriod' => $financePeriod,
            'years' => $years,
            'months' => $months,
            'assets' => $assets,
            'expenseGL' => $expenseGL,
            'assetType' => $aasetType,
            'mainCategory' => $mainCategory
        );

        return $this->sendResponse($output, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }

    public function filterAssetByCategory(Request $request) {
        $input = $request->all();

        $companyID = $input['companyId'] ?? 0;
        $mainCategory = $input['mainCategory'] ?? 0;
        $subCategory = $input['subCategory'] ?? [];

        $assets = FixedAssetMaster::where('confirmedYN',1)
            ->where('approved',-1)
            ->where('companySystemID',$companyID)
            ->when(is_array($mainCategory), 
                function($query) use ($mainCategory) {
                    return $query->whereIn('faCatID', collect($mainCategory)->pluck('id')->toArray());
                }, 
                function($query) use ($mainCategory) {
                    return $query->where('faCatID', $mainCategory);
                })
            ->where(function ($query) use ($subCategory) {
                $query->whereIn('faSubCatID', $subCategory)
                    ->orWhereIn('faSubCatID2', $subCategory)
                    ->orWhereIn('faSubCatID3', $subCategory);
            })
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('erp_fa_fa_asset_transfer_details')
                    ->whereColumn('erp_fa_fa_asset_transfer_details.fa_master_id', 'erp_fa_asset_master.faID');
            })
            ->get();

        return $this->sendResponse($assets, "Asset retrieved successfully");
    }

    public function validateReport(Request $request)
    {

        try {

            $reportID = $request->reportID;
            switch ($reportID) {
                case 'AMAR':
                    $validator = '';
                    if ($request->reportTypeID == 'ARD') { // Asset Register Detail
                        $validator = \Validator::make($request->all(), [
                            'reportTypeID' => 'required',
                            'fromDate' => 'required',
                            'assetCategory' => 'required',
                            'typeID' => 'required'
                        ]);
                    } else if ($request->reportTypeID == 'ARS') { // Asset Register Summary
                        $validator = \Validator::make($request->all(), [
                            'reportTypeID' => 'required',
                            'financePeriod' => 'required',
                            'financeYear' => 'required',
                            'assetCategory' => 'required',
                            'currencyID' => 'required',
                            'typeID' => 'required'
                        ]);
                    } else if ($request->reportTypeID == 'ARD2') { // Asset Register Detail 2
                        $validator = \Validator::make($request->all(), [
                            'reportTypeID' => 'required',
                            'fromMonth' => 'required',
                            'toMonth' => 'required',
                            'year' => 'required',
                            'assetCategory' => 'required',
                            'currencyID' => 'required',
                            'typeID' => 'required'
                        ]);
                    } else if ($request->reportTypeID == 'ARD3') { // Asset Register Detail 3
                        $validator = \Validator::make($request->all(), [
                            'reportTypeID' => 'required',
                            'fromDate' => 'required',
                            'assetCategory' => 'required',
                            'currencyID' => 'required',
                            'typeID' => 'required'
                        ]);
                    }else if ($request->reportTypeID == 'ARGD') { // Asset Register Grouped Detail
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'assetCategory' => 'required',
                        'typeID' => 'required'
                    ]);
                }
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                case 'AMAA':
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'reportTypeID' => 'required',
                    ]);
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                case 'AMAD':
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate'
                    ]);
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                case 'AMADR':
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'year' => 'required',
                        'month' => 'required',
                        'currencyID' => 'required'
                    ]);
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                case 'AMACWIP':
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'currencyID' => 'required'
                    ]);
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                 case 'AEA':
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'currencyID' => 'required',
                        'glAccounts' => 'required',
                        'assets' => 'required',
                    ]);
    
                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                case 'ATR':
                    $validator = \Validator::make($request->all(), [
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'assets' => 'required',
                    ]);

                    if ($validator->fails()) {
                        return $this->sendError($validator->messages(), 422);
                    }
                    break;
                default:
                    return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
            }

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
 
    }

    /*generate report according to each report id*/
    public function generateReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'AMAR': //Asset Register
                if ($request->reportTypeID == 'ARD') { // Asset Register Detail
                    /*shahmy*/
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                    $typeID = $request->typeID;
                    $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
                    $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
                    $assetCategory = join(',', $assetCategory);

                    $output = $this->getAssetRegisterDetail($request);
                    $outputArr = [];

                 

                    $COSTUNIT = 0;
                    $costUnitRpt = 0;
                    $depAmountLocal = 0;
                    $depAmountRpt = 0;
                    $localnbv = 0;
                    $rptnbv = 0;
                    if ($output) {
                        foreach ($output as $val) {
                            $localnbv += $val->localnbv;
                            $COSTUNIT += $val->COSTUNIT;
                            $costUnitRpt += $val->costUnitRpt;
                            $depAmountRpt += $val->depAmountRpt;
                            $depAmountLocal += $val->depAmountLocal;
                            $rptnbv += $val->rptnbv;
                            $outputArr[$val->financeCatDescription][] = $val;
                        }
                    }
                    $companyData = \Helper::companyCurrency($request->companySystemID);

                    $sort = 'asc';
                    return \DataTables::of($output)
                    ->addIndexColumn()
                    ->with('localcurrency', $companyData->localcurrency)
                    ->with('reportingcurrency', $companyData->reportingcurrency)
                    ->with('localnbv', $localnbv)
                    ->with('rptnbv', $rptnbv)
                    ->with('COSTUNIT', $COSTUNIT)
                    ->with('costUnitRpt', $costUnitRpt)
                    ->with('depAmountLocal', $depAmountLocal)
                    ->with('depAmountRpt', $depAmountRpt)
                    ->addIndexColumn()
                    // ->with('orderCondition', $sort)
                    ->make(true);

                   // return array('reportData' => $outputArr, 'localnbv' => $localnbv, 'rptnbv' => $rptnbv, 'COSTUNIT' => $COSTUNIT, 'costUnitRpt' => $costUnitRpt, 'depAmountLocal' => $depAmountLocal, 'depAmountRpt' => $depAmountRpt);
                }

                if ($request->reportTypeID == 'ARD3') { // Asset Register Detail 3
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                    $typeID = $request->typeID;
                    $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
                    $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
                    $assetCategory = join(',', $assetCategory);

                    $output = $this->getAssetRegisterDetail3($request);
                    $outputArr = [];

                    $COSTUNIT = 0;
                    $costUnitRpt = 0;
                    $depAmountLocal = 0;
                    $depAmountRpt = 0;
                    $localnbv = 0;
                    $rptnbv = 0;
                    $acDepAmountRpt = 0;
                    $adDepAmountLocal = 0;
                    $costUnitRptDisposed = 0;
                    $costUnitDisposed = 0;
                    $profitDisposalRpt = 0;
                    $profitDisposalLocal = 0;
                    if ($output) {
                        foreach ($output as $val) {
                            $localnbv += ($val->COSTUNIT - $val->depAmountLocal);
                            $rptnbv += ($val->costUnitRpt - $val->depAmountRpt);
                            $COSTUNIT += $val->COSTUNIT;
                            $costUnitRpt += $val->costUnitRpt;
                            $depAmountRpt += $val->depAmountRpt;
                            $acDepAmountRpt += $val->acDepAmountRpt;
                            $adDepAmountLocal += $val->adDepAmountLocal;
                            $depAmountLocal += $val->depAmountLocal;
                            $costUnitRptDisposed += (($val->DIPOSED == -1) ? $val->costUnitRpt : 0);
                            $costUnitDisposed += (($val->DIPOSED == -1) ? $val->COSTUNIT : 0);
                            $profitDisposalRpt += (($val->DIPOSED == -1 && $request->typeID == 1 && $val->disposalType == 6) ? ($val->sellingPriceRpt - ($val->costUnitRpt - $val->acDepAmountRpt)) : 0);
                            $profitDisposalLocal += (($val->DIPOSED == -1 && $request->typeID == 1 && $val->disposalType == 6) ? ($val->sellingPriceLocal - ($val->COSTUNIT - $val->adDepAmountLocal)) : 0);
                            $outputArr[$val->financeCatDescription][] = $val;
                        }
                    }

                    $companyData = \Helper::companyCurrency($request->companySystemID);
    
                    $sort = 'asc';

                    return \DataTables::of($output)
                                    ->addIndexColumn()
                                    ->with('localnbv', $localnbv)
                                    ->with('rptnbv', $rptnbv)
                                    ->with('localcurrency', $companyData->localcurrency)
                                    ->with('reportingcurrency', $companyData->reportingcurrency)
                                    ->with('COSTUNIT', $COSTUNIT)
                                    ->with('costUnitRpt', $costUnitRpt)
                                    ->with('depAmountLocal', $depAmountLocal)
                                    ->with('depAmountRpt', $depAmountRpt)
                                    ->with('costUnitRptDisposed', $costUnitRptDisposed)
                                    ->with('costUnitDisposed', $costUnitDisposed)
                                    ->with('profitDisposalRpt', $profitDisposalRpt)
                                    ->with('profitDisposalLocal', $profitDisposalLocal)
                                    ->with('acDepAmountRpt', $acDepAmountRpt)
                                    ->with('adDepAmountLocal', $adDepAmountLocal)
                                    ->addIndexColumn()
                                    ->make(true);
                }

                if ($request->reportTypeID == 'ARS') { // Asset Register Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $financePeriod = CompanyFinancePeriod::find($request->financePeriod);
                    $financeYear = CompanyFinanceYear::find($request->financeYear);
                    $beginingFinancialYear = Carbon::parse($financeYear->bigginingDate)->format('d-M-Y');
                    $output = $this->getAssetRegisterSummaryQRY($request);
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

                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                    return array('reportData' => $output, 'companyCurrency' => $companyCurrency, 'currencyID' => $request->currencyID, 'date' => Carbon::parse($financePeriod->dateTo)->format('Y-m-d'), 'companyName' => $companyCurrency->CompanyName);

                }

                if ($request->reportTypeID == 'ARD2') { // Asset Register Detail 2
                    //ini_set('memory_limit', '4096M');
                    //return phpinfo();
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('year', 'fromMonth', 'toMonth', 'currencyID', 'typeID'));
                    $output = $this->getAssetRegisterDetail2($request);
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    $fromDate = Carbon::parse($request->year . '-' . $request->fromMonth)->startOfMonth()->format('Y-m-d');
                    $toDate = Carbon::parse($request->year . '-' . $request->toMonth)->endOfMonth()->format('Y-m-d');

                    $totalClosingDep = 0;
                    $NBVTotal = 0;
                    $chargeDuringYear = 0;
                    $totalChargeOnDisposal = 0;
                    $decimalPlaces = ($request->currencyID === 2) ? $companyCurrency->localcurrency->DecimalPlaces : $companyCurrency->reportingcurrency->DecimalPlaces;
                    
                    foreach ($output['data'] as $row) {
                        $sumCharge = 0;
                        foreach ($output['period'] as $periodValue) {
                            $sumCharge += $row->$periodValue ?? 0;  
                        }
                    
                        
                        if ($row->disposedDep == 0) {
                            $closingDepValue = $row->openingDep + $sumCharge - $row->disposedDep;
                            $chargeOnDisposal = $row->disposedDep;
                        } else {
                            $closingDepValue = $row->openingDep - $row->disposedDep;
                            $chargeOnDisposal = $row->disposedDep + $sumCharge;
                        }

                        $nbvValue = $row->costClosing - $closingDepValue;
                    
                        $totalClosingDep += round($closingDepValue, $decimalPlaces);  
                        $NBVTotal += round($nbvValue, $decimalPlaces);               
                        $chargeDuringYear += round($sumCharge, $decimalPlaces);
                        $totalChargeOnDisposal += round($chargeOnDisposal, $decimalPlaces);
                    }

                    return array('reportData' => $output['data'], 'companyCurrency' => $companyCurrency, 'currencyID' => $request->currencyID, 'fromDate' => $fromDate, 'toDate' => $toDate, 'period' => $output['period'], 
                    'totalChargeOnDisposal' => round($totalChargeOnDisposal, $decimalPlaces),
                    'totalClosingDep' => round($totalClosingDep, $decimalPlaces),
                    'NBVTotal' => round($NBVTotal, $decimalPlaces),
                    'chargeDuringYear' => round($chargeDuringYear, $decimalPlaces));
                }

                if($request->reportTypeID == 'ARGD'){ // Asset Register Group Detail

                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));

                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                    $output = $this->getAssetRegisterDetail($request);

                    return $this->getAssetRegisterGroupedDetailFinalArray($output, $companyCurrency);

                }

                break;
            case 'AMAA': //Asset Additions
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                $output = $this->getAssetAdditionsQRY($request);

                $outputArr = array();
                $assetCostLocal = collect($output)->pluck('AssetCostLocal')->toArray();
                $assetCostLocal = array_sum($assetCostLocal);

                $assetCostRpt = collect($output)->pluck('AssetCostRpt')->toArray();
                $assetCostRpt = array_sum($assetCostRpt);

                foreach($output as $key => $val) {
                    $supplierInvoiceBSI = [];
                    $grvMaster = GRVMaster::where('grvPrimaryCode',$val->GRVCODE)->first();
                    if($grvMaster){
                        $supplierInvoice = BookInvSuppDet::where('grvAutoID',$grvMaster->grvAutoID)->get();
                        if($supplierInvoice){
                            foreach($supplierInvoice as $supInvoice){
                                $suppINVMaster = BookInvSuppMaster::where('bookingSuppMasInvAutoID',$supInvoice['bookingSuppMasInvAutoID'])->first();
                                $supplierInvoiceBSI[] = $suppINVMaster->bookingInvCode;
                            }
                        }
                    }
                    $concatenatedValues = implode(', ', $supplierInvoiceBSI);
                    $output[$key]->supplierInvoiceBSI = $concatenatedValues;
                }

                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][$val->companyID][] = $val;
                    }
                }

                $companyCurrency = \Helper::groupCompaniesCurrency($request->companySystemID);

                return array('reportData' => $outputArr, 'assetCostLocal' => $assetCostLocal, 'assetCostRpt' => $assetCostRpt, 'companyCurrency' => $companyCurrency);

                break;
            case 'AMAD': //Asset Disposal
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getAssetDisposal($request);

                $outputArr = array();
                if ($output) {
                    foreach ($output as $val) {
                        $outputArr[$val->CompanyName][] = $val;
                    }
                }


                $companyID = "";
                $checkIsGroup = Company::find($request->companySystemID);
                if ($checkIsGroup->isGroup) {
                    $companyID = \Helper::getGroupCompany($request->companySystemID);
                } else {
                    $companyID = [$request->companySystemID];
                }

                $companyCurrency = Company::with(['localcurrency', 'reportingcurrency'])
                    ->whereIN('companySystemID', $companyID)
                    ->get();

              
                $outputArr2 = [];
                if ($companyCurrency) {
                    foreach ($companyCurrency as $val) {
                        $outputArr2[$val->CompanyName] = $val;
                    }
                }

                $currency = \Helper::companyCurrency($request->companySystemID);
                $companyCurrency = $outputArr2;
           
                $total = array();
                $total['AssetCostLocal'] = array_sum(collect($output)->pluck('AssetCostLocal')->toArray());
                $total['AssetCostRPT'] = array_sum(collect($output)->pluck('AssetCostRPT')->toArray());
                $total['AccumulatedDepreciationLocal'] = array_sum(collect($output)->pluck('AccumulatedDepreciationLocal')->toArray());
                $total['AccumulatedDepreciationRPT'] = array_sum(collect($output)->pluck('AccumulatedDepreciationRPT')->toArray());
                $total['NetBookVALUELocal'] = array_sum(collect($output)->pluck('NetBookVALUELocal')->toArray());
                $total['NetBookVALUERPT'] = array_sum(collect($output)->pluck('NetBookVALUERPT')->toArray());
                $total['SellingPriceLocal'] = array_sum(collect($output)->pluck('SellingPriceLocal')->toArray());
                $total['SellingPriceRpt'] = array_sum(collect($output)->pluck('SellingPriceRpt')->toArray());
                $total['ProfitLocal'] = array_sum(collect($output)->pluck('ProfitLocal')->toArray());
                $total['ProfitRpt'] = array_sum(collect($output)->pluck('ProfitRpt')->toArray());
                return array('reportData' => $outputArr,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $currency->localcurrency->DecimalPlaces,
                    'decimalPlaceRpt' => $currency->reportingcurrency->DecimalPlaces,
                    'currencyLocal' => $currency->localcurrency->CurrencyCode,
                    'currencyRpt' => $currency->reportingcurrency->CurrencyCode,
                    'companyCurrency' => $companyCurrency
                );

                break;
            case 'AMADR': //Asset Depreciation Register
                $reportTypeID = $request->reportTypeID;
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationRegisterMonthlyQRY($request);

                    $grandTotalArr = array();
                    if ($output['month']) {
                        foreach ($output['month'] as $val) {
                            $total = collect($output['data'])->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output['data'], 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $output['month']);
                } else if ($reportTypeID == 'ADDM') { //Asset Depreciation Detail Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    if ($arrayMonth) {
                        foreach ($arrayMonth as $val) {
                            $total = collect($output)->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);

                } else if ($reportTypeID == 'ADDS') { //Asset Depreciation Detail Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationDetailSummaryQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    $prevYearDepAmount = collect($output)->pluck('prevYearDepAmount')->toArray();
                    $grandTotalArr['prevYearDepAmount'] = array_sum($prevYearDepAmount);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
                } else if ($reportTypeID == 'ADCS') { //Asset Depreciation Category Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    $prevYearDepAmount = collect($output)->pluck('prevYearDepAmount')->toArray();
                    $grandTotalArr['prevYearDepAmount'] = array_sum($prevYearDepAmount);

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
                } else if ($reportTypeID == 'ADCSM') { //Asset Depreciation Category Summary Monthly
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                    $checkIsGroup = Company::find($request->companySystemID);
                    $output = $this->assetDepreciationCategorySummaryMonthlyQRY($request);

                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    $grandTotalArr = array();
                    $currentMonthDepreciation = collect($output)->pluck('currentMonthDepreciation')->toArray();
                    $grandTotalArr['currentMonthDepreciation'] = array_sum($currentMonthDepreciation);

                    $cost = collect($output)->pluck('cost')->toArray();
                    $grandTotalArr['cost'] = array_sum($cost);

                    $accumulatedDepreciation = collect($output)->pluck('accumulatedDepreciation')->toArray();
                    $grandTotalArr['accumulatedDepreciation'] = array_sum($accumulatedDepreciation);

                    $netBookValue = collect($output)->pluck('netBookValue')->toArray();
                    $grandTotalArr['netBookValue'] = array_sum($netBookValue);

                    $currentYearDepAmount = collect($output)->pluck('currentYearDepAmount')->toArray();
                    $grandTotalArr['currentYearDepAmount'] = array_sum($currentYearDepAmount);

                    if ($arrayMonth) {
                        foreach ($arrayMonth as $val) {
                            $total = collect($output)->pluck($val)->toArray();
                            $grandTotalArr[$val] = array_sum($total);
                        }
                    }

                    $decimalPlaces = 2;
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($companyCurrency) {
                        if ($request->currencyID == 2) {
                            $decimalPlaces = $companyCurrency->localcurrency->DecimalPlaces;
                        } else if ($request->currencyID == 3) {
                            $decimalPlaces = $companyCurrency->reportingcurrency->DecimalPlaces;
                        }
                    }

                    return array('reportData' => $output, 'companyName' => $checkIsGroup->CompanyName, 'grandTotal' => $grandTotalArr, 'currencyDecimalPlace' => $decimalPlaces, 'month' => $arrayMonth);
                }
                break;
            case 'AMACWIP': //Asset CWIP
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                $decimalPlaces = 2;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                $output = $this->getAssetCWIPQRY($request);
                return array('reportData' => $output, 'companyName' => $companyCurrency->CompanyName, 'companyCurrency' => $companyCurrency, 'currencyID' => $request->currencyID);
                break;
            case 'AEA': //Asset expenses
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $output = $this->getAssetExpenseQRY($request);

                $outputArr = array();
                $grandTotalLocal = 0;
                $grandTotalRpt = 0;
                if ($output) {
                    foreach ($output as $val) {
                        if (isset($request->groupByAsset) && $request->groupByAsset) {
                            $outputArr[$val->assetID][] = $val;
                        } else {
                            $outputArr[$val->chartOfAccountSystemID][] = $val;
                        }
                        $grandTotalLocal += $val->amountLocal;
                        $grandTotalRpt += $val->amountRpt;
                    }
                }

                $companyCurrency = Company::with(['localcurrency', 'reportingcurrency'])->find($request->companySystemID);

                return array('reportData' => $outputArr,'companyCurrency' => $companyCurrency, 'grandTotalLocal' => $grandTotalLocal, 'grandTotalRpt' => $grandTotalRpt);

                break;

            case 'ATR': //Asset tracking report
                $output = $this->getAssetTrackingQRY($request);

                return \DataTables::of($output)
                    ->make(true);

                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    /*export report to csv according to each report id*/
    public function exportReport(Request $request, AssetManagementService $assetManagementService, ExportVatDetailReportService $service)
    {
        $reportID = $request->reportID;
        $type = $request->type;
        switch ($reportID) {
            case 'AMAR': //Asset Register
                if ($request->reportTypeID == 'ARD') { // Asset Register Detail
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                    $output = $this->getAssetRegisterDetail($request);
                    $data = $assetManagementService->generateDataToExport($request,$output);
                    $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                    $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                    $excelColumnFormat = [
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1

                    ];
                    $title = trans('custom.asset_register_detail_report');
                    $fileName = 'asset_register_detail';
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


                    if(!$exportToExcel['success'])
                        return $this->sendError(trans('custom.unable_to_export_excel'));

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                   
                    
                }

                if ($request->reportTypeID == 'ARD3') { // Asset Register Detail 3
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID', 'currencyID'));
                    $output = $this->getAssetRegisterDetail3($request);
                    $outputArr = [];
                    $x = 2;
                    $totAcq = 0;
                    $totDepPed = 0;
                    $totDisVal = 0;
                    $totNBV = 0;
                    $totDisPro = 0;
                    $data = [];

                    $companyData = \Helper::companyCurrency($request->companySystemID);

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
                            $data[$x][trans('custom.acquisition_date')] = ($value->dateAQ) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Helper::dateFormat($value->dateAQ)) : null;
                            $data[$x][trans('custom.supplier_name')] = $value->supplierName;
                            $data[$x][trans('custom.acquisition_cost')." (".$currencyCode.")"] = CurrencyService::convertNumberFormatToNumber(round(($request->currencyID == 3) ? $value->costUnitRpt : $value->COSTUNIT, $decimalPlaces));
                            $data[$x][trans('custom.place_in_service_date')] = ($value->dateDEP) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Helper::dateFormat($value->dateDEP)) : null;
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


                            if($value->DIPOSED == -1){
                                $totDisVal += $request->currencyID == 3 ? $value->costUnitRpt : $value->COSTUNIT;
                            }
                            if($value->DIPOSED == -1 && $request->typeID == 1 && $value->disposalType == 6){
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
                        $data[$x][9] = CurrencyService::convertNumberFormatToNumber(round($totAcq, $decimalPlaces));;
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

                    $companyCode = isset($companyData->CompanyID)?$companyData->CompanyID:'common';
                    $excelColumnFormat = [
                        'H' =>  \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'K' =>  \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];

                    $detail_array = array(
                        'company_code'=>$companyCode,
                        'excelFormat' => $excelColumnFormat
                    );

                    $fileName = 'asset_register_detail_3';
                    $path = 'asset_register/report/excel/';
                    $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                    if($basePath == '')
                    {
                         return $this->sendError(trans('custom.unable_to_export_excel'));
                    }
                    else
                    {
                         return $this->sendResponse($basePath, trans('custom.success_export'));
                    }
                }

                if ($request->reportTypeID == 'ARD2') { // Asset Register Detail 2
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('year', 'fromMonth', 'toMonth', 'currencyID', 'typeID', 'excelType'));


                    $output = $this->getAssetRegisterDetail2($request);
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($request->currencyID == 2) {
                        $currencyDecimalPlace = $companyCurrency->localcurrency->DecimalPlaces;
                        $currencyCode = $companyCurrency->localcurrency->CurrencyCode;
                    } else {
                        $currencyDecimalPlace = $companyCurrency->reportingcurrency->DecimalPlaces;
                        $currencyCode = $companyCurrency->reportingcurrency->CurrencyCode;
                    }

                    $dataArray = array();

                    $year = $request->year;
                    $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                    $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';

                    if(empty($dataArray)) {
                        $assetRegisterDetail2Header = new AssetRegisterDetail2();
                        $headers = collect($assetRegisterDetail2Header->getHeader())->toArray();

                        foreach ($headers as &$header) {
                            if (in_array($header, [
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
                                trans('custom.dec')
                            ])) {
                                $header .= '-' . substr($year, -2);
                            }
                        }
                        array_push($dataArray,$headers);
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

                            if ($val->disposedDep == 0) {
                                $financialData->setChargeOnDisposal(round($val->disposedDep, $currencyDecimalPlace));
                            } elseif ($val->disposedDep != 0) {
                                $financialData->setChargeOnDisposal(round($val->disposedDep + $sumPeriod, $currencyDecimalPlace));
                            }

                            if ($val->disposedDep == 0) {
                                $financialData->setClosingDep(round($val->openingDep + $sumPeriod - $val->disposedDep, $currencyDecimalPlace));
                            } elseif ($val->disposedDep != 0) {
                                $financialData->setClosingDep(round($val->openingDep - $val->disposedDep, $currencyDecimalPlace));
                            }

                            if ($val->disposedDep == 0) {
                                $financialData->setNbv(round($val->costClosing - ($val->openingDep + $sumPeriod - $val->disposedDep), $currencyDecimalPlace));
                            } elseif ($val->disposedDep != 0) {
                                $financialData->setNbv(round($val->costClosing - ($val->openingDep - $val->disposedDep), $currencyDecimalPlace));
                            }

                            for ($i = 0; $i < 12; $i++) {
                                $propertyName = $output['period'][$i];
                                $name = explode('-',$propertyName);
                                $methodName = 'set' . ucfirst(strtolower($name[0]));

                                if (method_exists($financialData, $methodName)) {
                                    $financialData->$methodName(round($val->{$propertyName}, $currencyDecimalPlace));
                                }
                            }

                            array_push($dataArray,collect($financialData)->toArray());

                        }
                    }
                    $excelColumnFormat = [
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];
                    $title = trans('custom.asset_register_detail2_report');
                    $fileName = 'asset_register_detail_2';
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
                        ->setData($dataArray)
                        ->setDateType(2)
                        ->setDetails()
                        ->generateExcel();

                    if(!$exportToExcel['success'])
                        return $this->sendError(trans('custom.unable_to_export_excel'));

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                }

                if ($request->reportTypeID == 'ARS') { // Asset Register Summary
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID', 'excelType'));
                    $financePeriod = CompanyFinancePeriod::find($request->financePeriod);
                    $financeYear = CompanyFinanceYear::find($request->financeYear);
                    $beginingFinancialYear = Carbon::parse($financeYear->bigginingDate)->format('d-M-Y');
                    $output = $this->getAssetRegisterSummaryQRY($request);
                    $assetCategory = $request->assetCategory;
                    $costTotal = [];
                    $depTotal = [];
                    $nbv = [];
                    $nbvEnd = [];
                    $depQry = collect($output['depQry']);
                    $costQry = collect($output['costQry']);

                    $filteredCostQry = $costQry->firstWhere('description', $beginingFinancialYear);
                    $filteredDepQry = $depQry->firstWhere('description', $beginingFinancialYear);
                    // Use the same data processing as generateReport function
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

                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    if ($request->currencyID == 2) {
                        $currencyDecimalPlace = $companyCurrency->localcurrency->DecimalPlaces;
                        $currencyCode = $companyCurrency->localcurrency->CurrencyCode;
                    } else {
                        $currencyDecimalPlace = $companyCurrency->reportingcurrency->DecimalPlaces;
                        $currencyCode = $companyCurrency->reportingcurrency->CurrencyCode;
                    }

                    // Generate data for Excel export
                    $data = array();
                    
                    // Add header row with proper column headers
                    $headerRow = array();
                    $headerRow[trans('custom.description')] = trans('custom.description');
                    if (count($assetCategory) > 0) {
                        foreach ($assetCategory as $val2) {
                            $headerRow[$val2['financeCatDescription']] = $val2['financeCatDescription'];
                        }
                    }
                    $headerRow[trans('custom.total')] = trans('custom.total');
                    $data[] = $headerRow;
                    
                    // Add Cost section
                    $costSectionRow = array_fill_keys(array_keys($headerRow), '');
                    $costSectionRow[trans('custom.description')] = 'Cost(' . $currencyCode . ')';
                    $data[] = $costSectionRow;
                    
                    foreach ($output['costQry'] as $val) {
                        $row = array();
                        $row[trans('custom.description')] = $val['description'];
                        if (count($assetCategory) > 0) {
                            foreach ($assetCategory as $val2) {
                                $row[$val2['financeCatDescription']] = CurrencyService::convertNumberFormatToNumber(round($val[$val2['financeCatDescription']], $currencyDecimalPlace));
                            }
                        }
                        $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round($val['total'], $currencyDecimalPlace));
                        $data[] = $row;
                    }
                    
                    // Add Depreciation section
                    $depSectionRow = array_fill_keys(array_keys($headerRow), '');
                    $depSectionRow[trans('custom.description')] = 'Depreciation(' . $currencyCode . ')';
                    $data[] = $depSectionRow;
                    
                    foreach ($output['depQry'] as $val) {
                        $row = array();
                        $row[trans('custom.description')] = $val['description'];
                        if (count($assetCategory) > 0) {
                            foreach ($assetCategory as $val2) {
                                $row[$val2['financeCatDescription']] = CurrencyService::convertNumberFormatToNumber(round($val[$val2['financeCatDescription']], $currencyDecimalPlace));
                            }
                        }
                        $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round($val['total'], $currencyDecimalPlace));
                        $data[] = $row;
                    }
                    
                    // Add Net Book Value section
                    $nbvSectionRow = array_fill_keys(array_keys($headerRow), '');
                    $nbvSectionRow[trans('custom.description')] = 'Net Book Value(' . $currencyCode . ')';
                    $data[] = $nbvSectionRow;
                    
                    foreach ($output['nbvQry'] as $val) {
                        $row = array();
                        $row[trans('custom.description')] = $val['description'];
                        if (count($assetCategory) > 0) {
                            foreach ($assetCategory as $val2) {
                                $row[$val2['financeCatDescription']] = CurrencyService::convertNumberFormatToNumber(round($val[$val2['financeCatDescription']], $currencyDecimalPlace));
                            }
                        }
                        $row[trans('custom.total')] = CurrencyService::convertNumberFormatToNumber(round($val['total'], $currencyDecimalPlace));
                        $data[] = $row;
                    }


                    $excelColumnFormat = [
                        'B' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'C' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Y' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Z' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];

                    $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                    $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                    
                    $title = trans('custom.asset_register_summary_report');
                    $fileName = 'asset_register_summary';
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

                    if(!$exportToExcel['success'])
                        return $this->sendError(trans('custom.unable_to_export_excel'));

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
                }

                if ($request->reportTypeID == 'ARGD') { // Asset Register Detail
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                    $output = $this->getAssetRegisterDetail($request);
                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                    $final = $this->getAssetRegisterGroupedDetailFinalArray($output, $companyCurrency);
                    $outputArr = $final['reportData'];

                    $x = 0;
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

                            $localDecimalPlace = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces: 3;
                            $rptDecimalPlace = isset($companyCurrency->reportingcurrency->DecimalPlaces) ? $companyCurrency->reportingcurrency->DecimalPlaces: 2;

                            foreach ($masterVal as $mainAsset => $assetArray) {

                                foreach ($assetArray as $value){

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
                                    $data[$x][trans('custom.date_acquired')] = ($value->postedDate) ?  $datetime->toDateString() : null;
                                    $data[$x][trans('custom.dep_start_date')] = ($value->dateDEP) ?  $datetime2->toDateString() : null;
                                    $data[$x][trans('custom.local_amount_unitcost')] = CurrencyService::convertNumberFormatToNumber(round($value->COSTUNIT, $localDecimalPlace));
                                    $data[$x][trans('custom.local_amount_accdep')] = CurrencyService::convertNumberFormatToNumber(round($value->depAmountLocal, $localDecimalPlace));
                                    $data[$x][trans('custom.local_amount_net_value')] = CurrencyService::convertNumberFormatToNumber(round($value->localnbv, $localDecimalPlace));
                                    $data[$x][trans('custom.rpt_amount_unit_cost')] = CurrencyService::convertNumberFormatToNumber(round($value->costUnitRpt, $rptDecimalPlace));
                                    $data[$x][trans('custom.rpt_amount_acc_dep')] = CurrencyService::convertNumberFormatToNumber(round($value->depAmountRpt, $rptDecimalPlace));
                                    $data[$x][trans('custom.rpt_amount_acc_net_value')] = CurrencyService::convertNumberFormatToNumber(round($value->rptnbv, $rptDecimalPlace));

                                    if(!$value->isHeader ){
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
                        $data[$x][trans('custom.local_amount_unitcost')] =  CurrencyService::convertNumberFormatToNumber($final['COSTUNIT']);
                        $data[$x][trans('custom.local_amount_accdep')] =  CurrencyService::convertNumberFormatToNumber($final['depAmountLocal']);
                        $data[$x][trans('custom.local_amount_net_value')] =  CurrencyService::convertNumberFormatToNumber($final['localnbv']);
                        $data[$x][trans('custom.rpt_amount_unit_cost')] =  CurrencyService::convertNumberFormatToNumber($final['costUnitRpt']);
                        $data[$x][trans('custom.rpt_amount_acc_dep')] =  CurrencyService::convertNumberFormatToNumber($final['depAmountRpt']);
                        $data[$x][trans('custom.rpt_amount_acc_net_value')] =  CurrencyService::convertNumberFormatToNumber($final['rptnbv']);
                    }

                    $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                    $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';

                    $excelColumnFormat = [
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                    $title = trans('custom.asset_register_grouped_detail_report');
                    $fileName = 'asset_register_group';
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

                    if(!$exportToExcel['success'])
                        return $this->sendError(trans('custom.unable_to_export_excel'));

                    return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));
                }

                return $this->sendResponse("", trans('custom.success_export'));
                break;
            case 'AMAA': //Asset Additions
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('typeID'));
                $type = $request->type;
                $output = $this->getAssetAdditionsQRY($request);

                foreach($output as $key => $val) {
                    $supplierInvoiceBSI = [];
                    $grvMaster = GRVMaster::where('grvPrimaryCode',$val->GRVCODE)->first();
                    if($grvMaster){
                        $supplierInvoice = BookInvSuppDet::where('grvAutoID',$grvMaster->grvAutoID)->get();
                        if($supplierInvoice){
                            foreach($supplierInvoice as $supInvoice){
                                $suppINVMaster = BookInvSuppMaster::where('bookingSuppMasInvAutoID',$supInvoice['bookingSuppMasInvAutoID'])->first();
                                $supplierInvoiceBSI[] = $suppINVMaster->bookingInvCode;
                            }
                        }
                    }
                    $concatenatedValues = implode(', ', $supplierInvoiceBSI);
                    $output[$key]->supplierInvoiceBSI = $concatenatedValues;
                }

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x][trans('custom.company_id')] = $val->companyID;
                        $data[$x][trans('custom.company_name')] = $val->CompanyName;
                        $data[$x][trans('custom.asset_category')] = $val->AssetCategory;
                        $data[$x][trans('custom.asset_type')] = $val->AssetType;
                        $data[$x][trans('custom.asset_code')] = $val->AssetCODE;
                        $data[$x][trans('custom.serial_number')] = $val->SerialNumber;
                        $data[$x][trans('custom.asset_description')] = $val->AssetDescription;
                        $data[$x][trans('custom.dep_percentage')] = $val->DEPpercentage;
                        $data[$x][trans('custom.posted_date')] = ($val->postedDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->postedDate)) : null;
                        $data[$x][trans('custom.grv_code')] = $val->GRVCODE;
                        $data[$x][trans('custom.supplier_invoice')] = $val->supplierInvoiceBSI;
                        $data[$x][trans('custom.po_code')] = $val->POCODE;
                        $data[$x][trans('custom.po_amount_rpt')] = $val->poTotalComRptCurrency;
                        $data[$x][trans('custom.payment_date')] = $val->paymentDate;
                        $data[$x][trans('custom.service_line')] = $val->ServiceLineDes;
                        $data[$x][trans('custom.supplier')] = $val->Supplier;
                        $data[$x][trans('custom.cost_gl')] = $val->COSTGLCODE;
                        $data[$x][trans('custom.cost_gl_desc')] = $val->COSTGLCODEdes;
                        $data[$x][trans('custom.asset_cost_local_curr')] = $val->localCurrency;
                        $data[$x][trans('custom.asset_cost_local')] = CurrencyService::convertNumberFormatToNumber($val->AssetCostLocal);
                        $data[$x][trans('custom.asset_cost_rpt_curr')] = $val->reportCurrency;
                        $data[$x][trans('custom.asset_cost_rpt')] = CurrencyService::convertNumberFormatToNumber($val->AssetCostRpt);
                        $x++;
                    }
                } else {
                    $data = array();
                }

                $excelColumnFormat = [
                    'I' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                    'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ];

                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                    'excelFormat' => $excelColumnFormat
                );
                $fileName = 'asset-addition';
                $path = 'asset/report/asset-addition/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }
                break;
            case 'AMAD': //Asset Disposal

                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                $output = $this->getAssetDisposal($request);

                $data = array();
                $x = 0;
                foreach ($output as $val) {
                    $data[$x][trans('custom.company_id')] = $val->companyID;
                    $data[$x][trans('custom.company_name')] = $val->CompanyName;

                    $data[$x][trans('custom.disposal_date')] = ($val->disposalDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(\Helper::dateFormat($val->disposalDate)): null;
                    $data[$x][trans('custom.doc_code')] = $val->disposalDocumentCode;
                    $data[$x][trans('custom.narration')] = $val->narration;
                    $data[$x][trans('custom.category')] = $val->AssetCategory;
                    $data[$x][trans('custom.asset_code')] = $val->AssetCODE;
                    $data[$x][trans('custom.serial_number')] = $val->AssetSerialNumber;
                    $data[$x][trans('custom.asset_description')] = $val->AssetDescription;

                    $data[$x][trans('custom.currency_local')] = $val->localCurrency;
                    $data[$x][trans('custom.currency_reporting')] = $val->reportCurrency;

                    $data[$x][trans('custom.asset_cost_local')] = CurrencyService::convertNumberFormatToNumber(round($val->AssetCostLocal, $val->localCurrencyDeci));
                    $data[$x][trans('custom.asset_cost_reporting')] = CurrencyService::convertNumberFormatToNumber(round($val->AssetCostRPT, $val->reportCurrencyDeci));

                    $data[$x][trans('custom.accumulated_depreciation_local')] = CurrencyService::convertNumberFormatToNumber(round($val->AccumulatedDepreciationLocal, $val->localCurrencyDeci));
                    $data[$x][trans('custom.accumulated_depreciation_reporting')] = CurrencyService::convertNumberFormatToNumber(round($val->AccumulatedDepreciationRPT, $val->reportCurrencyDeci));

                    $data[$x][trans('custom.net_book_value_local')] = CurrencyService::convertNumberFormatToNumber(round($val->NetBookVALUELocal, $val->localCurrencyDeci));
                    $data[$x][trans('custom.net_book_value_reporting')] = CurrencyService::convertNumberFormatToNumber(round($val->NetBookVALUERPT, $val->reportCurrencyDeci));


                    $data[$x][trans('custom.selling_price_local')] = CurrencyService::convertNumberFormatToNumber(round($val->SellingPriceLocal, $val->localCurrencyDeci));
                    $data[$x][trans('custom.selling_price_reporting')] = CurrencyService::convertNumberFormatToNumber(round($val->SellingPriceRpt, $val->reportCurrencyDeci));

                    $data[$x][trans('custom.profit_loss_local')] = CurrencyService::convertNumberFormatToNumber(round($val->ProfitLocal, $val->localCurrencyDeci));
                    $data[$x][trans('custom.profit_loss_reporting')] = CurrencyService::convertNumberFormatToNumber(round($val->ProfitRpt, $val->reportCurrencyDeci));


                    $x++;
                }

                $excelColumnFormat = [
                    'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                    'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ];

                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                    'excelFormat' => $excelColumnFormat
                );
                $fileName = 'asset_disposal';
                $path = 'asset/report/asset_disposal/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path, $detail_array);

                if($basePath == '')
                {
                     return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'AMADR': //Asset Depreciation Register
                $data = [];
                $reportTypeID = $request->reportTypeID;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                if ($reportTypeID == 'ADRM') { //Asset Depreciation Register Monthly
                    $output = $this->assetDepreciationRegisterMonthlyQRY($request);
                    if ($output['data']) {
                        $x = 0;
                        foreach ($output['data'] as $val) {
                            $data[$x][trans('custom.asset_code')] = $val->faCode;
                            $data[$x][trans('custom.asset_description')] = $val->assetDescription;
                            $data[$x][trans('custom.category')] = $val->AuditCategory;
                            foreach ($output['month'] as $val2) {
                                $data[$x][$val2] = CurrencyService::convertNumberFormatToNumber($val->$val2);
                            }
                            $x++;
                        }
                    }
                    $excelColumnFormat = [
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    ];
                }
                else if ($reportTypeID == 'ADDM') { //Asset Depreciation Detail Monthly
                    $output = $this->assetDepreciationDetailMonthlyQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x][trans('custom.asset_code')] = $val->faCode;
                            $data[$x][trans('custom.asset_description')] = $val->assetDescription;
                            $data[$x][trans('custom.category')] = $val->AuditCategory;
                            $data[$x][trans('custom.cost_amount')] = CurrencyService::convertNumberFormatToNumber($val->cost);
                            $data[$x][trans('custom.dep_percentage')] = $val->DEPpercentage;
                            $data[$x][trans('custom.dep_amount') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->currentMonthDepreciation);
                            $data[$x][trans('custom.opening_dep')] = 0;
                            $data[$x][trans('custom.current_year_dep')] = CurrencyService::convertNumberFormatToNumber($val->currentYearDepAmount);
                            $data[$x][trans('custom.accumulated_dep') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->accumulatedDepreciation);
                            $data[$x][trans('custom.net_book_value') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->netBookValue);
                            foreach ($arrayMonth as $val2) {
                                $data[$x][$val2] = CurrencyService::convertNumberFormatToNumber($val->$val2);
                            }
                            $x++;
                        }
                    }

                    $excelColumnFormat = [
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'T' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                }
                else if ($reportTypeID == 'ADDS') { //Depreciation Detail Summary
                    $output = $this->assetDepreciationDetailSummaryQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x][trans('custom.asset_code')] = $val->faCode;
                            $data[$x][trans('custom.asset_description')] = $val->assetDescription;
                            $data[$x][trans('custom.category')] = $val->AuditCategory;
                            $data[$x][trans('custom.cost_amount')] = CurrencyService::convertNumberFormatToNumber($val->cost);
                            $data[$x][trans('custom.dep_percentage')] = $val->DEPpercentage;
                            $data[$x][trans('custom.dep_amount') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->currentMonthDepreciation);
                            $data[$x][trans('custom.opening_dep')] = 0;
                            $data[$x][trans('custom.current_year_dep')] = CurrencyService::convertNumberFormatToNumber($val->currentYearDepAmount);
                            $data[$x][trans('custom.accumulated_dep') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->accumulatedDepreciation);
                            $data[$x][trans('custom.net_book_value') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->netBookValue);

                            $x++;
                        }
                    }

                    $excelColumnFormat = [
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                }
                else if ($reportTypeID == 'ADCS') { //Depreciation Category Summary
                    $output = $this->assetDepreciationCategorySummaryQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x][trans('custom.category')] = $val->AuditCategory;
                            $data[$x][trans('custom.cost_amount')] = CurrencyService::convertNumberFormatToNumber($val->cost);
                            $data[$x][trans('custom.dep_percentage')] = $val->DEPpercentage;
                            $data[$x][trans('custom.current_year_dep')] = CurrencyService::convertNumberFormatToNumber($val->currentYearDepAmount);
                            $data[$x][trans('custom.accumulated_dep') . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->accumulatedDepreciation);
                            $data[$x][trans('custom.net_book_value') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->netBookValue);
                            $x++;
                        }
                    }


                    $excelColumnFormat = [
                        'B' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                }
                else if ($reportTypeID == 'ADCSM') { //Depreciation Category Monthly Summary
                    $output = $this->assetDepreciationCategorySummaryMonthlyQRY($request);
                    $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x][trans('custom.category')] = $val->AuditCategory;
                            $data[$x][trans('custom.cost_amount')] = CurrencyService::convertNumberFormatToNumber($val->cost);
                            $data[$x][trans('custom.dep_percentage')] = $val->DEPpercentage;
                            $data[$x][trans('custom.current_year_dep')] = CurrencyService::convertNumberFormatToNumber($val->currentYearDepAmount);
                            $data[$x][trans('custom.accumulated_dep') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->accumulatedDepreciation);
                            $data[$x][trans('custom.net_book_value') . ' ' . $arrayMonth[$request->month - 1]] = CurrencyService::convertNumberFormatToNumber($val->netBookValue);
                            foreach ($arrayMonth as $val2) {
                                $data[$x][$val2] = CurrencyService::convertNumberFormatToNumber($val->$val2);
                            }
                            $x++;
                        }
                    }
                    $excelColumnFormat = [
                        'B' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'J' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'K' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                }

                $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID:null);
                $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                    'excelFormat'=>$excelColumnFormat
                );

                $fileName = 'asset_depreciation_register';
                $path = 'asset/report/asset_depreciation_register/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                    return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                    return $this->sendResponse($basePath, trans('custom.success_export'));
                }


                break;
            case 'AMACWIP': //Asset CWIP
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID', 'year', 'month', 'typeID'));
                $decimalPlaces = 2;
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);

                $output = $this->getAssetCWIPQRY($request);
                if (count($output) > 0) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x][trans('custom.grv_number')] = $val->grvPrimaryCode;
                        $data[$x][trans('custom.grv_posting_date')] = $val->approvedDate;
                        $data[$x][trans('custom.opening')] = $val->opening;
                        $data[$x][trans('custom.addition')] = $val->addition;
                        $data[$x][trans('custom.capitalization')] = $val->capitalization;
                        $data[$x][trans('custom.closing')] = $val->closing;
                        $x++;
                    }
                }
                $companyCode = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID:'common';
                $detail_array = array(
                    'company_code'=>$companyCode,
                );
                $fileName = 'asset_cwip';
                $path = 'asset/report/asset_cwip/excel/';
                $basePath = CreateExcel::process($data,$type,$fileName,$path,$detail_array);

                if($basePath == '')
                {
                     return $this->sendError(trans('custom.unable_to_export_excel'));
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;
            case 'AEA': //Asset Expense
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $decimalPlaces = 2;
                $output = $this->getAssetExpenseQRY($request);
                $fromDate = $request->fromDate;
                $toDate = $request->toDate;
                $data = [];
                $companyCurrency = Company::with(['localcurrency', 'reportingcurrency'])->find($request->companySystemID);
                if (count($output) > 0) {
                    $x = 0;
                    foreach ($output as $val) {
                        $data[$x][trans('custom.account_code')] = isset($val->chart_of_account->AccountCode) ? $val->chart_of_account->AccountCode : "";
                        $data[$x][trans('custom.account_description')] = isset($val->chart_of_account->AccountDescription) ? $val->chart_of_account->AccountDescription : "";
                        $data[$x][trans('custom.asset_code')] = isset($val->asset->faCode) ? $val->asset->faCode : "";
                        $data[$x][trans('custom.asset_description')] = isset($val->asset->assetDescription) ? $val->asset->assetDescription : "";
                        $data[$x][trans('custom.chart_of_account_system_id')] = isset($val->chartOfAccountSystemID) ? $val->chartOfAccountSystemID : 0;
                        $data[$x][trans('custom.asset_id')] = isset($val->assetID) ? $val->assetID : 0;

                        if ($val->documentSystemID == 11) {
                            $data[$x][trans('custom.document_code')] = isset($val->supplier_invoice->bookingInvCode) ? $val->supplier_invoice->bookingInvCode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->supplier_invoice->bookingDate) ? Carbon::parse($val->supplier_invoice->bookingDate)->format('Y-m-d') : "";
                        } else if ($val->documentSystemID == 4){
                            $data[$x][trans('custom.document_code')] = isset($val->payment_voucher->BPVcode) ? $val->payment_voucher->BPVcode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->payment_voucher->BPVdate) ? Carbon::parse($val->payment_voucher->BPVdate)->format('Y-m-d') : "";
                        }
                        else if ($val->documentSystemID == 3){
                            $data[$x][trans('custom.document_code')] = isset($val->grv->grvPrimaryCode) ? $val->grv->grvPrimaryCode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->grv->grvDate) ? Carbon::parse($val->grv->grvDate)->format('Y-m-d') : "";
                        }
                        else if ($val->documentSystemID == 17){
                            $data[$x][trans('custom.document_code')] = isset($val->journal_voucher->JVcode) ? $val->journal_voucher->JVcode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->journal_voucher->JVdate) ? Carbon::parse($val->journal_voucher->JVdate)->format('Y-m-d') : "";
                        }
                        else if ($val->documentSystemID == 161){
                            $data[$x][trans('custom.document_code')] = isset($val->ioue->bookingCode) ? $val->ioue->bookingCode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->ioue->bookingDate) ? Carbon::parse($val->ioue->bookingDate)->format('Y-m-d') : "";
                        }
                        else {
                            $data[$x][trans('custom.document_code')] = isset($val->meterial_issue->itemIssueCode) ? $val->meterial_issue->itemIssueCode : "";
                            $data[$x][trans('custom.document_date')] = isset($val->meterial_issue->master->issueDate) ? Carbon::parse($val->meterial_issue->master->issueDate)->format('Y-m-d') : "";
                        }

                        if ($request->currencyID == 2) {
                            $data[$x][trans('custom.currency')] = $companyCurrency->localcurrency->CurrencyCode;
                            $data[$x][trans('custom.amount')] = round($val->amountLocal, $companyCurrency->localcurrency->DecimalPlaces);
                        } else {
                             $data[$x][trans('custom.currency')] = $companyCurrency->reportingcurrency->CurrencyCode;
                            $data[$x][trans('custom.amount')] = round($val->amountRpt, $companyCurrency->reportingcurrency->DecimalPlaces);
                        }
                        
                        $x++;
                    }
                }
                if(isset($request->groupByAsset)) {
                    if ($request->groupByAsset == false) {
                        $headers = array();
                        foreach ($data as $element) {
                            $headers[$element['AccountCode']][] = $element;
                        }
                        $headers = array_values($headers);

                        usort($headers, function ($a, $b) {return $a[0]['ChartOfAccountSystemID'] > $b[0]['ChartOfAccountSystemID'];});



                        $reportData = array('reportData' => $data, 'headers' => $headers, 'fromDate' => $fromDate, 'toDate' => $toDate, 'currency' => $companyCurrency, 'currencyID' => $request->currencyID);
                        $templateName = "export_report.asset_expenses";

                    }
                    if ($request->groupByAsset == true) {
                        $headers = array();
                        foreach ($data as $element) {
                            $headers[$element['AssetCode']][] = $element;
                        }
                        $headers = array_values($headers);

                        usort($headers, function ($a, $b) {return $a[0]['AssetID'] > $b[0]['AssetID'];});

                        $reportData = array('reportData' => $data, 'headers' => $headers, 'fromDate' => $fromDate, 'toDate' => $toDate, 'currency' => $companyCurrency, 'currencyID' => $request->currencyID);
                        $templateName = "export_report.asset_wise_expenses";

                    }
                }
                else{
                    $headers = array();
                    foreach ($data as $element) {
                        $headers[$element['AccountCode']][] = $element;
                    }
                    $headers = array_values($headers);

                    usort($headers, function ($a, $b) {return $a[0]['ChartOfAccountSystemID'] > $b[0]['ChartOfAccountSystemID'];});

                    $reportData = array('reportData' => $data, 'headers' => $headers, 'fromDate' => $fromDate, 'toDate' => $toDate, 'currency' => $companyCurrency, 'currencyID' => $request->currencyID);
                    $templateName = "export_report.asset_expenses";
                }

                return \Excel::create('finance', function ($excel) use ($reportData, $templateName) {
                    $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                        $sheet->loadView($templateName, $reportData);
                    });
                })->download('xlsx');
                break;
            case 'ATR':
                $output = $this->getAssetTrackingQRY($request);
                $fromDate = $request->fromDate;
                $toDate = $request->toDate;

                if (count($output) > 0) {
                    $reportData = array('reportData' => $output,  'fromDate' => $fromDate, 'toDate' => $toDate);
                    $templateName = "export_report.asset_tracking";

                    return \Excel::create('finance', function ($excel) use ($reportData, $templateName) {
                        $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                            $sheet->loadView($templateName, $reportData);
                        });
                    })->download('xlsx');
                }
                break;
            default:
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.report_id')]));
        }
    }

    // Asset Additions Query
    function getAssetAdditionsQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');
        $typeID = $request->typeID;
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $query = 'SELECT
                    erp_fa_asset_master.companySystemID,
                    erp_fa_asset_master.companyID,
                    companymaster.CompanyName,
                    erp_fa_asset_master.faID,
                    erp_fa_financecategory.financeCatDescription AS AssetCategory,
                    erp_fa_assettype.typeDes AS AssetType,
                    erp_fa_asset_master.faCode AS AssetCODE,
                    erp_fa_asset_master.faUnitSerialNo AS SerialNumber,
                    erp_fa_asset_master.assetDescription AS AssetDescription,
                    ROUND(erp_fa_asset_master.DEPpercentage, 0) AS DEPpercentage,
                    serviceline.ServiceLineDes AS ServiceLineDes,
                    erp_fa_asset_master.dateAQ AS DateAquired,
                    erp_fa_asset_master.postedDate AS postedDate,
                    erp_fa_asset_master.docOrigin AS GRVCODE,
                    erp_purchaseordermaster.purchaseOrderCode AS POCODE,
                    erp_purchaseordermaster.poTotalComRptCurrency AS poTotalComRptCurrency,
                    inv.BPVdate AS paymentDate,
                    erp_fa_asset_master.serviceLineCode AS ServiceLine,
                    erp_fa_asset_master.MANUFACTURE AS Supplier,
                    erp_fa_asset_master.COSTUNIT AS AssetCostLocal,
                    erp_fa_asset_master.costUnitRpt AS AssetCostRpt,
                    locCur.CurrencyCode as localCurrency,
                    locCur.DecimalPlaces as localCurrencyDeci,
                    repCur.CurrencyCode as reportCurrency,
                    repCur.DecimalPlaces as reportCurrencyDeci,
                     erp_fa_asset_master.COSTGLCODE as COSTGLCODE,
                     erp_fa_asset_master.COSTGLCODEdes as COSTGLCODEdes
                FROM
                    erp_fa_asset_master
                LEFT JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
                -- LEFT JOIN erp_fa_assetcost ON erp_fa_asset_master.faID = erp_fa_assetcost.faID
                LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
                LEFT JOIN erp_grvmaster ON erp_fa_asset_master.docOriginSystemCode = erp_grvmaster.grvAutoID
                LEFT JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
                LEFT JOIN (SELECT
	MAX(BPVdate) as BPVdate,
	grvAutoID
FROM
	erp_bookinvsuppdet
	INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppdet.bookingSuppMasInvAutoID = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
	LEFT JOIN ( SELECT erp_paysupplierinvoicedetail.bookingInvSystemCode,erp_paysupplierinvoicedetail.addedDocumentSystemID,erp_paysupplierinvoicemaster.BPVdate FROM erp_paysupplierinvoicedetail INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_paysupplierinvoicedetail.PayMasterAutoId ORDER BY bookingInvSystemCode asc,BPVdate desc) pv ON pv.bookingInvSystemCode = erp_bookinvsuppdet.bookingSuppMasInvAutoID 
	AND pv.addedDocumentSystemID = erp_bookinvsuppmaster.documentSystemID) inv ON inv.grvAutoID = erp_grvmaster.grvAutoID
                LEFT JOIN erp_purchaseordermaster ON erp_grvdetails.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
                LEFT JOIN companymaster ON companymaster.companySystemID = erp_fa_asset_master.companySystemID
                LEFT JOIN serviceline ON erp_fa_asset_master.serviceLineSystemID = serviceline.serviceLineSystemID
                LEFT JOIN currencymaster as locCur ON locCur.currencyID = companymaster.localCurrencyID
                LEFT JOIN currencymaster as repCur ON repCur.currencyID = companymaster.reportingCurrency
                WHERE erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_fa_asset_master.deleted_at IS NULL
                    AND DATE(erp_fa_asset_master.postedDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                AND erp_fa_asset_master.approved = -1 AND  erp_fa_assettype.typeID =  ' . $typeID . '
                GROUP BY
                    erp_fa_asset_master.companySystemID,
                    erp_fa_asset_master.faID ORDER BY erp_fa_asset_master.companyID ASC';
        return \DB::select($query);
    }

    public function getAssetExpenseQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);

        $toDate = new Carbon($request->toDate);

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $assetIds = (isset($request->assets) && count($request->assets) > 0) ? collect($request->assets)->pluck('faID')->toArray() : [];
        $chartOfAccountIds = (isset($request->glAccounts) && count($request->glAccounts) > 0) ? collect($request->glAccounts)->pluck('chartOfAccountSystemID')->toArray() : [];

     

        return $assetAllocations = ExpenseAssetAllocation::whereIn('chartOfAccountSystemID', $chartOfAccountIds)
                                                  ->whereIn('assetID', $assetIds)
                                                  ->with(['asset','chart_of_account', 'supplier_invoice' => function($query) use ($companyID, $fromDate, $toDate) {
                                                        $query->whereIn('companySystemID', $companyID)
                                                              ->whereDate('bookingDate', '>=', $fromDate)
                                                              ->whereDate('bookingDate', '<=', $toDate);
                                                  }, 'payment_voucher'  => function($query) use ($companyID, $fromDate, $toDate) {
                                                        $query->whereIn('companySystemID', $companyID)
                                                              ->whereDate('BPVdate', '>=', $fromDate)
                                                              ->whereDate('BPVdate', '<=', $toDate);
                                                  },'journal_voucher'  => function($query) use ($companyID, $fromDate, $toDate) {
                                                    $query->whereIn('companySystemID', $companyID)
                                                          ->whereDate('JVdate', '>=', $fromDate)
                                                          ->whereDate('JVdate', '<=', $toDate);
                                                  }
                                                  ,'grv'  => function($query) use ($companyID, $fromDate, $toDate) {
                                                    $query->whereIn('companySystemID', $companyID)
                                                          ->whereDate('grvDate', '>=', $fromDate)
                                                          ->whereDate('grvDate', '<=', $toDate);
                                                  },'meterial_issue'  => function($query) use ($companyID, $fromDate, $toDate) {
                                                       $query->with(['master'=>function($query)use($companyID,$fromDate, $toDate){
                                                           $query->whereIn('companySystemID',$companyID)
                                                           ->whereDate('issueDate', '>=', $fromDate)
                                                           ->whereDate('issueDate', '<=', $toDate);
                                                       }]);
                                                  },'ioue'  => function($query) use ($companyID, $fromDate, $toDate) {
                                                    $query->whereIn('companyID', $companyID)
                                                    ->whereDate('bookingDate', '>=', $fromDate)
                                                    ->whereDate('bookingDate', '<=', $toDate);
                                                  }])
                                                  ->where(function($query) use ($companyID, $fromDate, $toDate) {
                                                      $query->where(function($query) use ($companyID, $fromDate, $toDate) {
                                                                $query->whereHas('supplier_invoice', function($query) use ($companyID, $fromDate, $toDate) {
                                                                        $query->whereIn('companySystemID', $companyID)
                                                                              ->whereDate('bookingDate', '>=', $fromDate)
                                                                              ->whereDate('bookingDate', '<=', $toDate);
                                                                    })
                                                                    ->where('documentSystemID', 11)->where('module_id', 1);
                                                          })
                                                         ->orWhere(function($query) use ($companyID, $fromDate, $toDate) {
                                                                $query->whereHas('payment_voucher', function($query) use ($companyID, $fromDate, $toDate) {
                                                                        $query->whereIn('companySystemID', $companyID)
                                                                              ->whereDate('BPVdate', '>=', $fromDate)
                                                                              ->whereDate('BPVdate', '<=', $toDate);
                                                                    })
                                                                    ->where('documentSystemID', 4)->where('module_id', 1);
                                                          })
                                                          ->orWhere(function($query) use ($companyID, $fromDate, $toDate) {
                                                            $query->whereHas('journal_voucher', function($query) use ($companyID, $fromDate, $toDate) {
                                                                    $query->whereIn('companySystemID', $companyID)
                                                                          ->whereDate('JVdate', '>=', $fromDate)
                                                                          ->whereDate('JVdate', '<=', $toDate);
                                                                })
                                                                ->where('documentSystemID',17)->where('module_id', 1);
                                                          })
                                                          ->orWhere(function($query) use ($companyID, $fromDate, $toDate) {
                                                            $query->whereHas('grv', function($query) use ($companyID, $fromDate, $toDate) {
                                                                    $query->whereIn('companySystemID', $companyID)
                                                                          ->whereDate('grvDate', '>=', $fromDate)
                                                                          ->whereDate('grvDate', '<=', $toDate);
                                                                })
                                                                ->where('documentSystemID',3)->where('module_id', 1);
                                                          })
                                                          ->orWhere(function($query) use ($companyID, $fromDate, $toDate) {
                                                            $query->whereHas('meterial_issue', function($query) use ($companyID, $fromDate, $toDate) {
                                                                $query->whereHas('master',function($query)use($companyID,$fromDate, $toDate){
                                                                    $query->whereIn('companySystemID',$companyID)
                                                                            ->whereDate('issueDate', '>=', $fromDate)
                                                                            ->whereDate('issueDate', '<=', $toDate);
                                                                })
                                                                ->where('documentSystemID', 8)->where('module_id', 1);
                                                            });
                                                      })->orWhere(function($query) use ($companyID, $fromDate, $toDate) {
                                                        $query->whereHas('ioue', function($query) use ($companyID, $fromDate, $toDate) {
                                                                $query->whereIn('companyID', $companyID)
                                                                ->whereDate('bookingDate', '>=', $fromDate)
                                                                ->whereDate('bookingDate', '<=', $toDate);
                                                            })
                                                            ->where('documentSystemID',161)->where('module_id', 2);
                                                      });
                                                  })
                                                  ->get();

    }

    public function getAssetTrackingQRY($request)
    {
        $fromDate = new Carbon($request->fromDate);

        $toDate = new Carbon($request->toDate);

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $assetIds = (isset($request->assets) && count($request->assets) > 0) ? collect($request->assets)->pluck('faID')->toArray() : [];

         $assetTransfer = FixedAssetCost::selectRaw(
             'erp_fa_assetcost.assetID as assetCode, 
             erp_fa_assettype.typeDes as assetType,
             erp_fa_asset_master.assetDescription as assetDescription, 
             erp_fa_category.catDescription as category,
             erp_fa_fa_asset_transfer.document_code as documentCode, 
             erp_fa_fa_asset_transfer.document_date as documentDate,
             IFNULL(fromLocation.locationName, "-") as fromName, 
             IFNULL(toLocation.locationName, "-") as toName, 
             IFNULL(location.locationName, "-") as locationName, 
             IFNULL(empRequest.empName, "-") as reqName, 
             IFNULL(depMaster.DepartmentDescription, "-") as depName, 
             IFNULL(transferDepMaster.DepartmentDescription, "-") as transferDepName, 
             IFNULL(fromEmployee.empName, "-") as fromEmpName, 
             IFNULL(toEmployee.empName, "-") as toEmpName, 
             erp_fa_asset_master.faID'
            )->addSelect([
             'erp_fa_fa_asset_transfer.type',
             DB::raw('(CASE 
                WHEN erp_fa_fa_asset_transfer.type = 1 THEN "Request Based - Employee"
                WHEN erp_fa_fa_asset_transfer.type = 2 THEN "Direct to Location"
                WHEN erp_fa_fa_asset_transfer.type = 3 THEN "Direct to Employee"
                WHEN erp_fa_fa_asset_transfer.type = 4 THEN "Request Based - Department"
                ELSE ""
             END) as transferType')
             ])
            ->leftjoin('erp_fa_asset_master', 'erp_fa_asset_master.faID', '=', 'erp_fa_assetcost.faID')
            ->leftjoin('erp_fa_fa_asset_transfer_details', 'erp_fa_fa_asset_transfer_details.fa_master_id', '=', 'erp_fa_assetcost.faID')
            ->leftjoin('erp_fa_fa_asset_transfer', 'erp_fa_fa_asset_transfer.id', '=', 'erp_fa_fa_asset_transfer_details.erp_fa_fa_asset_transfer_id')
            ->leftjoin('erp_fa_assettype', 'erp_fa_assettype.typeID', '=', 'erp_fa_asset_master.assetType')
            ->leftjoin('erp_fa_category', 'erp_fa_category.faCatID', '=', 'erp_fa_asset_master.faCatID')
            ->leftjoin('erp_location as fromLocation', 'fromLocation.locationID', '=', 'erp_fa_fa_asset_transfer_details.from_location_id')
            ->leftjoin('erp_location as toLocation', 'toLocation.locationID', '=', 'erp_fa_fa_asset_transfer_details.to_location_id')
            ->leftjoin('erp_location as location', 'location.locationID', '=', 'erp_fa_fa_asset_transfer.location')
            ->leftjoin('erp_fa_fa_asset_request', 'erp_fa_fa_asset_request.id', '=', 'erp_fa_fa_asset_transfer_details.erp_fa_fa_asset_request_id')
            ->leftjoin('employees as empRequest', 'empRequest.employeeSystemID', '=', 'erp_fa_fa_asset_request.emp_id')
            ->leftjoin('departmentmaster as depMaster', 'depMaster.departmentSystemID', '=', 'erp_fa_asset_master.departmentSystemID')
            ->leftjoin('departmentmaster as transferDepMaster', 'transferDepMaster.departmentSystemID', '=', 'erp_fa_fa_asset_transfer_details.departmentSystemID')
            ->leftjoin('employees as fromEmployee', 'fromEmployee.employeeSystemID', '=', 'erp_fa_fa_asset_transfer_details.from_emp_id')
            ->leftjoin('employees as toEmployee', 'toEmployee.employeeSystemID', '=', 'erp_fa_fa_asset_transfer_details.to_emp_id')
            ->where('erp_fa_fa_asset_transfer.approved_yn', -1)
            ->where('erp_fa_asset_master.approved', -1)
            ->whereDate('erp_fa_fa_asset_transfer.document_date', '>=', $fromDate)
            ->whereDate('erp_fa_fa_asset_transfer.document_date', '<=', $toDate)
            ->whereIn('erp_fa_asset_master.faID', $assetIds)
            ->whereIn('erp_fa_fa_asset_transfer.company_id', $companyID)
            ->orderBy('documentDate', 'asc')
            ->get();

        return $assetTransfer;
    }


    function getAssetDisposal($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $qry = 'SELECT
                    erp_fa_asset_disposalmaster.companyID,
                    companymaster.CompanyName,
                    customermaster.CustomerName,
                    erp_fa_asset_disposalmaster.disposalDocumentDate AS disposalDate,
                    erp_fa_asset_disposalmaster.disposalDocumentCode,
                    erp_fa_asset_disposalmaster.disposalType,
                    erp_fa_asset_disposalmaster.narration,
                    erp_fa_financecategory.financeCatDescription AS AssetCategory,
                    erp_fa_asset_disposaldetail.faCode AS AssetCODE,
                    erp_fa_asset_disposaldetail.faUnitSerialNo AS AssetSerialNumber,
                    erp_fa_asset_disposaldetail.assetDescription AS AssetDescription,
                    (erp_fa_asset_disposaldetail.sellingPriceLocal) AS SellingPriceLocal,
                    (erp_fa_asset_disposaldetail.sellingPriceRpt) AS SellingPriceRpt,
                    (erp_fa_asset_disposaldetail.sellingPriceLocal - erp_fa_asset_disposaldetail.netBookValueLocal) AS ProfitLocal,
                    (erp_fa_asset_disposaldetail.SellingPriceRpt - erp_fa_asset_disposaldetail.NetBookVALUERPT) AS ProfitRpt,
                    erp_fa_asset_disposaldetail.COSTUNIT AS AssetCostLocal,
                    erp_fa_asset_disposaldetail.COSTUNIT - erp_fa_asset_disposaldetail.netBookValueLocal AS AccumulatedDepreciationLocal,
                    erp_fa_asset_disposaldetail.netBookValueLocal AS NetBookVALUELocal,
                    erp_fa_asset_disposaldetail.costUnitRpt AS AssetCostRPT,
                    erp_fa_asset_disposaldetail.depAmountRpt AS AccumulatedDepreciationRPT,
                    erp_fa_asset_disposaldetail.netBookValueRpt AS NetBookVALUERPT,
                    locCur.CurrencyCode as localCurrency,
                    locCur.DecimalPlaces as localCurrencyDeci,
                    repCur.CurrencyCode as reportCurrency,
                    repCur.DecimalPlaces as reportCurrencyDeci
                FROM
                    erp_fa_asset_disposaldetail
                    INNER JOIN erp_fa_asset_master ON erp_fa_asset_disposaldetail.faID = erp_fa_asset_master.faID
                    INNER JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
                    INNER JOIN erp_fa_asset_disposalmaster ON erp_fa_asset_disposaldetail.assetdisposalMasterAutoID = erp_fa_asset_disposalmaster.assetdisposalMasterAutoID 
                    INNER JOIN companymaster ON erp_fa_asset_disposalmaster.companySystemID = companymaster.companySystemID
                    LEFT JOIN customermaster ON erp_fa_asset_disposalmaster.customerID = customermaster.customerCodeSystem
                    LEFT JOIN currencymaster as locCur ON locCur.currencyID = companymaster.localCurrencyID
                LEFT JOIN currencymaster as repCur ON repCur.currencyID = companymaster.reportingCurrency
                WHERE
                    DATE(erp_fa_asset_disposalmaster.disposalDocumentDate)
                    BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    AND erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
                    AND erp_fa_asset_disposalmaster.approvedYN =- 1;';

        $output = \DB::select($qry);

        return $output;
    }

    public function assetDepreciationRegisterMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $monthField = '';
        if ($currency == 2) {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "SUM(if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0)) as `" . $val . "`,";
                }
            }
        } else {
            if (!empty($arrayMonth)) { /* month wise in query*/
                foreach ($arrayMonth as $key => $val) {
                    $monthField .= "SUM(if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0)) as `" . $val . "`,";
                }
            }
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	 SUM( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) )  AS Jan,
	 SUM( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) )  AS Feb,
	 SUM( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) )  AS March,
	 SUM( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) )  AS April,
	 SUM( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) )  AS May,
	 SUM( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) )  AS June,
	 SUM( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) )  AS July,
	 SUM( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) )  AS Aug,
	 SUM( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) )  AS Sept,
	 SUM( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) )  AS Oct,
	 SUM( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) )  AS Nov,
	 SUM( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) )  AS Dece
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID AND erp_fa_asset_master.companySystemID = erp_fa_category.companySystemID
	INNER JOIN (-- assetDepreciation
SELECT
    ' . $monthField . '
	erp_fa_depmaster.depMasterAutoID,
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
	GROUP BY erp_fa_assetdepreciationperiods.faID, erp_fa_depmaster.companyID
	
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID';

        $output = \DB::select($sql);
        return ['data' => $output, 'month' => $arrayMonth];
    }

    public function assetDepreciationDetailSummaryQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;
        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $prevYearDep = "";
        if ($currency == 2) {
            $currentMonthDep = "SUM( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3)) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM((round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ))) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
            $prevYearDep = "SUM(IF
	( DepreciationTotalPrevYear.PreviousYearDepAmountLocal IS NULL, 0, DepreciationTotalPrevYear.PreviousYearDepAmountLocal )) AS prevYearDepAmount";
        } else {
            $currentMonthDep = "SUM( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) )  AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2)) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM((round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ))) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
            $prevYearDep = "SUM(IF
	( DepreciationTotalPrevYear.PreviousYearDepAmountRpt IS NULL, 0, DepreciationTotalPrevYear.PreviousYearDepAmountRpt )) AS prevYearDepAmount";
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
	' . $prevYearDep . ',
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID AND erp_fa_asset_master.companySystemID = erp_fa_category.companySystemID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.companySystemID,
	erp_fa_assetdepreciationperiods.faID,
      SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 )) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 )) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
	GROUP BY erp_fa_assetdepreciationperiods.faID, erp_fa_depmaster.companyID
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) )  AS currentYearDepAmountLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) )  AS currentYearDepAmountRpt
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . '
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurYear ON DepreciationTotalCurYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) )  AS PreviousYearDepAmountLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) )  AS PreviousYearDepAmountRpt
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') AND YEAR ( erp_fa_depmaster.depDate ) < ' . $year . '
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalPrevYear ON DepreciationTotalPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID AND disposal.faID = erp_fa_asset_master.faID 
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND erp_fa_asset_master.deleted_at IS NULL
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID;';

        return \DB::select($sql);
    }

    public function assetDepreciationDetailMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        $prevYearDep = "";
        if ($currency == 2) {
            $currentMonthDep = "SUM( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM((round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) ))) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
            $prevYearDep = "SUM(IF
	( DepreciationTotalPrevYear.PreviousYearDepAmountLocal IS NULL, 0, DepreciationTotalPrevYear.PreviousYearDepAmountLocal )) AS prevYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "SUM(IF(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0)) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "SUM( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM((round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) ))) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
            $prevYearDep = "SUM(IF
	( DepreciationTotalPrevYear.PreviousYearDepAmountRpt IS NULL, 0, DepreciationTotalPrevYear.PreviousYearDepAmountRpt )) AS prevYearDepAmount";

            foreach ($arrayMonth as $key => $val) {
                $monthField .= "SUM(IF(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0)) as `" . $val . "`,";
            }
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
	' . $prevYearDep . ',
	 SUM( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) )  AS Jan,
	 SUM( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) )  AS Feb,
	 SUM( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) )  AS March,
	 SUM( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) )  AS April,
	 SUM( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) )  AS May,
	 SUM( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) )  AS June,
	 SUM( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) )  AS July,
	 SUM( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) )  AS Aug,
	 SUM( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) )  AS Sept,
	 SUM( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) )  AS Oct,
	 SUM( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) )  AS Nov,
	 SUM( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) )  AS Dece,
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID AND erp_fa_asset_master.companySystemID = erp_fa_category.companySystemID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	' . $monthField . '
     SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 )) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 )) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
	GROUP BY erp_fa_assetdepreciationperiods.faID, erp_fa_depmaster.companyID
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) )  AS currentYearDepAmountLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) )  AS currentYearDepAmountRpt
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . '
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurYear ON DepreciationTotalCurYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) )  AS PreviousYearDepAmountLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) )  AS PreviousYearDepAmountRpt
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') AND YEAR ( erp_fa_depmaster.depDate ) < ' . $year . '
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalPrevYear ON DepreciationTotalPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID AND disposal.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.faID;';
        return \DB::select($sql);
    }

    public function assetDepreciationCategorySummaryQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;
        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID AND erp_fa_asset_master.companySystemID = erp_fa_category.companySystemID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
      SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 )) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
SUM(IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 )) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID AND disposal.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,
erp_fa_asset_master.AUDITCATOGARY;';

        return \DB::select($sql);
    }

    public function assetDepreciationCategorySummaryMonthlyQRY($request)
    {

        $year = $request->year;
        $month = sprintf("%02d", $request->month);

        $firstDayOfMonth = new Carbon($year . '-' . $month . '-01');
        $lastDayOfMonth = $firstDayOfMonth->endOfMonth();
        $lastDayOfMonth = $lastDayOfMonth->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $currency = $request->currencyID;

        $arrayMonth = array('Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dece');

        $currentMonthDep = "";
        $cost = "";
        $accumilatedAmount = "";
        $netBookValue = "";
        $currentYearDep = "";
        $monthField = "";
        if ($currency == 2) {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationLocal IS NULL, 0, assetDepreciation.runningMonthDepreciationLocal ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.COSTUNIT, 3 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationLocal IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationLocal ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountLocal IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountLocal )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountLocal, 2),0) as `" . $val . "`,";
            }
        } else {
            $currentMonthDep = "sum( ( IF ( assetDepreciation.runningMonthDepreciationRpt IS NULL, 0, assetDepreciation.runningMonthDepreciationRpt ) ) ) AS currentMonthDepreciation";
            $cost = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 )) AS cost";
            $accumilatedAmount = "SUM(IF
	( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt )) AS accumulatedDepreciation";
            $netBookValue = "SUM(round( erp_fa_asset_master.costUnitRpt, 2 ) - ( IF ( AccumulatedDepreciation.AccumulatedDepreciationRpt IS NULL, 0, AccumulatedDepreciation.AccumulatedDepreciationRpt ) )) AS netBookValue";
            $currentYearDep = "SUM(IF
	( DepreciationTotalCurPrevYear.currentYearDepAmountRpt IS NULL, 0, DepreciationTotalCurPrevYear.currentYearDepAmountRpt )) AS currentYearDepAmount";
            foreach ($arrayMonth as $key => $val) {
                $monthField .= "if(MONTH(erp_fa_depmaster.depDate) = " . ($key + 1) . ",round(erp_fa_assetdepreciationperiods.depAmountRpt, 2),0) as `" . $val . "`,";
            }
        }

        $sql = 'SELECT
	erp_fa_asset_master.companySystemID,
	erp_fa_asset_master.companyID,
	erp_fa_asset_master.faID,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	erp_fa_financecategory.financeCatDescription AS AuditCategory,
	erp_fa_category.catDescription Category,
	' . $currentMonthDep . ',
	' . $cost . ',
	' . $accumilatedAmount . ',
	' . $netBookValue . ',
	' . $currentYearDep . ',
	sum( ( IF ( assetDepreciation.Jan IS NULL, 0, assetDepreciation.Jan ) ) ) AS Jan,
	sum( ( IF ( assetDepreciation.Feb IS NULL, 0, assetDepreciation.Feb ) ) ) AS Feb,
	sum( ( IF ( assetDepreciation.March IS NULL, 0, assetDepreciation.March ) ) ) AS March,
	sum( ( IF ( assetDepreciation.April IS NULL, 0, assetDepreciation.April ) ) ) AS April,
	sum( ( IF ( assetDepreciation.May IS NULL, 0, assetDepreciation.May ) ) ) AS May,
	sum( ( IF ( assetDepreciation.June IS NULL, 0, assetDepreciation.June ) ) ) AS June,
	sum( ( IF ( assetDepreciation.July IS NULL, 0, assetDepreciation.July ) ) ) AS July,
	sum( ( IF ( assetDepreciation.Aug IS NULL, 0, assetDepreciation.Aug ) ) ) AS Aug,
	sum( ( IF ( assetDepreciation.Sept IS NULL, 0, assetDepreciation.Sept ) ) ) AS Sept,
	sum( ( IF ( assetDepreciation.Oct IS NULL, 0, assetDepreciation.Oct ) ) ) AS Oct,
	sum( ( IF ( assetDepreciation.Nov IS NULL, 0, assetDepreciation.Nov ) ) ) AS Nov,
	sum( ( IF ( assetDepreciation.Dece IS NULL, 0, assetDepreciation.Dece ) ) ) AS Dece,
	erp_fa_asset_master.DEPpercentage AS DEPpercentage
FROM
	erp_fa_asset_master
	LEFT JOIN erp_fa_financecategory ON erp_fa_asset_master.AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	LEFT JOIN erp_fa_category ON erp_fa_asset_master.faCatID = erp_fa_category.faCatID AND erp_fa_asset_master.companySystemID = erp_fa_category.companySystemID
	INNER JOIN (-- assetDepreciation
SELECT
	erp_fa_depmaster.companySystemID,
	erp_fa_depmaster.companyID,
	erp_fa_assetdepreciationperiods.faID,
	' . $monthField . '
      IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountLocal, 0 ) AS runningMonthDepreciationLocal,-- 7 is the month which is selected in the filter
IF
	( MONTH ( erp_fa_depmaster.depDate ) = ' . $month . ', erp_fa_assetdepreciationperiods.depAmountRpt, 0 ) AS runningMonthDepreciationRpt -- 7 is the month which is selected in the filter
FROM
	erp_fa_depmaster
	INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
WHERE
	erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ') 
	AND YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ' -- year which is selected in filter option -- year which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID
	) AS assetDepreciation ON assetDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND assetDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,
		sum( round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) AS AccumulatedDepreciationLocal,
		sum( round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) AS AccumulatedDepreciationRpt 
	FROM
		erp_fa_depmaster
		INNER JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_depmaster.depDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS AccumulatedDepreciation ON AccumulatedDepreciation.companySystemID = erp_fa_asset_master.companySystemID 
	AND AccumulatedDepreciation.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_depmaster.companySystemID,
		erp_fa_depmaster.companyID,
		erp_fa_assetdepreciationperiods.faID,-- 2018 is the year selected in filter option
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ), 0 ) ) AS currentYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . $year . ', round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ), 0 ) ) AS currentYearDepAmountRpt,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountLocal, 3 ) ) ) AS PreviousYearDepAmountLocal,
		sum( IF ( YEAR ( erp_fa_depmaster.depDate ) = ' . ($year - 1) . ', 0, round( erp_fa_assetdepreciationperiods.depAmountRpt, 2 ) ) ) AS PreviousYearDepAmountRpt 
	FROM
		erp_fa_depmaster
		LEFT JOIN erp_fa_assetdepreciationperiods ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
		AND erp_fa_depmaster.companySystemID = erp_fa_assetdepreciationperiods.companySystemID 
	WHERE
		erp_fa_depmaster.companySystemID IN (' . join(',', $companyID) . ')
	GROUP BY
		erp_fa_depmaster.companySystemID,
		erp_fa_assetdepreciationperiods.faID 
	) AS DepreciationTotalCurPrevYear ON DepreciationTotalCurPrevYear.companySystemID = erp_fa_asset_master.companySystemID 
	AND DepreciationTotalCurPrevYear.faID = erp_fa_asset_master.faID
	LEFT JOIN (
	SELECT
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposalmaster.companyID,
		erp_fa_asset_disposaldetail.faID,
		sum( round( erp_fa_asset_disposaldetail.depAmountLocal, 3 ) ) AS disposalAmountLocal,
		sum( round( erp_fa_asset_disposaldetail.depAmountRpt, 2 ) ) AS disposalAmountRpt 
	FROM
		erp_fa_asset_disposalmaster
		INNER JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposalmaster.assetdisposalMasterAutoID = erp_fa_asset_disposaldetail.assetdisposalMasterAutoID 
	WHERE
		erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyID) . ')
		AND DATE( erp_fa_asset_disposalmaster.disposalDocumentDate) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
		
	GROUP BY
		erp_fa_asset_disposalmaster.companySystemID,
		erp_fa_asset_disposaldetail.faID 
	) AS disposal ON disposal.companySystemID = erp_fa_asset_master.companySystemID AND disposal.faID = erp_fa_asset_master.faID
WHERE
	erp_fa_asset_master.companySystemID IN (' . join(',', $companyID) . ')
	AND DATE(erp_fa_asset_master.dateAQ) <= "' . $lastDayOfMonth . '" -- last date of the month which is selected in filter option
	
	AND erp_fa_asset_master.approved =- 1 
AND
IF
	(
		erp_fa_asset_master.DIPOSED =- 1,
	IF
		(
			( IF ( erp_fa_asset_master.disposedDate IS NULL, "1990-01-01", erp_fa_asset_master.disposedDate ) ) < "' . $lastDayOfMonth . '",
			1,
			0 
		),
		0 
	) = 0 
GROUP BY
	erp_fa_asset_master.companySystemID,erp_fa_asset_master.AUDITCATOGARY';

        return \DB::select($sql);
    }

    public function generateAssetDetailDrilldown(Request $request)
    {

        $typeID = $request->typeID;
        $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
        $assetCategory = join(',', $assetCategory);
        $faID = $request->faID;
        $input = $request->all();


        $qry = "SELECT
	groupTO,
	faUnitSerialNo,
	erp_fa_asset_master.faID,
	erp_fa_assettype.typeDes,
	erp_fa_financecategory.financeCatDescription,
	erp_fa_asset_master.COSTGLCODE,
	erp_fa_asset_master.ACCDEPGLCODE,
	assetType,
	serviceline.ServiceLineDes,
	erp_fa_asset_master.serviceLineCode,
	docOrigin,
	AUDITCATOGARY,
	faCode,
	assetDescription,
	DEPpercentage,
	dateAQ,
	dateDEP,
	COSTUNIT,
	IFNULL( depAmountLocal, 0 ) AS depAmountLocal,
	COSTUNIT - IFNULL( depAmountLocal, 0 ) AS localnbv,
	costUnitRpt,
	IFNULL( depAmountRpt, 0 ) AS depAmountRpt,
	costUnitRpt - IFNULL( depAmountRpt, 0 ) AS rptnbv 
FROM
	erp_fa_asset_master
	LEFT JOIN (
	SELECT
		faID,
		erp_fa_assetdepreciationperiods.depMasterAutoID,
		sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
		sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
	FROM
		erp_fa_assetdepreciationperiods
		INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.approved =- 1 
	GROUP BY
		faID 
	) t ON erp_fa_asset_master.faID = t.faID
	INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
	INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	INNER JOIN serviceline ON serviceline.ServiceLineCode = erp_fa_asset_master.serviceLineCode 
WHERE
	erp_fa_asset_master.companySystemID = $request->companySystemID 
	AND erp_fa_asset_master.dateAQ <= '$asOfDate' AND assetType = $typeID AND ( disposedDate IS NULL OR disposedDate > '$asOfDate' 
		OR DIPOSED = - 1 ) AND
	(erp_fa_asset_master.faID = $faID OR 	erp_fa_asset_master.groupTO = $faID )
	
	
	
	
";


        $output = \DB::select($qry);

        $outputArr = [];


        $COSTUNIT = 0;
        $costUnitRpt = 0;
        $depAmountLocal = 0;
        $depAmountRpt = 0;
        $localnbv = 0;
        $rptnbv = 0;
        if ($output) {
            foreach ($output as $val) {
                $localnbv += $val->localnbv;
                $COSTUNIT += $val->COSTUNIT;
                $costUnitRpt += $val->costUnitRpt;
                $depAmountRpt += $val->depAmountRpt;
                $depAmountLocal += $val->depAmountLocal;
                $rptnbv += $val->rptnbv;
                $outputArr[$val->financeCatDescription][] = $val;
            }
        }

        return array('reportData' => $outputArr, 'localnbv' => $localnbv, 'rptnbv' => $rptnbv, 'COSTUNIT' => $COSTUNIT, 'costUnitRpt' => $costUnitRpt, 'depAmountLocal' => $depAmountLocal, 'depAmountRpt' => $depAmountRpt);


    }


    function getAssetRegisterDetail($request)
    {
        $typeID = $request->typeID;
        $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
        $assetCategory = join(',', $assetCategory);

        $where = "";
        if (isset($request->searchText)) {
            $searchText = $request->searchText;
            if ($searchText != '') {
                $searchText = str_replace("\\", "\\\\", $searchText);
                $where = " AND ( assetGroup.faCode LIKE '%$searchText%' OR erp_fa_asset_master.assetDescription LIKE '%$searchText%' OR  
            erp_fa_asset_master.faCode LIKE '%$searchText%' )  ";
            }
        }

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [(int)$request->companySystemID];
        }

        $qry = "
SELECT * FROM ( SELECT
IF(groupTO IS NOT  NULL ,groupTO , erp_fa_asset_master.faID ) as sortfaID,
    
  groupTO,
	assetGroup.faCode as groupbydesc,
	erp_fa_asset_master.faUnitSerialNo,
	erp_fa_asset_master.faID,
	erp_fa_assettype.typeDes,
	erp_fa_financecategory.financeCatDescription,
	erp_fa_asset_master.COSTGLCODE,
	erp_fa_asset_master.ACCDEPGLCODE,
	assetType,
	serviceline.ServiceLineDes,
	erp_fa_asset_master.serviceLineCode,
	docOrigin,
	AUDITCATOGARY,
	postedDate,
	erp_fa_asset_master.faCode,
	erp_fa_asset_master.assetDescription,
	DEPpercentage,
	dateAQ,
	dateDEP,
	COSTUNIT,
	IFNULL( depAmountLocal, 0 ) AS depAmountLocal,
	CASE 
    WHEN CAST('$asOfDate' AS DATE) > accumulated_depreciation_date 
        THEN 0
    ELSE IFNULL(COSTUNIT, 0) - IFNULL(t.depAmountLocal, 0)
    END AS localnbv,
    costUnitRpt,
    IFNULL(depAmountRpt, 0) AS depAmountRpt,
    CASE 
        WHEN CAST('$asOfDate' AS DATE) > accumulated_depreciation_date 
            THEN 0
        ELSE IFNULL(costUnitRpt, 0) - IFNULL(depAmountRpt, 0)
    END AS rptnbv
FROM
	erp_fa_asset_master
	LEFT JOIN (
	SELECT
		faID,
		erp_fa_assetdepreciationperiods.depMasterAutoID,
		sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
		sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
	FROM
		erp_fa_assetdepreciationperiods
		INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
	WHERE
		erp_fa_depmaster.approved =- 1  AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= '$asOfDate'
	GROUP BY
		faID 
	) t ON erp_fa_asset_master.faID = t.faID
	INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
	INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
	INNER JOIN serviceline ON serviceline.serviceLineSystemID = erp_fa_asset_master.serviceLineSystemID
LEFT JOIN (SELECT assetDescription , faID ,faUnitSerialNo,faCode FROM erp_fa_asset_master WHERE erp_fa_asset_master.companySystemID IN (" . join(',', $companyID) . ")) assetGroup ON erp_fa_asset_master.groupTO= assetGroup.faID
WHERE
(
    (erp_fa_asset_master.DIPOSED = -1 AND DATE(erp_fa_asset_master.disposedDate) > '$asOfDate')
    OR
    (erp_fa_asset_master.DIPOSED = 0)
  )
    AND
	erp_fa_asset_master.companySystemID IN (" . join(',', $companyID) . ")  AND AUDITCATOGARY IN($assetCategory) AND approved =-1
	AND DATE(erp_fa_asset_master.postedDate) <= '$asOfDate' AND assetType = $typeID
	$where
	) t  ORDER BY sortfaID desc  ";


        $output = \DB::select($qry);

        return $output;
    }

    function getAssetRegisterSummaryQRY($request)
    {
        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [$request->companySystemID];
        }

        $financeYear = CompanyFinanceYear::find($request->financeYear);
        $financePeriod = CompanyFinancePeriod::find($request->financePeriod);

        $currencyColumn = '';
        $currencyColumnDep = '';
        if ($request->currencyID == 2) {
            $currencyColumn = 'COSTUNIT';
            $currencyColumnDep = 'depAmountLocal';
        } else {
            $currencyColumn = 'costUnitRpt';
            $currencyColumnDep = 'depAmountRpt';
        }

        $assetCategory = $request->assetCategory;
        $assetCategoryQry = '';
        $assetCategoryQryTotArr = [];
        $assetCategoryQryDisposal = '';
        $assetCategoryQryTotDisposalArr = [];
        $assetCategoryDepQry = '';
        $assetCategoryDepQryTotArr = [];
        $assetCategoryDepQryDisposal = '';
        $assetCategoryDepQryTotDisposalArr = [];
        if (count($assetCategory) > 0) {
            foreach ($assetCategory as $val) {
                $assetCategoryQry .= 'IFNULL(SUM(if(AUDITCATOGARY = ' . $val['faFinanceCatID'] . ',' . $currencyColumn . ',0)),0) AS `' . $val['financeCatDescription'] . '`,';
                $assetCategoryDepQry .= 'IFNULL(SUM(if(faFinanceCatID = ' . $val['faFinanceCatID'] . ',' . $currencyColumnDep . ',0)),0) AS `' . $val['financeCatDescription'] . '`,';
                $assetCategoryQryDisposal .= 'IFNULL(SUM(if(AUDITCATOGARY = ' . $val['faFinanceCatID'] . ',' . $currencyColumn . ',0)),0)* -1 AS `' . $val['financeCatDescription'] . '`,';
                $assetCategoryDepQryDisposal .= 'IFNULL(SUM(if(faFinanceCatID = ' . $val['faFinanceCatID'] . ',' . $currencyColumnDep . ',0)),0) * -1 AS `' . $val['financeCatDescription'] . '`,';

                $assetCategoryQryTotArr[] = 'IFNULL(SUM(if(AUDITCATOGARY = ' . $val['faFinanceCatID'] . ',' . $currencyColumn . ',0)),0)';
                $assetCategoryDepQryTotArr[] = 'IFNULL(SUM(if(faFinanceCatID = ' . $val['faFinanceCatID'] . ',' . $currencyColumnDep . ',0)),0)';

                $assetCategoryQryTotDisposalArr[] = 'IFNULL(SUM(if(AUDITCATOGARY = ' . $val['faFinanceCatID'] . ',' . $currencyColumn . ',0)),0)*-1';
                $assetCategoryDepQryTotDisposalArr[] = 'IFNULL(SUM(if(faFinanceCatID = ' . $val['faFinanceCatID'] . ',' . $currencyColumnDep . ',0)),0)*-1';
            }
        }

        $assetCategoryQryTot = '(' . join('+', $assetCategoryQryTotArr) . ') as total,';
        $assetCategoryDepQryTot = '(' . join('+', $assetCategoryDepQryTotArr) . ') as total,';

        $assetCategoryQryTotDisposal = '(' . join('+', $assetCategoryQryTotDisposalArr) . ') as total,';
        $assetCategoryDepQryTotDisposal = '(' . join('+', $assetCategoryDepQryTotDisposalArr) . ') as total,';

        $beginingFinancialYear = Carbon::parse($financeYear->bigginingDate)->format('d-M-Y');

        // asset cost
        $additionCostquery = FixedAssetMaster::selectRaw($assetCategoryQry . $assetCategoryQryTot . '"Additions during the year" as description,2 as type')->whereBetween(DB::raw('DATE(postedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->isApproved();

        $disposalCostquery = FixedAssetMaster::selectRaw($assetCategoryQryDisposal . $assetCategoryQryTotDisposal . '"Disposals" as description,3 as type')->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->disposed(-1)->isApproved();

        $beginingCostquery = FixedAssetMaster::selectRaw($assetCategoryQry . $assetCategoryQryTot . '"' . $beginingFinancialYear . '" as description,1 as type')->whereDate('postedDate', '<', $financeYear->bigginingDate)->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)')->assetType($request->typeID)->ofCompany($companyID)->isApproved()->union($additionCostquery)
            ->union($disposalCostquery)->get();

        // asset depreciation

        $additionDepquery = FixedAssetDepreciationPeriod::selectRaw($assetCategoryDepQry . $assetCategoryDepQryTot . '"Charge For The Period" as description,2 as type')->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
            //$q->whereBetween(DB::raw('DATE(depDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
            $q->where('approved', -1);
        })->whereHas('asset_by', function ($q) use ($request) {
            $q->assetType($request->typeID);
            $q->isApproved();
        })
        ->whereDate('depForFYperiodStartDate', '>=', $financeYear->bigginingDate)
        ->whereDate('depForFYperiodEndDate', '<=', $financePeriod->dateTo)
        ->ofCompany($companyID);

        $disposalDepquery = FixedAssetDepreciationPeriod::selectRaw($assetCategoryDepQryDisposal . $assetCategoryDepQryTotDisposal . '"Disposal" as description,3 as type')->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
            $q->whereDate('depDate', '<', $financePeriod->dateTo);
            $q->where('approved', -1);
        })->whereHas('asset_by', function ($q) use ($request, $financeYear, $financePeriod) {
            $q->assetType($request->typeID);
            $q->disposed(-1);
            $q->isApproved();
            $q->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
        })->ofCompany($companyID);

        $beginingDepquery = FixedAssetDepreciationPeriod::selectRaw($assetCategoryDepQry . $assetCategoryDepQryTot . '"' . $beginingFinancialYear . '" as description,1 as type')->whereHas('master_by', function ($q) use ($financeYear) {
            $q->whereDate('depDate', '<', $financeYear->bigginingDate);
            $q->where('approved', -1);
        })->whereHas('asset_by', function ($q) use ($request, $financeYear) {
            $q->assetType($request->typeID);
            $q->isApproved();
            $q->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)');
        })->ofCompany($companyID)->union($additionDepquery)
            ->union($disposalDepquery)->get();

        return ['depQry' => $beginingDepquery, 'costQry' => $beginingCostquery];

    }

    function getAssetRegisterSummaryDrillDownQRY(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('currencyID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [$request->companySystemID];
        }

        $financeYear = CompanyFinanceYear::find($request->financeYear);
        $financePeriod = CompanyFinancePeriod::find($request->financePeriod);

        $currencyColumn = '';
        $currencyColumnDep = '';
        if ($request->currencyID == 2) {
            $currencyColumn = 'COSTUNIT';
            $currencyColumnDep = 'depAmountLocal';
        } else {
            $currencyColumn = 'costUnitRpt';
            $currencyColumnDep = 'depAmountRpt';
        }
        $search = $request->input('search.value');

        // asset cost
        if ($request->catType == 1) {
            if ($request->subType == 1) { //opening
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereDate('postedDate', '<', $financeYear->bigginingDate)->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)')->assetType($request->typeID)->ofCompany($companyID)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
            if ($request->subType == 2) { //addition
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereBetween(DB::raw('DATE(postedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
            if ($request->subType == 3) {// disposal
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->disposed(-1)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
        }

        if ($request->catType == 2) {
            // asset depreciation

            if ($request->subType == 1) { //opening
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear) {
                    $q->whereDate('depDate', '<', $financeYear->bigginingDate);
                    $q->where('approved', -1);
                })->whereHas('asset_by', function ($q) use ($request, $financeYear) {
                    $q->assetType($request->typeID);
                    $q->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)');
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }

            if ($request->subType == 2) { //charge
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
                    //$q->whereBetween(DB::raw('DATE(depDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
                    $q->where('approved', -1);
                })
                ->whereDate('depForFYperiodStartDate', '>=', $financeYear->bigginingDate)
                ->whereDate('depForFYperiodEndDate', '<=', $financePeriod->dateTo)
                ->whereHas('asset_by', function ($q) use ($request) {
                    $q->assetType($request->typeID);
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }

            if ($request->subType == 3) { //disposed
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
                    $q->whereDate('depDate', '<', $financePeriod->dateTo);
                    $q->where('approved', -1);
                })->whereHas('asset_by', function ($q) use ($request, $financeYear, $financePeriod) {
                    $q->assetType($request->typeID);
                    $q->disposed(-1);
                    $q->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }
        }

        $total = $output->get();
        $total = collect($total)->sum('amount');

        return \DataTables::eloquent($output)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->with('totalAmount', [
                'totalAmount' => $total,
            ])
            ->make(true);

    }

    public function getAssetRegisterSummaryDrillDownExport(Request $request, ExportReportToExcelService $service)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('currencyID', 'excelType'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [$request->companySystemID];
        }

        $financeYear = CompanyFinanceYear::find($request->financeYear);
        $financePeriod = CompanyFinancePeriod::find($request->financePeriod);

        $currencyColumn = '';
        $currencyColumnDep = '';
        if ($request->currencyID == 2) {
            $currencyColumn = 'COSTUNIT';
            $currencyColumnDep = 'depAmountLocal';
        } else {
            $currencyColumn = 'costUnitRpt';
            $currencyColumnDep = 'depAmountRpt';
        }
        $search = $request->input('search.value');

        // asset cost
        if ($request->catType == 1) {
            if ($request->subType == 1) { //opening
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereDate('postedDate', '<', $financeYear->bigginingDate)->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)')->assetType($request->typeID)->ofCompany($companyID)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
            if ($request->subType == 2) { //addition
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereBetween(DB::raw('DATE(postedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
            if ($request->subType == 3) {// disposal
                $output = FixedAssetMaster::selectRaw('faCode,assetDescription,disposedDate,' . $currencyColumn . ' as amount,faCatID,faSubCatID')->with(['category_by', 'sub_category_by'])->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo])->assetType($request->typeID)->ofCompany($companyID)->disposed(-1)->where('AUDITCATOGARY', $request->faFinanceCatID)->isApproved();
                if ($search) {
                    $search = str_replace("\\", "\\\\", $search);
                    $output->where('faCode', 'like', $search);
                }
            }
        }

        if ($request->catType == 2) {
            // asset depreciation

            if ($request->subType == 1) { //opening
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear) {
                    $q->whereDate('depDate', '<', $financeYear->bigginingDate);
                    $q->where('approved', -1);
                })->whereHas('asset_by', function ($q) use ($request, $financeYear) {
                    $q->assetType($request->typeID);
                    $q->whereRAW('((DIPOSED = - 1  AND (  DATE(disposedDate) > "' . $financeYear->bigginingDate . '")) OR DIPOSED <>  -1)');
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }

            if ($request->subType == 2) { //charge
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
                    $q->whereBetween(DB::raw('DATE(depDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
                    $q->where('approved', -1);
                })->whereHas('asset_by', function ($q) use ($request) {
                    $q->assetType($request->typeID);
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }

            if ($request->subType == 3) { //disposed
                $output = FixedAssetDepreciationPeriod::selectRaw('' . $currencyColumnDep . ' as amount,faID')->with(['asset_by' => function ($q) {
                    $q->with(['category_by', 'sub_category_by']);
                }])->whereHas('master_by', function ($q) use ($financeYear, $financePeriod) {
                    $q->whereDate('depDate', '<', $financePeriod->dateTo);
                    $q->where('approved', -1);
                })->whereHas('asset_by', function ($q) use ($request, $financeYear, $financePeriod) {
                    $q->assetType($request->typeID);
                    $q->disposed(-1);
                    $q->whereBetween(DB::raw('DATE(disposedDate)'), [$financeYear->bigginingDate, $financePeriod->dateTo]);
                    $q->isApproved();
                    $search = $request->input('search.value');
                    if ($search) {
                        $search = str_replace("\\", "\\\\", $search);
                        $q->where('faCode', 'like', $search);
                    }
                })->ofCompany($companyID)->where('faFinanceCatID', $request->faFinanceCatID);
            }
        }

        $output = $output->get();
        $data = [];
        
        // Add header row
        $headerRow = array();
        $headerRow[] = trans('custom.asset_code');
        $headerRow[] = trans('custom.description');
        $headerRow[] = trans('custom.posted_date');
        $headerRow[] = trans('custom.main_category');
        $headerRow[] = trans('custom.sub_category');
        if ($request->catType == 1) {
            $headerRow[] = trans('custom.cost').'('.$request->currencyCode.')';
        } else {
            $headerRow[] = trans('custom.dep_amount').'('.$request->currencyCode.')';
        }
        $data[] = $headerRow;
        
        $x = 1; // Start from 1 since we added header at index 0
        foreach ($output as $val) {
            if ($request->catType == 1) {
                $data[$x][trans('custom.asset_code')] = $val->faCode;
            } else {
                $data[$x][trans('custom.asset_code')] = $val->asset_by->faCode;
            }

            if ($request->catType == 1) {
                $data[$x][trans('custom.description')] = $val->assetDescription;
            } else {
                $data[$x][trans('custom.description')] = $val->asset_by->assetDescription;
            }

            if ($request->subType == 3) {
                if ($request->catType == 1) {
                    $data[$x][trans('custom.disposal_date')] = \Helper::dateFormat($val->disposedDate);
                } else {
                    $data[$x][trans('custom.disposal_date')] = \Helper::dateFormat($val->asset_by->disposedDate);
                }
            } else {
                if ($request->catType == 1) {
                    $data[$x][trans('custom.posted_date')] = \Helper::dateFormat($val->postedDate);
                } else {
                    $data[$x][trans('custom.posted_date')] = \Helper::dateFormat($val->asset_by->postedDate);
                }
            }

            if ($request->catType == 1) {
                $data[$x][trans('custom.main_category')] = $val->category_by->catDescription;
            } else {
                $data[$x][trans('custom.main_category')] = $val->asset_by->category_by->catDescription;
            }

            if ($request->catType == 1) {
                $data[$x][trans('custom.sub_category')] = $val->sub_category_by->catDescription;
            } else {
                $data[$x][trans('custom.sub_category')] = $val->asset_by->sub_category_by->catDescription;
            }

            if ($request->catType == 1) {
                $data[$x][trans('custom.cost').'('.$request->currencyCode.')'] = round($val->amount, $request->decimalPlaces);
            } else {
                $data[$x][trans('custom.dep_amount').'('.$request->currencyCode.')'] = round($val->amount, $request->decimalPlaces);
            }
            $x++;
        }

        $companyMaster = Company::find(isset($request->companySystemID)?$request->companySystemID: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';

        $excelColumnFormat = [
            'A' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'B' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'C' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2,
            'D' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'E' => \PHPExcel_Style_NumberFormat::FORMAT_GENERAL,
            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
        ];

        $title = "";
        $fileName = 'asset_register_drilldown';
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

        if(!$exportToExcel['success'])
            return $this->sendError(trans('custom.unable_to_export_excel'));

        return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

    }

    function getAssetCWIPQRY($request)
    {
        /*$financeYear = CompanyFinanceYear::find($request->financeYear);
        $firstQ1 = Carbon::parse($financeYear->bigginingDate)->format('Y-m-d');
        $endFirstQ1 = Carbon::parse($financeYear->bigginingDate)->endOfQuarter();
        $secondQ1 = Carbon::parse($endFirstQ1->addDay())->format('Y-m-d');
        $endSecondQ1 = Carbon::parse($secondQ1)->endOfQuarter();
        $thirdQ1 = Carbon::parse($endSecondQ1->addDay())->format('Y-m-d');
        $endThirdQ1 = Carbon::parse($thirdQ1)->endOfQuarter();
        $fourthQ1 = Carbon::parse($endThirdQ1->addDay())->format('Y-m-d');
        $endfourthQ1 = Carbon::parse($fourthQ1)->endOfQuarter();

        $quarterArr = [1 => ['startDate' => $firstQ1, 'endDate' => $endFirstQ1->subDay()->format('Y-m-d')], 2 => ['startDate' => $secondQ1, 'endDate' => $endSecondQ1->subDay()->format('Y-m-d')], 3 => ['startDate' => $thirdQ1, 'endDate' => $endThirdQ1->subDay()->format('Y-m-d')], 4 => ['startDate' => $fourthQ1, 'endDate' => $endfourthQ1->format('Y-m-d')]];*/

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new Carbon($request->toDate))->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [(int)$request->companySystemID];
        }


        $additionColumn = '';
        $capitlizationColumn = '';
        if ($request->currencyID == 2) {
            $additionColumn = 'grvLocalAmount';
            $capitlizationColumn = 'costLocal';
        } else {
            $additionColumn = 'grvRptAmount';
            $capitlizationColumn = 'costRpt';
        }

        $addCapi = DB::table('erp_grvmaster')->selectRaw('grvPrimaryCode,
	approvedDate,
	0 as opening,
	IFNULL(grvd.' . $additionColumn . ',0) as addition,
	IFNULL(fa.' . $capitlizationColumn . ',0) as capitalization,
	(IFNULL(grvd.' . $additionColumn . ',0) - IFNULL(fa.' . $capitlizationColumn . ',0)) as closing, erp_grvmaster.grvAutoID,2 as type')
            ->join(DB::raw('(SELECT * FROM erp_grvdetails WHERE itemFinanceCategoryID = 3 GROUP BY grvAutoID) as grvdd'), function ($join) {
                $join->on('erp_grvmaster.grvAutoID', '=', 'grvdd.grvAutoID');
            })
            ->leftJoin(DB::raw('(SELECT IFNULL(SUM( landingCost_LocalCur*noQty ),0) AS grvLocalAmount,IFNULL(SUM( landingCost_RptCur*noQty ),0) AS grvRptAmount,grvAutoID FROM erp_grvdetails WHERE itemFinanceCategoryID = 3 AND itemFinanceCategorySubID IN (16, 162, 164, 166) GROUP BY grvAutoID) as grvd'), function ($query) {
            $query->on('erp_grvmaster.grvAutoID', '=', 'grvd.grvAutoID');
        })->leftJoin(DB::raw('(SELECT IFNULL(SUM( COSTUNIT ),0) AS costLocal, IFNULL(SUM( costUnitRpt ),0) AS costRpt, docOriginSystemCode, docOriginDocumentSystemID FROM erp_fa_asset_master WHERE DATE(postedDate) BETWEEN "' . $fromDate . '" 
	AND "' . $toDate . '" AND approved = -1 GROUP BY docOriginSystemCode, docOriginDocumentSystemID) as fa'), function ($query) {
            $query->on('erp_grvmaster.grvAutoID', '=', 'fa.docOriginSystemCode');
            $query->on('erp_grvmaster.documentSystemID', '=', 'fa.docOriginDocumentSystemID');
        })->whereIN('erp_grvmaster.companySystemID', $companyID)->where('erp_grvmaster.approved', -1)->whereRAW('DATE(erp_grvmaster.approvedDate) BETWEEN "' . $fromDate . '" 
	AND "' . $toDate . '" ')->where('erp_grvmaster.capitalizedYN', 0);

        $output = DB::table('erp_grvmaster')->selectRaw('grvPrimaryCode,
	approvedDate,
	IFNULL(grvd.' . $additionColumn . ',0) as opening,
	0 as addition,
	IFNULL(fa.' . $capitlizationColumn . ',0) as capitalization,
	(IFNULL(grvd.' . $additionColumn . ',0) - IFNULL(fa.' . $capitlizationColumn . ',0)) as closing, erp_grvmaster.grvAutoID,1 as type')
            ->join(DB::raw('(SELECT * FROM erp_grvdetails WHERE itemFinanceCategoryID = 3 GROUP BY grvAutoID) as grvdd'), function ($join) {
                $join->on('erp_grvmaster.grvAutoID', '=', 'grvdd.grvAutoID');
            })
            ->leftJoin(DB::raw('(SELECT IFNULL(SUM( landingCost_LocalCur*noQty ),0) AS grvLocalAmount,IFNULL(SUM( landingCost_RptCur*noQty ),0) AS grvRptAmount,grvAutoID FROM erp_grvdetails WHERE itemFinanceCategoryID = 3 AND itemFinanceCategorySubID IN (16, 162, 164, 166) GROUP BY grvAutoID) as grvd'), function ($query) {
            $query->on('erp_grvmaster.grvAutoID', '=', 'grvd.grvAutoID');
        })->leftJoin(DB::raw('(SELECT IFNULL(SUM( COSTUNIT ),0) AS costLocal, IFNULL(SUM( costUnitRpt ),0) AS costRpt, docOriginSystemCode, docOriginDocumentSystemID FROM erp_fa_asset_master WHERE DATE(postedDate) BETWEEN "' . $fromDate . '" 
	AND "' . $toDate . '" AND approved = -1 GROUP BY docOriginSystemCode, docOriginDocumentSystemID) as fa'), function ($query) {
            $query->on('erp_grvmaster.grvAutoID', '=', 'fa.docOriginSystemCode');
            $query->on('erp_grvmaster.documentSystemID', '=', 'fa.docOriginDocumentSystemID');
        })->whereIN('erp_grvmaster.companySystemID', $companyID)->where('erp_grvmaster.approved', -1)->whereDate('erp_grvmaster.approvedDate', '<', $fromDate)->where('erp_grvmaster.capitalizedYN', 0)->union($addCapi)->get();

        return $output;
    }

    function assetCWIPDrillDown(Request $request)
    {
        $input = $request->all();
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new Carbon($request->toDate))->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [(int)$request->companySystemID];
        }

        $capitlizationColumn = '';
        if ($request->currencyID == 2) {
            $capitlizationColumn = 'COSTUNIT';
        } else {
            $capitlizationColumn = 'costUnitRpt';
        }

        $output = FixedAssetMaster::selectRAW($capitlizationColumn . ' as capitalization,faCode,postedDate')->where('docOriginSystemCode', $request->grvAutoID)->where('docOriginDocumentSystemID', 3)->isApproved()->whereRaw('DATE(postedDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"');

        $total = $output->get();
        $total = collect($total)->sum('capitalization');

        return \DataTables::eloquent($output)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('faID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->with('totalAmount', [
                'totalAmount' => $total,
            ])
            ->make(true);
    }

    function getAssetRegisterDetail2($request)
    {
        $typeID = $request->typeID;
        $fromDate = Carbon::parse($request->year . '-' . $request->fromMonth)->startOfMonth()->format('Y-m-d');
        $toDate = Carbon::parse($request->year . '-' . $request->toMonth)->endOfMonth()->format('Y-m-d');
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
            $companyID = \Helper::getGroupCompany($request->companySystemID);
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
                    ( erp_fa_asset_master.DIPOSED = - 1 && ( "' . $fromDate . '" < erp_fa_asset_master.disposedDate && "' . $toDate . '" > erp_fa_asset_master.disposedDate ), ' . $currencyColumn . ', 0 )) as costClosing,
                    dep1.*,
                    dep2.* 
                FROM
                    erp_fa_asset_master
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = erp_fa_asset_master.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '"
                    AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
                    LEFT JOIN ( 
                    SELECT 0 as openingDep,IFNULL(SUM(' . $currencyColumnDep . '),0) as additionDep,SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && (erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 )) AS disposedDep,
                    (0 + IFNULL(SUM(' . $currencyColumnDep . '),0) - SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && (erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 ))) as closingDep,erp_fa_assetdepreciationperiods.faID as faID3 
                    FROM erp_fa_assetdepreciationperiods
                    INNER JOIN erp_fa_asset_master ON  erp_fa_assetdepreciationperiods.faID = erp_fa_asset_master.faID
                    INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID 
                    AND erp_fa_depmaster.approved = -1 AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '"
                    AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" GROUP BY erp_fa_assetdepreciationperiods.faID 
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
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = serviceline.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '"
                    AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
                    LEFT JOIN (
                     SELECT IFNULL(SUM(' . $currencyColumnDep . '),0) as openingDep,0 as additionDep, SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && (erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 )) AS disposedDep,
                    (IFNULL(SUM(' . $currencyColumnDep . '),0)+ 0 - SUM(IF
                    ( erp_fa_asset_master.DIPOSED = - 1 && (erp_fa_asset_master.disposedDate < "' . $toDate . '"), ' . $currencyColumnDep . ', 0 ))) as closingDep,erp_fa_assetdepreciationperiods.faID as faID3
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
                    LEFT JOIN serviceline ON serviceline.serviceLineSystemID  = serviceline.serviceLineSystemID
                    LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem  = erp_fa_asset_master.supplierIDRentedAsset
                    LEFT JOIN erp_fa_asset_master a2 ON a2.faID  = erp_fa_asset_master.groupTo
                    LEFT JOIN erp_fa_category ON erp_fa_category.faCatID  = erp_fa_asset_master.faCatID
                    LEFT JOIN ( SELECT ' . $periodQry . ' faID as faID2 FROM erp_fa_assetdepreciationperiods INNER JOIN erp_fa_depmaster ON erp_fa_assetdepreciationperiods.depMasterAutoID = erp_fa_depmaster.depMasterAutoID AND approved = -1 AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodStartDate) >= "' . $fromDate . '"
                    AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= "' . $toDateNew . '" GROUP BY faID ) dep1 ON dep1.faID2 = erp_fa_asset_master.faID
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


    function getAssetRegisterDetail3($request)
    {
        $typeID = $request->typeID;
        $asOfDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $assetCategory = collect($request->assetCategory)->pluck('faFinanceCatID')->toArray();
        $assetCategory = join(',', $assetCategory);

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = [(int)$request->companySystemID];
        }

        $where = "";
        if (isset($request->searchText)) {
            $searchText = $request->searchText;
            if ($searchText != '') {
                $searchText = str_replace("\\", "\\\\", $searchText);
                $where = " AND ( assetGroup.faCode LIKE '%$searchText%' OR erp_fa_asset_master.assetDescription LIKE '%$searchText%' OR  
            erp_fa_asset_master.faCode LIKE '%$searchText%' )  ";
            }
        }

        $qry = "
                SELECT * 
                FROM ( 
                    SELECT
                        IF(groupTO IS NOT  NULL ,groupTO , erp_fa_asset_master.faID ) as sortfaID,
                        groupTO,
                        assetGroup.faCode as groupbydesc,
                        erp_fa_asset_master.faUnitSerialNo,
                        erp_fa_asset_master.faID,
                        erp_fa_asset_master.DIPOSED,
                        erp_fa_assettype.typeDes,
                        erp_fa_financecategory.financeCatDescription,
                        erp_fa_asset_master.COSTGLCODE,
                        erp_fa_asset_master.ACCDEPGLCODE,
                        erp_fa_asset_disposalmaster.disposalType,   
                        assetType,
                        serviceline.ServiceLineDes,
                        erp_fa_asset_master.serviceLineCode,
                        docOrigin,
                        AUDITCATOGARY,
                        erp_fa_asset_master.postedDate,
                        erp_fa_asset_master.faCode,
                        erp_fa_asset_master.assetDescription,
                        DEPpercentage,
                        dateAQ,
                        dateDEP,
                        erp_fa_asset_master.depMonth * 12 as depMonth,
                        locationName,
                        supplierName,
                        depreciatedMonths,
                        erp_fa_asset_master.COSTUNIT,
                        IFNULL( t.depAmountLocal, 0 ) AS depAmountLocal,
                        IFNULL( adDepAmountLocal, 0 ) AS adDepAmountLocal,
                        IFNULL( acDepAmountRpt, 0 ) AS acDepAmountRpt,
                        CASE 
                        WHEN '$asOfDate' > erp_fa_asset_master.accumulated_depreciation_date 
                            THEN 0
                        ELSE IFNULL(erp_fa_asset_master.COSTUNIT, 0) - IFNULL(ad.adDepAmountLocal, 0)
                        END AS localnbv,                    
                        CASE 
                            WHEN '$asOfDate' > erp_fa_asset_master.accumulated_depreciation_date 
                                THEN 0
                            ELSE IFNULL(erp_fa_asset_master.costUnitRpt, 0) - IFNULL(ad.acDepAmountRpt, 0)
                        END AS rptnbv,
                        erp_fa_asset_master.costUnitRpt,
                        sellingPriceLocal,
                        sellingPriceRpt,
                        IFNULL( t.depAmountRpt, 0 ) AS depAmountRpt
                    FROM
                        erp_fa_asset_master
                        LEFT JOIN (
                            SELECT
                                count(faID) as depreciatedMonths,
                                faID,
                                erp_fa_assetdepreciationperiods.depMasterAutoID,
                                sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS depAmountLocal,
                                sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS depAmountRpt 
                            FROM
                                erp_fa_assetdepreciationperiods
                                INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
                            WHERE
                                erp_fa_depmaster.approved =- 1  AND DATE(erp_fa_assetdepreciationperiods.depForFYperiodEndDate) <= '$asOfDate'
                            GROUP BY
                                faID 
                        ) t ON erp_fa_asset_master.faID = t.faID

                        LEFT JOIN (
                            SELECT
                                faID,
                                sum( erp_fa_assetdepreciationperiods.depAmountLocal ) AS adDepAmountLocal,
                                sum( erp_fa_assetdepreciationperiods.depAmountRpt ) AS acDepAmountRpt 
                            FROM
                                erp_fa_assetdepreciationperiods
                                INNER JOIN erp_fa_depmaster ON erp_fa_depmaster.depMasterAutoID = erp_fa_assetdepreciationperiods.depMasterAutoID 
                            WHERE
                                erp_fa_depmaster.approved =- 1
                            GROUP BY
                                faID 
                        ) ad ON erp_fa_asset_master.faID = ad.faID
                        INNER JOIN erp_fa_assettype ON erp_fa_assettype.typeID = erp_fa_asset_master.assetType
                        LEFT JOIN erp_location ON erp_location.locationID = erp_fa_asset_master.LOCATION
                        LEFT JOIN erp_grvmaster ON erp_grvmaster.grvAutoID = erp_fa_asset_master.docOriginSystemCode
                        LEFT JOIN erp_fa_asset_disposaldetail ON erp_fa_asset_disposaldetail.faID = erp_fa_asset_master.faID
                        LEFT JOIN erp_fa_asset_disposalmaster ON erp_fa_asset_disposaldetail.assetdisposalMasterAutoID = erp_fa_asset_disposalmaster.assetdisposalMasterAutoID 
                        INNER JOIN erp_fa_financecategory ON AUDITCATOGARY = erp_fa_financecategory.faFinanceCatID
                        INNER JOIN serviceline ON serviceline.serviceLineSystemID = erp_fa_asset_master.serviceLineSystemID
                    LEFT JOIN (SELECT assetDescription , faID ,faUnitSerialNo,faCode FROM erp_fa_asset_master WHERE erp_fa_asset_master.companySystemID IN (" . join(',', $companyID) . ")   )  assetGroup ON erp_fa_asset_master.groupTO= assetGroup.faID
                    WHERE
                        erp_fa_asset_master.companySystemID IN (" . join(',', $companyID) . ")  AND AUDITCATOGARY IN($assetCategory) AND erp_fa_asset_master.approved =-1
                        AND DATE(erp_fa_asset_master.postedDate) <= '$asOfDate' AND assetType = $typeID
                        $where
                        ) t  ORDER BY sortfaID desc  ";


        $output = \DB::select($qry);

        return $output;
    }

    private function getAssetRegisterGroupedDetailFinalArray($output, $companyCurrency){
        $finalArr = [];
        $totalArray = [];

        $totCOSTUNIT = 0;
        $totcostUnitRpt = 0;
        $totdepAmountLocal = 0;
        $totdepAmountRpt = 0;
        $totlocalnbv = 0;
        $totrptnbv = 0;

        if ($output) {

            foreach ($output as $val) {     // for getting final toatal
                $totlocalnbv += $val->localnbv;
                $totCOSTUNIT += $val->COSTUNIT;
                $totcostUnitRpt += $val->costUnitRpt;
                $totdepAmountRpt += $val->depAmountRpt;
                $totdepAmountLocal += $val->depAmountLocal;
                $totrptnbv += $val->rptnbv;
            }

            $outputArr= collect($output)->groupBy(['financeCatDescription','groupbydesc']);

            $subkeys = [];
            foreach ($outputArr as $key => $masterArray){
                $subkeys[$key] = $masterArray->keys();
            }

            // setting final array
            foreach ($subkeys as $key => $value){

                foreach ($value as $v){

                    foreach ($output as $x){
                        $x->isHeader = false;
                        if($v != '' && $v == $x->faCode){

                            $finalArr[$key][$v][] = $x;     // get main asset detail master code empty

                            $finalArr[$key][$v][] = clone $x;   // duplicate main asset detail

                        }elseif($key==$x->financeCatDescription && $v==$x->groupbydesc){

                            if($v == ''  && in_array($x->faCode,$value->toArray())){    // check main asset details are on emty master code array
                                continue;                       // remove main asset detail from master code empty
                            }
                            $finalArr[$key][$v][] = $x;
                        }

                    }
                }

            }

            // get main asset wise sum
            foreach ( $finalArr as $masterKey=> $value){


                foreach ($value as $key => $val) {

                    if($key != '' ){

                        $COSTUNIT = 0;
                        $costUnitRpt = 0;
                        $depAmountLocal = 0;
                        $depAmountRpt = 0;
                        $localnbv = 0;
                        $rptnbv = 0;
                        foreach ($val as $no => $array){

                            if($array->groupbydesc == '' && $no==0) {
                                continue;
                            }
                            $COSTUNIT +=  $array->COSTUNIT;
                            $costUnitRpt +=  $array->costUnitRpt;
                            $depAmountLocal +=  $array->depAmountLocal;
                            $depAmountRpt +=  $array->depAmountRpt;
                            $localnbv +=  $array->localnbv;
                            $rptnbv +=  $array->rptnbv;


                        }

                        $totalArray[$masterKey][$key]['COSTUNIT'] = $COSTUNIT;
                        $totalArray[$masterKey][$key]['costUnitRpt'] = $costUnitRpt;
                        $totalArray[$masterKey][$key]['depAmountLocal'] = $depAmountLocal;
                        $totalArray[$masterKey][$key]['depAmountRpt'] = $depAmountRpt;
                        $totalArray[$masterKey][$key]['localnbv'] = $localnbv;
                        $totalArray[$masterKey][$key]['rptnbv'] = $rptnbv;

                    }

                }

            }

            // set main asset wise sum
            foreach ($totalArray as $masterKey => $masterValue){

                foreach ($masterValue as $mainAsset => $sumArray){


                    if (isset($finalArr[$masterKey][$mainAsset][0]) && $finalArr[$masterKey][$mainAsset][0]->groupTO ==''){

                        $finalArr[$masterKey][$mainAsset][0]->COSTUNIT = isset($totalArray[$masterKey][$mainAsset]['COSTUNIT'])?$totalArray[$masterKey][$mainAsset]['COSTUNIT']:0;
                        $finalArr[$masterKey][$mainAsset][0]->costUnitRpt = isset($totalArray[$masterKey][$mainAsset]['costUnitRpt'])?$totalArray[$masterKey][$mainAsset]['costUnitRpt']:0;
                        $finalArr[$masterKey][$mainAsset][0]->depAmountLocal = isset($totalArray[$masterKey][$mainAsset]['depAmountLocal'])?$totalArray[$masterKey][$mainAsset]['depAmountLocal']:0;
                        $finalArr[$masterKey][$mainAsset][0]->depAmountRpt = isset($totalArray[$masterKey][$mainAsset]['depAmountRpt'])?$totalArray[$masterKey][$mainAsset]['depAmountRpt']:0;
                        $finalArr[$masterKey][$mainAsset][0]->localnbv = isset($totalArray[$masterKey][$mainAsset]['localnbv'])?$totalArray[$masterKey][$mainAsset]['localnbv']:0;
                        $finalArr[$masterKey][$mainAsset][0]->rptnbv = isset($totalArray[$masterKey][$mainAsset]['rptnbv'])?$totalArray[$masterKey][$mainAsset]['rptnbv']:0;
                        $finalArr[$masterKey][$mainAsset][0]->isHeader = true;
                    }


                }
            }

        }
        $localDecimalPlace = isset($companyCurrency->localcurrency->DecimalPlaces) ? $companyCurrency->localcurrency->DecimalPlaces: 3;
        $rptDecimalPlace = isset($companyCurrency->reportingcurrency->DecimalPlaces) ? $companyCurrency->reportingcurrency->DecimalPlaces: 2;

        return array(
            'reportData' => $finalArr,
            'localnbv' => $totlocalnbv,
            'rptnbv' => $totrptnbv,
            'localDecimal' => $localDecimalPlace,
            'rptDecimal' => $rptDecimalPlace,
            'COSTUNIT' => $totCOSTUNIT,
            'costUnitRpt' => $totcostUnitRpt,
            'depAmountLocal' => $totdepAmountLocal,
            'depAmountRpt' => $totdepAmountRpt
        );
    }
}

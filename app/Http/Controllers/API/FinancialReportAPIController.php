<?php

/**
 * =============================================
 * -- File Name : FinancialReportAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Report
 * -- Author : Nazir
 * -- Create date : 05 - July 2018
 * -- Description : This file contains the all the report generation code
 * -- Date: 06-july 2018 By: Fayas Description: Added new functions named as getTrialBalance()
 * -- Date: 11-july 2018 By: Fayas Description: Added new functions named as getTrialBalanceDetails()
 * -- Date: 13-july 2018 By: Fayas Description: Added new functions named as getTrialBalanceCompanyWise()
 * -- Date: 07-january 2018 By: Fayas Description: Added new functions named as getAFRFilterChartOfAccounts()
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;
use App\Exports\GeneralLedger\Financials\ExcelColumnFormat;
use App\Exports\GeneralLedger\GeneralLedger\GeneralLedgerReport;
use App\Models\AccountsPayableLedger;
use App\Models\CustomReportColumns;
use App\Models\GroupCompanyStructure;
use App\Models\Tax;
use App\Models\GroupParents;
use App\Models\ReportCustomColumn;
use App\Services\ConsolidationReportService;
use App\Services\Currency\CurrencyService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\helper\Helper;
use App\Jobs\Report\GeneralLedgerPdfJob;
use App\Http\Controllers\AppBaseController;
use App\Models\AccountsType;
use App\Models\BookInvSuppDet;
use Illuminate\Support\Str;
use App\Models\ChartOfAccount;
use App\Models\ChartOfAccountsAssigned;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\CompanyFinanceYear;
use App\Models\Contract;
use App\Models\CurrencyMaster;
use App\Models\ExpenseClaimType;
use App\Models\GeneralLedger;
use App\Models\Months;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateColumnLink;
use App\Models\ReportTemplateColumns;
use App\Models\ReportTemplateDetails;
use App\Models\ReportTemplateDocument;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateNumbers;
use App\Models\SegmentMaster;
use App\Models\BudgetConsumedData;
use App\Models\ProjectGlDetail;
use App\Models\ErpProjectMaster;
use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\helper\CreateExcel;
use App\Models\BookInvSuppMaster;
use App\Models\Employee;
use Illuminate\Support\Facades\Storage;
use App\Models\SystemGlCodeScenarioDetail;
use App\Services\Excel\ExportGeneralLedgerReportService;
ini_set('max_execution_time', 500);
use App\Models\ReportTemplateEquity;
use App\Models\SupplierAssigned;

class FinancialReportAPIController extends AppBaseController
{
    protected $globalFormula; //keep whole formula ro replace
    protected $subAssociateJVCompanies = []; 
    protected $accJvCompanies = []; //keep whole formula ro replace

    public function getFRFilterData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $company = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup', 0)->get();

        $companyFinanceYear = CompanyFinanceYear::select(DB::raw("companyFinanceYearID,isCurrent,CONCAT(DATE_FORMAT(bigginingDate, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(endingDate, '%d/%m/%Y')) as financeYear,bigginingDate,endingDate"));
        $companyFinanceYear = $companyFinanceYear->whereIn('companySystemID', $companiesByGroup);
        if (isset($request['type']) && $request['type'] == 'add') {
            $companyFinanceYear = $companyFinanceYear->where('isActive', -1);
        }
        $companyFinanceYear = $companyFinanceYear->groupBy('bigginingDate')->orderBy('bigginingDate', 'DESC')->get();

        $departments1 = collect(\Helper::getCompanyServicelineWithMaster($selectedCompanyId));
      

        $departments2Info = DB::table('serviceline')->selectRaw('serviceline.companySystemID,serviceline.serviceLineSystemID,serviceline.ServiceLineCode,serviceline.serviceLineMasterCode,CONCAT(case when serviceline.masterID IS NULL then serviceline.ServiceLineCode else parents.ServiceLineCode end," - ",serviceline.ServiceLineDes) as ServiceLineDes')
                                            ->leftJoin('serviceline as parents', 'serviceline.masterID', '=', 'parents.serviceLineSystemID')
                                            ->leftJoin('service_line_assigned', 'serviceline.serviceLineSystemID', '=', 'service_line_assigned.serviceLineSystemID')
                                            ->where('serviceline.approved_yn', 1)
                                            ->whereIn('service_line_assigned.companySystemID', $companiesByGroup)
                                            ->where('service_line_assigned.isActive', 1)
                                            ->where('service_line_assigned.isAssigned', 1)
                                            ->where('serviceline.serviceLineSystemID', 24)
                                            ->where('serviceline.isFinalLevel', 1)
                                            ->where('serviceline.isDeleted', 0)
                                            ->get();
        $departments2 = collect($departments2Info);
        $segmentX = SegmentMaster::getSegmentX();
        $departments = $departments1
            ->merge($departments2)
            ->merge($segmentX)
            ->all();

        $supplierData = SupplierAssigned::whereIn('companySystemID', $companiesByGroup)
                                        ->where('isAssigned', -1)
                                        ->get();

        $controlAccount = ChartOfAccountsAssigned::leftJoin('chartofaccounts', 'chartofaccountsassigned.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
        ->whereIn('chartofaccountsassigned.companySystemID', $companiesByGroup)
        ->get([
            'chartofaccountsassigned.chartOfAccountSystemID',
            'chartofaccountsassigned.AccountCode',
            'chartofaccountsassigned.AccountDescription',
            'chartofaccountsassigned.catogaryBLorPL',
            \DB::raw('COALESCE(chartofaccounts.is_retained_earnings, 0) as is_retained_earnings')
        ]);

        $contracts = Contract::whereIN('companySystemID', $companiesByGroup)->get(['contractUID', 'ContractNumber', 'contractDescription']);

        $accountType = AccountsType::all();

        $templateType = ReportTemplate::where('isActive', 1)
            ->whereIN('companySystemID', $companiesByGroup)
            ->get();


        $financePeriod = CompanyFinancePeriod::select(DB::raw("companyFinancePeriodID,isCurrent,CONCAT(DATE_FORMAT(dateFrom, '%d/%m/%Y'), ' | ' ,DATE_FORMAT(dateTo, '%d/%m/%Y')) as financePeriod,companyFinanceYearID,dateFrom,dateTo"));
        $financePeriod = $financePeriod->whereIN('companySystemID', $companiesByGroup);
        $financePeriod = $financePeriod->where('departmentSystemID', 5);
        if (isset($request['type']) && $request['type'] == 'add') {
            $financePeriod = $financePeriod->where('isActive', -1);
        }
        $financePeriod = $financePeriod->groupBy('dateFrom')->get();

        $groupCompanies = Company::whereHas('allSubAssociateJVCompanies')->get();
        $generalColumns = [1, 2, 3, 6, 9,10,11, 16,17,26,29,19,20,23,30,34];
        $defaultSelectedColumns = [1,2,3,6,9,11,29,30,23];

        if ($request->selectionType == 1) {
            $generalColumns = array_merge($generalColumns, [27,28,4, 5, 7, 8, 15]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [4,5,7,8]);
        }

        if ($request->selectionType == 2) {
            $generalColumns = array_merge($generalColumns, [4, 5 , 12, 13, 14]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [4, 5, 12, 13, 14]);

        }

        if ($request->selectionType == 3) {
            $generalColumns = array_merge($generalColumns, [32,33,15,27,28]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [32,33]);

        }

        if ($request->selectionType == 4) {
            // debit note
            $generalColumns = array_merge($generalColumns, [27,28,30]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [7,8]);

        }
        if ($request->selectionType == 5) {
            $generalColumns = array_merge($generalColumns, [12, 13,14]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [12,13,14]);

        }
        

        if (isset($request->reportViewID) && $request->reportViewID == 1) {
            if ($request->selectionType == 1) {
                $generalColumns = array_merge($generalColumns, [31]);
            }

            $columnIds = ReportCustomColumn::where('isActive', 1)
                ->whereIn('id', $generalColumns)
                ->pluck('id')
                ->toArray();
        } else {
            $generalColumns = array_merge($generalColumns, [21, 23, 24,29,30]);
            $defaultSelectedColumns = array_merge($defaultSelectedColumns, [21, 24]);
            if ($request->selectionType == 1 || $request->selectionType == 3) {
                $generalColumns = array_merge($generalColumns, [24,31]);
            }

            $columnIds = ReportCustomColumn::where('isActive', 1)
                ->whereIn('id', $generalColumns)
                ->pluck('id')
                ->toArray();
        }


        $columns = DB::table('report_custom_columns')
            ->where('isActive',true)
            ->whereIn('id',$columnIds)
            ->get(['id','column_name']);

        $output = array(
            'companyFinanceYear' => $companyFinanceYear,
            'departments' => $departments,
            'supplierData' => $supplierData,
            'controlAccount' => $controlAccount,
            'contracts' => $contracts,
            'accountType' => $accountType,
            'groupCompanies' => $groupCompanies,
            'templateType' => $templateType,
            'segment' => $departments,
            'company' => $company,
            'financePeriod' => $financePeriod,
            'companiesByGroup' => $companiesByGroup,
            'isGroup' => $isGroup,
            'columns' => $columns,
            'defaultSelectedColumns' => ReportCustomColumn::whereIn('id',$defaultSelectedColumns)->get()
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getUtilizationFilterFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $company = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup', 0)->get();


        $departments1 = collect(\Helper::getCompanyServiceline($selectedCompanyId));
        $departments2 = collect(SegmentMaster::where('serviceLineSystemID', 24)->get());
        $departments = $departments1->merge($departments2)->all();

        $segment = SegmentMaster::where('isActive', 1)
                                ->approved()
                                ->withAssigned($companiesByGroup)
                                ->get();
        $segmentX = SegmentMaster::getSegmentX();
        $segment = $segment->merge($segmentX)->values();


        $output = array(
            'departments' => $departments,
            'segment' => $segment,
            'company' => $company,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getSubsidiaryCompanies(Request $request)
    {
        $input = $request->all();

        $companies = Company::where('companySystemID', $input['companySystemID'])->with(['allSubAssociateJVCompanies'])->first();

        if(!empty($companies)){
            $this->subAssociateJVCompanies[] = $input['companySystemID'];
        }
        if ($companies && count($companies->allSubAssociateJVCompanies) > 0) {
            $this->getSubSubsidiaryCompanies($companies->allSubAssociateJVCompanies);
        }

        $companiesData = Company::whereIn('companySystemID', $this->subAssociateJVCompanies)->get();
        return $this->sendResponse($companiesData, "companies retrived successfully");        
    }

    public function getSubSubsidiaryCompanies($subsidiary_companies)
    {
        foreach ($subsidiary_companies as $key => $value) {
            $this->subAssociateJVCompanies[] = $value->companySystemID;

            $companies = Company::where('companySystemID', $value->companySystemID)->with(['allSubAssociateJVCompanies'])->whereHas('allSubAssociateJVCompanies')->first();

            if ($companies && count($companies->allSubAssociateJVCompanies) > 0) {
                $this->getSubSubsidiaryCompanies($companies->allSubAssociateJVCompanies);
            }
        }
    }

    public function getAssociateJvCompanies($groupCompanySystemID)
    {
        $input = count($groupCompanySystemID) > 0 ? $groupCompanySystemID[0] : [];
        $companiesData = [];
        if (isset($input['companySystemID'])) {
            $companies = Company::where('companySystemID', $input['companySystemID'])->with(['accosiate_jv_companies', 'subsidiary_companies'])->first();
            
            if ($companies && count($companies->accosiate_jv_companies) > 0) {
                $this->getSubAssociateJvCompanies($companies->accosiate_jv_companies);
            }

            if ($companies && count($companies->subsidiary_companies) > 0) {
                $this->getSubSubsidaryCompanies($companies->subsidiary_companies);
            }

            $companiesData = Company::whereIn('companySystemID', $this->accJvCompanies)->get();
        }

        return $companiesData;        
    }

    public function getSubAssociateJvCompanies($accosiate_jv_companies)
    {
        foreach ($accosiate_jv_companies as $key => $value) {
            $this->accJvCompanies[] = $value->companySystemID;

            $companies = Company::where('companySystemID', $value->companySystemID)->with(['accosiate_jv_companies'])->whereHas('accosiate_jv_companies')->first();

            if ($companies && count($companies->accosiate_jv_companies) > 0) {
                $this->getSubAssociateJvCompanies($companies->accosiate_jv_companies);
            } 
        }
    }

    public function getSubSubsidaryCompanies($subsidiary_companies)
    {
        foreach ($subsidiary_companies as $key => $value) {
            $this->accJvCompanies[] = $value->companySystemID;

            $companies = Company::where('companySystemID', $value->companySystemID)->with(['subsidiary_companies'])->whereHas('subsidiary_companies')->first();

            if ($companies && count($companies->subsidiary_companies) > 0) {
                $this->getSubSubsidaryCompanies($companies->subsidiary_companies);
            } 
        }
    }

    public function getAFRFilterChartOfAccounts(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if (\Helper::checkIsCompanyGroup($selectedCompanyId)) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $input = $request->all();
        $inCategoryBLorPLID = [];

        if (isset($input['isBS']) && $input['isBS'] == 'true') {
            array_push($inCategoryBLorPLID, 1);
        }

        if (isset($input['isPL']) && $input['isPL'] == 'true') {
            array_push($inCategoryBLorPLID, 2);
        }

        if (count($inCategoryBLorPLID) == 0) {
            $inCategoryBLorPLID = [1, 2];
        }


        $controlAccount = ChartOfAccountsAssigned::leftJoin('chartofaccounts', 'chartofaccountsassigned.chartOfAccountSystemID', '=', 'chartofaccounts.chartOfAccountSystemID')
            ->whereIn('chartofaccountsassigned.companySystemID', $companiesByGroup)
            ->whereIN('chartofaccountsassigned.catogaryBLorPLID', $inCategoryBLorPLID)
            ->get([
                'chartofaccountsassigned.chartOfAccountSystemID',
                'chartofaccountsassigned.AccountCode',
                'chartofaccountsassigned.AccountDescription',
                'chartofaccountsassigned.catogaryBLorPL',
                \DB::raw('COALESCE(chartofaccounts.is_retained_earnings, 0) as is_retained_earnings')
            ]);

        $output = array(
            'controlAccount' => $controlAccount
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateFRReport(Request $request)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'companyFinanceYearID' => 'required'
                ]);

                $companyFinanceYearID = $request->companyFinanceYearID;

                $companyFinanceYear = CompanyFinanceYear::where("companyFinanceYearID", $companyFinanceYearID)->first();


                if (empty($companyFinanceYear)) {
                    return $this->sendError('Finance Year Not Found');
                }

                $bigginingDate = (new Carbon($companyFinanceYear->bigginingDate))->format('Y-m-d');
                $endingDate = (new Carbon($companyFinanceYear->endingDate))->format('Y-m-d');

                $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
                $toDate = (new   Carbon($request->toDate))->format('Y-m-d');


                if (!($fromDate >= $bigginingDate) || !($fromDate <= $endingDate)) {
                    return $this->sendError('From Date not between Financial year !', 500);
                } else if (!($toDate >= $bigginingDate) || !($toDate <= $endingDate)) {
                    return $this->sendError('To Date not between Financial year !', 500);
                } else if ($fromDate > $toDate) {
                    return $this->sendError('The To date must be greater than the From date !', 500);
                }

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if ($request->reportTypeID == 'FTBM') {

                    $validator1 = \Validator::make($request->all(), [
                        'currencyID' => 'required',
                    ]);

                    if ($validator1->fails()) {
                        return $this->sendError($validator1->messages(), 422);
                    }
                }

                break;

            case 'FGL':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'glCodes' => 'required',
                    'departments' => 'required',
                    // 'contracts' => 'required'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'FTD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required|date',
                    'toDate' => 'required|date|after_or_equal:fromDate'
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                if(!isset($request->reportViewID) || $request->reportViewID == 0)
                {
                    return $this->sendError('Please select a report type');
                }

                if (!isset($request->tempType) || $request->tempType == 0) {
                    return $this->sendError('Please select a document type');
                }


                break;
            case 'FCT':
                $validator = \Validator::make($request->all(), [
                    'accountType' => 'required',
                    'companyFinanceYearID' => 'required',
                    'templateType' => 'required',
                    'companySystemID' => 'required',
                    'serviceLineSystemID' => 'required_if:accountType,2|nullable',
                    'currency' => 'required',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                    'month' => 'required_if:dateType,2',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                $input = $request->all();

                $reportTemplate = ReportTemplate::where('companyReportTemplateID', $input['templateType'])->first();

                $checkDetails = ReportTemplateDetails::where('companyReportTemplateID', $input['templateType'])->exists();

                if (!$checkDetails && $input['accountType'] != 4) {
                    return $this->sendError("Report rows are not configured");
                }

                $checkColoumns = ReportTemplateColumnLink::where('templateID', $input['templateType'])->get();

                if($reportTemplate->isConsolidation == 1 && $reportTemplate->reportID == 1) {
                    if($reportTemplate->columnTemplateID == null) {
                        if(count($checkColoumns) > 0) {
                            $requiredColumns = ["CONS", "ELMN", "CMB"];
                            $missingColumns = array_values(array_diff($requiredColumns, $checkColoumns->pluck('shortCode')->toArray()));
                            if(count($missingColumns) > 0) {
                                $columnData = ReportTemplateColumns::whereIn('shortCode', $missingColumns)->pluck('description')->toArray();
                                if(count($columnData) > 0) {
                                    return $this->sendError("Column configuration not completed, assign " . join(",",$columnData) . " Column/s.");
                                }
                            }
                        }
                        else {
                            return $this->sendError("Column configuration not completed, assign Combined, Elimination and Consolidation Columns or a Template.");
                        }
                    }
                }
                else {
                    if ((count($checkColoumns) == 0) && $input['accountType'] != 4) {
                        return $this->sendError("Report columns are not configured");
                    }
                }

                $checkRows = ReportTemplateEquity::where('companySystemID', $input['selectedCompanyID'])->where('templateMasterID', $input['templateType'])->first();

                if (!$checkRows && $input['accountType'] == 4) {
                    return $this->sendError("Report columns are not configured");
                }

                $isRetaineGlExists =  ReportTemplateLinks::where('companySystemID', $input['selectedCompanyID'])->where('templateMasterID', $input['templateType'])->whereHas('chartofaccount',function($q){
                    $q->where('is_retained_earnings',1);
                  })->first();

                  if(!$isRetaineGlExists && $input['accountType'] == 4)
                  {
                    return $this->sendError("Retained Earnings GL not assigned to template.");
                  }

                break;
            case 'JVD':
                $validator = \Validator::make($request->all(), [
                    'companySystemID' => 'required',
                    'jvType' => 'required|array',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            case 'BCD':
                $validator = \Validator::make($request->all(), [
                    'reportTypeID' => 'required',
                    'fromDate' => 'required',
                    'toDate' => 'required|date|after_or_equal:fromDate',
                    'glCodes' => 'required',
                    'currencyID' => 'required',
                    'selectedServicelines' => 'required',
                    // 'contracts' => 'required'
                ]);
            case 'RTD':
                    $validator = \Validator::make($request->all(), [
                        'reportTypeID' => 'required',
                        'fromDate' => 'required',
                        'toDate' => 'required|date|after_or_equal:fromDate',
                        'currencyID' => 'required',
                        'suppliers' => 'required',
                    ],[
                        'currencyID.required' => 'The currency field is required.',
                    ]);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function validatePUReport(Request $request)
    {
        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');

        if ($fromDate > $toDate) {
            return $this->sendError('The To date must be greater than the From date !', 500);
        }

        $projectID = $request->projectID;
        $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();

        $budgetConsumedData = BudgetConsumedData::with('purchase_order')->where('projectID', $projectID)->where('documentSystemID', 2)->get();


        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            })
            ->sum('consumedRptAmount');

        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->where('documentSystemID', 2)
            ->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                $query->whereDate('approvedDate', '<', $fromDate);
            })
            ->sum('consumedRptAmount');

            $detailsPOWise = BudgetConsumedData::with(['purchase_order_detail' => function ($query) use ($fromDate, $toDate) {
                $query->whereBetween('approvedDate', [$fromDate, $toDate]);
            }])
                ->whereHas('purchase_order_detail', function ($query) use ($fromDate, $toDate) {
                    $query->whereBetween('approvedDate', [$fromDate, $toDate]);
                })
                ->where('projectID', $projectID)
                ->where('documentSystemID', 2)
                ->selectRaw('sum(consumedRptAmount) as documentAmount, documentCode, documentSystemCode')
                ->groupBy('documentSystemCode')
                ->get();

        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');

        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;


        $closingBalance = $openingBalance - $budgetAmount;

        $output = array(
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function generateprojectUtilizationReport(Request $request)
    {
        $documentSystemIDs = [2, 3, 4, 18, 21, 19, 15, 17, 11];
        $dateFrom = (new Carbon($request->fromDate))->format('d/m/Y');
        $dateTo = (new Carbon($request->toDate))->format('d/m/Y');

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');

        $startDay = Carbon::parse($fromDate)->startOfDay(); // Sets time to 00:00:00
        $endDay = Carbon::parse($toDate)->endOfDay();      // Sets time to 23:59:59

        $projectID = $request->projectID;
        $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();
        $serviceline = collect($request->selectedServicelines)->pluck('serviceLineSystemID')->toArray();
        
        $companySystemID = $projectDetail['companySystemID'];
        $transactionCurrencyID = $projectDetail->currency['currencyID'];
        $documentCurrencyID = $projectDetail->currency['currencyID'];
        $reportingCurrency = Company::with('reportingcurrency')->where('companySystemID',$companySystemID)->first();

        $budgetConsumedData = BudgetConsumedData::with(['purchase_order',
                                                        'debit_note', 
                                                        'credit_note', 
                                                        'direct_payment_voucher', 
                                                        'grv_master', 
                                                        'jv_master',
                                                        'supplier_invoice_master' => function ($query) {
                                                                $query->select('bookingSuppMasInvAutoID', 'comments', 'bookingDate');
                                                            },
                                                        ])
                                                    ->where('projectID', $projectID)
                                                    ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                                                        $query->whereIn('serviceLineSystemID', $serviceline);
                                                    })
                                                    ->whereIn('documentSystemID', $documentSystemIDs)->get();

        $detailsPOWise = BudgetConsumedData::with(['segment_by','chart_of_account','purchase_order_detail' => function ($query) use ($startDay, $endDay) {
                $query->whereBetween('approvedDate', [$startDay, $endDay]);
                }, 
                'debit_note_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                }, 
                'credit_note_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                }, 
                'direct_payment_voucher_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                }, 
                'grv_master_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                }, 
                'jv_master_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                }, 
                'supplier_invoice_master' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay])
                            ->select('bookingSuppMasInvAutoID', 'comments', 'bookingDate');
                }
            ])

            ->where(function($subQuery) use ($startDay, $endDay)
            {   
                $subQuery->whereHas('purchase_order_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('approvedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('debit_note_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('credit_note_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('direct_payment_voucher_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('grv_master_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                })
                ->orWhereHas('jv_master_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay]);
                });
            })
            ->where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->get();

        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->where(function($subQuery) use ($startDay, $endDay)
            {   
                $subQuery->whereHas('purchase_order', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('approvedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('debit_note', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('credit_note', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('direct_payment_voucher', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('grv_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                })
                ->orWhereHas('jv_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay]);
                });
            })

            ->sum('consumedRptAmount');



        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->where(function($subQuery) use ($fromDate, $toDate)
            {   
                $subQuery->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('approvedDate', '<', $fromDate);
                })
                ->orWhereHas('debit_note', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('debitNoteDate', '<', $fromDate);
                })
                ->orWhereHas('credit_note', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('creditNoteDate', '<', $fromDate);
                })
                ->orWhereHas('direct_payment_voucher', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('postedDate', '<', $fromDate);
                })
                ->orWhereHas('grv_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('grvDate', '<', $fromDate);
                })
                ->orWhereHas('jv_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('JVdate', '<', $fromDate);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('bookingDate', '<', $fromDate);
                });
            })
            ->sum('consumedRptAmount');

        
        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');
        $getProjectAmountsCurrencyConvertion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $documentCurrencyID, $projectAmount);
        $projectAmount = $getProjectAmountsCurrencyConvertion['reportingAmount'];

        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;


        $closingBalance = $openingBalance - $budgetAmount;

        $output = array(
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise,
            'companyReportingCurrency' => $reportingCurrency,
            'fromDate' => $dateFrom,
            'toDate' => $dateTo,
            'reportTittle' => 'Project Utilization Report'
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function generateEmployeeLedgerReport(Request $request) {

        $input = $request->all();

        $fromDate = Carbon::parse($input['fromDate'])->format('Y-m-d');

        $toDate = Carbon::parse($input['toDate'])->format('Y-m-d');
        
        $companyID = $input['comapnyID'];

        $isGroup = \Helper::checkIsCompanyGroup($companyID);
        if ($isGroup) {
            $childCompanies = \Helper::getGroupCompany($companyID);
        } else {
            $childCompanies = [$companyID];
        }

        $currencyID = $input['currencyID'];

        if(!$companyID) {
            return $this->sendError('Company ID not found');
        }

        // Retrieve company currency information
        $companyCurrency = \Helper::companyCurrency($companyID);
        $companyName =  $companyCurrency->CompanyName;
        $currencyCodeLocal = $companyCurrency->localcurrency->CurrencyCode;
        $currencyCodeRpt = $companyCurrency->reportingcurrency->CurrencyCode;
        $currencyDecimalLocal = $companyCurrency->localcurrency->DecimalPlaces;
        $currencyDecimalRpt = $companyCurrency->reportingcurrency->DecimalPlaces;

        // Fetch employee data
        if(isset($input['all_employee']) && $input['all_employee']){
            $employeeDatas = Employee::leftJoin('erp_bookinvsuppmaster', function ($join) use ($childCompanies){
                $join->on('employees.employeeSystemID', '=', 'erp_bookinvsuppmaster.employeeID')
                     ->where('erp_bookinvsuppmaster.documentType', 4)
                     ->where('erp_bookinvsuppmaster.approved', -1)
                     ->whereIn('erp_bookinvsuppmaster.companySystemID', $childCompanies);
            })
            ->leftJoin('erp_paysupplierinvoicemaster', function ($join) use ($childCompanies){
                $join->on('employees.employeeSystemID', '=', 'erp_paysupplierinvoicemaster.directPaymentPayeeEmpID')
                     ->where('erp_paysupplierinvoicemaster.invoiceType', 7)
                     ->where('erp_paysupplierinvoicemaster.approved', -1)
                     ->whereIn('erp_paysupplierinvoicemaster.companySystemID', $childCompanies);
            })
            ->where(function ($query) {
                $query->whereNotNull('erp_bookinvsuppmaster.employeeID')
                      ->orWhereNotNull('erp_paysupplierinvoicemaster.directPaymentPayeeEmpID');
            })
            ->groupBy('employees.employeeSystemID')->pluck('employees.employeeSystemID');
        }
        else{
            if (array_key_exists('employeeID', $input)) {
                $employeeDatas = (array)$input['employeeID'];
                $employeeDatas = collect($employeeDatas)->pluck('employeeSystemID');
            }
        }

        if (array_key_exists('typeID', $input)) {
            $typeID = (array)$input['typeID'];
            $typeID = collect($typeID)->pluck('id');
        }

        $childCompanies = collect($childCompanies);

        $employees = collect($this->getGeneralLedgerSelectedEmployees($fromDate,$toDate,$typeID,$childCompanies,$employeeDatas));

        // Fetch general ledger query data
        $data = $this->getGeneralLedgerQueryData($fromDate,$toDate,$typeID,$childCompanies,$employees->pluck('employeeID'));

        $refAmounts = $this->getGeneralLedgerRefAmount();

        $refIouAmounts = DB::select("SELECT * FROM (SELECT
                   srp_erp_ioubookingmaster.companyLocalAmount as referenceAmountLocal,
                   srp_erp_ioubookingmaster.companyReportingAmount as referenceAmountRpt,
                   srp_erp_ioubookingmaster.bookingMasterID AS masterID,    
                   1 as refType
               FROM srp_erp_ioubookingmaster 
               WHERE srp_erp_ioubookingmaster.approvedYN = 1)As t2");

        $i = 0;
        $arrayTemp = array();
        $indexArray = array();

        $grandSumArray = [
            'grandSumLocal' => 0,
            'grandSumRpt' => 0,
            'grandRefSumLocal' => 0,
            'grandRefSumRpt' => 0
        ];

        foreach ($data as $da){
            $da->referenceAmountLocal = 0;
            $da->referenceAmountRpt = 0;
            $da->isLine = 0;
            $da->refType = 0;

            foreach($refAmounts as $amount) {
                if($da->masterID == $amount->masterID) {
                    if(($da->type == 1 || $da->type == 4) && $da->employeeID == $amount->employeeID){
                        $da->referenceAmountLocal = $amount->referenceAmountLocal;
                        $da->referenceAmountRpt = $amount->referenceAmountRpt;
                        $da->refType = $amount->refType;
                    }
                    if($da->type == 2) {
                        $da->referenceAmountLocal = $amount->referenceAmountLocal;
                        $da->referenceAmountRpt = $amount->referenceAmountRpt;
                        $da->refType = $amount->refType;
                    }
                }
            }

            foreach ($refIouAmounts as $iouAmount){
                if($da->masterID == $iouAmount->masterID && $da->type == 3) {
                    $da->referenceAmountLocal = $iouAmount->referenceAmountLocal;
                    $da->referenceAmountRpt = $iouAmount->referenceAmountRpt;
                }
            }

            // remove duplicate type 3 and update first type 3 values
            if($da->type == 3 && in_array($da->documentCode, $arrayTemp)) {
                $documentCode = $da->documentCode;
                $referenceDoc = $da->referenceDoc;
                $referenceDocDate = Carbon::parse($da->referenceDocDate)->format("d/m/Y");

                unset($data[$i]);

                $bookingMaster = DB::table('srp_erp_ioubookingmaster')->where('approvedYN',1)->where('bookingCode',$referenceDoc)->first();
                $refLocalAmount = $bookingMaster->companyLocalAmount ?? 0;
                $refRptAmount = $bookingMaster->companyReportingAmount ?? 0;

                foreach ($indexArray as $oldData) {
                    if($oldData['data'] == $documentCode){
                        $data[$oldData['index']]->referenceDoc = $data[$oldData['index']]->referenceDoc . ', ' . $referenceDoc;

                        try {
                            $data[$oldData['index']]->referenceDocDate = Carbon::parse($data[$oldData['index']]->referenceDocDate)->format("d/m/Y"). ', ' . $referenceDocDate;
                        } catch (\Exception $e) {
                            $data[$oldData['index']]->referenceDocDate = $data[$oldData['index']]->referenceDocDate. ', ' . $referenceDocDate;
                        }
                        $data[$oldData['index']]->referenceAmountLocal = $data[$oldData['index']]->referenceAmountLocal + $refLocalAmount;
                        $data[$oldData['index']]->referenceAmountRpt = $data[$oldData['index']]->referenceAmountRpt + $refRptAmount;
                        $data[$oldData['index']]->isLine = 1;
                    }
                }
            }
            else {
                $arrayTemp[] = $da->documentCode;

                $indexArray[] = [
                  'index' => $i,
                  'data' => $da->documentCode
                ];
            }

            $recordOwner = $employees->where('employeeID', $da->employeeID)->first();

            if(!isset($recordOwner->totalSumLocal)) {
                $recordOwner->totalSumLocal = 0;
                $recordOwner->totalSumRpt = 0;
                $recordOwner->totalSumRefReferenceAmountLocal = 0;
                $recordOwner->totalSumRefReferenceAmountRpt = 0;
            }

            if(!isset($recordOwner->isSetOpeningBalance)){
                $recordOwner->openingBalanceLocal = 0;
                $recordOwner->openingBalanceRpt = 0;
                $recordOwner->isSetOpeningBalance = true;
            }

            if($recordOwner->isSetOpeningBalance){
                $openingBalanceSum = $this->getOpeningBalanceData($fromDate,$typeID,$childCompanies,$da->employeeID);
                $recordOwner->openingBalanceLocal = $openingBalanceSum[0]->amountLocal ?: 0;
                $recordOwner->openingBalanceRpt = $openingBalanceSum[0]->amountRpt ?: 0;

                $recordOwner->totalSumLocal += $recordOwner->openingBalanceLocal;
                $recordOwner->totalSumRpt += $recordOwner->openingBalanceRpt;

                $grandSumArray['grandSumLocal'] += $recordOwner->openingBalanceLocal;
                $grandSumArray['grandSumRpt'] += $recordOwner->openingBalanceRpt;

                $recordOwner->isSetOpeningBalance = false;
            }

            if (($da->type == 7 || $da->type == 5 || $da->type == 6 || $da->type == 3) && $da->type != 2){
                // update each employee table total
                $recordOwner->totalSumLocal += $da->amountLocal * -1;
                $recordOwner->totalSumRpt += $da->amountRpt * -1;

                // calculate grand sum
                $grandSumArray['grandSumLocal'] += $da->amountLocal * -1;
                $grandSumArray['grandSumRpt'] += $da->amountRpt * -1;

                if ($da->refType == 1) {
                    // update each employee table total
                    $recordOwner->totalSumRefReferenceAmountLocal += $da->referenceAmountLocal;
                    $recordOwner->totalSumRefReferenceAmountRpt += $da->referenceAmountRpt;

                    // calculate grand sum ref
                    $grandSumArray['grandRefSumLocal'] += $da->referenceAmountLocal;
                    $grandSumArray['grandRefSumRpt'] += $da->referenceAmountRpt;
                }
                else{
                    // update each employee table total
                    $recordOwner->totalSumRefReferenceAmountLocal += $da->referenceAmountLocal * -1;
                    $recordOwner->totalSumRefReferenceAmountRpt += $da->referenceAmountRpt * -1;

                    // calculate grand sum ref
                    $grandSumArray['grandRefSumLocal'] += $da->referenceAmountLocal * -1;
                    $grandSumArray['grandRefSumRpt'] += $da->referenceAmountRpt * -1;
                }
            }
            else {
                if($da->type != 2){
                    // update each employee table total
                    $recordOwner->totalSumLocal += $da->amountLocal < 0 ? $da->amountLocal * -1 : $da->amountLocal;
                    $recordOwner->totalSumRpt += $da->amountRpt < 0 ? $da->amountRpt * -1 : $da->amountRpt;

                    $recordOwner->totalSumRefReferenceAmountLocal += $da->referenceAmountLocal < 0 ? $da->referenceAmountLocal * -1 : $da->referenceAmountLocal;
                    $recordOwner->totalSumRefReferenceAmountRpt += $da->referenceAmountRpt < 0 ? $da->referenceAmountRpt * -1 : $da->referenceAmountRpt;

                    // calculate grand sum
                    $grandSumArray['grandSumLocal'] += $da->amountLocal;
                    $grandSumArray['grandSumRpt'] += $da->amountRpt;

                    $grandSumArray['grandRefSumLocal'] += $da->referenceAmountLocal < 0 ? $da->referenceAmountLocal * -1 : $da->referenceAmountLocal;
                    $grandSumArray['grandRefSumRpt'] += $da->referenceAmountRpt < 0 ? $da->referenceAmountRpt * -1 : $da->referenceAmountRpt;
                }
            }

            $i++;
        }

        foreach ($employees as $value) {
            $recordOwner = $employees->where('employeeID', $value->employeeID)->first();

            if(!isset($recordOwner->totalSumLocal)) {
                $recordOwner->totalSumLocal = 0;
                $recordOwner->totalSumRpt = 0;
            }

            if(!isset($recordOwner->isSetOpeningBalance)){
                $recordOwner->openingBalanceLocal = 0;
                $recordOwner->openingBalanceRpt = 0;
                $recordOwner->isSetOpeningBalance = true;
            }

            if($recordOwner->isSetOpeningBalance){
                $openingBalanceSum = $this->getOpeningBalanceData($fromDate,$typeID,$childCompanies,$value->employeeID);
                $recordOwner->openingBalanceLocal = $openingBalanceSum[0]->amountLocal ?: 0;
                $recordOwner->openingBalanceRpt = $openingBalanceSum[0]->amountRpt ?: 0;

                $recordOwner->totalSumLocal += $recordOwner->openingBalanceLocal;
                $recordOwner->totalSumRpt += $recordOwner->openingBalanceRpt;

                $grandSumArray['grandSumLocal'] += $recordOwner->openingBalanceLocal;
                $grandSumArray['grandSumRpt'] += $recordOwner->openingBalanceRpt;

                $recordOwner->isSetOpeningBalance = false;
            }
        }

        // Re-index data array
        $data = array_values($data);

        if(isset($input['downloadReport']) && $input['downloadReport']){
            $currencyID = $currencyID[0] ?? $currencyID;

            $reportData = array(
                'companyName' => $companyName,
                'report_tittle' => 'Employee Ledger',
                'datas' => $data,
                'employees' => $employees,
                'currencyCodeLocal' => $currencyCodeLocal,
                'currencyCodeRpt' => $currencyCodeRpt,
                'currencyDecimalLocal' => $currencyDecimalLocal,
                'currencyDecimalRpt' => $currencyDecimalRpt,
                'fromDate' => $fromDate,
                'toDate' => $toDate,
                'currencyID' => $currencyID,
                'grandSumData' => $grandSumArray
            );
            
            $templateName = "export_report.employee_ledger_report";

            return \Excel::create('finance', function ($excel) use ($reportData, $templateName) {
                $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName) {
                    $sheet->loadView($templateName, $reportData);
                });
            })->download('xlsx');
        }
        else{
            return $this->sendResponse([
                $data,
                $employees,
                $currencyCodeLocal,
                $currencyCodeRpt,
                $currencyDecimalLocal,
                $currencyDecimalRpt,
                $grandSumArray
            ], 'Record retrieved successfully');
        }
    }

    public function generateCustomizedFRReport($request, $showZeroGL, $showRetained, $companyWiseTemplate = false)
    {


        if ($request->accountType == 1) { // if account type is BS and if any new chart of account created automatically link the gl account
            $detID = ReportTemplateDetails::ofMaster($request->templateType)->where('itemType', 4)->whereNotNull('masterID')->first();
            if (!empty($detID->detID) && !is_null($detID->detID)) {
                $notExistPLAccount = ChartOfAccount::where('isActive', 1)->where('isApproved', 1)->where('catogaryBLorPL', 'PL')->whereDoesntHave('templatelink', function ($query) use ($request, $detID) {
                    $query->where('templateMasterID', $request->templateType)->where('templateDetailID', $detID->detID);
                })->get();
                if (count($notExistPLAccount) > 0) {
                    $company = Company::find($request->selectedCompanyID);
                    if ($company) {
                        $data['companyID'] = $company->CompanyID;
                    }
                    foreach ($notExistPLAccount as $val) {
                        $data['templateMasterID'] = $request->templateType;
                        $data['templateDetailID'] = $detID->detID;
                        $data['sortOrder'] = 1;
                        $data['glAutoID'] = $val['chartOfAccountSystemID'];
                        $data['glCode'] = $val['AccountCode'];
                        $data['glDescription'] = $val['AccountDescription'];
                        $data['companySystemID'] = $val['selectedCompanyID'];
                        $data['createdPCID'] = gethostname();
                        $data['createdUserID'] = \Helper::getEmployeeID();
                        $data['createdUserSystemID'] = \Helper::getEmployeeSystemID();
                        ReportTemplateLinks::create($data);
                    }
                }
            }
        }

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);

        $company = Company::find($request->selectedCompanyID);
        $template = ReportTemplate::find($request->templateType);
        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $companyArray = $request->companySystemID ?? [];
        $segmentArray = $request->serviceLineSystemID ?? [];
        $currency = $request->currency[0] ?? $request->currency;
        $groupCompanySystemID = $request->groupCompanySystemID[0] ?? $request->groupCompanySystemID;
        

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $serviceLineIDs = array_map(function($item) {
            return $item['serviceLineSystemID'];
        }, $segmentArray);

        $month = '';
        $period = '';
        if ($request->dateType != 1) {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $month = Carbon::parse($toDate)->format('Y-m-d');
        }

        // get generated customize column query
        $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request, false, $companyWiseTemplate);
        $linkedcolumnQry = $generatedColumn['linkedcolumnQry']; // generated select statement
        $columnKeys = $generatedColumn['columnKeys'];
        $currencyColumn = $generatedColumn['currencyColumn']; // currency column whether local or reporting
        $columnHeader = $generatedColumn['columnHeader']; // column name with detail
        $columnHeaderMapping = $generatedColumn['columnHeaderMapping']; // column name
        $linkedcolumnQry2 = $generatedColumn['linkedcolumnQry2']; // generated select statement
        $budgetQuery = $generatedColumn['budgetQuery']; // generated select statement for budget query
        $budgetWhereQuery = $generatedColumn['budgetWhereQuery']; // generated select statement for budget query
        $cominedColumnKey = $generatedColumn['cominedColumnKey']; // generated select statement for budget query
        $CYYTDColumnKey = $generatedColumn['CYYTDColumnKey']; // generated select statement for budget query
        $CONSColumnKey = $generatedColumn['CONSColumnKey']; // generated select statement for budget query
        $columnTemplateID = $generatedColumn['columnTemplateID']; // customized coloumn from template

        // Main query
        $outputCollect = collect($this->getCustomizeFinancialRptQry($request, $linkedcolumnQry, $linkedcolumnQry2, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $showZeroGL, $cominedColumnKey));

        // Detail query
        $outputDetail = collect($this->getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $showZeroGL, $cominedColumnKey,$linkedcolumnQry2));

        // Generate consolidation data & merge with existing detail data
        $consolidationKeys = [];
        if($template->columnTemplateID == null && $template->isConsolidation == 1) {

            // Get consolidation column keys
            foreach ($columnHeaderMapping as $key => $value) {
                if (in_array($value,['CMB','ELMN','CONS'])) {
                    $consolidationKeys[] = $key;
                }
            }

            if(!empty($consolidationKeys)){
                // Get CMB, ELMN, CONS data
                $consolidationData = ConsolidationReportService::generateConsolidationReportData($request,$consolidationKeys);

                $outputDetail = $outputDetail->sortBy('glAutoID')->keyBy('glAutoID');

                // Replace consolidation values or if not exists add as new one
                $newDataForFinalReport = [];
                foreach ($consolidationData as $newData) {
                    if(!empty($outputDetail->get($newData->glAutoID))) {
                        foreach ($consolidationKeys as $key) {
                            try {
                                $outputDetail[$newData->glAutoID]->$key = $newData->$key;
                            }
                            catch (\Exception $e) {
                                Log::error($e->getMessage());
                            }
                        }
                    }
                    else {
                        $newDataForFinalReport[] = $newData;
                    }
                }

                // Reset keys, merge new dataset with old dataset and reorder
                $outputDetail = $outputDetail->values()->merge(collect($newDataForFinalReport))->sortBy('sortOrder');

                // fix sub-total level values
                $subTotalLevelTwo = $outputCollect->where('isFinalLevel', 1)->where('itemType', 2);

                if ($template->reportID == 1) {
                    // BS report retain earning total level
                    $subTotalLevelTwo = $subTotalLevelTwo->merge($outputCollect->where('isFinalLevel', 1)->where('itemType', 4));

                    $reportDataNetTotal = $outputCollect->where('netProfitStatus', 1)->where('itemType', 3)->first();
                    // Add net total hidden row for BS report if not available. (use this for calculate NCI & Share Holder amount finally)
                    if (empty($reportDataNetTotal)) {
                        $hiddenTotal = [
                            'detDescription' => 'Total',
                            'detID' => 0,
                            'isFinalLevel' => 0,
                            'sortOrder' => 10,
                            'masterID' => null,
                            'itemType' => 3,
                            'netProfitStatus' => 1,
                            'hideHeader' => 1,
                            'expanded' => 1,
                            'bgColor' => null,
                            'fontColor' => '#000000'
                        ];

                        foreach ($columnHeaderMapping as $key => $value) {
                            $hiddenTotal[$key] = $outputDetail->sum($key);
                        }
                        
                        $outputCollect->push((object) $hiddenTotal);
                    }
                }

                foreach ($subTotalLevelTwo as $levelTwo) {

                    $glCodesArray = [];
                    foreach (ReportTemplateDetails::find($levelTwo->detID)->gl_codes as $glCodeData) {
                        $glCodesArray[] = $glCodeData->glAutoID;
                    }

                    if(!empty($glCodesArray)) {
                        $outputData = $outputDetail->whereIn('glAutoID', $glCodesArray);

                        foreach ($consolidationKeys as $valueHolder) {
                            $value = $outputData->sum($valueHolder);
                            $levelTwo->$valueHolder = $value;
                        }
                    }
                }
            }
        }

        if((isset($request->reportID) && $request->reportID == "FCT") && $outputCollect)
        {
            $outputCollect->each(function ($item) use($outputDetail,$columnKeys) {
                $detID = ($item->detID) ?  : null;
                if($detID)
                {
                    $data = $outputDetail->filter(function($detail) use ($detID) {
                        return $detail->templateDetailID == $detID;
                    });

                    if($data->isNotEmpty() && (isset($item->itemType) && $item->itemType != 3))
                    {
                        collect($columnKeys)->each(function($colKey) use ($item,$data)
                        {
                            $key = explode('-',$colKey);
                            if(isset($key[0]) && in_array($key[0],["BCM","BYTD","BYTDFULL"]))
                                $item->$colKey = collect($data)->sum($colKey);
                        });
                    }

                    collect($columnKeys)->each(function($colKey) use ($item,$data)
                    {
                        if ((isset($item->itemType) && $item->itemType != 3)) {
                            $key = explode('-',$colKey);
                            if(isset($key[0]) && in_array($key[0],["BCM","BYTD","BYTDFULL"]))
                                $item->$colKey = collect($data)->sum($colKey);
                        }
                    });

                }

            });
        }


        $headers = $outputCollect->where('masterID', null)->sortBy('sortOrder')->values();
        $grandTotalUncatArr = [];
        $uncategorizeArr = [];
        $uncategorizeDetailArr = [];
        $grandTotal = [];
        if ($request->accountType == 1 || $request->accountType == 2) { // get uncategorized value
            $uncategorizeData = collect($this->getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $cominedColumnKey, $companyWiseTemplate));
            $grandTotal = collect($this->getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $cominedColumnKey));
            if ($uncategorizeData['output']) {
                foreach ($columnKeys as $key => $val) {
                    $uncategorizeArr[$val] = $uncategorizeData['output'][0]->$val;
                }
            }
            $uncategorizeDetailArr = $uncategorizeData['outputDetail'];
        } else {
            $grandTotal[0] = [];
        }

        if(!empty($grandTotal) && count($outputDetail) > 0)
        {
            $numericKeys = collect($outputDetail->first())
                ->keys()
                ->filter(function($key) { return Str::contains($key, '-'); });

            $grandTotalComputed = $numericKeys->mapWithKeys(function($key) use ($outputDetail) {
                return [$key => $outputDetail->sum($key)];
            });

           $grandTotal[0] = $grandTotalComputed;
        }

        $outputOpeningBalance = '';
        $outputOpeningBalanceArr = [];
        $outputClosingBalanceArr = [];
        if ($request->accountType == 3) { // if report is cash flow type get opening and closing balance
            $outputOpeningBalance = $this->getCashflowOpeningBalanceQry($request, $currencyColumn, $columnTemplateID);
            if ($columnTemplateID == null) {
                $outputOpeningBalance = !empty($outputOpeningBalance->openingBalance) ? $outputOpeningBalance->openingBalance : 0;

                $lastColumn = collect($headers)->last(); // considering net total
                foreach ($columnKeys as $key => $val) {
                    if ($key == 0) {
                        $outputOpeningBalanceArr[] = $outputOpeningBalance;
                        $outputClosingBalanceArr[] = $lastColumn->$val + $outputOpeningBalance;
                    } else {
                        $outputOpeningBalanceArr[] = $outputClosingBalanceArr[$key - 1];
                        $outputClosingBalanceArr[] = $lastColumn->$val + $outputClosingBalanceArr[$key - 1];
                    }
                }
            }
        }


        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $removedFromArray = [];
        $companyHeaderColumns = [];
        $serviceLineDescriptions = [];
        if ($columnTemplateID == 1 || $columnTemplateID == 2) {
            if ($request->accountType == 1 || $request->accountType == 2) {
                $companyWiseGrandTotal = ($columnTemplateID == 1) ? $grandTotal->groupBy('compID') : $grandTotal->groupBy('serviceLineID');
            } else {
                $companyWiseGrandTotal = [];
                $uncategorizeData = [];
            }
            $res = $this->processColumnTemplateData($headers, $outputCollect, $outputDetail, $columnKeys, $uncategorizeData, $companyWiseGrandTotal, $outputOpeningBalance, $request, $columnTemplateID, $showRetained);
            $headers = $res['headers'];
            $companyHeaderColumns = $res['companyHeaderColumns'];
            $uncategorizeDetailArr = $res['uncategorizeDetailArr'];
            $uncategorizeArr = $res['uncategorizeArr'];
            $companyWiseGrandTotalArray = $res['companyWiseGrandTotalArray'];
            $outputOpeningBalanceArr = $res['outputOpeningBalanceArr'];
            $outputClosingBalanceArr = $res['outputClosingBalanceArr'];
            $serviceLineDescriptions = $res['serviceLineDescriptions'];
            $firstLevel = $res['firstLevel'];
            $secondLevel = $res['secondLevel'];
            $thirdLevel = $res['thirdLevel'];
            $fourthLevel = $res['fourthLevel'];
        }
        else {
            $firstLevel = false;
            $secondLevel = false;
            $thirdLevel = false;
            $fourthLevel = false;
            $fifthLevel = false;

            if (count($headers) > 0) {
                foreach ($headers as $key => $val) {
                    if ($val->detID != 0) {
                        $details = $outputCollect->where('masterID', $val->detID)->sortBy('sortOrder')->values();
                        if($val->itemType == 3)
                        {
                            $detailsArray = array();
                            foreach (ReportTemplateDetails::find($val->detID)->gl_codes as $glCodeData)
                            {
                                $detailsArray[] = $glCodeData->subcategory->detID;
                            }

                            $outputData = $outputDetail->whereIn('templateDetailID', $detailsArray)->sortBy('sortOrder')->values();

                            $data =  $outputData
                                ->reduce(function ($carry, $item) use ($val) {
                                    foreach ($item as $key => $value) {
                                        if (strpos($key, '-') !== false) {
                                            $tempKeyValue = explode('-', $key);
                                            $columnData = ReportTemplateColumns::where('shortCode', $tempKeyValue[0])->first();
                                            if (isset($columnData) && !in_array($columnData->type, [4,5]) && is_numeric($value)) {
                                                $carry[$key] = ($carry[$key] ?? 0) + $value;
                                            }
                                        }

                                    }
                                    return $carry;
                                }, []);

                            foreach ($data as $key => $value) {
                                if (collect($val)->contains($key)) {
                                    $val->$key = $value;
                                }
                            }

                        }else {
                            $val->detail = $details;
                        }
                        $firstLevel = true;
                        foreach ($details as $key2 => $val2) {
                            if ($val2->isFinalLevel == 1) {
                                if($val2->itemType == 3)
                                {
                                    $glCodesArray = array();
                                    foreach (ReportTemplateDetails::find($val2->detID)->gl_codes as $glCodeData)
                                    {
                                        $glCodesArray[] = $glCodeData->subcategory->detID ?? null;
                                    }
                                    if(!empty($glCodesArray))
                                    {
                                        $outputData = $outputDetail->whereIn('templateDetailID', $glCodesArray)->sortBy('sortOrder')->values();
                                        $data =  $outputData
                                            ->reduce(function ($carry, $item) use ($val2) {
                                                foreach ($item as $key => $value) {
                                                    if (strpos($key, '-') !== false) {
                                                        if (is_numeric($value)) {
                                                            $carry[$key] = ($carry[$key] ?? 0) + $value;
                                                        }
                                                    }

                                                }
                                                return $carry;
                                            }, []);
                                        foreach ($data as $key => $value) {
                                            if (collect($val2)->contains($key)) {
                                                $val2->$key = $value;
                                            }
                                        }
                                    }

                                }else {
                                    $val2->glCodes = $outputDetail->where('templateDetailID', $val2->detID)->sortBy('sortOrder')->values();
                                }

                                if (strpos($val2->detDescription, "Retained Earning") !== false && $showRetained == false) {
                                    if($val2->detDescription == "Retained Earning")
                                    {
                                        $retainedCode = '';
                                        $retainedDes = '';
                                        if (!empty($val2->glCodes)) {
                                            $glAutoIDs = collect($val2->glCodes)->pluck('glAutoID');

                                            if ($glAutoIDs->isNotEmpty()) {
                                                $isRetained = ChartOfAccount::whereIn('chartOfAccountSystemID', $glAutoIDs)
                                                    ->where('is_retained_earnings', 1)
                                                    ->first();

                                                if ($isRetained) {
                                                    $retainedCode = $isRetained->AccountCode;
                                                    $retainedDes = $isRetained->AccountDescription;
                                                }
                                            }
                                        }
                                        $val2->detDescription = $val2->detDescription.' ('.$retainedCode.' -'.$retainedDes.')';
                                    }
                                    $val2->glCodes = null;
                                }
                            } else {
                                $detailLevelTwo = $outputCollect->where('masterID', $val2->detID)->sortBy('sortOrder')->values();
                                $val2->detail = $detailLevelTwo;
                                $secondLevel = true;
                                foreach ($detailLevelTwo as $key3 => $val3) {
                                    if ($val3->isFinalLevel == 1) {
                                        if($val3->itemType == 3)
                                        {
                                            $val3 = $this->getGlCodes($outputDetail,$val3);
                                        }else {
                                            $val3->glCodes = $outputDetail->where('templateDetailID', $val3->detID)->sortBy('sortOrder')->values();
                                        }
                                    } else {
                                        $detailLevelThree = $outputCollect->where('masterID', $val3->detID)->sortBy('sortOrder')->values();
                                        $val3->detail = $detailLevelThree;
                                        $thirdLevel = true;
                                        foreach ($detailLevelThree as $key4 => $val4) {
                                            if ($val4->isFinalLevel == 1) {
                                                if($val4->itemType == 3)
                                                {
                                                    $val4 = $this->getGlCodes($outputDetail,$val4);
                                                }else {
                                                    $val4->glCodes = $outputDetail->where('templateDetailID', $val4->detID)->sortBy('sortOrder')->values();
                                                }
                                            } else {
                                                $detailLevelFour = $outputCollect->where('masterID', $val4->detID)->sortBy('sortOrder')->values();
                                                $val4->detail = $detailLevelFour;
                                                $fourthLevel = true;
                                                foreach ($detailLevelFour as $key5 => $val5) {
                                                    if ($val5->isFinalLevel == 1) {
                                                        if($val5->itemType == 3)
                                                        {
                                                            $val5 = $this->getGlCodes($outputDetail,$val5);
                                                        }else {
                                                            $val5->glCodes = $outputDetail->where('templateDetailID', $val5->detID)->sortBy('sortOrder')->values();
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if ($val->itemType != 3 && $val->itemType != 5) {
                            if (count($details) == 0) {
                                $removedFromArray[] = $key;
                            }
                        }
                    }
                }
            }

            $headers = collect($headers)->forget($removedFromArray)->values();

        }

        //remove records which has no detail except total
        // get devision value
        $divisionValue = 1;
        if ($template) {
            if ($template->showNumbersIn !== 1) {
                $numbers = ReportTemplateNumbers::find($template->showNumbersIn);
                $divisionValue = (float)$numbers->value;
            }
        }


        $grandTotal = ($columnTemplateID == 1 || $columnTemplateID == 2) ? collect($companyWiseGrandTotalArray)->toArray() : $grandTotal[0];

        $servicelineIDs = collect($companyHeaderColumns)->pluck('companyCode')->toArray();


        $segemntsDta = SegmentMaster::with(['parent'])->whereIn('ServiceLineCode', $servicelineIDs)->get();

        $segmentParentData = [];
        foreach ($segemntsDta as $key => $value) {
            $segmentParentData[$value->ServiceLineCode] = (is_null($value->parent)) ? "-" : $value->parent->ServiceLineDes;
        }

        if(!$grandTotal instanceof Collection){
            $grandTotal = collect($grandTotal);
        }

        // Finally add opening balance to grand total & net total
        if($template->columnTemplateID == null && $template->isConsolidation == 1) {
            if ($template->reportID == 1) {
                // Calculate BS Report Opening Balance
                $openingBalanceAmount = ConsolidationReportService::generateBSConsolidationOpeningBalance($request,$consolidationKeys);
                foreach ($columnHeaderMapping as $key => $value) {
                    if (in_array($value, ['CMB', 'CONS'])) {
                        if (key_exists($key, $outputOpeningBalanceArr)) {
                            $outputOpeningBalanceArr[$key] += $openingBalanceAmount;
                        }
                        else {
                            $outputOpeningBalanceArr[$key] = $openingBalanceAmount;
                        }
                    }
                    else {
                        if (key_exists($key, $outputOpeningBalanceArr)) {
                            $outputOpeningBalanceArr[$key] += 0;
                        }
                        else {
                            $outputOpeningBalanceArr[$key] = 0;
                        }
                    }
                }

                $reportDataNetTotal = $outputCollect->where('netProfitStatus', 1)->where('itemType', 3)->first();
                if ($reportDataNetTotal) {
                    foreach ($columnHeaderMapping as $key => $value) {
                        $reportDataNetTotal->$key += $outputOpeningBalanceArr[$key];
                    }
                }

                $grandTotal = !is_array($grandTotal) ? $grandTotal->toArray() : $grandTotal;
                foreach ($outputOpeningBalanceArr as $key => $value) {
                    $grandTotal[$key] += $value;
                }
            }
        }

        return array(
            'reportData' => $headers,
            'template' => $template,
            'company' => $company,
            'companyCurrency' => $companyCurrency,
            'columns' => $columnKeys,
            'columnHeader' => $columnHeader,
            'columnHeaderMapping' => $columnHeaderMapping,
            'openingBalance' => $outputOpeningBalanceArr,
            'closingBalance' => $outputClosingBalanceArr,
            'uncategorize' => $uncategorizeArr,
            'uncategorizeDrillDown' => $uncategorizeDetailArr,
            'grandTotalUncatArr' => !is_array($grandTotal) ? $grandTotal->toArray():$grandTotal,
            'numbers' => $divisionValue,
            'columnTemplateID' => $columnTemplateID,
            'companyHeaderData' => $companyHeaderColumns,
            'CYYTDColumnKey' => $CYYTDColumnKey,
            'CONSColumnKey' => $CONSColumnKey,
            'serviceLineDescriptions' => $serviceLineDescriptions,
            'segmentParentData' => $segmentParentData,
            'month' => $month,
            'firstLevel' => $firstLevel,
            'secondLevel' => $secondLevel,
            'thirdLevel' => $thirdLevel,
            'fourthLevel' => $fourthLevel
        );
    }

    public function getGlCodes($outputDetail,$val2)
    {
        foreach (ReportTemplateDetails::find($val2->detID)->gl_codes as $glCodeData)
        {
            $glCodesArray[] = $glCodeData->subcategory->detID;
        }
        if(!empty($glCodesArray))
        {
            $outputData = $outputDetail->whereIn('templateDetailID', $glCodesArray)->sortBy('sortOrder')->values();
            $data =  $outputData
                ->reduce(function ($carry, $item) use ($val2) {
                    foreach ($item as $key => $value) {
                        if (strpos($key, '-') !== false) {
                            if (is_numeric($value)) {
                                $carry[$key] = ($carry[$key] ?? 0) + $value;
                            }
                        }

                    }
                    return $carry;
                }, []);
            foreach ($data as $key => $value) {
                if (collect($val2)->contains($key)) {
                    $val2->$key = $value;
                }
            }
        }

        return $val2;
    }

    /*generate report according to each report id*/
    public function generateFRReport(Request $request)
    {

        $reportID = $request->reportID;
        if($request->glCodeWiseOption) {
           return $this->getGlCodeWiseOptionData($request);
        }
        switch ($reportID) {
            case 'FTB': // Trial Balance

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);

                $type = $request->reportTypeID;

                $output = array();
                $headers = array();

                if ($type == 'FTB') {
                    $output = $this->getTrialBalance($request);

                } else if ($type == 'FTBM') {
                    $result = $this->getTrialBalanceMonthWise($request);
                    $output = $result['data'];
                    $headers = $result['headers'];
                }

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if($companyCurrency) {
                    $requestCurrencyLocal = $companyCurrency->localcurrency;
                    $requestCurrencyRpt = $companyCurrency->reportingcurrency;
                }

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('documentLocalAmountDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('documentLocalAmountCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('documentRptAmountDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('documentRptAmountCredit')->toArray());
                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'headers' => $headers,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;
            case 'FTBD': // Trial Balance

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalanceDetails($request);

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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );
                break;
            case 'FGL': // General Ledger

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $checkIsGroup = Company::find($request->companySystemID);

                if(isset($request->month)) {
                    $request->toDate = $request->month."".Carbon::parse($request->month)->endOfMonth()
                    ->format('d').",2022";
                }


                $output = $this->getGeneralLedger($request);

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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $previousLocalBalanceAmount = 0;
                $previousReportingBalanceAmount = 0;
                if (count($request->glCodes) === 1) {
                    $result = array();
                    foreach($output as $ou) {
                        $ou->doucmentLocalBalanceAmount += $previousLocalBalanceAmount;
                        $previousLocalBalanceAmount = $ou->doucmentLocalBalanceAmount;

                        $ou->documentRptBalanceAmount += $previousReportingBalanceAmount;
                        $previousReportingBalanceAmount = $ou->documentRptBalanceAmount;

                        array_push($result,$ou);
                    }
                    $output = $result;
                }
                
                $sort = 'asc';
                $dataArrayNew = array();

                if(isset($request->isClosing) && !$request->isClosing && isset($request->month)) {
                    foreach($output as $ou) {
                        if(Carbon::parse($ou->documentDate)->format('d/m/Y') <= Carbon::parse($request->toDate)->format('d/m/Y')  && (Carbon::parse($ou->documentDate)->format('m')  == Carbon::parse($request->toDate)->format('m')) ) {
                            array_push($dataArrayNew,$ou);
                        }
                    }

                    $total = array();
                    $total['documentLocalAmountDebit'] = array_sum(collect($dataArrayNew)->pluck('localDebit')->toArray());
                    $total['documentLocalAmountCredit'] = array_sum(collect($dataArrayNew)->pluck('localCredit')->toArray());
                    $total['documentRptAmountDebit'] = array_sum(collect($dataArrayNew)->pluck('rptDebit')->toArray());
                    $total['documentRptAmountCredit'] = array_sum(collect($dataArrayNew)->pluck('rptCredit')->toArray());
    
                    return \DataTables::of($dataArrayNew)
                    ->addIndexColumn()
                    ->with('companyName', $checkIsGroup->CompanyName)
                    ->with('isGroup', $checkIsGroup->isGroup)
                    ->with('total', $total)
                    ->with('decimalPlaceLocal', $decimalPlaceLocal)
                    ->with('decimalPlaceRpt', $decimalPlaceRpt)
                    ->with('currencyLocal', $requestCurrencyLocal->CurrencyCode)
                    ->with('currencyRpt', $requestCurrencyRpt->CurrencyCode)
                    ->addIndexColumn()
                    // ->with('orderCondition', $sort)
                    ->make(true);
                }else {
                    $total = array();
                    $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                    $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                    $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                    $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
    
                    return \DataTables::of($output)
                    ->addIndexColumn()
                    ->with('companyName', $checkIsGroup->CompanyName)
                    ->with('isGroup', $checkIsGroup->isGroup)
                    ->with('total', $total)
                    ->with('decimalPlaceLocal', $decimalPlaceLocal)
                    ->with('decimalPlaceRpt', $decimalPlaceRpt)
                    ->with('currencyLocal', $requestCurrencyLocal->CurrencyCode)
                    ->with('currencyRpt', $requestCurrencyRpt->CurrencyCode)
                    ->addIndexColumn()
                    // ->with('orderCondition', $sort)
                    ->make(true);
                }
                


                

                /*return array('reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );*/
                break;
            case 'FTD': // Tax Detail

                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID','tempType','reportViewID'));

                $checkIsGroup = Company::with(['reportingcurrency'])->find($request->companySystemID);
                $output = $this->getTaxDetailQry($request);
                $total = array();

                $currencyCode = "";
                if(!empty($output))
                    $currencyCode = ($checkIsGroup->reportingcurrency) ? $checkIsGroup->reportingcurrency->CurrencyCode :  '-';

                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'tempType' => $request->tempType,
                    'currencyCode' => ($checkIsGroup->reportingcurrency) ?$checkIsGroup->reportingcurrency->CurrencyCode : 'USD'
                );
                break;
            case 'FCT': // Finance Customize reports (Income statement, P&L, Cash flow)
                $request = (object)$request->all();
                $showZeroGL = $request->showZeroGL ?? false;
                $showRetained = $request->showRetained ?? false;
                $consolidationStatus = isset($request->type) && $request->type ? $request->type : 1;
                
                $response = $request->accountType == 4 ?
                    $this->generateCustomizedFREquityReport($request, $showZeroGL, $consolidationStatus, $showRetained) :
                    $this->generateCustomizedFRReport($request, $showZeroGL, $showRetained);

                if ($request->type == 2) {
                    /**
                     * Process data for
                     * 1 - Share of associates profit/loss
                     * 2 - NCI
                     * 3 - Share Holder
                     */
                    $response = $this->processConsolidationData($request, $response);
                }

                return $response;
                break;
            case 'JVD':
                $type = $request->reportTypeID;
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->jvDetailQry($request);

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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $total = array();
                $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('debitAmountLocal')->toArray());
                $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('creditAmountLocal')->toArray());
                $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('debitAmountRpt')->toArray());
                $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('creditAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'isGroup' => $checkIsGroup->isGroup,
                    'total' => $total,
                    'decimalPlaceLocal' => $decimalPlaceLocal,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyLocal' => $requestCurrencyLocal->CurrencyCode,
                    'currencyRpt' => $requestCurrencyRpt->CurrencyCode,
                );

                break;
            case 'RTD':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->getRTDReportQry($request);


                $companyCurrency = \Helper::companyCurrency($request->companySystemID);


                if($request->currencyID == 1) {
                    $currency = $companyCurrency->localcurrency;
                }
                if($request->currencyID == 2) {
                    $currency = $companyCurrency->reportingcurrency;
                }   
                if($request->currencyID == 3) {
                    if(!empty($output->first()->supplierTransactionCurrencyID)) {
                        $currency = CurrencyMaster::find($output->first()->supplierTransactionCurrencyID);
                    } else {
                        $currency = $companyCurrency->localcurrency;
                    }
                }
   
               

                return array(
                    'reportData' => $output,
                    'companyName' => $checkIsGroup->CompanyName,
                    'decimalPlaceRpt' => $currency->DecimalPlaces,
                    'currencyLocal' => $currency->CurrencyCode,
                    'currencyRpt' => $currency->CurrencyCode,
                );
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function getRTDReportQry($request) {

        
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');
        if(isset($request->type)) {
            unset($request->type);
        }
        if(isset($request->tempType)) {
            unset($request->tempType);
        }
        if(isset($request->isBS)) {
            unset($request->isBS);
        }
        if(isset($request->isPL)) {
            unset($request->isPL);
        }
        if(isset($request->glCodeWiseOption)) {
            unset($request->glCodeWiseOption);
        }
        if(isset($request->jvType)) {
            unset($request->jvType);
        }
        if(isset($request->selectedServicelines)) {
            unset($request->selectedServicelines);
        }
        if(isset($request->departments)) {  
            unset($request->departments);
        }
        if(isset($request->glCodes)) {
            unset($request->glCodes);
        }
        if(isset($request->contracts)) {    
            unset($request->contracts);
        }
        if(isset($request->selectedColumn)) {
            unset($request->selectedColumn);
        }

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $supplierCodeSytem = collect($request->suppliers)->pluck('supplierCodeSytem')->toArray();
        
        // Fix datetime issue - ensure toDate includes full day
        $fromDatetime = \Carbon\Carbon::parse($fromDate)->startOfDay()->format('Y-m-d H:i:s');
        $toDatetime = \Carbon\Carbon::parse($toDate)->endOfDay()->format('Y-m-d H:i:s');
        

        // Get ProcumentOrder records that have supplier invoices with documentType = 0
        $result = BookInvSuppMaster::with('detail.unbilled_grv', 'detail.pomaster.po_logistics_details', 'paysuppdetail.payment_master', 'company', 'transactioncurrency', 'localcurrency', 'rptcurrency', 'supplier')
                     ->whereIn('companySystemID', $companyID)
                     ->whereIn('supplierID', $supplierCodeSytem)
                     ->where('whtApplicableYN', 1)
                     ->where('whtType', '>', '0')
                     ->where('whtPercentage', '>', '0')
                     ->whereBetween('bookingDate', [$fromDatetime, $toDatetime])
                     ->where('approved', -1)
                     ->where('documentType', 0)
                     ->get();

        
        // Calculate poAmount and paymentDueDate for each BookInvSuppMaster record
        foreach ($result as $key => $bookInvSuppMaster) {
            $poAmount = 0;
            
            // Add logistic amounts (reqAmount and VATAmount) for the same supplier
            $logisticReqAmount = 0;
            $logisticVATAmount = 0;
            $poMasterCode = null;
            $hasLogisticDetails = false;
            $hasItemDetails = false;
            $vatAmountLocal = 0;
            $vatAmountRpt = 0;
            $vatAmountTrans = 0;
            // Check what types of details this invoice contains
            if ($bookInvSuppMaster->detail) {
                foreach ($bookInvSuppMaster->detail as $detail) {
                    if ($detail->unbilled_grv) {
                        if ($detail->unbilled_grv->logisticYN == 1) {
                            $hasLogisticDetails = true;
                        } else {
                            $hasItemDetails = true;
                        }
                    }else {
                        // $hasItemDetails = true;
                    }
                }
            }
            
            // Get PO details from BookInvSuppDet - ensure each PO is only counted once
            $processedPOs = []; // Track processed POs to avoid duplicates
            if ($bookInvSuppMaster->detail) {
                foreach ($bookInvSuppMaster->detail as $detail) {
                    if ($detail->pomaster && !in_array($detail->purchaseOrderID, $processedPOs)) {
                        $procumentOrder = $detail->pomaster;
                        $processedPOs[] = $detail->purchaseOrderID; // Mark this PO as processed
                        $poMasterCode = $procumentOrder->purchaseOrderCode;    
                        $vatAmountLocal += $procumentOrder->VATAmountLocal ?? 0;
                        $vatAmountRpt += $procumentOrder->VATAmountRpt ?? 0;
                        $vatAmountTrans += $procumentOrder->VATAmount ?? 0;
                        // Always calculate logistic amounts if logistic details exist
                        if ($hasLogisticDetails && $procumentOrder->po_logistics_details) {
                            foreach ($procumentOrder->po_logistics_details as $logisticDetail) {
                                if ($logisticDetail->supplierID == $bookInvSuppMaster->supplierID) {
                                    if($request->currencyID == 1) {
                                        $logisticReqAmount += $logisticDetail->reqAmountInPOLocalCur ?? 0;
                                        $logisticVATAmount += $logisticDetail->VATAmountLocal ?? 0;
                                    }
                                    if($request->currencyID == 2) {
                                        $logisticReqAmount += $logisticDetail->reqAmountInPORptCur ?? 0;
                                        $logisticVATAmount += $logisticDetail->VATAmountRpt ?? 0;
                                    }
                                    if($request->currencyID == 3) {
                                        $logisticReqAmount += $logisticDetail->reqAmountInPOTransCur ?? 0;
                                        $logisticVATAmount += $logisticDetail->VATAmount ?? 0;
                                    }       
                                }
                            }
                        }
                       
                        // Calculate PO line items amount if item details exist
                        if ($hasItemDetails) {
                            if($request->currencyID == 1) {
                                if ($procumentOrder->poTotalLocalCurrency) {
                                        $poAmount += $procumentOrder->poTotalLocalCurrency - (($procumentOrder->rcmActivated ? $procumentOrder->VATAmountLocal : 0));    
                                }
                            }
                            if($request->currencyID == 2) {
                                if ($procumentOrder->poTotalComRptCurrency) {
                                    $poAmount += $procumentOrder->poTotalComRptCurrency - (($procumentOrder->rcmActivated ? $procumentOrder->VATAmountRpt : 0));
                                }
                            }
                            if($request->currencyID == 3) {
                                if ($procumentOrder->poTotalSupplierTransactionCurrency) {
                                    $poAmount += $procumentOrder->poTotalSupplierTransactionCurrency - (($procumentOrder->rcmActivated ? $procumentOrder->VATAmount : 0));
                                }
                            }
                        }
                        

                    }
                }
            }

       
            // Store item amounts separately for retention calculation
            $itemAmount = $poAmount;
            
            // Set poAmount based on invoice type
            if ($hasLogisticDetails && $hasItemDetails) {
                // Mixed invoice - add both logistic and item amounts
                $poAmount = $poAmount + $logisticReqAmount + $logisticVATAmount;
            } elseif ($hasLogisticDetails && !$hasItemDetails) {
                // Pure logistic invoice - only logistic amounts
                $poAmount = $logisticReqAmount + $logisticVATAmount;
            } else {
                // Pure item invoice - only item amounts (poAmount already calculated above)
                // No additional calculation needed
            }
     
            
            // Get payment details from paysuppdetail
            $paymentVoucherDate = null;
            $paymentVoucherStatus = 2; // Default to 3 (not created)
            $paymentDetailsFound = false;
            $payInvoiceTotal = 0;
            if ($bookInvSuppMaster->paysuppdetail) {
                foreach ($bookInvSuppMaster->paysuppdetail->sortByDesc('PayMasterAutoId') as $payDetail) {
                    if($payDetail->payment_master->approved == -1) {
                        $payInvoiceTotal += $payDetail->payment_master->payAmountSuppTrans;
                        if ($payDetail->payment_master && $payDetail->payment_master->BPVdate) {
                            $paymentVoucherDate = $payDetail->payment_master->BPVdate ?? null;
                            $paymentDetailsFound = true;
                        }
                    }
                }
            }

            if($bookInvSuppMaster) {
                $tax = Tax::where('taxCategory',3)->where('isDefault',1)->where('isActive',1)->where('companySystemID', $companyID)->first();

                if($tax) {
                    $whtSupplierInvoice = $bookInvSuppMaster->paysuppdetail->where('supplierCodeSystem', $tax->authorityAutoID)->first();      
                    $bookInvSuppMaster->actualDateOfPaymentOfWithholdingTax = (isset($whtSupplierInvoice->payment_master) && $whtSupplierInvoice->payment_master->approved == -1) ? $whtSupplierInvoice->payment_master->BPVdate : null;     
                }else {
                    $bookInvSuppMaster->actualDateOfPaymentOfWithholdingTax = null;
                }
            }else {
                $bookInvSuppMaster->actualDateOfPaymentOfWithholdingTax = null;
            }
            
           
            // Calculate paymentDueDate based on booking date
            $paymentDueDate = null;
            if ($bookInvSuppMaster->bookingDate) {
                $bookingDate = \Carbon\Carbon::parse($bookInvSuppMaster->bookingDate);
                $dayOfMonth = $bookingDate->day;
                
                if ($dayOfMonth <= 14) {
                    // If invoice is created on or before the 14th, due date is 14th of same month
                    $paymentDueDate = $bookingDate->copy()->day(14);
                } else {
                    // If invoice is created after the 14th, due date is 14th of next month
                    $paymentDueDate = $bookingDate->copy()->addMonth()->day(14);
                }
            }
            
            // Calculate number of months delay
            $numberOfMonthsDelay = 0;
            if (isset($bookInvSuppMaster->actualDateOfPaymentOfWithholdingTax) && $paymentDueDate) {
                $dueDate = \Carbon\Carbon::parse($paymentDueDate);
                $paymentDate = \Carbon\Carbon::parse($bookInvSuppMaster->actualDateOfPaymentOfWithholdingTax);
                
                // Calculate base months difference
                $baseMonthsDelay = $paymentDate->diffInMonths($dueDate);
                
                // If payment is after the 14th, add 1 month for each month delayed
                if ($paymentDate->day > 14) {
                    $numberOfMonthsDelay = $baseMonthsDelay + 1;
                } else {
                    $numberOfMonthsDelay = $baseMonthsDelay;
                }
                
                // Ensure delay is not negative (payment before due date)
                if ($paymentDate->lt($dueDate)) {
                    $numberOfMonthsDelay = 0;
                }
            }
            
            
            // Add calculated fields to each BookInvSuppMaster record
            $bookInvSuppMaster->poAmount = $poAmount;
            $bookInvSuppMaster->supplierInvoiceNo = $bookInvSuppMaster->bookingInvCode;
            $bookInvSuppMaster->bookingInvCode = $poMasterCode;
            $bookInvSuppMaster->invoiceDate = $bookInvSuppMaster->bookingDate; // Date of invoice with WHT applicable
            $bookInvSuppMaster->logisticReqAmount = $logisticReqAmount;
            $bookInvSuppMaster->logisticVATAmount = $logisticVATAmount;
            $bookInvSuppMaster->isLogisticInvoice = $hasLogisticDetails && !$hasItemDetails; // Pure logistic invoice
            $bookInvSuppMaster->hasLogisticDetails = $hasLogisticDetails;
            $bookInvSuppMaster->hasItemDetails = $hasItemDetails;
            $bookInvSuppMaster->dueDateForPaymentOfWithholdingTax = $paymentDueDate ? $paymentDueDate->format('Y-m-d') : null;
            $bookInvSuppMaster->paymentVoucherDate = $paymentVoucherDate;
            $bookInvSuppMaster->hasPaymentDetails = !is_null($paymentVoucherDate);
            $bookInvSuppMaster->numberOfMonthsDelay = $numberOfMonthsDelay;
            $bookInvSuppMaster->withholdingTax = $poAmount * ($bookInvSuppMaster->whtPercentage / 100);
            $bookInvSuppMaster->additionalTax = $bookInvSuppMaster->withholdingTax * $numberOfMonthsDelay * 0.01;
            $bookInvSuppMaster->total = ($bookInvSuppMaster->withholdingTax + $bookInvSuppMaster->additionalTax);
            $bookInvSuppMaster->currency = CurrencyMaster::find($bookInvSuppMaster->supplierTransactionCurrencyID)->CurrencyCode;
            
            // Set supplier invoice amount based on selected currency
            if($request->currencyID == 1) {
                $bookInvSuppMaster->supplierInvoiceAmount = $bookInvSuppMaster->bookingAmountLocal;
            } elseif($request->currencyID == 2) {
                $bookInvSuppMaster->supplierInvoiceAmount = $bookInvSuppMaster->bookingAmountRpt;
            } else {
                $bookInvSuppMaster->supplierInvoiceAmount = $bookInvSuppMaster->bookingAmountTrans;
            }
            
            // Calculate supplier net amount for each currency (similar to frontend calculation)
            // Net amount = (Order Amount + Tax Amount) - WHT - Additional Tax
            // For RCM activated: Net amount = Order Amount - WHT - Additional Tax
            
            // Local Currency Net Amount
            $localOrderAmount = $bookInvSuppMaster->bookingAmountLocal ?? 0;
            $localTaxAmount = $bookInvSuppMaster->VATAmountLocal ?? 0;
            $localWHTAmount = ($bookInvSuppMaster->whtAmount > 0) ? $bookInvSuppMaster->bookingAmountLocal * ($bookInvSuppMaster->whtPercentage / 100) : 0;
            
            // Calculate retention amount based on frontend logic
            if ($procumentOrder->rcmActivated) {
                $localRetentionAmount = ($localOrderAmount * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100;
            } else {
                $localRetentionAmount = ((($localOrderAmount + $localTaxAmount) * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100) - ($bookInvSuppMaster->retentionVatPortion ?? 0);
            }
            
            if ($procumentOrder->rcmActivated) {
                $bookInvSuppMaster->supplierInvoiceAmount = $localOrderAmount - $localWHTAmount - $localRetentionAmount;
            } else {
                $bookInvSuppMaster->supplierInvoiceAmount = ($localOrderAmount + $localTaxAmount) - $localWHTAmount - $localRetentionAmount;
            }
            
            // Report Currency Net Amount
            $rptOrderAmount = $bookInvSuppMaster->bookingAmountRpt ?? 0;
            $rptTaxAmount = $bookInvSuppMaster->VATAmountRpt ?? 0;
            $rptWHTAmount = ($bookInvSuppMaster->whtAmount > 0) ? $bookInvSuppMaster->bookingAmountRpt * ($bookInvSuppMaster->whtPercentage / 100) : 0;
            
            // Calculate retention amount based on frontend logic
            if ($procumentOrder->rcmActivated) {
                $rptRetentionAmount = ($rptOrderAmount * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100;
            } else {
                $rptRetentionAmount = ((($rptOrderAmount + $rptTaxAmount) * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100) - ($bookInvSuppMaster->retentionVatPortion ?? 0);
            }
            if ($procumentOrder->rcmActivated) {
                $bookInvSuppMaster->supplierInvoiceAmount = $rptOrderAmount - $rptWHTAmount  - $rptRetentionAmount;
            } else {
                $bookInvSuppMaster->supplierInvoiceAmount = ($rptOrderAmount + $rptTaxAmount) - $rptWHTAmount  - $rptRetentionAmount;
            }
            
            // Transaction Currency Net Amount
            $transOrderAmount = $bookInvSuppMaster->bookingAmountTrans ?? 0;
            $transTaxAmount = $bookInvSuppMaster->VATAmount ?? 0;
            $transWHTAmount = ($bookInvSuppMaster->whtAmount > 0) ? $bookInvSuppMaster->bookingAmountTrans * ($bookInvSuppMaster->whtPercentage / 100) : 0;
            
            // Calculate retention amount based on frontend logic
            if ($procumentOrder->rcmActivated) {
                $transRetentionAmount = ($transOrderAmount * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100;
            } else {
                $transRetentionAmount = ((($transOrderAmount + $transTaxAmount) * ($bookInvSuppMaster->retentionPercentage ?? 0)) / 100) - ($bookInvSuppMaster->retentionVatPortion ?? 0);
            }
            
            if ($procumentOrder->rcmActivated) {
                $bookInvSuppMaster->supplierInvoiceAmount = $transOrderAmount - $transWHTAmount - $transRetentionAmount;
            } else {
                $bookInvSuppMaster->supplierInvoiceAmount = ($transOrderAmount + $transTaxAmount) - $transWHTAmount - $transRetentionAmount;
            }
            
            $bookInvSuppMaster->whtPercentage = $bookInvSuppMaster->whtPercentage;

            if((abs($payInvoiceTotal - $bookInvSuppMaster->bookingAmountTrans) < 0.000000001))
            {
                $paymentVoucherStatus = 1;
            }else {
                $paymentVoucherStatus = 2;
            }
            $bookInvSuppMaster->paymentVoucherStatus = $paymentVoucherStatus;
        }   
        
        // Consolidate supplier invoices that have the same PO and are both item-based (not logistic)
        $consolidatedResult = collect();
        $groupedInvoices = $result->groupBy(function($invoice) {
            // Group by supplier ID and PO code
            $poCode = null;
            if ($invoice->detail) {
                foreach ($invoice->detail as $detail) {
                    if ($detail->pomaster) {
                        $poCode = $detail->pomaster->purchaseOrderCode;
                        break; // Get the first PO code
                    }
                }
            }
            return $invoice->supplierID . '_' . $poCode;
        });
        
        foreach ($groupedInvoices as $groupKey => $invoices) {
            // Check if all invoices in this group are item-based (not logistic)
            $allItemBased = true;
            foreach ($invoices as $invoice) {
                if ($invoice->hasLogisticDetails) {
                    $allItemBased = false;
                    break;
                }
            }
            
            if ($allItemBased && $invoices->count() > 1) {
                // Consolidate multiple item-based invoices for the same PO
                // Use the first invoice with PO details, don't sum up PO amounts
                $consolidatedInvoice = $invoices->first();
                
                // Sum up only the supplier invoice amounts and withholding tax amounts
                $totalSupplierInvoiceAmount = 0;
                $totalWithholdingTax = 0;
                $totalAdditionalTax = 0;
                $totalTotal = 0;
                $totalSupplierNetAmountLocal = 0;
                $totalSupplierNetAmountRpt = 0;
                $totalSupplierNetAmountTrans = 0;
                
                foreach ($invoices as $invoice) {
                    $totalSupplierInvoiceAmount += $invoice->supplierInvoiceAmount ?? 0;
                    $totalWithholdingTax += $invoice->withholdingTax ?? 0;
                    $totalAdditionalTax += $invoice->additionalTax ?? 0;
                    $totalTotal += $invoice->total ?? 0;
                    $totalSupplierNetAmountLocal += $invoice->supplierNetAmountLocal ?? 0;
                    $totalSupplierNetAmountRpt += $invoice->supplierNetAmountRpt ?? 0;
                    $totalSupplierNetAmountTrans += $invoice->supplierNetAmountTrans ?? 0;
                }
                
                // Update the consolidated invoice - keep original PO amount, sum only supplier amounts
                $consolidatedInvoice->supplierInvoiceAmount = $totalSupplierInvoiceAmount;
                $consolidatedInvoice->withholdingTax = $totalWithholdingTax;
                $consolidatedInvoice->additionalTax = $totalAdditionalTax;
                $consolidatedInvoice->total = $totalTotal;
                $consolidatedInvoice->supplierNetAmountLocal = $totalSupplierNetAmountLocal;
                $consolidatedInvoice->supplierNetAmountRpt = $totalSupplierNetAmountRpt;
                $consolidatedInvoice->supplierNetAmountTrans = $totalSupplierNetAmountTrans;
                
                // Add a flag to indicate this is a consolidated invoice
                $consolidatedInvoice->isConsolidated = true;
                $consolidatedInvoice->originalInvoiceCount = $invoices->count();
                
                $consolidatedResult->push($consolidatedInvoice);
            } else {
                // Add individual invoices (either logistic invoices or single item-based invoices)
                foreach ($invoices as $invoice) {
                    $invoice->isConsolidated = false;
                    $invoice->originalInvoiceCount = 1;
                    $consolidatedResult->push($invoice);
                }
            }
        }
        
        return $consolidatedResult;
    }

    public function processConsolidationData($request,$response) {

        $request = collect($request)->toArray();

        // Get report template net total line
        $reportDataNetTotal = $response['reportData']->where('netProfitStatus', 1)->where('itemType', 3)->first();

        foreach ($response['reportData'] as $data) {
            $data = (object) $data;

            // Calculate Share of Associates Profit/Loss amount
            if(isset($data->itemType) && ($data->itemType == 5) && ($data->netProfitStatus == 0)) {
                $request['selectedRow'] = "CONS-0000";
                $output = ConsolidationReportService::processConsolidationDataForDrillDownAndReport($request);
                $totalShareOfAssociateProfitLoss = $output['total'];

                foreach ($response['columns'] as $column) {
                    $key = explode("-", $column)[0];

                    if (isset($key) && in_array($key,["CMB","CONS"])) {
                        $data->$column = $totalShareOfAssociateProfitLoss;
                        if(array_key_exists($column, $response['grandTotalUncatArr'])) {
                            $response['grandTotalUncatArr'][$column] += $totalShareOfAssociateProfitLoss;
                        }
                        if($reportDataNetTotal) {
                            $reportDataNetTotal->$column += $totalShareOfAssociateProfitLoss;
                        }
                    }
                }
            }

            // Calculate NCI & Share Holder amount
            if(isset($data->itemType) && ($data->itemType == 6) && ($data->netProfitStatus == 0)) {
                $request['selectedRow'] = "NCI-0000";
                $output = ConsolidationReportService::processConsolidationDataForDrillDownAndReport($request);
                $totalNCIAmount = $output['total'];

                $details = collect($data->detail);

                if($details) {
                    $nci = $details->where('itemType', 8)->where('netProfitStatus', 0)->first();
                    if($nci) {
                        foreach ($response['columns'] as $column) {
                            $key = explode("-", $column)[0];
                            if (isset($key) && ($key == "CONS")) {
                                $nci->$column = $totalNCIAmount;
                            }
                        }
                    }

                    if ($request['accountType'] != 1) {
                        $shareHolder = $details->where('itemType', 7)->where('netProfitStatus', 0)->first();
                        if($shareHolder) {
                            foreach ($response['columns'] as $column) {
                                $key = explode("-", $column)[0];
                                if (isset($key) && ($key == "CONS")) {
                                    if($reportDataNetTotal) {
                                        $shareHolder->$column = $reportDataNetTotal->$column - $totalNCIAmount;
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }

        return $response;
    }

    public function generateCustomizedFREquityReport($request, $showZeroGL, $consolidationStatus, $showRetained, $companyWiseTemplate = false)
    {

        $checkRows = ReportTemplateEquity::where('companySystemID', $request->selectedCompanyID)->where('templateMasterID', $request->templateType);
        $columnDetails = $checkRows->get();
        $transformedData = $columnDetails->map(function ($item) {
            return [
                "description" => $item->description,
                "bgColor" => null,
                $item->description => $item->description,
                "width" => null
            ];
        })->toArray();
        $columns = $checkRows->pluck('description');
        $headers = array();
        $company = Company::find($request->selectedCompanyID);
        $template = ReportTemplate::find($request->templateType);
        $companyCurrency = \Helper::companyCurrency($request->companySystemID);

        if ($request->dateType == 1) {
            $fromDate = Carbon::parse($request->fromDate)->startOfDay()->format('Y-m-d H:i:s');
            $toDate = Carbon::parse($request->toDate)->endOfDay()->format('Y-m-d H:i:s');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $fromDate = Carbon::parse($period->dateFrom)->startOfDay()->format('Y-m-d H:i:s');
            $toDate = Carbon::parse($period->dateTo)->endOfDay()->format('Y-m-d H:i:s');
        }
        \DB::select("SET SESSION group_concat_max_len = 1000000");

        $currency = isset($request->currency[0]) ? $request->currency[0]: $request->currency;   
        $amountColumn = ($currency == 1) ? 'documentLocalAmount' : 'documentRptAmount';     
        $dynamicColumnNames = DB::table('erp_report_template_equity')
                        ->where('templateMasterID', $request->templateType)  
                        ->where('companySystemID', $request->selectedCompanyID)  
                        ->pluck('description')
                        ->toArray();

        $dynamicColumns = collect($dynamicColumnNames)
        ->map(function ($desc) use ($fromDate,$amountColumn,$request) {
            return "
                SUM(CASE 
                    WHEN d.description = 'Profit after tax' THEN 0
                    WHEN d.description = 'Comprehensive income' THEN 0
                    WHEN d.description = 'Comprehensive income' THEN 0
                    WHEN d.description = 'Other changes' THEN 0
                    WHEN d.description = 'Closing balance' THEN 0
                    WHEN e.description = '$desc' AND g.documentDate < '$fromDate' AND g.companySystemID = $request->selectedCompanyID THEN g.$amountColumn *-1
                    ELSE 0 
                END) AS `$desc`
            ";
        })
       ->implode(', ');

        $sql = "
            SELECT 
                d.description AS detDescription,
                d.companySystemID,
                $dynamicColumns,
                CASE 
                    WHEN d.description = 'Opening Balance' THEN (
                        SELECT COALESCE(SUM(g2.$amountColumn ), 0)
                        FROM erp_generalledger g2
                        WHERE g2.companySystemID = $request->selectedCompanyID
                        AND g2.glAccountType = 'BS'
                        AND g2.documentDate < '$fromDate'
                    )
                    ELSE 0
                END AS RetainedAutomated,
                    (SELECT e.description
                FROM erp_report_template_equity e
                JOIN erp_companyreporttemplatelinks cl ON cl.templateDetailID = e.id
                JOIN chartofaccounts ca ON ca.chartOfAccountSystemID = cl.glAutoID
                WHERE ca.is_retained_earnings = 1
                AND e.templateMasterID = $request->templateType
                LIMIT 1) AS Retain,
                d.masterID as masterID,
                d.isFinalLevel as isFinalLevel,
                d.bgColor as bgColor,
            d.fontColor as fontColor,
            d.itemType as itemType,
                d.netProfitStatus as netProfitStatus,
                d.hideHeader as hideHeader,
                1 as expanded,
             (
                SELECT 
                    COALESCE(SUM(
                        CASE 
                            WHEN ca.controlAccountsSystemID = 1 THEN g.$amountColumn * -1 
                            WHEN ca.controlAccountsSystemID = 2 THEN -g.$amountColumn
                            ELSE 0 
                        END
                    ), 0)
                FROM erp_generalledger g
                JOIN chartofaccounts ca ON g.chartOfAccountSystemID = ca.chartOfAccountSystemID
                WHERE g.companySystemID = $request->selectedCompanyID
                AND g.documentDate BETWEEN '$fromDate' AND '$toDate'
             ) AS `Profit`,
                (
                    SELECT COALESCE(SUM(g.documentLocalAmount), 0)
                    FROM erp_generalledger g
                    JOIN chartofaccounts ca ON g.chartOfAccountSystemID = ca.chartOfAccountSystemID
                    WHERE g.companySystemID = $request->selectedCompanyID
                    AND ca.controlAccountsSystemID IN (1, 2)
                    AND g.documentLocalAmount > 0
                    AND g.documentDate < '$fromDate'
                ) AS Debit,
                (
                    SELECT COALESCE(SUM(g.documentLocalAmount * -1), 0)
                    FROM erp_generalledger g
                    JOIN chartofaccounts ca ON g.chartOfAccountSystemID = ca.chartOfAccountSystemID
                    WHERE g.companySystemID = $request->selectedCompanyID
                    AND ca.controlAccountsSystemID IN (1, 2)
                    AND g.documentLocalAmount < 0
                    AND g.documentDate < '$fromDate'
                ) AS Credit,
                         (
                    SELECT COALESCE(SUM(g.documentLocalAmount), 0)
                    FROM erp_generalledger g
                    JOIN chartofaccounts ca ON g.chartOfAccountSystemID = ca.chartOfAccountSystemID
                    WHERE g.companySystemID = $request->selectedCompanyID
                    AND ca.controlAccountsSystemID IN (1, 2)
                    AND g.documentLocalAmount > 0
                    AND g.documentDate BETWEEN '$fromDate' AND '$toDate'
                ) AS DebitPL,
                (
                    SELECT COALESCE(SUM(g.documentLocalAmount * -1), 0)
                    FROM erp_generalledger g
                    JOIN chartofaccounts ca ON g.chartOfAccountSystemID = ca.chartOfAccountSystemID
                    WHERE g.companySystemID = $request->selectedCompanyID
                    AND ca.controlAccountsSystemID IN (1, 2)
                    AND g.documentLocalAmount < 0
                    AND g.documentDate BETWEEN '$fromDate' AND '$toDate'
                ) AS CreditPL
            FROM 
                erp_companyreporttemplatedetails d
            LEFT JOIN 
                erp_companyreporttemplate t ON d.companyReportTemplateID = t.companyReportTemplateID
            LEFT JOIN 
                erp_report_template_equity e ON t.companyReportTemplateID = e.templateMasterID
            LEFT JOIN 
                erp_companyreporttemplatelinks cl ON cl.templateMasterID = t.companyReportTemplateID
                AND cl.templateDetailID = e.id
            LEFT JOIN 
                erp_generalledger g ON g.chartOfAccountSystemID = cl.glAutoID
            LEFT JOIN 
                chartofaccounts ca ON ca.chartOfAccountSystemID = g.chartOfAccountSystemID
            WHERE 
                d.companyReportTemplateID = $request->templateType
            GROUP BY 
                d.detID
        ";
        

        $groupAutoGroupsDetail = "SELECT 
                    CONCAT('{',
                        GROUP_CONCAT(
                            DISTINCT 
                        CONCAT('\"', e.description, '\": [', 
                                (SELECT GROUP_CONCAT(DISTINCT cl.glAutoID ORDER BY cl.glAutoID SEPARATOR ',') 
                                FROM erp_companyreporttemplatelinks cl 
                                WHERE cl.templateDetailID = e.id AND cl.templateMasterID = $request->templateType AND cl.companySystemID = $request->selectedCompanyID),
                            ']')
                        SEPARATOR ', '),
                    '}') AS glAutoIDGroups
                FROM erp_report_template_equity e
                JOIN erp_companyreporttemplatelinks cl ON cl.templateDetailID = e.id
                WHERE e.templateMasterID = $request->templateType AND e.companySystemID = $request->selectedCompanyID";
        
        $groupAutoGroupsSum = DB::select($groupAutoGroupsDetail);
        $glAutoIDGroups = $groupAutoGroupsSum[0]->glAutoIDGroups ?? '';

        $result = DB::select($sql);
        $totalRetain = 0;
        $sums = array_fill_keys($dynamicColumnNames, 0);
        foreach($result as &$row)
        {
            $row = (array) $row;
            $row['glAutoIDGroups'] = $glAutoIDGroups;
            $row['Profit'] = abs($row['Profit']) * ($row['DebitPL'] > $row['CreditPL'] ? -1 : 1);

            if (in_array($row['detDescription'], ['Opening Balance', 'Profit after tax'])) 
            {
                $retainAutomated = abs($row['RetainedAutomated']) * ($row['Debit'] > $row['Credit'] ? -1 : 1);
               
                foreach ($dynamicColumnNames as $column) {
                    $glAutoIDs = json_decode($row['glAutoIDGroups'], true)[$column] ?? [];
                    if (!empty($glAutoIDs)) {
                        $ledgerData = DB::table('erp_generalledger AS g')
                        ->selectRaw("
                            CONCAT('[', GROUP_CONCAT(g.$amountColumn), ']') AS glDetails
                        ")
                        ->where('g.documentDate', '<', $fromDate)
                        ->where('g.companySystemID', $request->selectedCompanyID)
                        ->whereIn('g.chartOfAccountSystemID', $glAutoIDs)
                        ->first();
                    
                        $glDetails = json_decode($ledgerData->glDetails ?? '[]', true) ?: [];
                
                        $filteredDetails = array_values(array_filter($glDetails, function ($v) {
                            return $v !== null;
                        }));
                        $debit = array_sum(array_filter($filteredDetails, function ($v) {
                            return $v > 0;
                        }));
                
                        $credit = abs(array_sum(array_filter($filteredDetails, function ($v) {
                            return $v < 0;
                        })));
                        $row[$column] = abs($row[$column]) * ($debit > $credit ? -1 : 1);

                    } else {
                        $row[$column . ' GL Positive Sum'] = 0;
                        $row[$column . ' GL Negative Sum'] = 0;
                    }
                }
                $row[$row['Retain']] = ($row['detDescription'] === 'Opening Balance')
                ? (($row[$row['Retain']] ?? 0) + ($row['RetainedAutomated'] ?? 0))
                : $row['Profit'];
                $totalRetain += $row[$row['Retain']];
            }
           
            if ($row['detDescription'] == 'Comprehensive income') {  
                $row[$row['Retain']] = $totalRetain;
                continue;
            }

            if ($row['detDescription'] == 'Other changes') {  
                $row['glAutoIDGroups'] = json_decode($row['glAutoIDGroups'], true);
                $row['isFinalLevel'] = 1;
                if (!empty($row['glAutoIDGroups']) && is_array($row['glAutoIDGroups'])) 
                {
                    foreach ($row['glAutoIDGroups'] as $key => $value) {

                        
                        $generalLedgerData = DB::table('erp_generalledger AS g')
                        ->selectRaw("
                            SUM(CASE 
                                WHEN g.documentDate BETWEEN ? AND ? 
                                AND g.companySystemID = ? 
                                THEN g.$amountColumn * -1 
                                ELSE 0 
                            END) AS totalAmount",
                            [$fromDate, $toDate, $request->selectedCompanyID]
                        )
                        ->whereIn('g.chartOfAccountSystemID', $value)->first();

                        $totalAmount = $generalLedgerData->totalAmount ?? 0;
                        $row[$key] = $totalAmount; 
                    };
                }
              }
            if ($row['detDescription'] === 'Closing balance') {

                    $sum = 0;
                    foreach ($dynamicColumnNames as $column) {
                        $sum = array_sum(array_column(
                            array_filter($result, function ($item) {
                                return in_array($item['detDescription'], ['Opening Balance','Other changes','Profit after tax']);
                            }),
                            $column
                        ));

                        $row[$column] = $sum;
                    }
            }

        }
     
        return array(
            'reportData' => collect($result),
            'template' => $template,
            'company' => $company,
            'companyCurrency' => $companyCurrency,
            'columns' => $columns,
            'columnHeader' => $transformedData,
            'columnHeaderMapping' => (object) [],
            "openingBalance"=> [],
            "closingBalance" => [],
            'uncategorize' => (object) [],
            'uncategorizeDrillDown' => [],
            'grandTotalUncatArr' => (object) [],
            "numbers"=> 1000,
            "columnTemplateID"=> $template->columnTemplateID,
            "companyHeaderData"=> [
            ],
            "CYYTDColumnKey"=>"",
            "CONSColumnKey"=> "",
            "serviceLineDescriptions" => [],
            "segmentParentData" => [],
            "month" => "",
            "firstLevel" => true,
            "secondLevel" => true,
            "thirdLevel" => false,
            "fourthLevel" => false
        );
    }

    public function getGlCodeWiseOptionData($request) {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();

                if ($reportTypeID == 'FTB') {
                    if ($request->reportSD == 'company_wise_summary') {
                        $companyID = "";
                        $checkIsGroup = Company::find($request->companySystemID);
                        if ($checkIsGroup->isGroup) {
                            $companyID = \Helper::getGroupCompany($request->companySystemID);
                        } else {
                            $companyID = (array)$request->companySystemID;
                        }

                        $subCompanies = Company::whereIn('companySystemID', $companyID)->get(['companySystemID', 'CompanyID', 'CompanyName']);

                        $output = $this->getTrialBalanceCompanyWise($request, $subCompanies);

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;
                                foreach ($subCompanies as $company) {
                                    $comCode = $company['CompanyID'];
                                    $data[$x][$comCode] = round($val->$comCode, 2);
                                }
                                $x++;
                            }
                        }
                    } else {
                        $output = $this->getTrialBalance($request);

                        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                        if($companyCurrency) {
                            $requestCurrencyLocal = $companyCurrency->localcurrency;
                            $requestCurrencyRpt = $companyCurrency->reportingcurrency;
                        }

                        $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;


                        $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                        $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                if ($request->reportSD == 'company_wise') {
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                }
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;

                                if ($checkIsGroup->isGroup == 0) {
                                    $data[$x]['Opening Balance (Local Currency - ' . $currencyLocal . ')'] = round((isset($val->openingBalLocal) ? $val->openingBalLocal : 0), $decimalPlaceLocal);
                                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->documentLocalAmountDebit, $decimalPlaceLocal);
                                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->documentLocalAmountCredit, $decimalPlaceLocal);
                                    $data[$x]['Closing Balance (Local Currency - ' . $currencyLocal . ')'] = round((isset($val->openingBalLocal) ? $val->openingBalLocal : 0) + $val->documentLocalAmountDebit - $val->documentLocalAmountCredit, $decimalPlaceLocal);
                                }
                                $data[$x]['Opening Balance (Reporting Currency - ' . $currencyRpt . ')'] = round(isset($val->openingBalRpt) ? $val->openingBalRpt : 0, $decimalPlaceRpt);
                                $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->documentRptAmountDebit, $decimalPlaceRpt);
                                $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->documentRptAmountCredit, $decimalPlaceRpt);
                                $data[$x]['Closing Balance (Reporting Currency - ' . $currencyRpt . ')'] = round(isset($val->openingBalRpt) ? $val->openingBalRpt : 0 + $val->documentRptAmountDebit - $val->documentRptAmountCredit, $decimalPlaceRpt);
                                $x++;
                            }
                        }
                    }
                } else if ($reportTypeID == 'FTBM') {
                    $result = $this->getTrialBalanceMonthWise($request);
                    $output = $result['data'];
                    $headers = $result['headers'];


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

                    $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                    $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                    $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                    $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                    $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                    $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                    $decimalPlace = 2;
                    if ($request->currencyID == 1) {
                        $decimalPlace = $decimalPlaceLocal;
                    } else if ($request->currencyID == 2) {
                        $decimalPlace = $decimalPlaceRpt;
                    }

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            /*if ($request->reportSD == 'company_wise') {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                            }*/
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['Type'] = $val->glAccountType;
                            $data[$x]['Opening Balance'] = round($val->Opening, $decimalPlace);
                            foreach ($headers as $header) {
                                $closing = $header . 'Closing';
                                $data[$x][$header] = round($val->$header, $decimalPlace);
                                $data[$x][$header . ' Closing'] = round($val->$closing, $decimalPlace);
                            }

                            $x++;
                        }
                    }
                }
         
                return $data;

                break;

            case 'FTBD':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalanceDetails($request);
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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                $data = array();
                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {
                        if ($request->reportSD == 'company_wise') {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                        }
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Document Narration'] = $val->documentNarration;

                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                        $x++;
                    }
                }

                return $data;
                break;
            case 'FGL':
                $reportTypeID = $request->reportTypeID;
                $reportSD = $request->reportSD;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();
                $output = $this->getGeneralLedger($request);


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

                if ($reportSD == "glCode_wise") {
                    if (!empty($output)) {
                        $outputArr = array();
                        foreach ($output as $val1) {
                            $outputArr[$val1->glCode . ' - ' . $val1->AccountDescription][] = $val1;
                        }

                        $x = 0;
                        $total = array();
                        $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
                        $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
                        $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
                        $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());
                        foreach ($outputArr as $key => $values) {
                            $data[$x]['key'] = $key;
                            
                            if (!empty($values)) {
                                $subTotalDebitRpt = 0;
                                $subTotalCreditRpt = 0;
                                $subTotalDebitLocal = 0;
                                $subTotalCreditRptLocal = 0;
                                foreach ($values as $val) {
                                    $x++;
                                    $data[$x]['decimalPlaceLocal']= $decimalPlaceLocal;
                                    $data[$x]['decimalPlaceRpt']= $decimalPlaceRpt;
                                    $data[$x]['com_id'] = $val->companyID;
                                    $data[$x]['com_name'] = $val->CompanyName;
                                    $data[$x]['gl_type'] = $val->glAccountType;
                                    $data[$x]['tem_desc'] = $val->templateDetailDescription;
                                    $data[$x]['doc_type'] = $val->documentID;
                                    $data[$x]['doc_no'] = $val->documentCode;
                                    $data[$x]['data'] = \Helper::dateFormat($val->documentDate);
                                    $data[$x]['doc_narration'] = $val->documentNarration;
                                    $data[$x]['documentSystemCode'] = $val->documentSystemCode;
                                    $data[$x]['documentSystemID'] = $val->documentSystemID;
                                    $data[$x]['documentCode'] = $val->documentCode;
                                    $data[$x]['severice_line'] = $val->serviceLineCode;
                                    $data[$x]['contract'] = $val->clientContractID;
                                        $data[$x]['confirmed_by'] = $val->confirmedBy;
                                        $data[$x]['confirmed_date'] = \Helper::dateFormat($val->documentConfirmedDate);
                                        $data[$x]['approved_by'] = $val->approvedBy;
                                        $data[$x]['approved_date'] = \Helper::dateFormat($val->documentFinalApprovedDate);


                                    $data[$x]['Supplier/Customer'] = $val->isCustomer;
                                    if ($checkIsGroup->isGroup == 0) {
                                        $data[$x]['debit_local'] = round($val->localDebit, $decimalPlaceLocal);
                                        $data[$x]['credit_local'] = round($val->localCredit, $decimalPlaceLocal);
                                    }

                                    $data[$x]['debit_report'] = round($val->rptDebit, $decimalPlaceRpt);
                                    $data[$x]['credit_report'] = round($val->rptCredit, $decimalPlaceRpt);
                                    $data[$x]['isGroup']= $checkIsGroup->isGroup;
                                    $subTotalDebitRpt += $val->rptDebit;
                                    $subTotalCreditRpt += $val->rptCredit;

                                    $subTotalDebitLocal += $val->localDebit;
                                    $subTotalCreditRptLocal += $val->localCredit;
                                }
                                $x++;
                                $data[$x]['show_total']= true;
                                if ($checkIsGroup->isGroup == 0) {
                                    $data[$x]['decimalPlaceLocal']= $decimalPlaceLocal;
                                    $data[$x]['decimalPlaceRpt']= $decimalPlaceRpt;
                                    $data[$x]['debit_total_local'] = round($subTotalDebitLocal, $decimalPlaceLocal);
                                    $data[$x]['credit_total_local'] = round($subTotalCreditRptLocal, $decimalPlaceLocal);
                                    $balanceLocal = $subTotalDebitLocal - $subTotalCreditRptLocal;
                                    $data[$x]['balanceLocal'] = round($balanceLocal, $decimalPlaceLocal);

                                }

                                $data[$x]['isGroup']= $checkIsGroup->isGroup;
                                $data[$x]['debit_total_repot'] = round($subTotalDebitRpt, $decimalPlaceRpt);
                                $data[$x]['credit_total_repot'] = round($subTotalCreditRpt, $decimalPlaceRpt);
                                $balanceReport = $subTotalDebitRpt - $subTotalCreditRpt;
                                $data[$x]['balanceReport'] = round($balanceReport, $decimalPlaceRpt);

                                $x++;
                            }
                        }
                        // $x++;

                        $data[$x]['decimalPlaceLocal']= $decimalPlaceLocal;
                        $data[$x]['decimalPlaceRpt']= $decimalPlaceRpt;
                        $data[$x]['com_id'] = "";
                        $data[$x]['isGroup']= $checkIsGroup->isGroup;
                        $data[$x]['show_grand_total'] = true;
                        $data[$x]['com_name'] = "";
                        $data[$x]['gl_type'] = "";
                        $data[$x]['tem_desc'] = "";
                        $data[$x]['doc_type'] = "";
                        $data[$x]['doc_no'] = "";
                        $data[$x]['data'] = "";
                        $data[$x]['doc_narration'] = "";
                        $data[$x]['documentSystemCode'] = "";
                        $data[$x]['documentSystemID'] = "";
                        $data[$x]['documentCode'] = "";
                        $data[$x]['severice_line'] = "";
                        $data[$x]['contract'] = "";
                        $data[$x]['confirmed_by'] = "";
                        $data[$x]['confirmed_date'] = "";
                        $data[$x]['approved_by'] = "";
                        $data[$x]['approved_date'] = "";
                        $data[$x]['Supplier/Customer'] = "Grand Total";
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['debit_local'] = round($total['documentLocalAmountDebit'], $decimalPlaceLocal);
                            $data[$x]['credit_local'] = round($total['documentLocalAmountCredit'], $decimalPlaceLocal);
                        }
                        $data[$x]['debit_report'] = round($total['documentRptAmountDebit'], $decimalPlaceRpt);
                        $data[$x]['credit_report'] = round($total['documentRptAmountCredit'], $decimalPlaceRpt);

                        $x++;

                        $data[$x]['decimalPlaceLocal']= $decimalPlaceLocal;
                        $data[$x]['decimalPlaceRpt']= $decimalPlaceRpt;
                        $data[$x]['com_id'] = "";
                        $data[$x]['show_grand_total_balance'] = true;
                        $data[$x]['com_name'] = "";
                        $data[$x]['gl_type'] = "";
                        $data[$x]['tem_desc'] = "";
                        $data[$x]['doc_type'] = "";
                        $data[$x]['doc_no'] = "";
                        $data[$x]['isGroup']= $checkIsGroup->isGroup;
                        $data[$x]['data'] = "";
                        $data[$x]['doc_narration'] = "";
                        $data[$x]['documentSystemCode'] = "";
                        $data[$x]['documentSystemID'] = "";
                        $data[$x]['documentCode'] = "";
                        $data[$x]['severice_line'] = "";
                        $data[$x]['contract'] = "";
                        $data[$x]['confirmed_by'] = "";
                        $data[$x]['confirmed_date'] = "";
                        $data[$x]['approved_by'] = "";
                        $data[$x]['approved_date'] = "";
                        $data[$x]['Supplier/Customer'] = "Grand Total";
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['debit_local'] = "";
                            $data[$x]['grand_local_balance'] = round($total['documentLocalAmountDebit'] - $total['documentLocalAmountCredit'], $decimalPlaceLocal);
                        }
                        $data[$x]['debit_report'] = "";
                        $data[$x]['grand_report_balance'] = round($total['documentRptAmountDebit'] - $total['documentRptAmountCredit'], $decimalPlaceRpt);
                    }
                } else {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['GL  Type'] = $val->glAccountType;
                            $data[$x]['Template Description'] = $val->templateDetailDescription;
                            $data[$x]['Document Type'] = $val->documentID;
                            $data[$x]['Document Number'] = $val->documentCode;
                            $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Narration'] = $val->documentNarration;
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract'] = $val->clientContractID;
                            $data[$x]['Supplier/Customer'] = $val->isCustomer;
                            if (in_array('confi_name', $extraColumns)) {
                                $data[$x]['Confirmed By'] = $val->confirmedBy;
                            }

                            if (in_array('confi_date', $extraColumns)) {
                                $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->documentConfirmedDate);
                            }

                            if (in_array('app_name', $extraColumns)) {
                                $data[$x]['Approved By'] = $val->approvedBy;
                            }

                            if (in_array('app_date', $extraColumns)) {
                                $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                            }

                            if ($checkIsGroup->isGroup == 0) {
                                $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                                $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                            }

                            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                            $x++;
                        }
                    }
                }


                return $data;

                break;

            case 'FTD':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->getTaxDetailQry($request);
                $data = array();
                if ($request->tempType == 1) {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Document Code'] = $val->bookingInvCode;
                            $data[$x]['Document Date'] = $val->bookingDate;
                            $data[$x]['Invoice No'] = $val->supplierInvoiceNo;
                            $data[$x]['Invoice Date'] = $val->supplierInvoiceDate;
                            $data[$x]['Narration'] = $val->comments;
                            $data[$x]['Supplier Code'] = $val->primarySupplierCode;
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['Currency'] = $val->CurrencyCode;
                            $data[$x]['Value'] = ($val->bookingAmountTrans - $val->taxTotalAmount);
                            $data[$x]['Discount'] = 0;
                            $data[$x]['Net Value'] = ($val->bookingAmountTrans - $val->taxTotalAmount);
                            $data[$x]['VAT'] = $val->taxTotalAmount;
                            $data[$x]['Due Amount'] = $val->bookingAmountTrans;
                            $data[$x]['Posted Date'] = $val->postedDate;
                            $x++;
                        }
                    }
                } else {
                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Document Code'] = $val->bookingInvCode;
                            $data[$x]['Document Date'] = $val->bookingDate;
                            $data[$x]['Invoice No'] = $val->bookingInvCode;
                            $data[$x]['Invoice Date'] = $val->bookingDate;
                            $data[$x]['Narration'] = $val->comments;
                            $data[$x]['Customer Code'] = $val->CutomerCode;
                            $data[$x]['Customer Short Code'] = $val->customerShortCode;
                            $data[$x]['Customer Name'] = $val->CustomerName;
                            $data[$x]['Currency'] = $val->CurrencyCode;
                            $data[$x]['Value'] = $val->bookingAmountTrans;
                            $data[$x]['Discount'] = 0;
                            $data[$x]['Net Value'] = $val->bookingAmountTrans;
                            $data[$x]['VAT'] = $val->taxTotalAmount;
                            $data[$x]['Due Amount'] = ($val->bookingAmountTrans + $val->taxTotalAmount);
                            $data[$x]['Posted Date'] = $val->postedDate;
                            $x++;
                        }
                    }
                }

                return $data;


            case 'JVD':

                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->jvDetailQry($request);
                $data = array();
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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {

                        $data[$x]['Company ID'] = $val->companyID;
                        //$data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Year'] = $val->YEAR;
                        $data[$x]['Document Narration'] = $val->documentNarration;
                        if ($reportTypeID == 'JVDD') {
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['Type'] = $val->glAccountType;
                        }
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->debitAmountLocal, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->creditAmountLocal, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->debitAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->creditAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                        $data[$x]['Confirmed By'] = $val->confirmedByName;
                        $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                        $data[$x]['Approved By'] = $val->FinalApprovedBy;
                        $x++;
                    }
                }

                return $data;

                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function jvDetailQry($request)
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

        $jvType = collect($request->jvType)->pluck('id')->toArray();
        $jvType = array_unique($jvType);
        if ($request->reportTypeID == 'JVDD') {

            $query = 'SELECT
                      erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_jvmaster.confirmedDate,
                        YEAR ( erp_generalledger.documentDate ) AS YEAR,
                        erp_generalledger.documentNarration,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        chartofaccounts.AccountDescription,
                       CASE erp_jvmaster.jvType
                            WHEN 0 THEN "Standard JV"
                            WHEN 1 THEN "Accrual JV"
                            WHEN 2 THEN "Recurring JV"
                            WHEN 3 THEN "Salary JV"
                            WHEN 4 THEN "Allocation JV"
                            WHEN 5 THEN "PO Accrual JV"
                            ELSE "Unknown"
                        END AS jv_type,
                        IF
                            ( documentLocalAmount < 0, documentLocalAmount *- 1, 0 ) AS creditAmountLocal,
                        IF
                            ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS debitAmountLocal,
                            IF
                            ( documentRptAmount < 0, documentRptAmount *- 1, 0 ) AS creditAmountRpt,
                        IF
                            ( documentRptAmount > 0, documentRptAmount, 0 ) AS debitAmountRpt,
                        employees.empName AS FinalApprovedBy,
                        erp_jvmaster.createdUserID,
                        erp_jvmaster.confirmedByName FROM erp_generalledger 
                        LEFT JOIN chartofaccounts ON erp_generalledger.glCode = chartofaccounts.AccountCode
                        LEFT JOIN employees ON erp_generalledger.documentFinalApprovedBy = employees.empID
                        INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_generalledger.companySystemID 
                        AND erp_jvmaster.documentSystemID = erp_generalledger.documentSystemID 
                        AND erp_jvmaster.jvMasterAutoId = erp_generalledger.documentSystemCode
                        WHERE erp_generalledger.documentSystemID = 17 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_jvmaster.jvType IN (' . join(',', $jvType) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
        }
        if ($request->reportTypeID == 'JVDS') {
            $query = 'SELECT
                        companySystemID,
                        companyID,
                        documentSystemID,
                        documentRptCurrencyID,
                        documentLocalCurrencyID,
                        documentSystemCode,
                        documentCode,
                        documentDate,
                        YEAR,
                        documentNarration,
                        SUM( creditAmountLocal ) AS creditAmountLocal,
                        SUM( debitAmountLocal ) AS debitAmountLocal,
                        SUM( creditAmountRpt ) AS creditAmountRpt,
                        SUM( debitAmountRpt ) AS debitAmountRpt,
                        confirmedDate,
                        confirmedByName,
                        documentFinalApprovedDate,
                        FinalApprovedBy 
                    FROM
                        (
                     SELECT
                      erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_jvmaster.confirmedDate,
                        YEAR ( erp_generalledger.documentDate ) AS YEAR,
                        erp_generalledger.documentNarration,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        chartofaccounts.AccountDescription,
                        IF
                            ( documentLocalAmount < 0, documentLocalAmount *- 1, 0 ) AS creditAmountLocal,
                        IF
                            ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS debitAmountLocal,
                            IF
                            ( documentRptAmount < 0, documentRptAmount *- 1, 0 ) AS creditAmountRpt,
                        IF
                            ( documentRptAmount > 0, documentRptAmount, 0 ) AS debitAmountRpt,
                        employees.empName AS FinalApprovedBy,
                        erp_jvmaster.createdUserID,
                        erp_jvmaster.confirmedByName FROM erp_generalledger 
                        LEFT JOIN chartofaccounts ON erp_generalledger.glCode = chartofaccounts.AccountCode
                        LEFT JOIN employees ON erp_generalledger.documentFinalApprovedBy = employees.empID
                        INNER JOIN erp_jvmaster ON erp_jvmaster.companySystemID = erp_generalledger.companySystemID 
                        AND erp_jvmaster.documentSystemID = erp_generalledger.documentSystemID 
                        AND erp_jvmaster.jvMasterAutoId = erp_generalledger.documentSystemCode
                        WHERE erp_generalledger.documentSystemID = 17 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_jvmaster.jvType IN (' . join(',', $jvType) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '")
                        as a GROUP BY a.documentSystemCode';
        }

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public function processColumnTemplateData($headers, $outputCollect, $outputDetail, $columnKeys, $uncategorizeData, $companyWiseGrandTotal, $outputOpeningBalance, $request, $columnTemplateID, $showRetained)
    {
        $companyCodes = [];
        $serviceLineDescriptions = [];
        $uncategorizeDetailArr = [];
        $uncategorizeArr = [];

        if ($columnTemplateID == 1) {
            $companyData = Company::all();
            foreach ($companyData as $key => $value) {
                $companyCodes[$value->companySystemID] = $value->CompanyID;
            }

            $groupByColumnName = 'compID';
        } else {
            $segmentData = SegmentMaster::all();
            foreach ($segmentData as $key => $value) {
                $companyCodes[$value->serviceLineSystemID] = $value->ServiceLineCode;
                $serviceLineDescriptions[$value->ServiceLineCode] = $value->ServiceLineDes;
            }
            $groupByColumnName = 'serviceLineID';
        }


        $uncategorizeArr['columnData'] = [];
        if (isset($uncategorizeData['output'])) {
            foreach ($uncategorizeData['output'] as $key1 => $value1) {
                if (!is_null($value1->$groupByColumnName)) {
                    foreach ($columnKeys as $key => $val) {
                        $companyID = (isset($companyCodes[$value1->$groupByColumnName])) ? $companyCodes[$value1->$groupByColumnName] : $value1->$groupByColumnName;
                        $uncategorizeArr['columnData'][$companyID][$val] = $value1->$val;
                    }
                }
            }
        }

        if (isset($uncategorizeData['outputDetail'])) {
            $temp = [];
            foreach ($uncategorizeData['outputDetail'] as $key1 => $value1) {
                foreach ($columnKeys as $key => $val) {

                    $temp[$value1->chartOfAccountSystemID]['chartOfAccountSystemID'] = $value1->chartOfAccountSystemID;
                    $temp[$value1->chartOfAccountSystemID]['glCode'] = $value1->glCode;
                    $temp[$value1->chartOfAccountSystemID]['glDescription'] = $value1->glDescription;
                    $temp[$value1->chartOfAccountSystemID]['glAutoID'] = $value1->glAutoID;
                    $temp[$value1->chartOfAccountSystemID]['glAutoID'] = $value1->glAutoID;

                    foreach ($columnKeys as $key2 => $value) {
                        $companyID = (isset($companyCodes[$value1->$groupByColumnName])) ? $companyCodes[$value1->$groupByColumnName] : $value1->$groupByColumnName;
                        $temp[$value1->chartOfAccountSystemID]['columnData'][$companyID][$value] = $value1->$value;
                        if (isset($companyCodes[$value1->$groupByColumnName])) {
                            $companyHeaderData[$value1->$groupByColumnName]['companyCode'] = $companyCodes[$value1->$groupByColumnName];
                        }
                    }
                }
            }

            foreach ($temp as $key => $value) {
                $uncategorizeDetailArr[] = $value;
            }
        }

        $companyWiseGrandTotalArray = [];
        foreach ($companyWiseGrandTotal as $key => $value) {
            if ($key != "") {
                $companyID = (isset($companyCodes[$key])) ? $companyCodes[$key] : $key;
                $companyWiseGrandTotalArray[$companyID] = $value[0];
            }
        }

        $companyHeaderData = [];
        $newHeaders = [];
        $removedFromArray = [];

        foreach ($headers as $key => $value) {
            $newHeaders[$value->detID]['detDescription'] = $value->detDescription;
            $newHeaders[$value->detID]['detID'] = $value->detID;
            $newHeaders[$value->detID]['sortOrder'] = $value->sortOrder;
            $newHeaders[$value->detID]['masterID'] = $value->masterID;
            $newHeaders[$value->detID]['isFinalLevel'] = $value->isFinalLevel;
            $newHeaders[$value->detID]['bgColor'] = $value->bgColor;
            $newHeaders[$value->detID]['fontColor'] = $value->fontColor;
            $newHeaders[$value->detID]['itemType'] = $value->itemType;
            $newHeaders[$value->detID]['netProfitStatus'] = $value->netProfitStatus;
            $newHeaders[$value->detID]['hideHeader'] = $value->hideHeader;
            $newHeaders[$value->detID]['expanded'] = $value->expanded;

            foreach ($columnKeys as $key1 => $value1) {
                $groupByColumn = $columnTemplateID == 1 ? 'CompanyID' : 'ServiceLineSystemID';
                $companyID = (isset($companyCodes[$value->$groupByColumn])) ? $companyCodes[$value->$groupByColumn] : $value->$groupByColumn;
                $newHeaders[$value->detID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->$groupByColumn])) {
                    $companyHeaderData[$value->$groupByColumn]['companyCode'] = $companyCodes[$value->$groupByColumn];
                }
            }
        }

        $newHeaders = collect($newHeaders)->sortBy('sortOrder');

        $newOutputCollect = [];
        foreach ($outputCollect as $key => $value) {
            $newOutputCollect[$value->detID]['detDescription'] = $value->detDescription;
            $newOutputCollect[$value->detID]['detID'] = $value->detID;
            $newOutputCollect[$value->detID]['sortOrder'] = $value->sortOrder;
            $newOutputCollect[$value->detID]['masterID'] = $value->masterID;
            $newOutputCollect[$value->detID]['isFinalLevel'] = $value->isFinalLevel;
            $newOutputCollect[$value->detID]['bgColor'] = $value->bgColor;
            $newOutputCollect[$value->detID]['fontColor'] = $value->fontColor;
            $newOutputCollect[$value->detID]['itemType'] = $value->itemType;
            $newOutputCollect[$value->detID]['netProfitStatus'] = $value->netProfitStatus;
            $newOutputCollect[$value->detID]['hideHeader'] = $value->hideHeader;
            $newOutputCollect[$value->detID]['expanded'] = $value->expanded;

            foreach ($columnKeys as $key1 => $value1) {
                $groupByColumn = $columnTemplateID == 1 ? 'CompanyID' : 'ServiceLineSystemID';
                $companyID = (isset($companyCodes[$value->$groupByColumn])) ? $companyCodes[$value->$groupByColumn] : $value->$groupByColumn;
                $newOutputCollect[$value->detID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->$groupByColumn])) {
                    $companyHeaderData[$value->$groupByColumn]['companyCode'] = $companyCodes[$value->$groupByColumn];
                }
            }
        }


        $newOutputDetail = [];
        foreach ($outputDetail as $key => $value) {
            $newOutputDetail[$value->glAutoID]['glCode'] = $value->glCode;
            $newOutputDetail[$value->glAutoID]['glDescription'] = $value->glDescription;
            $newOutputDetail[$value->glAutoID]['glAutoID'] = $value->glAutoID;
            $newOutputDetail[$value->glAutoID]['templateDetailID'] = $value->templateDetailID;
            $newOutputDetail[$value->glAutoID]['linkCatType'] = $value->linkCatType;
            $newOutputDetail[$value->glAutoID]['templateCatType'] = $value->templateCatType;

            foreach ($columnKeys as $key1 => $value1) {
                $companyID = (isset($companyCodes[$value->$groupByColumnName])) ? $companyCodes[$value->$groupByColumnName] : $value->$groupByColumnName;
                $newOutputDetail[$value->glAutoID]['columnData'][$companyID][$value1] = $value->$value1;
                if (isset($companyCodes[$value->$groupByColumnName])) {
                    $companyHeaderData[$value->$groupByColumnName]['companyCode'] = $companyCodes[$value->$groupByColumnName];
                }
            }
        }

        $firstLevel = false;
        $secondLevel = false;
        $thirdLevel = false;
        $fourthLevel = false;
        $finalHeaders = [];
        foreach ($newHeaders as $key => $val) {
            $temp = [];
            foreach ($val as $key3 => $value3) {
                $temp[$key3] = $value3;
            }
            $details = collect($newOutputCollect)->where('masterID', $val['detID'])->sortBy('sortOrder')->values();
            foreach ($details as $key2 => $val2) {
                $temp2 = [];
                foreach ($val2 as $key4 => $value4) {
                    $temp2[$key4] = $value4;
                }
                $firstLevel = true;
                if ($val2['isFinalLevel'] == 1) {
                    $temp2['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val2['detID'])->sortBy('sortOrder')->values();
                    if($val2['detDescription'] == "Retained Earning" && $showRetained == false) {
                        $temp2['glCodes'] = null;
                    }
                } else {
                    $detailsTwo = collect($newOutputCollect)->where('masterID', $val2['detID'])->sortBy('sortOrder')->values();
                    $secondLevel = true;
                    foreach ($detailsTwo as $key7 => $val7) {
                        $temp3 = [];
                        foreach ($val7 as $key8 => $value8) {
                            $temp3[$key8] = $value8;
                        }
                        if ($val7['isFinalLevel'] == 1) {
                            $temp3['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val7['detID'])->sortBy('sortOrder')->values();
                        } else {
                            $detailsThree = collect($newOutputCollect)->where('masterID', $val7['detID'])->sortBy('sortOrder')->values();
                            $thirdLevel = true;
                            foreach ($detailsThree as $key9 => $val9) {
                                $temp4 = [];
                                foreach ($val9 as $key10 => $value10) {
                                    $temp4[$key10] = $value10;
                                }
                                if ($val9['isFinalLevel'] == 1) {
                                    $temp4['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val9['detID'])->sortBy('sortOrder')->values();
                                } else {
                                    $detailsFour = collect($newOutputCollect)->where('masterID', $val9['detID'])->sortBy('sortOrder')->values();
                                    $fourthLevel = true;
                                    foreach ($detailsFour as $key11 => $val11) {
                                        $temp5 = [];
                                        foreach ($val11 as $key12 => $value12) {
                                            $temp5[$key12] = $value12;
                                        }
                                        if ($val11['isFinalLevel'] == 1) {
                                            $temp5['glCodes'] = collect($newOutputDetail)->where('templateDetailID', $val11['detID'])->sortBy('sortOrder')->values();
                                        }
                                        $temp4['detail'][] = $temp5;
                                    }
                                }
                                $temp3['detail'][] = $temp4;
                            }
                        }
                        $temp2['detail'][] = $temp3;
                    }
                }
                $temp['detail'][] = $temp2;
            }
            if ($val['itemType'] != 3) {
                if (count($details) == 0) {
                    $removedFromArray[] = $key;
                }
            }
            $finalHeaders[] = $temp;
        }

        $headers = collect($finalHeaders)->forget($removedFromArray)->values();
        $companyHeaderData = collect($companyHeaderData)->sortBy('companyCode')->toArray();
        $companyHeaderColumns = [];
        foreach ($companyHeaderData as $key => $value) {
            $companyHeaderColumns[] = $value;
        }

        $outputOpeningBalanceArr = [];
        $outputClosingBalanceArr = [];
        if ($request->accountType == 3) {

            $lastColumn = collect($headers)->last();
            foreach ($outputOpeningBalance as $ke => $value) {
                if ($columnTemplateID == 1) {
                    $companyID = (isset($companyCodes[$value->companySystemID])) ? $companyCodes[$value->companySystemID] : $value->companySystemID;
                } else {
                    $companyID = (isset($companyCodes[$value->serviceLineSystemID])) ? $companyCodes[$value->serviceLineSystemID] : $value->serviceLineSystemID;
                }

                foreach ($columnKeys as $key => $val) {
                    if ($key == 0) {
                        $outputOpeningBalanceArr[$companyID][] = $value->openingBalance;
                        $outputClosingBalanceArr[$companyID][] = ((isset($lastColumn['columnData'][$companyID])) ? $lastColumn['columnData'][$companyID]->$val : 0) + $value->openingBalance;
                    } else {
                        $outputOpeningBalanceArr[$companyID][] = $outputClosingBalanceArr[$companyID][$key - 1];
                        $outputClosingBalanceArr[$companyID][] = ((isset($lastColumn['columnData'][$companyID])) ? $lastColumn['columnData'][$companyID]->$val : 0) + $outputClosingBalanceArr[$companyID][$key - 1];
                    }
                }
            }
        }

        return ['headers' => $headers, 'companyHeaderColumns' => $companyHeaderColumns, 'uncategorizeArr' => $uncategorizeArr, 'uncategorizeDetailArr' => $uncategorizeDetailArr, 'companyWiseGrandTotalArray' => $companyWiseGrandTotalArray, 'outputOpeningBalanceArr' => $outputOpeningBalanceArr, 'outputClosingBalanceArr' => $outputClosingBalanceArr, 'firstLevel' => $firstLevel, 'secondLevel' => $secondLevel, 'thirdLevel' => $thirdLevel, 'fourthLevel' => $fourthLevel, 'serviceLineDescriptions' => $serviceLineDescriptions];
    }

    public function downloadProjectUtilizationReport(Request $request)
    {
        $documentSystemIDs = [2, 3, 4, 18, 21, 19, 15, 17, 11];
        $dateFrom = (new Carbon($request->fromDate))->format('d/m/Y');
        $dateTo = (new Carbon($request->toDate))->format('d/m/Y');

        $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
        $toDate = (new   Carbon($request->toDate))->format('Y-m-d');

        $startDay = Carbon::parse($fromDate)->startOfDay(); // Sets time to 00:00:00
        $endDay = Carbon::parse($toDate)->endOfDay();      // Sets time to 23:59:59

        $projectID = $request->projectID;
         $projectDetail = ErpProjectMaster::with('currency', 'service_line')->where('id', $projectID)->first();

         $serviceline = collect($request->selectedServicelines)->pluck('serviceLineSystemID')->toArray();

         $companySystemID = $projectDetail['companySystemID'];
        $transactionCurrencyID = $projectDetail->currency['currencyID'];
        $documentCurrencyID = $projectDetail->currency['currencyID'];
        $reportingCurrency = Company::with('reportingcurrency')->where('companySystemID',$companySystemID)->first();


        $budgetConsumedData = BudgetConsumedData::with(['purchase_order',
                                                        'debit_note', 
                                                        'credit_note', 
                                                        'direct_payment_voucher', 
                                                        'grv_master', 
                                                        'jv_master',
                                                        'supplier_invoice_master' => function ($query) {
                                                                $query->select('bookingSuppMasInvAutoID', 'comments', 'bookingDate'); 
                                                            },
                                                        ])
                                                ->where('projectID', $projectID)
                                                ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                                                    $query->whereIn('serviceLineSystemID', $serviceline);
                                                })
                                                ->whereIn('documentSystemID', $documentSystemIDs)->get();

        $detailsPOWise = BudgetConsumedData::with(['segment_by','chart_of_account','purchase_order_detail' => function ($query) use ($startDay, $endDay) {
                $query->whereBetween('approvedDate', [$startDay, $endDay]);
                }, 
                'debit_note_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                }, 
                'credit_note_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                }, 
                'direct_payment_voucher_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                }, 
                'grv_master_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                }, 
                'jv_master_detail' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                }, 
                'supplier_invoice_master' => function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay])
                            ->select('bookingSuppMasInvAutoID', 'comments', 'bookingDate');
                }
            ])

            ->where(function($subQuery) use ($startDay, $endDay)
            {   
                $subQuery->whereHas('purchase_order_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('approvedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('debit_note_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('credit_note_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('direct_payment_voucher_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('grv_master_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                })
                ->orWhereHas('jv_master_detail', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay]);
                });
            })
            ->where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->get();

        $budgetAmount = BudgetConsumedData::where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->where(function($subQuery) use ($startDay, $endDay)
            {   
                $subQuery->whereHas('purchase_order', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('approvedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('debit_note', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('debitNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('credit_note', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('creditNoteDate', [$startDay, $endDay]);
                })
                ->orWhereHas('direct_payment_voucher', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('postedDate', [$startDay, $endDay]);
                })
                ->orWhereHas('grv_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('grvDate', [$startDay, $endDay]);
                })
                ->orWhereHas('jv_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('JVdate', [$startDay, $endDay]);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($startDay, $endDay) {
                    $query->whereBetween('bookingDate', [$startDay, $endDay]);
                });
            })

            ->sum('consumedRptAmount');



        $budgetOpeningConsumption = BudgetConsumedData::where('projectID', $projectID)
            ->whereIn('documentSystemID', $documentSystemIDs)
            ->when(count($serviceline) > 0, function ($query) use ($serviceline) {
                $query->whereIn('serviceLineSystemID', $serviceline);
            })
            ->where(function($subQuery) use ($fromDate, $toDate)
            {   
                $subQuery->whereHas('purchase_order', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('approvedDate', '<', $fromDate);
                })
                ->orWhereHas('debit_note', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('debitNoteDate', '<', $fromDate);
                })
                ->orWhereHas('credit_note', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('creditNoteDate', '<', $fromDate);
                })
                ->orWhereHas('direct_payment_voucher', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('postedDate', '<', $fromDate);
                })
                ->orWhereHas('grv_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('grvDate', '<', $fromDate);
                })
                ->orWhereHas('jv_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('JVdate', '<', $fromDate);
                })
                ->orWhereHas('supplier_invoice_master', function ($query) use ($fromDate, $toDate) {
                    $query->whereDate('bookingDate', '<', $fromDate);
                });
            })
            ->sum('consumedRptAmount');

        
        $getProjectAmounts = ProjectGlDetail::where('projectID', $projectID)->get();
        $projectAmount = collect($getProjectAmounts)->sum('amount');
        $getProjectAmountsCurrencyConvertion = \Helper::currencyConversion($companySystemID, $transactionCurrencyID, $documentCurrencyID, $projectAmount);
        $projectAmount = $getProjectAmountsCurrencyConvertion['reportingAmount'];

        if ($projectAmount > 0) {
            $projectAmount = $projectAmount;
        } else {
            $projectAmount = 0;
        }

        $openingBalance = $projectAmount - $budgetOpeningConsumption;

        if(isset($reportingCurrency))
        {
            $cur_rep = $reportingCurrency->reportingcurrency;
        }
        else
        {
           $cur_rep = null;      
        }

        $closingBalance = $openingBalance - $budgetAmount;
        $output = array(
            'companyName' => $reportingCurrency->CompanyName,
            'projectDetail' => $projectDetail,
            'projectAmount' => $projectAmount,
            'budgetConsumedData' => $budgetConsumedData,
            'budgetConsumptionAmount' => $budgetAmount,
            'openingBalance' => $openingBalance,
            'closingBalance' => $closingBalance,
            'detailsPOWise' => $detailsPOWise,
            'fromDate' => $dateFrom,
            'toDate' => $dateTo,
            'reportTittle' => 'Project Utilization Report',
            'companyReportingCurrency' => $cur_rep,
        );

        return \Excel::create('upload_budget_template', function ($excel) use ($output) {
            $excel->sheet('New sheet', function ($sheet) use ($output) {
                $sheet->loadView('export_report.project_utilization_report', $output);
            });
        })->download('xlsx');
    }

    public function exportReport(Request $request, ExportGeneralLedgerReportService $exportGlToExcelService)
    {
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FTB':
                $reportTypeID = $request->reportTypeID;

                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $currencyId =  $request->currencyID;

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $data = array();

                if ($reportTypeID == 'FTB') {
                    if ($request->reportSD == 'company_wise_summary') {
                        $companyID = "";
                        $checkIsGroup = Company::find($request->companySystemID);
                        if ($checkIsGroup->isGroup) {
                            $companyID = \Helper::getGroupCompany($request->companySystemID);
                        } else {
                            $companyID = (array)$request->companySystemID;
                        }

                        $subCompanies = Company::whereIn('companySystemID', $companyID)->get(['companySystemID', 'CompanyID', 'CompanyName']);

                        $output = $this->getTrialBalanceCompanyWise($request, $subCompanies);
                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;
                                foreach ($subCompanies as $company) {
                                    $comCode = $company['CompanyID'];
                                    $data[$x][$comCode] = CurrencyService::convertNumberFormatToNumber(number_format($val->$comCode, 2));
                                }
                                $x++;
                            }
                        }
                        $excelFormat = [
                            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

                        ];
                    }
                    else {
                        $output = $this->getTrialBalance($request);
                        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                        if($companyCurrency) {
                            $requestCurrencyLocal = $companyCurrency->localcurrency;
                            $requestCurrencyRpt = $companyCurrency->reportingcurrency;
                        }

                        $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;


                        $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                        $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                        $totalOpeningBalanceRpt = 0;
                        $totalOpeningBalanceLocal = 0;
                        $totaldocumentLocalAmountDebit = 0;
                        $totaldocumentRptAmountDebit = 0;
                        $totaldocumentLocalAmountCredit= 0;
                        $totaldocumentRptAmountCredit = 0;
                        $totalClosingBalanceRpt = 0;
                        $totalClosingBalanceLocal= 0;

                        if ($output) {
                            $x = 0;
                            foreach ($output as $val) {
                                if ($request->reportSD == 'company_wise') {
                                    $data[$x]['Company ID'] = $val->companyID;
                                    $data[$x]['Company Name'] = $val->CompanyName;
                                }
                                $data[$x]['Account Code'] = $val->glCode;
                                $data[$x]['Account Description'] = $val->AccountDescription;
                                $data[$x]['Type'] = $val->glAccountType;
                                if ($checkIsGroup->isGroup == 0 && $currencyId ==1 || $currencyId ==3) {
                                    $totalOpeningBalanceLocal = $totalOpeningBalanceLocal + $val->openingBalLocal;
                                    $totaldocumentLocalAmountDebit = $totaldocumentLocalAmountDebit + $val->documentLocalAmountDebit;
                                    $totaldocumentLocalAmountCredit = $totaldocumentLocalAmountCredit + $val->documentLocalAmountCredit;

                                    $totalClosingBalanceLocal = $totalClosingBalanceLocal + $val->openingBalLocal + ($val->documentLocalAmountDebit - $val->documentLocalAmountCredit);

                                    $data[$x]['Opening Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format((isset($val->openingBalLocal) ? $val->openingBalLocal : 0), $decimalPlaceLocal));
                                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmountDebit, $decimalPlaceLocal));
                                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentLocalAmountCredit, $decimalPlaceLocal));
                                    $data[$x]['Closing Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format((isset($val->openingBalLocal) ? $val->openingBalLocal : 0) + $val->documentLocalAmountDebit - $val->documentLocalAmountCredit, $decimalPlaceLocal));
                                }
                                if($currencyId == 2 || $currencyId == 3) {
                                    $totalOpeningBalanceRpt = $totalOpeningBalanceRpt + $val->openingBalRpt;
                                    $totaldocumentRptAmountDebit = $totaldocumentRptAmountDebit + $val->documentRptAmountDebit;
                                    $totalClosingBalanceRpt = $totalClosingBalanceRpt + $val->openingBalRpt + ($val->documentRptAmountDebit - $val->documentRptAmountCredit);

                                    $totaldocumentRptAmountCredit = $totaldocumentRptAmountCredit + $val->documentRptAmountCredit;

                                    $data[$x]['Opening Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format(isset($val->openingBalRpt) ? $val->openingBalRpt : 0, $decimalPlaceRpt));
                                    $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmountDebit, $decimalPlaceRpt));
                                    $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->documentRptAmountCredit, $decimalPlaceRpt));
                                    $data[$x]['Closing Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format((isset($val->openingBalRpt) ? $val->openingBalRpt : 0) + $val->documentRptAmountDebit - $val->documentRptAmountCredit, $decimalPlaceRpt));
    
                                }
                                $x++;
                            }
                        }

                        if ($request->reportSD == 'company_wise') {
                            $data[$x]['Company ID'] = "";
                            $data[$x]['Company Name'] = "";
                        }
                        $data[$x]['Account Code'] = "";
                        $data[$x]['Account Description'] = "Grand Total";
                        $data[$x]['Type'] = "";
                        if ($checkIsGroup->isGroup == 0 && $currencyId ==1 || $currencyId ==3) { 
                            $data[$x]['Opening Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totalOpeningBalanceLocal, $decimalPlaceLocal));
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totaldocumentLocalAmountDebit, $decimalPlaceLocal));
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totaldocumentLocalAmountCredit, $decimalPlaceLocal));
                            $data[$x]['Closing Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totalClosingBalanceLocal, $decimalPlaceLocal));
                        }
                        if($currencyId == 2 || $currencyId == 3) { 
                            $data[$x]['Opening Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totalOpeningBalanceRpt, $decimalPlaceRpt));
                            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totaldocumentRptAmountDebit, $decimalPlaceRpt));
                            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totaldocumentRptAmountCredit, $decimalPlaceRpt));
                            $data[$x]['Closing Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($totalClosingBalanceRpt, $decimalPlaceRpt));
                        }
                        $excelFormat = [
                            'D' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'E' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'F' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'G' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'H' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                            'I' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

                        ];

                    }

                }
                else if ($reportTypeID == 'FTBM') {
                    $result = $this->getTrialBalanceMonthWise($request);
                    $output = $result['data'];
                    $headers = $result['headers'];

                    $totalArray =  array(
                        'Account Code' => '',
                        'Account Description' => 'Grand Total',
                        'Type' => '',
                        'Opening Balance' => 0,
                        'Jan' => 0,
                        'JanClosing' => 0,
                        'Feb' => 0,
                        'FebClosing' => 0,
                        'Mar' => 0,
                        'MarClosing' => 0,
                        'Apr' => 0,
                        'AprClosing' => 0,
                        'May' => 0,
                        'MayClosing' => 0,
                        'Jun' => 0,
                        'JunClosing' => 0,
                        'Jul' => 0,
                        'JulClosing' => 0,
                        'Aug'=> 0,
                        'AugClosing'=> 0,
                        'Sep' => 0,
                        'SepClosing' => 0,
                        'Oct' => 0,
                        'OctClosing' => 0,
                        'Nov' => 0,
                        'NovClosing' => 0,
                        'Dece' => 0,
                        'DeceClosing' => 0
                    );
                    $opening_total = 0;
                    foreach($output as $ou) {
                        foreach($headers as $head) {
                            $totalArray[$head] =  round($totalArray[$head] ,2) + round($ou->$head,2);
                            $title = $head.'Closing';
                            $totalArray[$head.'Closing'] = round($totalArray[$head.'Closing'],2) + round($ou->$title,2);
                        }
                        $opening_total += round($ou->Opening,2);
                    }

                    $totalArray['Opening Balance'] = round($opening_total,2);

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

                    $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                    $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                    $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                    $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                    $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                    $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                    $decimalPlace = 2;
                    if ($request->currencyID == 1) {
                        $decimalPlace = $decimalPlaceLocal;
                    } else if ($request->currencyID == 2) {
                        $decimalPlace = $decimalPlaceRpt;
                    }

                    if ($output) {
                        $x = 0;
                        foreach ($output as $val) {
                            /*if ($request->reportSD == 'company_wise') {
                                $data[$x]['Company ID'] = $val->companyID;
                                $data[$x]['Company Name'] = $val->CompanyName;
                            }*/
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['Type'] = $val->glAccountType;
                            $data[$x]['Opening Balance'] = round($val->Opening, $decimalPlace);
                            foreach ($headers as $header) {
                                $closing = $header . 'Closing';
                                $data[$x][$header] = round($val->$header, $decimalPlace);
                                $data[$x][$header . ' Closing'] = round($val->$closing, $decimalPlace);
                            }

                            $x++;
                        }
                    }
                    $data[$x]['Account Code'] = '';
                    $data[$x]['Account Description'] = '';
                    $data[$x]['Type'] = '';
                    $data[$x]['Opening Balance'] = '';
                    $data[$x]['Jan'] = '';
                    $data[$x]['JanClosing'] = '';
                    $data[$x]['Feb'] = '';
                    $data[$x]['FebClosing'] = '';
                    $data[$x]['Mar'] = '';
                    $data[$x]['MarClosing'] = '';
                    $data[$x]['Apr'] = '';
                    $data[$x]['AprClosing'] = '';
                    $data[$x]['May'] = '';
                    $data[$x]['MayClosing'] = '';
                    $data[$x]['Jun'] = '';
                    $data[$x]['JunClosing'] = '';
                    $data[$x]['Jul'] = '';
                    $data[$x]['JulClosing'] = '';
                    $data[$x]['Aug'] = '';
                    $data[$x]['AugClosing'] = '';
                    $data[$x]['Sep'] = '';
                    $data[$x]['SepClosing'] = '';
                    $data[$x]['Oct'] = '';
                    $data[$x]['OctClosing'] = '';
                    $data[$x]['May'] = '';
                    $data[$x]['MayClosing'] = '';
                    $data[$x]['Nov'] = '';
                    $data[$x]['NovClosing'] = '';
                    $data[$x]['Dece'] = '';
                    $data[$x]['DeceClosing'] = '';
    
                    array_push($data,$totalArray);
                    $excelFormat = [
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
                        'W' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Y' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Z' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'AA' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'AB' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

                    ];
                }

               
              

         
                $company_name = $companyCurrency->CompanyName;
                $to_date = $request->toDate;
                $from_date = $request->fromDate;
                if ($reportTypeID == 'FTBM') {
                    $title = 'Financial Trial Balance Month Wise';
                    if ($request->currencyID == 1) {
                        $cur = $currencyLocal;
                    } else if ($request->currencyID == 2) {
                        $cur = $currencyRpt;
                    }
                    $isString = true;
                } else {
                    $title = 'Financial Trial Balance';
                    $cur = null;
                    $isString = false;
                }

                $companyCode = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID:'common';

                $fileName = 'financial_trial_balance';
                $path = 'general-ledger/report/trial_balance/excel/';

                $exportToExcel = $exportGlToExcelService
                    ->setTitle($title)
                    ->setFileName($fileName)
                    ->setCurrency($cur,$isString)
                    ->setPath($path)
                    ->setCompanyCode($companyCode)
                    ->setCompanyName($company_name)
                    ->setFromDate($from_date)
                    ->setToDate($to_date)
                    ->setReportType(4)
                    ->setData($data)
                    ->setType('xls')
                    ->setDateType()
                    ->setExcelFormat($excelFormat)
                    ->setDetails()
                    ->generateExcel();


                if(!$exportToExcel['success'])
                    return $this->sendError('Unable to export excel');

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                break;

            case 'FTBD':
                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                $output = $this->getTrialBalanceDetails($request);
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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                $data = array();
                if ($output) {
                    if($request->glAccountTypeID == 1) {
                        $data[0]['Document Code'] = '';
                        $data[0]['Document Date'] = '';
                        $data[0]['Document Narration'] = 'Opening Balance';

                        if ($checkIsGroup->isGroup == 0) {
                            $data[0]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($request->openingBalance['openingBalDebitLocal'], $decimalPlaceLocal);
                            $data[0]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($request->openingBalance['openingBalCreditLocal'], $decimalPlaceLocal);
                        }

                        $data[0]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($request->openingBalance['openingBalDebitRpt'], $decimalPlaceRpt);
                        $data[0]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($request->openingBalance['openingBalCreditRpt'], $decimalPlaceRpt);

                        $x = 1;    
                    } else {
                        $x = 0;
                    }
                    
                    foreach ($output as $val) {
                        if ($request->reportSD == 'company_wise') {
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                        }
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Document Narration'] = $val->documentNarration;

                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->localDebit, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->localCredit, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptDebit, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->rptCredit, $decimalPlaceRpt);
                        $x++;
                    }
                }

                \Excel::create('trial_balance_details', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            case 'FGL':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);
                if(isset($request->month)) {
                    $request->toDate = $request->month."".Carbon::parse($request->month)->endOfMonth()
                    ->format('d').",2022";
                }
                $output = $this->getGeneralLedger($request);
                $currencyIdLocal = 1;
                $currencyIdRpt = 2;
                $decimalPlaceCollectLocal = collect($output)->pluck('documentLocalCurrencyID')->toArray();
                $decimalPlaceUniqueLocal = array_unique($decimalPlaceCollectLocal);
                $decimalPlaceCollectRpt = collect($output)->pluck('documentRptCurrencyID')->toArray();
                $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);

                if (!empty($decimalPlaceUniqueLocal))
                    $currencyIdLocal = $decimalPlaceUniqueLocal[0];

                if (!empty($decimalPlaceUniqueRpt))
                    $currencyIdRpt = $decimalPlaceUniqueRpt[0];


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
                $toDate = $request->toDate;
                $fromDate = $request->fromDate;
                $reportSD = $request->reportSD;
                $company_name = $companyCurrency->CompanyName;
                $cur = null;
                $title = "Financial General Ledger";
                $companyCode = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID:'common';
                $fileName = 'financial_general_ledger';
                $path = 'general-ledger/report/general_ledger/excel/';

                if ($reportSD == "glCode_wise") {
                    $data = $this->getGlCodeWiseRecordsToExport($output,$request,$extraColumns,$checkIsGroup,$currencyLocal,$currencyRpt,$decimalPlaceLocal,$decimalPlaceRpt);
                    $excelFormat = [
                        'G' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'L' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'M' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                } else {
                    $data = $this->getGLAllRecordsToExport($output,$request,$extraColumns,$checkIsGroup,$currencyLocal,$currencyRpt,$decimalPlaceLocal,$decimalPlaceRpt);
                    $excelFormat = [
                        'I' => \PHPExcel_Style_NumberFormat::FORMAT_DATE_DDMMYYYY,
                        'N' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'O' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'P' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'Q' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'R' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                        'S' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1
                    ];
                }
                $exportToExcel = $exportGlToExcelService
                                ->setTitle($title)
                                ->setFileName($fileName)
                                ->setPath($path)
                                ->setCompanyCode($companyCode)
                                ->setCompanyName($company_name)
                                ->setFromDate($fromDate)
                                ->setToDate($toDate)
                                ->setReportType(1)
                                ->setData($data)
                                ->setType('xls')
                                ->setDateType()
                                ->setExcelFormat($excelFormat)
                                ->setDetails()
                                ->generateExcel();


                if(!$exportToExcel['success'])
                    return $this->sendError('Unable to export excel');

                return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

                break;

            case 'FTD':
                $type = $request->type;
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID','tempType','reportViewID'));

                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->getTaxDetailQry($request);
                $data = array();

                $selectedColumns = collect($request->selectedColumn)->pluck(['id'])->toArray();
                $reporingCurrencyCode = ($output[0]) ? $output[0]->rptCurrencyCode : null;

                $cur = null;
                $title = 'Tax Details';
                $company_name = $companyCurrency->CompanyName;
                $companyID = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID: null;
                $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
                $toDate = (new Carbon($request->toDate))->format('Y-m-d');
                $detail_array = array(  'type' => 1,
                                        'fromDate'=>$fromDate,
                                        'toDate'=>$toDate,
                                        'company_name'=>$company_name,
                                        'company_code'=>$companyID,
                                        'cur'=>$cur,
                                        'tempType' => $request->tempType,
                                        'reportViewID' => $request->reportViewID,
                                        'reportData' => $output,
                                        'selectedColumns' => $selectedColumns,
                                        'reporingCurrencyCode' => $reporingCurrencyCode
                );


                $templateName = "export_report.generalLedger.taxdetails";
                $fileName = 'tax_details';
                $path = 'general-ledger/report/tax_details/excel/';
                $type = "xls";
                $excelColumnFormat = [
                    'U' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'V' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'W' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'X' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'Y' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'Z' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'AA' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'AB' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'AC' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'AD' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                    'AE' => \PHPExcel_Style_NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
                ];

                $basePath = CreateExcel::loadView($detail_array, $type, $fileName, $path, $templateName, $excelColumnFormat);

                if($basePath == '')
                {
                     return $this->sendError('Unable to export excel');
                }
                else
                {
                     return $this->sendResponse($basePath, trans('custom.success_export'));
                }

                break;


            case 'JVD':

                $reportTypeID = $request->reportTypeID;
                $type = $request->type;
                $checkIsGroup = Company::find($request->companySystemID);

                $output = $this->jvDetailQry($request);
                $data = array();
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

                $requestCurrencyLocal = CurrencyMaster::where('currencyID', $currencyIdLocal)->first();
                $requestCurrencyRpt = CurrencyMaster::where('currencyID', $currencyIdRpt)->first();

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                if ($output) {
                    $x = 0;
                    foreach ($output as $val) {

                        $data[$x]['Company ID'] = $val->companyID;
                        //$data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['Document Code'] = $val->documentCode;
                        $data[$x]['Document Date'] = \Helper::dateFormat($val->documentDate);
                        $data[$x]['Year'] = $val->YEAR;
                        $data[$x]['Document Narration'] = $val->documentNarration;
                        if ($reportTypeID == 'JVDD') {
                            $data[$x]['Account Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['JV Type'] = $val->jv_type;
                            $data[$x]['Type'] = $val->glAccountType;
                        }
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = round($val->debitAmountLocal, $decimalPlaceLocal);
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = round($val->creditAmountLocal, $decimalPlaceLocal);
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->debitAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = round($val->creditAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Confirmed Date'] = \Helper::dateFormat($val->confirmedDate);
                        $data[$x]['Confirmed By'] = $val->confirmedByName;
                        $data[$x]['Approved Date'] = \Helper::dateFormat($val->documentFinalApprovedDate);
                        $data[$x]['Approved By'] = $val->FinalApprovedBy;
                        $x++;
                    }
                }

                \Excel::create('jv_detail', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');

                break;
                case 'RTD':
                    $type = $request->type;
                    $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID','tempType','reportViewID'));

                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                    $checkIsGroup = Company::find($request->companySystemID);

                    $output = $this->getRTDReportQry($request);
                    $data = array();

                    $cur = null;
                    $title = 'Report Tax Details';
                    $company_name = $companyCurrency->CompanyName;
                    $companyID = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID: null;
                    $fromDate = (new Carbon($request->fromDate))->format('Y-m-d');
                    $toDate = (new Carbon($request->toDate))->format('Y-m-d');


                    $companyCurrency = \Helper::companyCurrency($request->companySystemID);


                    if($request->currencyID == 1) {
                        $currency = $companyCurrency->localcurrency;
                    }
                    if($request->currencyID == 2) {
                        $currency = $companyCurrency->reportingcurrency;
                    }   
                    if($request->currencyID == 3) {
                        if(!empty($output->first()->supplierTransactionCurrencyID)) {
                            $currency = CurrencyMaster::find($output->first()->supplierTransactionCurrencyID);
                        } else {
                            $currency = $companyCurrency->localcurrency;
                        }
                    }
       
                   


                    $detail_array = array(  'type' => 1,
                                            'fromDate'=>$fromDate,  
                                            'toDate'=>$toDate,
                                            'company_name'=>$company_name,
                                            'company_code'=>$companyID,
                                            'cur'=>$cur,
                                            'tempType' => 'csv',
                                            'reportViewID' => 1,
                                            'reportData' => $output,
                                            'taxExtraColumn' => isset($request->taxExtraColumn) ? $request->taxExtraColumn : null,
                                            'decimalPlaceRpt' => $currency->DecimalPlaces,
                                            'currencyRpt' => $currency->CurrencyCode,
                    );

                    $templateName = "export_report.generalLedger.rtd_details";
                    $fileName = 'Tax Deductibility';
                    $path = 'general-ledger/report/rtd_details/excel/';
                    $type = "xls";
                    $excelColumnFormat = [

                    ];


                    $basePath = CreateExcel::loadView($detail_array, $type, $fileName, $path, $templateName, $excelColumnFormat);

                    if($basePath == '')
                    {
                         return $this->sendError('Unable to export excel');
                    }
                    else
                    {
                         return $this->sendResponse($basePath, trans('custom.success_export'));
                    }

                    break;
                default:
                return $this->sendError('No report ID found');
        }
    }

    private function getGlCodeWiseRecordsToExport($output,$request,$extraColumns,$checkIsGroup,$currencyLocal,$currencyRpt,$decimalPlaceLocal,$decimalPlaceRpt) : Array {
        if (!empty($output)) {
            $outputArr = array();
            foreach ($output as $val1) {
                $outputArr[$val1->glCode . ' - ' . $val1->AccountDescription][] = $val1;
            }

            $x = 0;
            $total = array();
            $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
            $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
            $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
            $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());

            $data = array();

            foreach ($outputArr as $key => $values) {
                $data[$x][''] = $key;
                $x++;
                $data[$x]['Company ID'] = 'Company ID';
                $data[$x]['Company Name'] = 'Company Name';
                $data[$x]['GL  Type'] = 'GL  Type';
                $data[$x]['Template Description'] = 'Template Description';
                $data[$x]['Document Type'] = 'Document Type';
                $data[$x]['Document Number'] = 'Document Number';
                $data[$x]['Date'] = 'Date';
                $data[$x]['Document Narration'] = 'Document Narration';
                $data[$x]['Service Line'] = 'Service Line';
                $data[$x]['Contract'] = 'Contract';

                if (in_array('confi_name', $extraColumns)) {
                    $data[$x]['Confirmed By'] = 'Confirmed By';
                }

                if (in_array('confi_date', $extraColumns)) {
                    $data[$x]['Confirmed Date'] = 'Confirmed Date';
                }

                if (in_array('app_name', $extraColumns)) {
                    $data[$x]['Approved By'] = 'Approved By';
                }

                if (in_array('app_date', $extraColumns)) {
                    $data[$x]['Approved Date'] = 'Approved Date';
                }
                $data[$x]['Supplier/Customer'] = 'Supplier/Customer';
                if ($checkIsGroup->isGroup == 0) {
                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = 'Debit (Local Currency - ' . $currencyLocal . ')';
                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = 'Credit (Local Currency - ' . $currencyLocal . ')';
                    $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = 'Balance (Local Currency - ' . $currencyLocal . ')';
                }
                $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = 'Debit (Reporting Currency - ' . $currencyRpt . ')';
                $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = 'Credit (Reporting Currency - ' . $currencyRpt . ')';
                $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = 'Balance (Reporting Currency - ' . $currencyRpt . ')';
                if (!empty($values)) {
                    $subTotalDebitRpt = 0;
                    $subTotalCreditRpt = 0;
                    $subTotalDebitLocal = 0;
                    $subTotalCreditRptLocal = 0;
                    $runningBalanceLocal = 0;
                    $runningBalanceRpt = 0;
                    foreach ($values as $val) {
                        $runningBalanceLocal += $val->doucmentLocalBalanceAmount;
                        $runningBalanceRpt += $val->documentRptBalanceAmount;
                        $x++;
                        $data[$x]['Company ID'] = $val->companyID;
                        $data[$x]['Company Name'] = $val->CompanyName;
                        $data[$x]['GL  Type'] = $val->glAccountType;
                        $data[$x]['Template Description'] = $val->templateDescription;
                        $data[$x]['Document Type'] = $val->documentID;
                        $data[$x]['Document Number'] = $val->documentCode;
                        $data[$x]['Date'] = ($val->documentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->documentDate)) : null;
                        $data[$x]['Document Narration'] = $val->documentNarration;
                        $data[$x]['Service Line'] = $val->serviceLineCode;
                        $data[$x]['Contract'] = $val->clientContractID;

                        if (in_array('confi_name', $extraColumns)) {
                            $data[$x]['Confirmed By'] = $val->documentNarration == "Opening Balance" ? "" : $val->confirmedBy;
                        }

                        if (in_array('confi_date', $extraColumns)) {
                            $data[$x]['Confirmed Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentConfirmedDate);
                        }

                        if (in_array('app_name', $extraColumns)) {
                            $data[$x]['Approved By'] = $val->documentNarration == "Opening Balance" ? "" : $val->approvedBy;
                        }

                        if (in_array('app_date', $extraColumns)) {
                            $data[$x]['Approved Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentFinalApprovedDate);
                        }
                        $data[$x]['Supplier/Customer'] = $val->isCustomer;
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($val->localDebit, $decimalPlaceLocal));
                            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($val->localCredit, $decimalPlaceLocal));
                            $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($runningBalanceLocal, $decimalPlaceLocal));
                        }

                        $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($val->rptDebit, $decimalPlaceRpt));
                        $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($val->rptCredit, $decimalPlaceRpt));
                        $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($runningBalanceRpt, $decimalPlaceRpt));
                        $subTotalDebitRpt +=  round($val->rptDebit, $decimalPlaceRpt);
                        $subTotalCreditRpt += round($val->rptCredit, $decimalPlaceRpt);

                        $subTotalDebitLocal += round($val->localDebit, $decimalPlaceLocal);
                        $subTotalCreditRptLocal += round($val->localCredit, $decimalPlaceLocal);
                    }
                    $x++;
                    $data[$x]['Company ID'] = '';
                    $data[$x]['Company Name'] = '';
                    $data[$x]['GL  Type'] = '';
                    $data[$x]['Template Description'] = '';
                    $data[$x]['Document Type'] = '';
                    $data[$x]['Document Number'] = '';
                    $data[$x]['Date'] = '';
                    $data[$x]['Document Narration'] = '';
                    $data[$x]['Service Line'] = '';
                    $data[$x]['Contract'] = '';

                    if (in_array('confi_name', $extraColumns)) {
                        $data[$x]['Confirmed By'] = '';
                    }

                    if (in_array('confi_date', $extraColumns)) {
                        $data[$x]['Confirmed Date'] = '';
                    }

                    if (in_array('app_name', $extraColumns)) {
                        $data[$x]['Approved By'] = '';
                    }

                    if (in_array('app_date', $extraColumns)) {
                        $data[$x]['Approved Date'] = '';
                    }
                    $data[$x]['Supplier/Customer'] = 'Total';
                    if ($checkIsGroup->isGroup == 0) {
                        $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitLocal, $decimalPlaceLocal));
                        $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalCreditRptLocal, $decimalPlaceLocal));
                        $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
                    }

                    $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitRpt, $decimalPlaceRpt));
                    $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalCreditRpt, $decimalPlaceRpt));
                    $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = "";

                    $x++;
                    $data[$x]['Company ID'] = '';
                    $data[$x]['Company Name'] = '';
                    $data[$x]['GL  Type'] = '';
                    $data[$x]['Template Description'] = '';
                    $data[$x]['Document Type'] = '';
                    $data[$x]['Document Number'] = '';
                    $data[$x]['Date'] = '';
                    $data[$x]['Document Narration'] = '';
                    $data[$x]['Service Line'] = '';
                    $data[$x]['Contract'] = '';

                    if (in_array('confi_name', $extraColumns)) {
                        $data[$x]['Confirmed By'] = '';
                    }

                    if (in_array('confi_date', $extraColumns)) {
                        $data[$x]['Confirmed Date'] = '';
                    }

                    if (in_array('app_name', $extraColumns)) {
                        $data[$x]['Approved By'] = '';
                    }

                    if (in_array('app_date', $extraColumns)) {
                        $data[$x]['Approved Date'] = '';
                    }
                    $data[$x]['Supplier/Customer'] = 'Balance';
                    if ($checkIsGroup->isGroup == 0) {
                        $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] =  '';
                        $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitLocal-$subTotalCreditRptLocal, $decimalPlaceLocal));
                        $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
                    }

                    $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] =  '';
                    $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitRpt-$subTotalCreditRpt, $decimalPlaceRpt));
                    $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = "";

                    $x++;
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                    if ($checkIsGroup->isGroup == 0) {
                        $data[$x][''] = '';
                        $data[$x][''] = '';
                    }
                    $data[$x][''] = '';
                    $data[$x][''] = '';
                }
            }
            $x++;
            $data[$x]['Company ID'] = '';
            $data[$x]['Company Name'] = '';
            $data[$x]['GL  Type'] = '';
            $data[$x]['Template Description'] = '';
            $data[$x]['Document Type'] = '';
            $data[$x]['Document Number'] = '';
            $data[$x]['Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['Service Line'] = '';
            $data[$x]['Contract'] = '';

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed By'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved By'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier/Customer'] = 'Grand Total';
            if ($checkIsGroup->isGroup == 0) {
                $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountDebit'], $decimalPlaceLocal));
                $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountCredit'], $decimalPlaceLocal));
                $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
            }
            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountDebit'], $decimalPlaceRpt));
            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountCredit'], $decimalPlaceRpt));
            $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = "";

            $x++;
            $data[$x]['Company ID'] = '';
            $data[$x]['Company Name'] = '';
            $data[$x]['GL  Type'] = '';
            $data[$x]['Template Description'] = '';
            $data[$x]['Document Type'] = '';
            $data[$x]['Document Number'] = '';
            $data[$x]['Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['Service Line'] = '';
            $data[$x]['Contract'] = '';

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed By'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved By'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier/Customer'] = 'Total Balance';
            if ($checkIsGroup->isGroup == 0) {
                $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = "";
                $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountDebit'] - $total['documentLocalAmountCredit'], $decimalPlaceLocal));
                $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
            }
            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = "";
            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] =CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountDebit'] - $total['documentRptAmountCredit'], $decimalPlaceRpt));
            $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] ="";
        }

        return $data;
    }
    private function getGLAllRecordsToExport($output,$request,$extraColumns,$checkIsGroup,$currencyLocal,$currencyRpt,$decimalPlaceLocal,$decimalPlaceRpt): Array {
        $data = array();
        $x = 0;

        if ($output) {
            $subTotalDebitRpt = 0;
            $subTotalCreditRpt = 0;
            $subTotalDebitLocal = 0;
            $subTotalCreditRptLocal = 0;
            $runningBalanceLocal = 0;
            $runningBalanceRpt = 0;
            $dataArrayNew = array();

            if(isset($request->isClosing) && !$request->isClosing && isset($request->month)) {
                foreach($output as $ou) {
                    if(Carbon::parse($ou->documentDate)->format('d/m/Y') <= Carbon::parse($request->toDate)->format('d/m/Y')  && (Carbon::parse($ou->documentDate)->format('m')  == Carbon::parse($request->toDate)->format('m')) ) {
                        array_push($dataArrayNew,$ou);
                    }
                }

                $output = $dataArrayNew;
            }

            $viewBalance = 0;
            if(count($request->glCodes) == 1) {
                $viewBalance = 1;
            }

            foreach ($output as $val) {
                if($viewBalance == 1) {
                    $runningBalanceLocal += $val->doucmentLocalBalanceAmount;
                    $runningBalanceRpt += $val->documentRptBalanceAmount;
                }
                $data[$x]['Company ID'] = $val->companyID;
                $data[$x]['Company Name'] = $val->CompanyName;
                $data[$x]['GL Code'] = $val->glCode;
                $data[$x]['Account Description'] = $val->AccountDescription;
                $data[$x]['GL  Type'] = $val->glAccountType;
                $data[$x]['Template Description'] = $val->templateDescription;
                $data[$x]['Document Type'] = $val->documentID;
                $data[$x]['Document Number'] = $val->documentCode;
                $data[$x]['Date'] = ($val->documentDate) ? \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel(Helper::dateFormat($val->documentDate)) : null;
                $data[$x]['Document Narration'] = $val->documentNarration;
                $data[$x]['Service Line'] = $val->serviceLineCode;
                $data[$x]['Contract'] = $val->clientContractID;
                $data[$x]['Supplier/Customer'] = $val->isCustomer;
                if (in_array('confi_name', $extraColumns)) {
                    $data[$x]['Confirmed By'] = $val->documentNarration == "Opening Balance" ? "" : $val->confirmedBy;
                }

                if (in_array('confi_date', $extraColumns)) {
                    $data[$x]['Confirmed Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentConfirmedDate);
                }

                if (in_array('app_name', $extraColumns)) {
                    $data[$x]['Approved By'] = $val->documentNarration == "Opening Balance" ? "" : $val->approvedBy;
                }

                if (in_array('app_date', $extraColumns)) {
                    $data[$x]['Approved Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentFinalApprovedDate);
                }

                if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
                    $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->localDebit, $decimalPlaceLocal));
                    $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->localCredit, $decimalPlaceLocal));

                    if($viewBalance == 1) {
                        $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($runningBalanceLocal, $decimalPlaceLocal));
                    }
                }

                if($request->currencyID == 2 || !isset($request->month)) {
                    $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->rptDebit, $decimalPlaceRpt));
                    $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($val->rptCredit, $decimalPlaceRpt));
                }
                if($viewBalance == 1) {
                    $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($runningBalanceRpt, $decimalPlaceLocal));
                }

                $subTotalDebitRpt += round($val->rptDebit, $decimalPlaceRpt);
                $subTotalCreditRpt += round($val->rptCredit, $decimalPlaceRpt);

                $subTotalDebitLocal += round($val->localDebit, $decimalPlaceLocal);
                $subTotalCreditRptLocal += round($val->localCredit, $decimalPlaceLocal);
                $x++;
            }
        }
        $data[$x]['Company ID'] = "";
        $data[$x]['Company Name'] = "";
        $data[$x]['GL Code'] = "";
        $data[$x]['Account Description'] = "";
        $data[$x]['GL  Type'] = "";
        $data[$x]['Template Description'] = "";
        $data[$x]['Document Type'] = "";
        $data[$x]['Document Number'] = "";
        $data[$x]['Date'] = "";
        $data[$x]['Document Narration'] = "";
        $data[$x]['Service Line'] = "";
        $data[$x]['Contract'] = "";

        $data[$x]['Supplier/Customer'] = "Grand Total";
        if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitLocal, $decimalPlaceLocal));
            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalCreditRptLocal, $decimalPlaceLocal));
            if($viewBalance == 1) {
                $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
            }
        }

        if($request->currencyID == 2 || !isset($request->month)) {
            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitRpt, $decimalPlaceRpt));
            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalCreditRpt, $decimalPlaceRpt));

            if($viewBalance == 1) {
                $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = "";
            }
        }
        $x++;
        $data[$x]['Company ID'] = "";
        $data[$x]['Company Name'] = "";
        $data[$x]['GL Code'] = "";
        $data[$x]['Account Description'] = "";
        $data[$x]['GL  Type'] = "";
        $data[$x]['Template Description'] = "";
        $data[$x]['Document Type'] = "";
        $data[$x]['Document Number'] = "";
        $data[$x]['Date'] = "";
        $data[$x]['Document Narration'] = "";
        $data[$x]['Service Line'] = "";
        $data[$x]['Contract'] = "";

        $data[$x]['Supplier/Customer'] = "";
        if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
            $data[$x]['Debit (Local Currency - ' . $currencyLocal . ')'] = "";
            $data[$x]['Credit (Local Currency - ' . $currencyLocal . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitLocal - $subTotalCreditRptLocal, $decimalPlaceLocal));
            if($viewBalance == 1) {
                $data[$x]['Balance (Local Currency - ' . $currencyLocal . ')'] = "";
            }
        }

        if($request->currencyID == 2 || !isset($request->month)) {
            $data[$x]['Debit (Reporting Currency - ' . $currencyRpt . ')'] = "";
            $data[$x]['Credit (Reporting Currency - ' . $currencyRpt . ')'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitRpt - $subTotalCreditRpt, $decimalPlaceRpt));
            if($viewBalance == 1) {
                $data[$x]['Balance (Reporting Currency - ' . $currencyRpt . ')'] = "";
            }
        }

        return $data;
    }

    public function exportGLReport(Request $request, ExportGeneralLedgerReportService $exportGlToExcelService){
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', -1);
        $type = $request->type;
        $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
        $companyCurrency = \Helper::companyCurrency($request->companySystemID);
        $checkIsGroup = Company::find($request->companySystemID);
        $data = array();

        if(isset($request->month)) {
            $request->toDate = $request->month."".Carbon::parse($request->month)->endOfMonth()
                    ->format('d').",2022";
        }

        $output = $this->getGeneralLedgerSortedData($request);

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

        $reportSD = $request->reportSD;

        if ($reportSD == "glCode_wise") {
            $x = 0;
            $total = array();
            $total['documentLocalAmountDebit'] = array_sum(collect($output)->pluck('localDebit')->toArray());
            $total['documentLocalAmountCredit'] = array_sum(collect($output)->pluck('localCredit')->toArray());
            $total['documentRptAmountDebit'] = array_sum(collect($output)->pluck('rptDebit')->toArray());
            $total['documentRptAmountCredit'] = array_sum(collect($output)->pluck('rptCredit')->toArray());

            if (!empty($output)) {
                $outputArr = array();
                foreach ($output as $val1) {
                    $outputArr[$val1->glCode . ' - ' . $val1->AccountDescription][] = $val1;
                }

                foreach ($outputArr as $key => $values) {
                    $data[$x][''] = $key;
                    $x++;
                    $data[$x]['Company ID'] = 'Company ID';
                    $data[$x]['Company Name'] = 'Company Name';
                    $data[$x]['Document Type'] = 'Document Type';
                    $data[$x]['Document Description'] = 'Document Description';
                    $data[$x]['Document Code'] = 'Document Code';
                    $data[$x]['Posted Date'] = 'Posted Date';
                    $data[$x]['Document Narration'] = 'Document Narration';
                    $data[$x]['GL created date'] = 'GL created date';
                    $data[$x]['Service Line'] = 'Service Line';
                    $data[$x]['Contract'] = 'Contract';
                    $data[$x]['GL Code'] = 'GL Code';
                    $data[$x]['Account Description'] = 'Account Description';
                    $data[$x]['GL Type'] = 'GL Type';

                    $data[$x]['Transaction Currency'] = 'Transaction Currency';
                    $data[$x]['Transaction Debit Amount'] = 'Transaction Debit Amount';
                    $data[$x]['Transaction Credit Amount'] = 'Transaction Credit Amount';

                    if ($checkIsGroup->isGroup == 0) {
                        $data[$x]['Local Currency'] = 'Local Currency';
                        $data[$x]['Local Debit Amount'] = 'Local Debit Amount';
                        $data[$x]['Local Credit Amount'] = 'Local Credit Amount';
                    }
                    $data[$x]['Reporting Currency'] = 'Reporting Currency';
                    $data[$x]['Reporting Debit Amount'] = 'Reporting Debit Amount';
                    $data[$x]['Reporting Credit Amount'] = 'Reporting Credit Amount';

                    if (in_array('confi_name', $extraColumns)) {
                        $data[$x]['Confirmed User'] = 'Confirmed User';
                    }

                    if (in_array('confi_date', $extraColumns)) {
                        $data[$x]['Confirmed Date'] = 'Confirmed Date';
                    }

                    if (in_array('app_name', $extraColumns)) {
                        $data[$x]['Approved User'] = 'Approved User';
                    }

                    if (in_array('app_date', $extraColumns)) {
                        $data[$x]['Approved Date'] = 'Approved Date';
                    }
                    $data[$x]['Supplier Name/Customer Name'] = 'Supplier Name/Customer Name';
                    $data[$x]['Supplier Code/Customer Code'] = 'Supplier Code/Customer Code';
                    $data[$x]['Document Year'] = 'Document Year';

                    if (!empty($values)) {
                        $subTotalDebitRpt = 0;
                        $subTotalCreditRpt = 0;
                        $subTotalDebitLocal = 0;
                        $subTotalCreditLocal = 0;
                        foreach ($values as $val) {
                            $x++;
                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['Company Name'] = $val->CompanyName;
                            $data[$x]['Document Type'] = $val->documentID;
                            $data[$x]['Document Description'] = $val->documentNarration == "Opening Balance" ? "" : $val->documentDescription;
                            $data[$x]['Document Code'] = $val->documentCode;
                            $data[$x]['Posted Date'] = \Helper::dateFormat($val->documentDate);
                            $data[$x]['Document Narration'] = $val->documentNarration;
                            $data[$x]['GL created date'] = \Helper::dateFormat($val->createdDateTime);
                            $data[$x]['Service Line'] = $val->serviceLineCode;
                            $data[$x]['Contract'] = $val->clientContractID;
                            $data[$x]['GL Code'] = $val->glCode;
                            $data[$x]['Account Description'] = $val->AccountDescription;
                            $data[$x]['GL Type'] = $val->glAccountType;

                            $requestCurrencyTrans = CurrencyMaster::where('currencyID', $val->documentTransCurrencyID)->first();
                            $currencyTrans = !empty($requestCurrencyTrans) ? $requestCurrencyTrans->CurrencyCode : 'OMR';
                            $decimalPlaceTrans = !empty($requestCurrencyTrans) ? $requestCurrencyTrans->DecimalPlaces : 3;

                            $data[$x]['Transaction Currency'] = $val->documentNarration == "Opening Balance" ? "" : $currencyTrans;
                            $data[$x]['Transaction Debit Amount'] = $val->documentNarration == "Opening Balance" ? "" : CurrencyService::convertNumberFormatToNumber(round($val->transDebit, $decimalPlaceTrans));
                            $data[$x]['Transaction Credit Amount'] = $val->documentNarration == "Opening Balance" ? "" : CurrencyService::convertNumberFormatToNumber(round($val->transCredit, $decimalPlaceTrans));
                            if ($checkIsGroup->isGroup == 0) {
                                $data[$x]['Local Currency'] = $currencyLocal;
                                $data[$x]['Local Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($val->localDebit, $decimalPlaceLocal));
                                $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($val->localCredit, $decimalPlaceLocal));
                            }

                            $data[$x]['Reporting Currency'] = $currencyRpt;
                            $data[$x]['Reporting Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($val->rptDebit, $decimalPlaceRpt));
                            $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($val->rptCredit, $decimalPlaceRpt));

                            if (in_array('confi_name', $extraColumns)) {
                                $data[$x]['Confirmed User'] = $val->documentNarration == "Opening Balance" ? "" : $val->confirmedBy;
                            }

                            if (in_array('confi_date', $extraColumns)) {
                                $data[$x]['Confirmed Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentConfirmedDate);
                            }

                            if (in_array('app_name', $extraColumns)) {
                                $data[$x]['Approved User'] = $val->documentNarration == "Opening Balance" ? "" : $val->approvedBy;
                            }

                            if (in_array('app_date', $extraColumns)) {
                                $data[$x]['Approved Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentFinalApprovedDate);
                            }
                            $data[$x]['Supplier Name/Customer Name'] = $val->supplierOrCustomerName;
                            $data[$x]['Supplier Code/Customer Code'] = $val->supplierOrCustomerCode;
                            $data[$x]['Document Year'] = $val->documentYear;
                            $subTotalDebitRpt +=  round($val->rptDebit, $decimalPlaceRpt);
                            $subTotalCreditRpt += round($val->rptCredit, $decimalPlaceRpt);

                            $subTotalDebitLocal += round($val->localDebit, $decimalPlaceLocal);
                            $subTotalCreditLocal += round($val->localCredit, $decimalPlaceLocal);

                        }
                        $x++;
                        $data[$x]['Company ID'] = '';
                        $data[$x]['Company Name'] = '';
                        $data[$x]['Document Type'] = '';
                        $data[$x]['Document Description'] = '';
                        $data[$x]['Document Code'] = '';
                        $data[$x]['Posted Date'] = '';
                        $data[$x]['Document Narration'] = '';
                        $data[$x]['GL created date'] = '';
                        $data[$x]['Service Line'] = '';
                        $data[$x]['Contract'] = '';
                        $data[$x]['GL Code'] = '';
                        $data[$x]['Account Description'] = '';
                        $data[$x]['GL Type'] = 'Total';

                        $data[$x]['Transaction Currency'] = "";
                        $data[$x]['Transaction Debit Amount'] = "";
                        $data[$x]['Transaction Credit Amount'] = "";
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Local Currency'] = $currencyLocal;
                            $data[$x]['Local Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitLocal, $decimalPlaceLocal));
                            $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalCreditLocal, $decimalPlaceLocal));
                        }

                        $data[$x]['Reporting Currency'] = $currencyRpt;
                        $data[$x]['Reporting Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitRpt, $decimalPlaceRpt));
                        $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalCreditRpt, $decimalPlaceRpt));

                        if (in_array('confi_name', $extraColumns)) {
                            $data[$x]['Confirmed User'] = '';
                        }

                        if (in_array('confi_date', $extraColumns)) {
                            $data[$x]['Confirmed Date'] = '';
                        }

                        if (in_array('app_name', $extraColumns)) {
                            $data[$x]['Approved User'] = '';
                        }

                        if (in_array('app_date', $extraColumns)) {
                            $data[$x]['Approved Date'] = '';
                        }
                        $data[$x]['Supplier Name/Customer Name'] = '';
                        $data[$x]['Supplier Code/Customer Code'] = '';
                        $data[$x]['Document Year'] = '';

                        $x++;
                        $data[$x]['Company ID'] = '';
                        $data[$x]['Company Name'] = '';
                        $data[$x]['Document Type'] = '';
                        $data[$x]['Document Description'] = '';
                        $data[$x]['Document Code'] = '';
                        $data[$x]['Posted Date'] = '';
                        $data[$x]['Document Narration'] = '';
                        $data[$x]['GL created date'] = '';
                        $data[$x]['Service Line'] = '';
                        $data[$x]['Contract'] = '';
                        $data[$x]['GL Code'] = '';
                        $data[$x]['Account Description'] = '';
                        $data[$x]['GL Type'] = 'Balance';

                        $data[$x]['Transaction Currency'] = "";
                        $data[$x]['Transaction Debit Amount'] = "";
                        $data[$x]['Transaction Credit Amount'] = "";
                        if ($checkIsGroup->isGroup == 0) {
                            $data[$x]['Local Currency'] = $currencyLocal;
                            $data[$x]['Local Debit Amount'] = '';
                            $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitLocal-$subTotalCreditLocal, $decimalPlaceLocal));
                        }

                        $data[$x]['Reporting Currency'] = $currencyRpt;
                        $data[$x]['Reporting Debit Amount'] = '';
                        $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($subTotalDebitRpt-$subTotalCreditRpt, $decimalPlaceRpt));

                        if (in_array('confi_name', $extraColumns)) {
                            $data[$x]['Confirmed User'] = '';
                        }

                        if (in_array('confi_date', $extraColumns)) {
                            $data[$x]['Confirmed Date'] = '';
                        }

                        if (in_array('app_name', $extraColumns)) {
                            $data[$x]['Approved User'] = '';
                        }

                        if (in_array('app_date', $extraColumns)) {
                            $data[$x]['Approved Date'] = '';
                        }
                        $data[$x]['Supplier Name/Customer Name'] = '';
                        $data[$x]['Supplier Code/Customer Code'] = '';
                        $data[$x]['Document Year'] = '';

                        $x++;
                        $data[$x]['Company ID'] = '';
                        $x++;
                    }

                }
            }
            $x++;
            $data[$x]['Company ID'] = '';
            $data[$x]['Company Name'] = '';
            $data[$x]['Document Type'] = '';
            $data[$x]['Document Description'] = '';
            $data[$x]['Document Code'] = '';
            $data[$x]['Posted Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['GL created date'] = '';
            $data[$x]['Service Line'] = '';
            $data[$x]['Contract'] = '';
            $data[$x]['GL Code'] = '';
            $data[$x]['Account Description'] = '';
            $data[$x]['GL Type'] = 'Grand Total';

            $data[$x]['Transaction Currency'] = "";
            $data[$x]['Transaction Debit Amount'] = "";
            $data[$x]['Transaction Credit Amount'] = "";
            if ($checkIsGroup->isGroup == 0) {
                $data[$x]['Local Currency'] = $currencyLocal;
                $data[$x]['Local Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountDebit'], $decimalPlaceLocal));
                $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountCredit'], $decimalPlaceLocal));
            }
            $data[$x]['Reporting Currency'] = $currencyRpt;
            $data[$x]['Reporting Debit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountDebit'], $decimalPlaceRpt));
            $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountCredit'], $decimalPlaceRpt));

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed User'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved User'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier Name/Customer Name'] = '';
            $data[$x]['Supplier Code/Customer Code'] = '';
            $data[$x]['Document Year'] = '';

            $x++;
            $data[$x]['Company ID'] = '';
            $data[$x]['Company Name'] = '';
            $data[$x]['Document Type'] = '';
            $data[$x]['Document Description'] = '';
            $data[$x]['Document Code'] = '';
            $data[$x]['Posted Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['GL created date'] = '';
            $data[$x]['Service Line'] = '';
            $data[$x]['Contract'] = '';
            $data[$x]['GL Code'] = '';
            $data[$x]['Account Description'] = '';
            $data[$x]['GL Type'] = 'Total Balance';

            $data[$x]['Transaction Currency'] = "";
            $data[$x]['Transaction Debit Amount'] = "";
            $data[$x]['Transaction Credit Amount'] = "";
            if ($checkIsGroup->isGroup == 0) {
                $data[$x]['Local Currency'] = $currencyLocal;
                $data[$x]['Local Debit Amount'] = '';
                $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentLocalAmountDebit']-$total['documentLocalAmountCredit'], $decimalPlaceLocal));
            }
            $data[$x]['Reporting Currency'] = $currencyRpt;
            $data[$x]['Reporting Debit Amount'] = '';
            $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(round($total['documentRptAmountDebit']-$total['documentRptAmountCredit'], $decimalPlaceRpt));

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed User'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved User'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier Name/Customer Name'] = '';
            $data[$x]['Supplier Code/Customer Code'] = '';
            $data[$x]['Document Year'] = '';
        } else {
            $x = 0;
            $subTotalDebitRpt = 0;
            $subTotalCreditRpt = 0;
            $subTotalDebitLocal = 0;
            $subTotalCreditLocal = 0;

            if ($output) {
                $dataArrayNew = array();

                if(isset($request->isClosing) && !$request->isClosing && isset($request->month)) {
                    foreach($output as $ou) {
                        if(Carbon::parse($ou->documentDate)->format('d/m/Y') <= Carbon::parse($request->toDate)->format('d/m/Y')  && (Carbon::parse($ou->documentDate)->format('m')  == Carbon::parse($request->toDate)->format('m')) ) {
                            array_push($dataArrayNew,$ou);
                        }
                    }

                    $output = $dataArrayNew;
                }


                foreach ($output as $val) {
                    $data[$x]['Company ID'] = $val->companyID;
                    $data[$x]['Company Name'] = $val->CompanyName;
                    $data[$x]['Document Type'] = $val->documentID;
                    $data[$x]['Document Description'] = $val->documentNarration == "Opening Balance" ? "" : $val->documentDescription;
                    $data[$x]['Document Code'] = $val->documentCode;
                    $data[$x]['Posted Date'] = \Helper::dateFormat($val->documentDate);
                    $data[$x]['Document Narration'] = $val->documentNarration;
                    $data[$x]['GL created date'] = \Helper::dateFormat($val->createdDateTime);
                    $data[$x]['Service Line'] = $val->serviceLineCode;
                    $data[$x]['Contract'] = $val->clientContractID;
                    $data[$x]['GL Code'] = $val->glCode;
                    $data[$x]['Account Description'] = $val->AccountDescription;
                    $data[$x]['GL Type'] = $val->glAccountType;

                    $requestCurrencyTrans = CurrencyMaster::where('currencyID', $val->documentTransCurrencyID)->first();
                    $currencyTrans = !empty($requestCurrencyTrans) ? $requestCurrencyTrans->CurrencyCode : 'OMR';
                    $decimalPlaceTrans = !empty($requestCurrencyTrans) ? $requestCurrencyTrans->DecimalPlaces : 3;

                    if(!isset($request->month)){
                        $data[$x]['Transaction Currency'] = $val->documentNarration == "Opening Balance" ? "" : $currencyTrans;
                        $data[$x]['Transaction Debit Amount'] = $val->documentNarration == "Opening Balance" ? "" : round($val->transDebit, $decimalPlaceTrans);
                        $data[$x]['Transaction Credit Amount'] = $val->documentNarration == "Opening Balance" ? "" : round($val->transCredit, $decimalPlaceTrans);
                    }

                    if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
                        $data[$x]['Local Currency'] = $currencyLocal;
                        $data[$x]['Local Debit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->localDebit, $decimalPlaceLocal));
                        $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->localCredit, $decimalPlaceLocal));
                    }

                    if($request->currencyID == 2 || !isset($request->month)) {
                        $data[$x]['Reporting Currency'] = $currencyRpt;
                        $data[$x]['Reporting Debit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->rptDebit, $decimalPlaceRpt));
                        $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($val->rptCredit, $decimalPlaceRpt));
                    }

                    if (in_array('confi_name', $extraColumns)) {
                        $data[$x]['Confirmed User'] = $val->documentNarration == "Opening Balance" ? "" : $val->confirmedBy;
                    }

                    if (in_array('confi_date', $extraColumns)) {
                        $data[$x]['Confirmed Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentConfirmedDate);
                    }

                    if (in_array('app_name', $extraColumns)) {
                        $data[$x]['Approved User'] = $val->documentNarration == "Opening Balance" ? "" : $val->approvedBy;
                    }

                    if (in_array('app_date', $extraColumns)) {
                        $data[$x]['Approved Date'] = $val->documentNarration == "Opening Balance" ? "" : \Helper::dateFormat($val->documentFinalApprovedDate);
                    }
                    $data[$x]['Supplier Name/Customer Name'] = $val->supplierOrCustomerName;
                    $data[$x]['Supplier Code/Customer Code'] = $val->supplierOrCustomerCode;
                    $data[$x]['Document Year'] = $val->documentYear;

                    $subTotalDebitRpt += round($val->rptDebit, $decimalPlaceRpt);
                    $subTotalCreditRpt += round($val->rptCredit, $decimalPlaceRpt);

                    $subTotalDebitLocal += round($val->localDebit, $decimalPlaceLocal);
                    $subTotalCreditLocal += round($val->localCredit, $decimalPlaceLocal);

                    $x++;
                }
                $data[$x]['Company ID'] = "";
                $x++;
            }
            $data[$x]['Company ID'] = "";
            $data[$x]['Company Name'] = "";
            $data[$x]['Document Type'] = "";
            $data[$x]['Document Description'] = "";
            $data[$x]['Document Code'] = "";
            $data[$x]['Posted Date'] = "";
            $data[$x]['Document Narration'] = "";
            $data[$x]['GL created date'] = "";
            $data[$x]['Service Line'] = "";
            $data[$x]['Contract'] = "";
            $data[$x]['GL Code'] = "";
            $data[$x]['Account Description'] = "";
            $data[$x]['GL Type'] = "Grand Total";

            if($request->currencyID == 1 || !isset($request->month)){
                $data[$x]['Transaction Currency'] = "";
                $data[$x]['Transaction Debit Amount'] = "";
                $data[$x]['Transaction Credit Amount'] = "";
            }

            if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
                $data[$x]['Local Currency'] = $currencyLocal;
                $data[$x]['Local Debit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitLocal, $decimalPlaceLocal));
                $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalCreditLocal, $decimalPlaceLocal));
            }

            if($request->currencyID == 2 || !isset($request->month)) {
                $data[$x]['Reporting Currency'] = $currencyRpt;
                $data[$x]['Reporting Debit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitRpt, $decimalPlaceRpt));
                $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalCreditRpt, $decimalPlaceRpt));
            }

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed User'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved User'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier Name/Customer Name'] = '';
            $data[$x]['Supplier Code/Customer Code'] = '';
            $data[$x]['Document Year'] = '';

            $x++;
            $data[$x]['Company ID'] = "";
            $data[$x]['Company Name'] = "";
            $data[$x]['Document Type'] = "";
            $data[$x]['Document Description'] = "";
            $data[$x]['Document Code'] = "";
            $data[$x]['Posted Date'] = "";
            $data[$x]['Document Narration'] = "";
            $data[$x]['GL created date'] = "";
            $data[$x]['Service Line'] = "";
            $data[$x]['Contract'] = "";
            $data[$x]['GL Code'] = "";
            $data[$x]['Account Description'] = "";
            $data[$x]['GL Type'] = "Total Balance";

            if($request->currencyID == 1 || !isset($request->month)){
                $data[$x]['Transaction Currency'] = "";
                $data[$x]['Transaction Debit Amount'] = "";
                $data[$x]['Transaction Credit Amount'] = "";
            }

            if (($checkIsGroup->isGroup == 0 && ($request->currencyID == 1)) || !isset($request->month)) {
                $data[$x]['Local Currency'] = $currencyLocal;
                $data[$x]['Local Debit Amount'] = "";
                $data[$x]['Local Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitLocal - $subTotalCreditLocal, $decimalPlaceLocal));
            }

            if($request->currencyID == 2 || !isset($request->month)) {
                $data[$x]['Reporting Currency'] = $currencyRpt;
                $data[$x]['Reporting Debit Amount'] = "";
                $data[$x]['Reporting Credit Amount'] = CurrencyService::convertNumberFormatToNumber(number_format($subTotalDebitRpt - $subTotalCreditRpt, $decimalPlaceRpt));
            }

            if (in_array('confi_name', $extraColumns)) {
                $data[$x]['Confirmed User'] = '';
            }

            if (in_array('confi_date', $extraColumns)) {
                $data[$x]['Confirmed Date'] = '';
            }

            if (in_array('app_name', $extraColumns)) {
                $data[$x]['Approved User'] = '';
            }

            if (in_array('app_date', $extraColumns)) {
                $data[$x]['Approved Date'] = '';
            }
            $data[$x]['Supplier Name/Customer Name'] = '';
            $data[$x]['Supplier Code/Customer Code'] = '';
            $data[$x]['Document Year'] = '';
        }

        $company_name = $companyCurrency->CompanyName;
        $toDate = $request->toDate;
        $fromDate = $request->fromDate;
        $cur = null;
        $title = "GL Dump Report";

        $companyCode = isset($companyCurrency->CompanyID)?$companyCurrency->CompanyID:'common';

        $fileName = 'GL_Dump_Report';
        $path = 'general-ledger/report/general_ledger/excel/';

        $excelFormat = [
            'R' => '#,##0.' . str_repeat('0', $decimalPlaceLocal),
            'S' => '#,##0.' . str_repeat('0', $decimalPlaceLocal),
            'U' => '#,##0.' . str_repeat('0', $decimalPlaceRpt),
            'V' => '#,##0.' . str_repeat('0', $decimalPlaceRpt),
        ];

        $exportToExcel = $exportGlToExcelService
            ->setTitle($title)
            ->setFileName($fileName)
            ->setPath($path)
            ->setCompanyCode($companyCode)
            ->setCompanyName($company_name)
            ->setFromDate($fromDate)
            ->setToDate($toDate)
            ->setReportType(1)
            ->setData($data)
            ->setType('xls')
            ->setDateType()
            ->setExcelFormat($excelFormat)
            ->setDetails()
            ->generateExcel();

        if(!$exportToExcel['success'])
            return $this->sendError('Unable to export excel');

        return $this->sendResponse($exportToExcel['data'], trans('custom.success_export'));

    }
    public function getTrialBalance($request)
    {
        $fromDate = new Carbon($request->fromDate);
        //$fromDate = $asOfDate->addDays(1);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        //$toDate = $toDate->addDays(1);
        $toDate = $toDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        //DB::enableQueryLog();

        $isCompanyWise = '';
        $isCompanyWiseGL = '';
        $isCompanyWiseGLGroupBy = '';

        if ($request->reportSD == 'company_wise') {
            $isCompanyWise = 'companySystemID,';
            $isCompanyWiseGL = 'erp_generalledger.companySystemID,';
        }

        $serviceLines = join(',', array_map(function ($sl) {
            return $sl['serviceLineSystemID'];
        }, $request->selectedServicelines));

        $query = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        glAccountTypeID,
                        documentLocalCurrencyID,
                        SUM(IF(documentLocalAmount >= 0, documentLocalAmount, 0)) AS documentLocalAmountDebit,
                        SUM(IF(documentLocalAmount < 0, -documentLocalAmount, 0)) AS documentLocalAmountCredit,
                        documentRptCurrencyID,
                        SUM(IF(documentRptAmount >= 0, documentRptAmount, 0)) AS documentRptAmountDebit,
                        SUM(IF(documentRptAmount < 0, -documentRptAmount, 0)) AS documentRptAmountCredit,
                        order_no
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "1" AS glAccountTypeID,
                        "Accumulated Retained Earnings (Automated)" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentLocalCurrencyER,
                        0 AS documentLocalAmount,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentRptCurrencyER,
                        0 documentRptAmount,
                        1 as order_no
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" -- filter by from date
                    GROUP BY
                        ' . $isCompanyWiseGL . '
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum -- ERP_qry_TBBS_BF_sum
                    UNION ALL
                SELECT
                    * 
                FROM
                    (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        erp_generalledger.documentDate AS documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode AS glCode,
                        erp_generalledger.glAccountType AS glAccountType,
                        erp_generalledger.glAccountTypeID AS glAccountTypeID,
                        "Retained Earnings" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentLocalCurrencyER,
                        0 AS documentLocalAmount,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentRptCurrencyER,
                        0 documentRptAmount,
                        2 as order_no 
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                         chartofaccounts.is_retained_earnings = 1    
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" -- filter by from date
                        GROUP BY glCode
                    ) AS ERP_qry_Manual_Code -- New manual code object
                UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            erp_generalledger.glAccountTypeID AS glAccountTypeID,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            0 AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            0 documentRptAmount,
                            3 as order_no 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                        WHERE
                            erp_generalledger.glAccountType = "BS" 
                            AND chartofaccounts.is_retained_earnings != 1
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBBS_BF -- filter by from date ERP_qry_TBBS_BF;
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            erp_generalledger.glAccountTypeID AS glAccountTypeID,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount,
                            4 as order_no  
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBBS -- ERP_qry_TBBS
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            erp_generalledger.glAccountTypeID AS glAccountTypeID,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount,
                            5 as order_no   
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                         ORDER BY
                        CASE 
                            WHEN AccountDescription = "Accumulated Retained Earnings (Automated)" THEN 1
                            WHEN AccountDescription = "Retained Earnings" THEN 2
                            ELSE 3
                        END,
                        glCode;';


        $output = \DB::select($query);
        $query1 = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                       SUM(documentLocalAmount) AS openingBalLocal,
                        documentRptCurrencyID,
                        SUM( documentRptAmount) AS openingBalRpt,
                        order_no
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Accumulated Retained Earnings (Automated)" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentLocalCurrencyER,
                        sum( erp_generalledger.documentLocalAmount *- 1 ) AS documentLocalAmount,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentRptCurrencyER,
                        sum( erp_generalledger.documentRptAmount * - 1 ) documentRptAmount,
                        1 as order_no  
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" -- filter by from date
                    GROUP BY
                        ' . $isCompanyWiseGL . '
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum -- ERP_qry_TBBS_BF_sum
                          UNION ALL
                SELECT
                    * 
                FROM
                    (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        erp_generalledger.chartOfAccountSystemID AS chartOfAccountSystemID,
                        erp_generalledger.glCode AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earnings" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentLocalCurrencyER,
                        sum( erp_generalledger.documentLocalAmount) AS documentLocalAmount,
                        erp_generalledger.documentRptCurrencyID,
                        erp_generalledger.documentRptCurrencyER,
                        sum( erp_generalledger.documentRptAmount) documentRptAmount,
                        2 as order_no  
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                         chartofaccounts.is_retained_earnings = 1
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" -- filter by from date
                         GROUP BY erp_generalledger.chartOfAccountSystemID
                    ) AS ERP_qry_Manual_Code -- New manual code object
                UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            erp_generalledger.documentLocalAmount AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            erp_generalledger.documentRptAmount documentRptAmount,
                            3 as order_no  
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                        WHERE
                            erp_generalledger.glAccountType = "BS" 
                            AND chartofaccounts.is_retained_earnings != 1
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBBS_BF -- filter by from date ERP_qry_TBBS_BF;
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            0 AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            0 documentRptAmount,
                            4 as order_no  
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBBS -- ERP_qry_TBBS
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            0 AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            0 documentRptAmount,
                            5 as order_no    
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                               AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                          ORDER BY
                          CASE 
                            WHEN AccountDescription = "Accumulated Retained Earnings (Automated)" THEN 1
                            WHEN AccountDescription = "Retained Earnings" THEN 2
                            ELSE 3
                          END,
                          glCode;';
        $output1 = \DB::select($query1);
        $i = 0;

        foreach ($output as $item) {

            if($item->glAccountTypeID == 1) {
                $output[$i]->openingBalLocal = $output1[$i]->openingBalLocal;
                $output[$i]->openingBalRpt = $output1[$i]->openingBalRpt;
            }
            if($item->glAccountTypeID > 1) {
                $output[$i]->openingBalLocal = 0;
                $output[$i]->openingBalRpt = 0;
            }
            $i++;

        }
        //dd(DB::getQueryLog());
        return $output;
    }

    public function getTrialBalanceMonthWise($request)
    {
        $fromDate1 = new Carbon($request->fromDate);
        $fromDate = $fromDate1->format('Y-m-d');

        $toDate1 = new Carbon($request->toDate);
        $toDate = $toDate1->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $serviceLines = join(',', array_map(function ($sl) {
            return $sl['serviceLineSystemID'];
        }, $request->selectedServicelines));

        //DB::enableQueryLog();

        $isCompanyWise = '';
        $isCompanyWiseGL = '';
        $isCompanyWiseGLGroupBy = '';

        if ($request->reportSD == 'company_wise') {
            $isCompanyWise = 'companySystemID,';
            $isCompanyWiseGL = 'erp_generalledger.companySystemID,';
        }

        $currencyClm = 'erp_generalledger.documentRptAmount';
        if ($request->currencyID == 1) {
            $currencyClm = 'erp_generalledger.documentLocalAmount';
        }

        $period = \Carbon\CarbonPeriod::create($fromDate, '1 month', $toDate);

        $defaultMonth = array();
        $defaultMonthSum = array();
        foreach ($period as $dt) {
            //echo $dt->format("M") . "<br>\n";
            $monthName = $dt->format("M");
            if($monthName == 'Dec'){
                $monthName = 'Dece';
            }

            array_push($defaultMonth,$monthName);
            $monthId = ltrim($dt->format("m"), "0");
            $temMonthSum = "IF ( MONTH ( erp_generalledger.documentDate ) = " . $monthId .", " . $currencyClm . ", 0 ) AS ".$monthName;
            array_push($defaultMonthSum,$temMonthSum);
        }


        /*$defaultMonth = array(
            'Jan',
            'Feb',
            'March',
            'April',
            'May',
            'June',
            'July',
            'Aug',
            'Sept',
            'Oct',
            'Nov',
            'Dece'
        );*/

       /* $defaultMonthSum = array(
            "IF
                    ( MONTH ( erp_generalledger.documentDate ) = 1, " . $currencyClm . ", 0 ) AS Jan",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 2, " . $currencyClm . ", 0 ) AS Feb",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 3, " . $currencyClm . ", 0 ) AS March",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 4, " . $currencyClm . ", 0 ) AS April",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 5, " . $currencyClm . ", 0 ) AS May",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 6, " . $currencyClm . ", 0 ) AS June",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 7, " . $currencyClm . ", 0 ) AS July",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 8, " . $currencyClm . ", 0 ) AS Aug",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 9, " . $currencyClm . ", 0 ) AS Sept",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 10, " . $currencyClm . ", 0 ) AS Oct",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 11, " . $currencyClm . ", 0 ) AS Nov",
            " IF
                    ( MONTH ( erp_generalledger.documentDate ) = 12, " . $currencyClm . ", 0 ) AS Dece"
        );*/

        $monthClosing = array(); //'sum(Opening + Jan) AS JanClosing';

        $availableMonth = array();
        $monthSum = array();
        $monthZero = array();
        $month = array();

        $totalMonth = "";

        foreach ($defaultMonth as $key => $value) {
            //if (($key + 1) <= intval($toDate1->format('m')) && ($key + 1) >= intval($fromDate1->format('m'))) {
                array_push($availableMonth, $value);
            //}
        }

        foreach ($defaultMonthSum as $key => $value) {
           // if (($key + 1) <= intval($toDate1->format('m')) && ($key + 1) >= intval($fromDate1->format('m'))) {
                array_push($month, $value);
           // }
        }


        foreach ($availableMonth as $value) {
            array_push($monthSum, 'sum(' . $value . ') as ' . $value);
            array_push($monthZero, '0 as ' . $value);
        }

        foreach ($availableMonth as $key => $value) {
            if ($key == 0) {
                $totalMonth = $totalMonth . $value;
            } else {
                $totalMonth = $totalMonth . '+' . $value;
            }
            $opening = "sum(Opening +" . $totalMonth . ") AS " . $value . 'Closing';

            array_push($monthClosing, $opening);
        }


        $monthArray = implode(",", $availableMonth);
        $monthSum = implode(",", $monthSum);
        $monthZero = implode(",", $monthZero);
        $month = implode(",", $month);
        $monthClosing = implode(",", $monthClosing);

        /*$monthArray = 'Jan,
                 Feb,
                 March,
                 April,
                 May,
                 June,
                 July,
                 Aug,
                 Sept,
                 Oct,
                 Nov,
                 Dece';

        $monthSum = 'sum(Jan) as Jan,
                    sum(Feb) as Feb,
                    sum(March) as March,
                    sum(April) as April,
                    sum(May) as May,
                    sum(June) as June,
                    sum(July) as July,
                    sum(Aug) as Aug,
                    sum(Sept) as Sept,
                    sum(Oct) as Oct,
                    sum(Nov) as Nov,
                    sum(Dece) as Dece';

        $monthZero = '0 AS Jan,
                        0 AS Feb,
                        0 AS March,
                        0 AS April,
                        0 AS May,
                        0 AS June,
                        0 AS July,
                        0 AS Aug,
                        0 AS Sept,
                        0 AS Oct,
                        0 AS Nov,
                        0 AS Dece';

        $month = 'IF
                    ( MONTH ( erp_generalledger.documentDate ) = 1, ' . $currencyClm . ', 0 ) AS Jan,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 2, ' . $currencyClm . ', 0 ) AS Feb,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 3, ' . $currencyClm . ', 0 ) AS March,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 4, ' . $currencyClm . ', 0 ) AS April,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 5, ' . $currencyClm . ', 0 ) AS May,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 6, ' . $currencyClm . ', 0 ) AS June,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 7, ' . $currencyClm . ', 0 ) AS July,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 8, ' . $currencyClm . ', 0 ) AS Aug,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 9, ' . $currencyClm . ', 0 ) AS Sept,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 10, ' . $currencyClm . ', 0 ) AS Oct,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 11, ' . $currencyClm . ', 0 ) AS Nov,
                IF
                    ( MONTH ( erp_generalledger.documentDate ) = 12, ' . $currencyClm . ', 0 ) AS Dece';


        $monthClosing = 'sum(Opening + Jan) AS JanClosing,
                        sum(Opening + Jan + Feb) AS FebClosing,
                        sum(Opening + Jan + Feb + March) AS MarchClosing,
                        sum(Opening + Jan + Feb + March + April) AS AprilClosing,
                        sum(Opening + Jan + Feb + March + April + May) AS MayClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June) AS JuneClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July) AS JulyClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug) AS AugClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept) AS SeptClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct) AS OctClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct + Nov) AS NovClosing,
                        sum(Opening + Jan + Feb + March + April + May +  June + July + Aug + Sept + Oct + Nov + Dece) AS DeceClosing';*/


        $query = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        0 AS documentAmountOpening,
                        ' . $monthSum . '
                    FROM
                        (
      
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earning" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                        ' . $monthZero . '
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                         AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                    GROUP BY
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                             ' . $month . '
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBBS 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            MONTH ( erp_generalledger.documentDate ) AS DocMONTH,
                             ' . $month . '
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';

        $query1 = 'SELECT
                        companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        SUM(documentAmount) AS documentAmountOpening,
                        ' . $monthZero . '
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        companymaster.CompanyName,
                        "" AS documentDate,
                        0 AS chartOfAccountSystemID,
                        "-" AS glCode,
                        "BS" AS glAccountType,
                        "Retained Earning" AS AccountDescription,
                        erp_generalledger.documentLocalCurrencyID,
                        erp_generalledger.documentRptCurrencyID,
                        sum( ' . $currencyClm . ' * -1) AS documentAmount
                    FROM
                        erp_generalledger
                        LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                        INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID
                    WHERE
                        erp_generalledger.glAccountType = "BS" 
                        AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                         AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        glCode
                        ) AS ERP_qry_TBBS_BF_sum 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            ' . $currencyClm . ' AS documentAmount
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBBS 
                    UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            erp_generalledger.documentDate AS documentDate,
                            erp_generalledger.chartOfAccountSystemID,
                            erp_generalledger.glCode AS glCode,
                            erp_generalledger.glAccountType AS glAccountType,
                            chartofaccounts.AccountDescription AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentRptCurrencyID,
                            0 AS documentAmount
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID 
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            chartofaccounts.catogaryBLorPL = "PL" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                             AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                        ) AS ERP_qry_TBPL 
                        ) AS FINAL 
                    GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';


        $finalQry = 'SELECT *,
                        ' . $monthClosing . '
                        FROM (SELECT  companySystemID,
                        companyID,
                        CompanyName,
                        chartOfAccountSystemID,
                        glCode,
                        AccountDescription,
                        glAccountType,
                        documentLocalCurrencyID,
                        documentRptCurrencyID,
                        sum(documentAmountOpening) As Opening,
                        ' . $monthArray . ' FROM (SELECT * FROM (' . $query . ') AS a UNION ALL SELECT * FROM (' . $query1 . ') AS b) AS c GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode) f GROUP BY
                        ' . $isCompanyWise . 'chartOfAccountSystemID
                        order by glAccountType,glCode';

        //return $finalQry;
        $output = \DB::select($finalQry);
        //dd(DB::getQueryLog());
        return array('data' => $output, 'headers' => $availableMonth);
    }

    public function getTrialBalanceDetails($request)
    {
        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        $chartOfAccountID = $request->chartOfAccountSystemID;
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }
        $chartOfAccount = ChartOfAccount::find($chartOfAccountID);

        $serviceLines = join(',', array_map(function ($sl) {
            return $sl['serviceLineSystemID'];
        }, $request->selectedServicelines));


        $dateQry = '';
        if ($chartOfAccount) {
            // if ($chartOfAccount->catogaryBLorPLID == 2) {
            //     $dateQry = 'DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
            // } else {
            //     $dateQry = 'DATE(erp_generalledger.documentDate) <= "' . $toDate . '" ';
            // }
            $dateQry = 'DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
        }

        $query = 'SELECT erp_generalledger.companyID,
                                    companymaster.CompanyName,
                                    erp_generalledger.documentLocalCurrencyID,
                                    erp_generalledger.documentRptCurrencyID,
                                    erp_generalledger.documentID,
                                    erp_generalledger.documentSystemID,
                                    erp_generalledger.documentSystemCode,
                                    erp_generalledger.documentCode,
                                    erp_generalledger.documentDate,
                                    erp_generalledger.glCode,
                                    erp_generalledger.documentNarration,
                                    If ( documentLocalAmount> 0,documentLocalAmount, 0 ) as localDebit,
                                    If ( documentLocalAmount> 0,0, documentLocalAmount*-1 ) as localCredit,
                                    If ( documentRptAmount> 0,documentRptAmount, 0 ) as rptDebit,
                                    If ( documentRptAmount> 0,0, documentRptAmount*-1 ) as rptCredit
                                FROM
                                    erp_generalledger 
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                                WHERE
                                    ' . $dateQry . '    
                                    AND erp_generalledger.chartOfAccountSystemID  = ' . $chartOfAccountID . '
                                    AND erp_generalledger.serviceLineSystemID IN (' . $serviceLines . ')
                                    AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')  ORDER BY erp_generalledger.documentDate;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public
    function getTrialBalanceCompanyWise($request, $subCompanies)
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

        $sumQry = "";
        $groupIfQry = "";

        foreach ($subCompanies as $company) {
            $sumQry = $sumQry . ' SUM(`' . $company['CompanyID'] . '`) AS `' . $company['CompanyID'] . '`,';
            $groupIfQry = $groupIfQry . ' IF ( companySystemID = ' . $company['companySystemID'] . ', documentRptAmount, 0 ) AS `' . $company['CompanyID'] . '`,';
        }

        $query = 'SELECT chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $sumQry . '
                            documentRptCurrencyID,
                            companySystemID FROM(SELECT chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $sumQry . '
                            documentRptCurrencyID,
                            companySystemID
                            FROM
                        (SELECT
                            chartOfAccountSystemID,
                            glCode,
                            AccountDescription,
                            glAccountType,
                            ' . $groupIfQry . '
                            documentRptCurrencyID,
                            companySystemID
                        FROM
                            (
                        SELECT
                            * 
                        FROM
                            (
                        SELECT
                            erp_generalledger.companySystemID,
                            erp_generalledger.companyID,
                            companymaster.CompanyName,
                            "" AS documentDate,
                            0 AS chartOfAccountSystemID,
                            "-" AS glCode,
                            "BS" AS glAccountType,
                            "Retained Earning" AS AccountDescription,
                            erp_generalledger.documentLocalCurrencyID,
                            erp_generalledger.documentLocalCurrencyER,
                            sum( erp_generalledger.documentLocalAmount *- 1 ) AS documentLocalAmount,
                            erp_generalledger.documentRptCurrencyID,
                            erp_generalledger.documentRptCurrencyER,
                            sum( erp_generalledger.documentRptAmount * - 1 ) documentRptAmount 
                        FROM
                            erp_generalledger
                            LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                            INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                        WHERE
                            erp_generalledger.glAccountType = "BS" 
                            AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                            AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"  
                            
                        GROUP BY
                            companySystemID,
                            glCode,
                            glAccountType 
                            ) AS ERP_qry_TBBS_BF_sum
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                erp_generalledger.glAccountType = "BS" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '" 
                            ) AS ERP_qry_TBBS_BF 
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                chartofaccounts.catogaryBLorPL = "BS" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                            ) AS ERP_qry_TBBS 
                        UNION ALL
                        SELECT
                            * 
                        FROM
                            (
                            SELECT
                                erp_generalledger.companySystemID,
                                erp_generalledger.companyID,
                                companymaster.CompanyName,
                                erp_generalledger.documentDate AS documentDate,
                                erp_generalledger.chartOfAccountSystemID,
                                erp_generalledger.glCode AS glCode,
                                erp_generalledger.glAccountType AS glAccountType,
                                chartofaccounts.AccountDescription AS AccountDescription,
                                erp_generalledger.documentLocalCurrencyID,
                                erp_generalledger.documentLocalCurrencyER,
                                erp_generalledger.documentLocalAmount AS documentLocalAmount,
                                erp_generalledger.documentRptCurrencyID,
                                erp_generalledger.documentRptCurrencyER,
                                erp_generalledger.documentRptAmount documentRptAmount 
                            FROM
                                erp_generalledger
                                LEFT JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                                INNER JOIN companymaster ON erp_generalledger.companySystemID = companymaster.companySystemID 
                            WHERE
                                chartofaccounts.catogaryBLorPL = "PL" 
                                AND erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                            ) AS ERP_qry_TBPL 
                            ) AS FINAL ) AS FINALFINL GROUP BY chartOfAccountSystemID,companySystemID ORDER BY glCode) As fi GROUP BY chartOfAccountSystemID;';

        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }


    public function getGeneralLedger($request)
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

        $glCodes = (array)$request->glCodes;

        $type = $request->type;
        $chartOfAccountIdAll = collect($glCodes)->pluck('chartOfAccountSystemID')->toArray();
        $chartOfAccountId = collect($glCodes)->pluck('chartOfAccountSystemID')->toArray();
        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());
        $chartOfAccountIdRetainedVal = collect($glCodes)
                ->where('is_retained_earnings', 1)
                ->first();
        array_push($serviceLineId, 24);
        $chartOfAccountIdRetained = $chartOfAccountIdRetainedVal ? $chartOfAccountIdRetainedVal['chartOfAccountSystemID'] : 0;

        $chartOfAccountIdCount = count($chartOfAccountId);

        if($chartOfAccountIdRetained != 0 && count($chartOfAccountId) > 1)
        {
            $chartOfAccountId= array_filter($chartOfAccountId, function ($item) use($chartOfAccountIdRetained){
                return $item !== $chartOfAccountIdRetained;
            });
            $chartOfAccountId = array_values($chartOfAccountId);
        }

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());
        array_push($contractsId, 159);
        //contracts
        $query = 'SELECT * 
                    FROM
                        (
                            SELECT
                                * 
                            FROM
                                (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.serviceLineSystemID,
                                        erp_generalledger.serviceLineCode,
                                        erp_generalledger.documentSystemID,
                                        erp_generalledger.documentID,
                                        erp_generalledger.documentSystemCode,
                                        erp_generalledger.documentCode,
                                        erp_generalledger.documentDate,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        erp_generalledger.glAccountType,
                                        erp_generalledger.documentNarration,
                                        erp_generalledger.clientContractID,
                                        erp_generalledger.supplierCodeSystem,
                                        erp_generalledger.documentLocalCurrencyID,
                                        chartofaccounts.AccountDescription,
                                        companymaster.CompanyName,
                                        erp_templatesglcode.templatesDetailsAutoID as templatesDetailsAutoID,
                                        approveEmp.empName as approvedBy,
                                        confirmEmp.empName as confirmedBy,
                                        erp_generalledger.documentConfirmedDate,
                                        erp_generalledger.documentFinalApprovedDate,
                                        erp_templatesglcode.templateMasterID,
                                        erp_templatesdetails.templateDetailDescription,
                                        erp_companyreporttemplatedetails.description as templateDescription,
                                        4 As orderNo,
                                    IF
                                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                                    IF
                                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                                       CASE 
                                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                            (
                                                IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) - (
                                                IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 )) ELSE (
                                                IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0) - (
                                                IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ))
                                        END AS doucmentLocalBalanceAmount,
                                        erp_generalledger.documentRptCurrencyID,
                                    IF
                                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                                    IF
                                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                                    CASE
                                        WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                        (
                                            IF ( documentRptAmount > 0, documentRptAmount, 0 )) - (
                                            IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 )) ELSE (
                                            IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) - (
                                            IF ( documentRptAmount > 0, documentRptAmount, 0 ))) 
                                    END AS documentRptBalanceAmount,
                                    IF
                                        ( erp_generalledger.documentSystemID = 87 OR erp_generalledger.documentSystemID = 71 OR erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer 
                                    FROM
                                        erp_generalledger
                                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                                        )
                                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                                        LEFT JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatedetails.detID = chartofaccounts.reportTemplateCategory 
                                    WHERE
                                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountIdAll) . ')
                                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                                ) AS erp_qry_GL UNION ALL
                            SELECT
                                * 
                            FROM
                                (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.serviceLineSystemID,
                                        erp_generalledger.serviceLineCode,
                                        "" AS documentSystemID,
                                        "" AS documentID,
                                        "" AS documentSystemCode,
                                        "" AS documentCode,
                                        "" AS documentDate,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        "BS" AS glAccountType,
                                        "Opening Balance" AS documentNarration,
                                        "" AS clientContractID,
                                        "" AS supplierCodeSystem,
                                        erp_generalledger.documentLocalCurrencyID,
                                        chartofaccounts.AccountDescription,
                                        companymaster.CompanyName,
                                        erp_templatesglcode.templatesDetailsAutoID,
                                        approveEmp.empName as approvedBy,
                                        confirmEmp.empName as confirmedBy,
                                        erp_generalledger.documentConfirmedDate,
                                        erp_generalledger.documentFinalApprovedDate,
                                        erp_templatesglcode.templateMasterID,
                                        erp_templatesdetails.templateDetailDescription,
                                        erp_companyreporttemplatedetails.description as templateDescription,
                                        3 As orderNo,
                                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                                        CASE    
                                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                            (
                                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) )) - (
                                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) )) ELSE (
                                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) - (
                                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ))) 
                                        END AS doucmentLocalBalanceAmount,
                                        erp_generalledger.documentRptCurrencyID,
                                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                                        CASE
                                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                            (
                                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) )) - (
                                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) )) ELSE (
                                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) - (
                                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ))) 
                                        END AS documentRptBalanceAmount,
                                        "" AS isCustomer
                                    FROM
                                        erp_generalledger
                                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                                        )
                                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
                                        LEFT JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatedetails.detID = chartofaccounts.reportTemplateCategory 
                                        WHERE
                                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                        AND erp_generalledger.glAccountTypeID = 1
                                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                                    GROUP BY
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.serviceLineSystemID,
                                        erp_generalledger.chartOfAccountSystemID
                                ) AS erp_qry_gl_bf   
                            UNION ALL
                            SELECT
                                * 
                            FROM
                                (
                                    SELECT
                                        erp_generalledger.companySystemID,
                                        erp_generalledger.companyID,
                                        erp_generalledger.serviceLineSystemID,
                                        erp_generalledger.serviceLineCode,
                                        "" AS documentSystemID,
                                        "" AS documentID,
                                        "" AS documentSystemCode,
                                        "" AS documentCode,
                                        "" AS documentDate,
                                        erp_generalledger.chartOfAccountSystemID,
                                        erp_generalledger.glCode,
                                        "BS" AS glAccountType,
                                        "Opening Linked" AS documentNarration,
                                        "" AS clientContractID,
                                        "" AS supplierCodeSystem,
                                        erp_generalledger.documentLocalCurrencyID,
                                        "Retained Earnings" AS AccountDescription,
                                        companymaster.CompanyName,
                                        erp_templatesglcode.templatesDetailsAutoID,
                                        approveEmp.empName as approvedBy,
                                        confirmEmp.empName as confirmedBy,
                                        erp_generalledger.documentConfirmedDate,
                                        erp_generalledger.documentFinalApprovedDate,
                                        erp_templatesglcode.templateMasterID,
                                        erp_templatesdetails.templateDetailDescription,
                                        erp_companyreporttemplatedetails.description as templateDescription,
                                        2 As orderNo,
                                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                                        CASE    
                                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                            (
                                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) )) - (
                                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) )) ELSE (
                                                sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) - (
                                                sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ))) 
                                        END AS doucmentLocalBalanceAmount,
                                        erp_generalledger.documentRptCurrencyID,
                                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                                        CASE
                                            WHEN controlAccounts = "BSA" OR controlAccounts = "PLE" THEN
                                            (
                                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) )) - (
                                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) )) ELSE (
                                                sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) - (
                                                sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ))) 
                                        END AS documentRptBalanceAmount,
                                        "" AS isCustomer
                                    FROM
                                        erp_generalledger
                                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                                        )
                                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
                                        LEFT JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatedetails.detID = chartofaccounts.reportTemplateCategory 
                                        WHERE
                                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                                        AND chartofaccounts.is_retained_earnings = 1
                                        AND erp_generalledger.chartOfAccountSystemID = ' . $chartOfAccountIdRetained . '
                                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                                          AND ' . intval($chartOfAccountIdCount) . ' > 1
                                    GROUP BY
                                        erp_generalledger.chartOfAccountSystemID
                                ) AS erp_retained_earning_manual                               

                    ) AS GL_final 
                    ORDER BY
                        orderNo ASC';

        return  \DB::select($query);
    }

    public function getGeneralLedgerSortedData($request)
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

        $glCodes = (array)$request->glCodes;

        $type = $request->type;
        $chartOfAccountId = collect($glCodes)->pluck('chartOfAccountSystemID')->toArray();
        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());

        array_push($serviceLineId, 24);

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());
        array_push($contractsId, 159);
        //contracts
        $query = 'SELECT * 
                    FROM
                        (
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.documentYear,
                        erp_generalledger.createdDateTime,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        erp_generalledger.documentNarration,
                        erp_generalledger.clientContractID,
                        erp_generalledger.supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        erp_templatesglcode.templatesDetailsAutoID as templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                        erp_companyreporttemplatedetails.description as templateDescription,
                        erp_documentmaster.documentDescription,
                        IF
                            ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                        IF
                            ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                        IF
                            ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                        IF
                            ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                        erp_generalledger.documentRptCurrencyID,
                        IF
                            ( documentTransAmount > 0, documentTransAmount, 0 ) AS transDebit,
                        IF
                            ( documentTransAmount < 0, ( documentTransAmount *- 1 ), 0 ) AS transCredit,
                        erp_generalledger.documentTransCurrencyID,
                        IF
                        ( erp_generalledger.documentSystemID = 87 OR erp_generalledger.documentSystemID = 71 OR erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS supplierOrCustomerName,
                        IF
                        ( erp_generalledger.documentSystemID = 87 OR erp_generalledger.documentSystemID = 71 OR erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CutomerCode, suppliermaster.primarySupplierCode ) AS supplierOrCustomerCode
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID 
                        LEFT JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatedetails.detID = chartofaccounts.reportTemplateCategory 
                        LEFT JOIN erp_documentmaster ON erp_documentmaster.documentSystemID = erp_generalledger.documentSystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        * 
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        "" AS documentYear,
                        "" AS createdDateTime,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        "BS" AS glAccountType,
                        "Opening Balance" AS documentNarration,
                        "" AS clientContractID,
                        "" AS supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        erp_templatesglcode.templatesDetailsAutoID,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate,
                        erp_templatesglcode.templateMasterID,
                        erp_templatesdetails.templateDetailDescription,
                        erp_companyreporttemplatedetails.description as templateDescription,
                        erp_documentmaster.documentDescription,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentTransAmount > 0, documentTransAmount, 0 ) ) AS transDebit,
                        sum( IF ( documentTransAmount < 0, ( documentTransAmount *- 1 ), 0 ) ) AS transCredit,
                        erp_generalledger.documentTransCurrencyID,
                        "" AS supplierOrCustomerName,
                        "" AS supplierOrCustomerCode
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem 
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID 
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID 
                        LEFT JOIN erp_templatesglcode ON erp_templatesglcode.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID AND erp_templatesglcode.templateMasterID IN (
                            SELECT erp_templatesmaster.templatesMasterAutoID FROM erp_templatesmaster
                                  WHERE erp_templatesmaster.isActive = -1 AND  erp_templatesmaster.isBudgetUpload = -1
                        )
                        LEFT JOIN erp_templatesdetails ON erp_templatesdetails.templatesDetailsAutoID = erp_templatesglcode.templatesDetailsAutoID
                        LEFT JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatedetails.detID = chartofaccounts.reportTemplateCategory 
                        LEFT JOIN erp_documentmaster ON erp_documentmaster.documentSystemID = erp_generalledger.documentSystemID
                        WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountTypeID = 1
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf 
                        ) AS GL_final 
                    ORDER BY
                        documentCode ASC';

        return  \DB::select($query);
    }

    public function getGeneralLedgerQryForPDF($request)
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

        $glCodes = (array)$request->glCodes;
        $type = $request->type;
        $chartOfAccountId = array_filter(collect($glCodes)->pluck('chartOfAccountSystemID')->toArray());

        $departments = (array)$request->departments;
        $serviceLineId = array_filter(collect($departments)->pluck('serviceLineSystemID')->toArray());

        array_push($serviceLineId, 24);

        $contracts = (array)$request->contracts;
        $contractsId = array_filter(collect($contracts)->pluck('contractUID')->toArray());

        array_push($contractsId, 159);
        //contracts

        //DB::enableQueryLog();
        $query = 'SELECT *
                    FROM
                        (
                    SELECT
                        *
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        erp_generalledger.documentSystemID,
                        erp_generalledger.documentID,
                        erp_generalledger.documentSystemCode,
                        erp_generalledger.documentCode,
                        erp_generalledger.documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        erp_generalledger.glAccountType,
                        erp_generalledger.documentNarration,
                        erp_generalledger.clientContractID,
                        erp_generalledger.supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                    IF
                        ( documentLocalAmount > 0, documentLocalAmount, 0 ) AS localDebit,
                    IF
                        ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                    IF
                        ( documentRptAmount > 0, documentRptAmount, 0 ) AS rptDebit,
                    IF
                        ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) AS rptCredit,
                    IF
                        ( erp_generalledger.documentSystemID = 20 OR erp_generalledger.documentSystemID = 21 OR erp_generalledger.documentSystemID = 19, customermaster.CustomerName, suppliermaster.supplierName ) AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        ) AS erp_qry_GL UNION ALL
                    SELECT
                        *
                    FROM
                        (
                    SELECT
                        erp_generalledger.companySystemID,
                        erp_generalledger.companyID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.serviceLineCode,
                        "" AS documentSystemID,
                        "" AS documentID,
                        "" AS documentSystemCode,
                        "" AS documentCode,
                        "" AS documentDate,
                        erp_generalledger.chartOfAccountSystemID,
                        erp_generalledger.glCode,
                        "BS" AS glAccountType,
                        "Opening Balance" AS documentNarration,
                        "" AS clientContractID,
                        "" AS supplierCodeSystem,
                        erp_generalledger.documentLocalCurrencyID,
                        chartofaccounts.AccountDescription,
                        companymaster.CompanyName,
                        sum( IF ( documentLocalAmount > 0, documentLocalAmount, 0 ) ) AS localDebit,
                        sum( IF ( documentLocalAmount < 0, ( documentLocalAmount *- 1 ), 0 ) ) AS localCredit,
                        erp_generalledger.documentRptCurrencyID,
                        sum( IF ( documentRptAmount > 0, documentRptAmount, 0 ) ) AS rptDebit,
                        sum( IF ( documentRptAmount < 0, ( documentRptAmount *- 1 ), 0 ) ) AS rptCredit,
                        "" AS isCustomer,
                        approveEmp.empName as approvedBy,
                        confirmEmp.empName as confirmedBy,
                        erp_generalledger.documentConfirmedDate,
                        erp_generalledger.documentFinalApprovedDate
                    FROM
                        erp_generalledger
                        LEFT JOIN employees as approveEmp ON erp_generalledger.documentFinalApprovedByEmpSystemID = approveEmp.employeeSystemID
                        LEFT JOIN employees as confirmEmp ON erp_generalledger.documentConfirmedByEmpSystemID = confirmEmp.employeeSystemID
                        LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN customermaster ON customermaster.customerCodeSystem = erp_generalledger.supplierCodeSystem
                        LEFT JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                        LEFT JOIN companymaster ON companymaster.companySystemID = erp_generalledger.companySystemID
                    WHERE
                        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ')
                        AND erp_generalledger.glAccountType = "BS"
                        AND  erp_generalledger.chartOfAccountSystemID IN (' . join(',', $chartOfAccountId) . ')
                        AND  erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceLineId) . ')
                        AND  erp_generalledger.contractUID IN (' . join(',', $contractsId) . ')
                        AND DATE(erp_generalledger.documentDate) < "' . $fromDate . '"
                    GROUP BY
                        erp_generalledger.glCode,
                        erp_generalledger.companySystemID,
                        erp_generalledger.serviceLineSystemID,
                        erp_generalledger.chartOfAccountSystemID
                        ) AS erp_qry_gl_bf
                        ) AS GL_final
                    ORDER BY
                        documentDate,glCode ASC';
        $output = \DB::select($query);
        //dd(DB::getQueryLog());
        return $output;
    }

    public
    function getTaxDetailQry($request)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $selectedColumns = $request->selectedColumn;


        if(!empty($selectedColumns))
        {
            $selectedColumns = collect($selectedColumns)->pluck('id')->toArray();
            if($request->reportViewID == 2)
            {
                array_push($selectedColumns,22);
            }
            $selectedColumns = ReportCustomColumn::where('isActive',1)->whereIn('id',$selectedColumns)->select(['id','column_name','column_slug','column_reference','master_column_reference','isDate','isDetails'])->get();

        }else {
            if($request->reportViewID == 2)
            {
                array_push($selectedColumns,22);
            }
            $selectedColumns = ReportCustomColumn::where('isActive',1)->whereIn('id',$selectedColumns)->select(['id','column_name','column_slug','column_reference','master_column_reference','isDate','isDetails'])->get();

        }


        $companyID = "";
        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $isCurrencyColumnEmpty = collect($request->selectedColumn)->where('id',9)->isEmpty();

        $reportViewID = ($request->reportViewID) ?? 1;



        if ($request->tempType == 1) {
            return ($this->getSupplierInvoiceTaxDetailsQuery($request));
        }
        else if ($request->tempType == 4){
            if(empty($request->selectedColumn))
            {
                $selectedColumns['selectedQuery'] = "*";
            }


            if($request->reportViewID == 1)
            {
                $conditionalQuery = 'GROUP_CONCAT(ed.glCode SEPARATOR ",") AS lineItemNumberALL,
                                    GROUP_CONCAT(0 SEPARATOR ",") AS exemptVATPortionALL,
                                    GROUP_CONCAT(DISTINCT  vatsub.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
                                    GROUP_CONCAT(DISTINCT CASE WHEN ed.VATPercentage != 0 THEN ROUND(ed.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,';

                $groupby = " GROUP BY  edn.debitNoteAutoID";
            }else {
                $conditionalQuery = '0 AS lineItemNumberALL,
                                    0 AS exemptVATPortionALL,
                                    0 AS subCategoryDescriptionALL,
                                    0 AS VATPercentageALL,';
                $groupby = " GROUP BY  ed.debitNoteDetailsID";
            }


            $query = 'SELECT
     edn.companyID AS companyID,
    edn.debitNoteAutoID AS primaryID,
    edn.debitNoteCode AS DocumentCode,
    DATE_FORMAT(edn.debitNoteDate, "%d/%m/%Y") AS DocumentDate,
    edn.comments AS comments,
    edn.supplierID AS supplierID,
    sm.primarySupplierCode,
    edn.supplierID AS supplierID,
    sm.supplierName,
    sm.vatNumber,
    edn.supplierTransactionCurrencyID AS supplierTransactionCurrencyID,
    cm.CurrencyCode,
    DATE_FORMAT(edn.debitNoteDate, "%d/%m/%Y") AS postedDate,
    edn.supplierID AS supplierID,
    ctm.countryName,
    cm.DecimalPlaces,
    CONCAT(ed.glCode," | ",ed.glCodeDes) AS lineItemNumber,
    SUM(ed.netAmount) AS bookingAmountTrans,
    vatsub.subCategoryDescription,
     0 AS discount,
    ( SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) as goodORService,
    (
        SELECT 
    GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
FROM supplierbusinesscategoryassign sbca
LEFT JOIN suppliercategorymaster scm 
    ON sbca.supCategoryMasterID = scm.supCategoryMasterID
LEFT JOIN suppliersubcategoryassign ssa 
    ON ssa.supplierID = sbca.supplierID
LEFT JOIN suppliercategorysub scs 
    ON ssa.supSubCategoryID = scs.supCategorySubID 
    AND scs.supMasterCategoryID = sbca.supCategoryMasterID
WHERE sbca.supplierID = sm.supplierCodeSystem
        ) as transcation,
    (   ed.netAmount) as value,
    (ed.netAmountRpt) as valueRpt,
    "Input VAT" AS vatCategory,
    CASE
        WHEN edn.type = 1 THEN "Supplier Debit Note"
        WHEN edn.type = 2 THEN "Employee Debit Note"
        ELSE "Unknown Type"
    END AS documentType,
    IFNULL(SUM(ed.netAmount), 0) AS taxTotalAmount,
    cm.DecimalPlaces,
    (edn.netAmountRpt) AS bookingAmountRpt,
    "Input VAT" AS vatCategory,
    (
        SELECT 
    GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
FROM supplierbusinesscategoryassign sbca
LEFT JOIN suppliercategorymaster scm 
    ON sbca.supCategoryMasterID = scm.supCategoryMasterID
LEFT JOIN suppliersubcategoryassign ssa 
    ON ssa.supplierID = sbca.supplierID
LEFT JOIN suppliercategorysub scs 
    ON ssa.supSubCategoryID = scs.supCategorySubID 
    AND scs.supMasterCategoryID = sbca.supCategoryMasterID
WHERE sbca.supplierID = sm.supplierCodeSystem
        ) as transcation,
        ( SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) as goodORService,
    CASE 
        WHEN edn.type = 1 THEN "Supplier Debit Note"
        WHEN edn.type = 2 THEN "Employee Debit Note"
        ELSE "Unknown Type"
    END AS documentType,
    SUM(ed.VATAmount) AS taxTotalAmount,
    ed.VATAmount AS VATAmount,
    ROUND(ed.VATPercentage,4) AS VATPercentage,
    0 AS discount,
    0 AS discountAmount,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = edn.companyReportingCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = edn.companyReportingCurrencyID) AS rptCurrencyCode,
     '.$conditionalQuery.'   
    edn.companyReportingER AS companyReportingER
FROM
    erp_debitnote AS edn
INNER JOIN
    suppliermaster AS sm ON sm.supplierCodeSystem = edn.supplierID
INNER JOIN
    currencymaster AS cm ON cm.currencyID = edn.supplierTransactionCurrencyID
INNER JOIN
    erp_debitnotedetails ed ON ed.debitNoteAutoID  = edn.debitNoteAutoID 
LEFT JOIN
    countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
LEFT JOIN erp_tax_vat_sub_categories vatsub ON  vatsub.taxVatSubCategoriesAutoID = ed.vatSubCategoryID 
LEFT JOIN
    (SELECT
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode,
        IFNULL(Sum(td.amount), 0) AS taxTotalAmount
    FROM
        erp_taxdetail AS td
    GROUP BY
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode
    ) AS tax ON tax.documentSystemID = edn.documentSystemID
                AND tax.companySystemID = edn.companySystemID
                AND tax.documentSystemCode = edn.debitNoteAutoID
WHERE DATE(edn.debitNoteDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND
    edn.companySystemID IN (' . join(',', $companyID) . ')
AND edn.approved = - 1  AND edn.type = 1 AND taxTotalAmount > 0 '      ;


            $query .= $groupby;

            $results = \DB::select($query);

            if(!empty($results))
            {
                usort($results, function ($a, $b) {
                    return strcmp($a->DocumentCode, $b->DocumentCode);
                });


                if($request->reportViewID == 2) {
                    $adddedDocCode = array();
                    foreach ($results as $item) {
                        $docCode = $item->DocumentCode;

                        if(!in_array($docCode,$adddedDocCode))
                        {
                            $item->borderTop = true;
                            array_push($adddedDocCode,$docCode);
                        }else {
                            $item->borderTop = false;
                        }
                    }
                }

            }
            return $results;
        }
        else if ($request->tempType == 5){
            if(empty($request->selectedColumn))
            {
                $selectedColumns['selectedQuery'] = "*";
            }


            if($request->reportViewID == 1)
            {
                $conditionalQuery = 'GROUP_CONCAT(ecnd.glCode SEPARATOR ",") AS lineItemNumberALL,
    GROUP_CONCAT(0 SEPARATOR ",") AS exemptVATPortionALL,
    GROUP_CONCAT(DISTINCT vatsub.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
    GROUP_CONCAT(DISTINCT CASE WHEN ecnd.VATPercentage != 0 THEN ROUND(ecnd.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,';

                $groupby = " GROUP BY  ecnd.creditNoteAutoID";

            }else {
                $conditionalQuery = '0 AS lineItemNumberALL,
    0 AS exemptVATPortionALL,
    0 AS subCategoryDescriptionALL,
    0 AS VATPercentageALL,';

                $groupby = " GROUP BY  ecnd.creditNoteDetailsID";

            }

            $query = 'SELECT
    ecn.companyID AS companyID,
    ecn.creditNoteAutoID AS primaryID,
    ecn.creditNoteCode AS DocumentCode,
    DATE_FORMAT(ecn.creditNoteDate, "%d/%m/%Y") AS DocumentDate,
    ecn.comments AS comments,
    DATE_FORMAT(ecn.creditNoteDate, "%d/%m/%Y") AS postedDate,
    country.countryName,
    curr.DecimalPlaces,
    (SUM(ecnd.netAmount)) AS bookingAmountTrans,
    "Output VAT" AS vatCategory,
    "Credit Note" AS documentType,
    cust.CutomerCode,
    cust.customerShortCode,
    cust.CustomerName,
     0 AS discount,
    IFNULL(SUM(ecnd.VATAmount), 0) AS taxTotalAmount,
    (ecnd.netAmount) as value,
    ecnd.VATAmount,
    ecnd.comRptAmount AS bookingAmountRpt,
    ecnd.comRptCurrencyER AS companyReportingER,
    ROUND(ecnd.VATPercentage,4) AS VATPercentage,
    CONCAT(ecnd.glCode," | ",ecnd.glCodeDes) AS lineItemNumber,
    cust.vatNumber,
    curr.CurrencyCode,
    vatsub.subCategoryDescription,
    (ecnd.netAmountRpt) AS valueRpt,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = ecn.companyReportingCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = ecn.companyReportingCurrencyID) AS rptCurrencyCode,
    '.$conditionalQuery.'
    0 AS discountAmount
FROM
    erp_creditnote ecn
INNER JOIN
    currencymaster curr ON curr.currencyID = ecn.customerCurrencyID
INNER JOIN
    customermaster cust ON cust.customerCodeSystem = ecn.customerID
LEFT JOIN
    countrymaster country ON country.countryID = cust.customerCountry
INNER JOIN erp_creditnotedetails ecnd ON ecnd.creditNoteAutoID = ecn.creditNoteAutoID        
LEFT JOIN erp_tax_vat_sub_categories vatsub ON  vatsub.taxVatSubCategoriesAutoID = ecnd.vatSubCategoryID         
LEFT JOIN (
    SELECT
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode,
        IFNULL(SUM(td.amount), 0) AS taxTotalAmount
    FROM
        erp_taxdetail td
    GROUP BY
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode
) tax ON tax.documentSystemID = ecn.documentSystemID
        AND tax.companySystemID = ecn.companySystemID
        AND tax.documentSystemCode = ecn.creditNoteAutoID -- Assuming debitNoteAutoID is the correct link
WHERE DATE(ecn.creditNoteDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND
    ecn.companySystemID IN (' . join(',', $companyID) . ')
AND ecn.approved = - 1 AND taxTotalAmount > 0' ;

            $query .= $groupby;
            $results = \DB::select($query);

            if(!empty($results))
            {
                usort($results, function ($a, $b) {
                    return strcmp($a->DocumentCode, $b->DocumentCode);
                });


                if($request->reportViewID == 2) {
                    $adddedDocCode = array();
                    foreach ($results as $item) {
                        $docCode = $item->DocumentCode;

                        if(!in_array($docCode,$adddedDocCode))
                        {
                            $item->borderTop = true;
                            array_push($adddedDocCode,$docCode);
                        }else {
                            $item->borderTop = false;
                        }
                    }
                }

            }
            return $results;

        }
        else if ($request->tempType == 3){
            $query = 'SELECT
    epsim.companyID AS companyID,
    epsim.BPVcode AS DocumentCode,
    epsim.PayMasterAutoId AS primaryID,
    DATE_FORMAT(epsim.BPVdate, "%d/%m/%Y") AS DocumentDate,
    epsim.BPVNarration AS comments,
    epsim.BPVsupplierID AS BPVsupplierID,
    IFNULL(sm.primarySupplierCode,emp.empID) AS primarySupplierCode,
    epsim.BPVsupplierID AS BPVsupplierID,
    epsim.directPaymentPayee AS supplierName,
    epsim.supplierTransCurrencyID AS supplierTransCurrencyID,
    cm.CurrencyCode,
    DATE_FORMAT(epsim.postedDate, "%d/%m/%Y") AS postedDate,
    epsim.BPVsupplierID AS BPVsupplierID,
    ctm.countryName,
    CONCAT(edpd.glCode," | ",edpd.glCodeDes) AS lineItemNumber,
    edpd.glCode,
    edpd.netAmount AS value,
    (edpd.netAmountRpt + edpd.VATAmountRpt) AS valueRpt,
    edpd.vatSubCategoryID AS vatSubCategoryID,
    etvsc1.subCategoryDescription,
    epsim.companySystemID AS companySystemID,
    IFNULL(cm.DecimalPlaces,3) AS DecimalPlaces,
    NULL AS exempt_vat_portion,
    GROUP_CONCAT(edpd.glCode SEPARATOR ",") AS lineItemNumberALL,
    GROUP_CONCAT(0 SEPARATOR ",") AS exemptVATPortionALL,
    (
        SELECT 
    GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
FROM supplierbusinesscategoryassign sbca
LEFT JOIN suppliercategorymaster scm 
    ON sbca.supCategoryMasterID = scm.supCategoryMasterID
LEFT JOIN suppliersubcategoryassign ssa 
    ON ssa.supplierID = sbca.supplierID
LEFT JOIN suppliercategorysub scs 
    ON ssa.supSubCategoryID = scs.supCategorySubID 
    AND scs.supMasterCategoryID = sbca.supCategoryMasterID
WHERE sbca.supplierID = sm.supplierCodeSystem
        ) as transcation,
        IFNULL(( SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID),"-") as goodORService,
    CASE
        WHEN epsim.invoiceType = 2 THEN "Supplier Payment"
        WHEN epsim.invoiceType = 3 THEN 
        CASE 
            WHEN epsim.directPaymentpayeeYN = -1 THEN "Direct Payment - Other"
            WHEN epsim.directPaymentPayeeSelectEmp = -1 THEN "Direct Payment - Employee"
        ELSE
            "Direct Payment - Supplier"
        END
        WHEN epsim.invoiceType = 5 THEN "Supplier Advance Payment"
        WHEN epsim.invoiceType = 6 THEN "Employee Payment"
        WHEN epsim.invoiceType = 7 THEN "Employee Advance Payment"
        ELSE "Unknown Type"
    END AS documentType,
    "Input VAT" AS vatCategory,
    SUM(edpd.netAmount) AS bookingAmountTrans,
    (epsim.netAmountRpt + epsim.VATAmountRpt) AS bookingAmountRpt,
    IFNULL(SUM(edpd.vatAmount), 0) AS taxTotalAmount,
    ROUND(edpd.VATPercentage,4) AS VATPercentage,
    edpd.vatAmount AS VATAmount,
    sm.vatNumber,    
    0 AS discount,
    0 AS discountAmount,
    IFNULL(epsim.companyRptCurrencyER,3) AS companyReportingER,    
    epsim.rcmActivated,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = epsim.companyRptCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = epsim.companyRptCurrencyID) AS rptCurrencyCode,
    GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
    GROUP_CONCAT(DISTINCT CASE WHEN edpd.VATPercentage != 0 THEN ROUND(edpd.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL
        FROM
    erp_paysupplierinvoicemaster AS epsim
LEFT JOIN
    suppliermaster AS sm ON sm.supplierCodeSystem = epsim.BPVsupplierID
LEFT JOIN
    employees AS emp ON emp.employeeSystemID = epsim.directPaymentPayeeEmpID
LEFT JOIN 
    hrms_employeedetails AS empDetails ON empDetails.employeeSystemID = emp.employeeSystemID
LEFT JOIN
    currencymaster AS cm ON cm.currencyID = epsim.supplierTransCurrencyID
LEFT JOIN
    countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
LEFT JOIN
    erp_directpaymentdetails AS edpd ON edpd.directPaymentAutoID = epsim.PayMasterAutoId
LEFT JOIN
    erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = edpd.vatSubCategoryID
LEFT JOIN
    (SELECT
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode,
        IFNULL(Sum(td.amount), 0) AS taxTotalAmount
    FROM
        erp_taxdetail AS td
    GROUP BY
        td.documentSystemID,
        td.companySystemID,
        td.documentSystemCode
    ) AS tax ON tax.documentSystemID = epsim.documentSystemID
                AND tax.companySystemID = epsim.companySystemID
                AND tax.documentSystemCode = epsim.PayMasterAutoId
WHERE
    DATE(epsim.postedDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"
AND epsim .companySystemID IN (' . join(',', $companyID) . ')
AND epsim .approved = - 1
AND epsim .cancelYN = 0
AND epsim .invoiceType = 3 AND taxTotalAmount > 0';


            if($request->reportViewID == 1)
            {
                $groupby = " GROUP BY  edpd.directPaymentAutoID";
            }else {
                $groupby = " GROUP BY  edpd.directPaymentDetailsID";
            }

            $query .= $groupby;
            $results = \DB::select($query);


            if(!empty($results))
            {
                usort($results, function ($a, $b) {
                    return strcmp($a->DocumentCode, $b->DocumentCode);
                });


                if($request->reportViewID == 2) {
                    $adddedDocCode = array();
                    foreach ($results as $item) {
                        $docCode = $item->DocumentCode;

                        if(!in_array($docCode,$adddedDocCode))
                        {
                            $item->borderTop = true;
                            array_push($adddedDocCode,$docCode);
                        }else {
                            $item->borderTop = false;
                        }
                    }
                }

            }
            return $results;
       }
        else {
            return $this->getCustomerInvoiceDetailsQuery($request);
        }
    }


    public function getSupplierInvoiceTaxDetailsQuery($request)
    {


        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $companyID = array_map('intval', $companyID);

        $query = '
            SELECT
                invoiceMaster.bookingSuppMasInvAutoID AS primaryKey,
                invoiceMaster.createdDateAndTime,
                invoiceMaster.companyID AS companyID,
                invoiceMaster.bookingInvCode AS DocumentCode,
                DATE_FORMAT(invoiceMaster.bookingDate, "%d/%m/%Y") AS DocumentDate,
                invoiceMaster.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(invoiceMaster.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                invoiceMaster.bookingInvCode,
                invoiceMaster.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.CurrencyCode,
                DATE_FORMAT(invoiceMaster.postedDate, "%d/%m/%Y") AS postedDate,
                invoiceMaster.rcmActivated AS rcmActivated,
                ctm.countryName,
                CONCAT(details.glCode," | ",details.glCodeDes) AS lineItemNumber,
                (details.DIAmount) AS value,
                (details.comRptAmount + details.VATAmountRpt) AS valueRpt,
                0 AS discount,
                0 AS discountAmount,
                (SUM(details.DIAmount))  AS bookingAmountTrans,
                (SUM(details.comRptAmount + details.VATAmountRpt)) AS bookingAmountRpt,
                (details.VATAmount - 
                CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                                CASE
                                    WHEN invoiceMaster.rcmActivated = 1 THEN 0
                                    ELSE
                                    (
                                      (
                                        details.VATAmount
                                        
                                      ) / 100
                                    ) * invoiceMaster.retentionPercentage
                                END    
                        END
                ) AS VATAmount,
                SUM(details.VATAmount  - 
                CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                                CASE
                                    WHEN invoiceMaster.rcmActivated = 1 THEN 0
                                    ELSE
                                    (
                                      (
                                        details.VATAmount
                                      ) / 100
                                    ) * invoiceMaster.retentionPercentage
                                END    
                            END
                ) AS taxTotalAmount,
                CASE 
                    WHEN (invoiceMaster.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0) THEN
                       IFNULL(SUM(details.DIAmount) / 100 * invoiceMaster.retentionPercentage, 0)
                    ELSE
                (
                IFNULL(SUM(details.DIAmount + 
                 CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            details.VATAmount
                            ELSE
                            0
                 END), 0)) / 100 * invoiceMaster.retentionPercentage END AS retentionAmount,    
                details.vatSubCategoryID,
                (IF(details.exempt_vat_portion IS NULL, 0,details.exempt_vat_portion) * (details.VATAmount / 100)) AS exempt_vat_portion,
                (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
                ) AS transcation,
                "Supplier Direct Invoice" AS documentType,
                (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
                (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptDecimalPlaces,
                (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptCurrencyCode,
                curm.DecimalPlaces,
                invoiceMaster.companyReportingER,
                "Input VAT" AS vatCategory,
                details.VATPercentage,
                IFNULL(etvsc1.subCategoryDescription,"-") AS subCategoryDescription,
                GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
                GROUP_CONCAT(DISTINCT CASE WHEN details.VATPercentage != 0 THEN ROUND(details.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
                GROUP_CONCAT(details.glCode SEPARATOR ",") AS lineItemNumberALL,
                GROUP_CONCAT(ROUND(IFNULL(details.exempt_vat_portion,0) * (details.VATAmount / 100),curm.DecimalPlaces) SEPARATOR ",") AS exemptVATPortionALL,
                invoiceMaster.documentType as documentTypeID,
                invoiceMaster.retentionPercentage
            FROM
                erp_bookinvsuppmaster AS invoiceMaster
            LEFT JOIN erp_directinvoicedetails details ON invoiceMaster.bookingSuppMasInvAutoID = details.directInvoiceAutoID
            INNER JOIN
                companymaster AS cm ON cm.companySystemID = invoiceMaster.companySystemID
            INNER JOIN
                suppliermaster AS sm ON sm.supplierCodeSystem = invoiceMaster.supplierID
            INNER JOIN
                currencymaster AS curm ON curm.currencyID = invoiceMaster.supplierTransactionCurrencyID
            LEFT JOIN
                countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
            LEFT JOIN
                erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
            WHERE DATE(invoiceMaster.postedDate) BETWEEN ? AND ?
            AND invoiceMaster.companySystemID IN (' . implode(',', $companyID) . ')
            AND invoiceMaster.approved = -1
            AND invoiceMaster.cancelYN = 0
            AND invoiceMaster.documentType =  1
            AND invoiceMaster.approved  = -1
            GROUP BY
                CASE WHEN ? = 1 THEN primaryKey ELSE details.directInvoiceDetailsID END
            HAVING (? != 1 OR taxTotalAmount > 0)
            UNION ALL
            SELECT
                invoiceMaster.bookingSuppMasInvAutoID AS primaryKey,
                invoiceMaster.createdDateAndTime,
                invoiceMaster.companyID AS companyID,
                invoiceMaster.bookingInvCode AS DocumentCode,
                DATE_FORMAT(invoiceMaster.bookingDate, "%d/%m/%Y") AS DocumentDate,
                invoiceMaster.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(invoiceMaster.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                invoiceMaster.bookingInvCode,
                invoiceMaster.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.CurrencyCode,
                DATE_FORMAT(invoiceMaster.postedDate, "%d/%m/%Y") AS postedDate,
                invoiceMaster.rcmActivated AS rcmActivated,
                ctm.countryName,
                CONCAT(details.itemPrimaryCode," | ",details.itemDescription) AS lineItemNumber,
                IFNULL((details.noQty * details.unitCost),0) AS value,
                IFNULL(((details.costPerUnitComRptCur * details.noQty) + (details.VATAmountRpt * details.noQty )),0) AS valueRpt,
                (details.discountAmount * details.noQty) AS discount,
                SUM(details.discountAmount * details.noQty) AS discountAmount,
                IFNULL((SUM(details.noQty * details.unitCost)),0) AS bookingAmountTrans,
                IFNULL((invoiceMaster.bookingAmountRpt),0) AS bookingAmountRpt,
                 ((details.VATAmount * details.noQty) - 
                CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                                CASE
                                    WHEN invoiceMaster.rcmActivated = 1 THEN 0
                                    ELSE
                                    (
                                      (
                                        details.VATAmount * details.noQty
                                        
                                      ) / 100
                                    ) * invoiceMaster.retentionPercentage
                                END    
                        END
                ) AS VATAmount,
                SUM((details.VATAmount * details.noQty)  - 
                CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                                CASE
                                    WHEN invoiceMaster.rcmActivated = 1 THEN 0
                                    ELSE
                                    (
                                      (
                                        (details.VATAmount * details.noQty)
                                        
                                      ) / 100
                                    ) * invoiceMaster.retentionPercentage
                                END    
                            END
                ) AS taxTotalAmount,
                CASE 
                    WHEN (invoiceMaster.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0) THEN
                        ((SUM(details.noQty * details.unitCost) - SUM(details.noQty * details.discountAmount))/100 * invoiceMaster.retentionPercentage)
                    ELSE
                ((SUM(details.noQty * ( details.unitCost+ (
                CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            details.VATAmount
                            ELSE
                            0
                 END           
                ))) - SUM(details.noQty * details.discountAmount))/100 * invoiceMaster.retentionPercentage)
                END AS retentionAmount,  
                details.vatSubCategoryID,
                (IF(details.exempt_vat_portion IS NULL, NULL,details.exempt_vat_portion) * (details.VATAmount / 100) * details.noQty) AS exempt_vat_portion,
                (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
                ) AS transcation,
                "Supplier Item Invoice" AS documentType,
                (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
                (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptDecimalPlaces,
                (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptCurrencyCode,
                curm.DecimalPlaces,
                invoiceMaster.companyReportingER,
                "Input VAT" AS vatCategory,
                details.VATPercentage,
                IFNULL(etvsc1.subCategoryDescription,"-") AS subCategoryDescription,
                GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
                GROUP_CONCAT(DISTINCT CASE WHEN details.VATPercentage != 0 THEN ROUND(details.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
                GROUP_CONCAT(details.itemPrimaryCode SEPARATOR ",") AS lineItemNumberALL,
                GROUP_CONCAT(ROUND(IFNULL(details.exempt_vat_portion,0) * (details.VATAmount / 100),curm.DecimalPlaces) * details.noQty SEPARATOR ",") AS exemptVATPortionALL,
                invoiceMaster.documentType as documentTypeID,
                invoiceMaster.retentionPercentage
            FROM
                erp_bookinvsuppmaster AS invoiceMaster
            LEFT JOIN supplier_invoice_items details ON invoiceMaster.bookingSuppMasInvAutoID = details.bookingSuppMasInvAutoID
            INNER JOIN
                companymaster AS cm ON cm.companySystemID = invoiceMaster.companySystemID
            INNER JOIN
                suppliermaster AS sm ON sm.supplierCodeSystem = invoiceMaster.supplierID
            INNER JOIN
                currencymaster AS curm ON curm.currencyID = invoiceMaster.supplierTransactionCurrencyID
            LEFT JOIN
                countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
            LEFT JOIN
                erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
            WHERE DATE(invoiceMaster.postedDate) BETWEEN ? AND ?
            AND invoiceMaster.companySystemID IN (' . implode(',', $companyID) . ')
            AND invoiceMaster.approved = -1
            AND invoiceMaster.cancelYN = 0
            AND invoiceMaster.documentType =  3
            AND invoiceMaster.approved  = -1
            GROUP BY
                CASE WHEN ? = 1 THEN primaryKey ELSE details.id END
            HAVING (? != 1 OR taxTotalAmount > 0)
            UNION ALL

            SELECT
                invoiceMaster.bookingSuppMasInvAutoID AS primaryKey,
                invoiceMaster.createdDateAndTime,
                invoiceMaster.companyID AS companyID,
                invoiceMaster.bookingInvCode AS DocumentCode,
                DATE_FORMAT(invoiceMaster.bookingDate, "%d/%m/%Y") AS DocumentDate,
                invoiceMaster.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(invoiceMaster.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                invoiceMaster.bookingInvCode,
                invoiceMaster.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.CurrencyCode,
                DATE_FORMAT(invoiceMaster.postedDate, "%d/%m/%Y") AS postedDate,
                pom.rcmActivated AS rcmActivated,
                ctm.countryName,
                CONCAT(ep.itemPrimaryCode," | ",ep.itemDescription) AS lineItemNumber,
                IFNULL((details.supplierInvoAmount + (ep.discountAmount * (details.supplierInvoAmount/(ep.unitCost - ep.discountAmount + ep.VATAmount))) - (((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (0 * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) )), 0)  AS value,
                CASE
                    WHEN details.balanceAmount = 0 THEN IFNULL(((ep.GRVcostPerUnitComRptCur * ep.noQty) + (ep.VATAmountRpt * ep.noQty)), 0)
                    ELSE IFNULL(((ep.GRVcostPerUnitComRptCur * ep.noQty) + (ep.VATAmountRpt * ep.noQty)), 0)
                END AS valueRpt,
                (ep.discountAmount * (details.supplierInvoAmount/(ep.unitCost - ep.discountAmount + ep.VATAmount))) AS discount,
                SUM(((details.supplierInvoAmount - (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))/(100-ep.discountPercentage))*ep.discountPercentage) AS discountAmount,
                IFNULL(
                SUM(
                    details.supplierInvoAmount + (ep.discountAmount * (details.supplierInvoAmount/(ep.unitCost - ep.discountAmount + ep.VATAmount)))
                    - (
                        (
                            details.supplierInvoAmount / (100 + ep.VATPercentage) * ep.VATPercentage
                        ) 
                        
                    )
                ), 
                0
            ) AS bookingAmountTrans,
                IFNULL((invoiceMaster.bookingAmountRpt), 0) AS bookingAmountRpt,
                 CASE 
                     WHEN pom.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0 THEN
                         ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                    ELSE
                     CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                                ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                            ELSE
                                ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) - (((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) / 100 * invoiceMaster.retentionPercentage)) 
                        END
                END AS VATAmount,
                CASE 
                    WHEN pom.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0 THEN
                         (SUM(details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - SUM(IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                    ELSE
                     CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                                SUM((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) 
                            ELSE
                                SUM((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) 
                        END
                END AS taxTotalAmount,
                CASE 
                    WHEN (pom.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0) THEN
                       IFNULL((SUM(details.supplierInvoAmount)/100*invoiceMaster.retentionPercentage),0)
                    ELSE
                        IFNULL((SUM(details.supplierInvoAmount)/100*invoiceMaster.retentionPercentage) - 
                        CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                            (SUM(details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) / 100 * invoiceMaster.retentionPercentage
                        END
                        ,0)
                END AS retentionAmount,    
                ep.vatSubCategoryID,
                (IF(ep.exempt_vat_portion IS NULL, NULL,ep.exempt_vat_portion) * (ep.VATAmount / 100) * ep.noQty) AS exempt_vat_portion,
                (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
                ) AS transcation,
                "Supplier PO Invoice" AS documentType,
                (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
                (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptDecimalPlaces,
                (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptCurrencyCode,
                curm.DecimalPlaces,
               CASE
                    WHEN ep.companyReportingER IS NULL OR ep.companyReportingER = 0 THEN pom.companyReportingER
                    ELSE ep.companyReportingER
                END AS companyReportingER,
                "Input VAT" AS vatCategory,
                ep.VATPercentage,
                IFNULL(etvsc1.subCategoryDescription,"-") AS subCategoryDescription,
                GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
                GROUP_CONCAT(DISTINCT CASE WHEN ep.VATPercentage != 0 THEN ROUND(ep.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
                GROUP_CONCAT(ep.itemPrimaryCode SEPARATOR ",") AS lineItemNumberALL,
                GROUP_CONCAT(ROUND(IFNULL(ep.exempt_vat_portion,0) * (ep.VATAmount / 100) * ep.noQty,curm.DecimalPlaces) SEPARATOR ",") AS exemptVATPortionALL,
                invoiceMaster.documentType as documentTypeID,
                invoiceMaster.retentionPercentage
            FROM
                erp_bookinvsuppmaster AS invoiceMaster
            LEFT JOIN erp_bookinvsupp_item_det details ON invoiceMaster.bookingSuppMasInvAutoID = details.bookingSuppMasInvAutoID
            LEFT JOIN erp_grvdetails eg ON eg.grvDetailsID = details.grvDetailsID
            LEFT JOIN erp_purchaseorderdetails ep ON ep.purchaseOrderDetailsID = eg.purchaseOrderDetailsID
            LEFT JOIN erp_purchaseorderadvpayment adv ON adv.poAdvPaymentID = details.logisticID and adv.currencyID = invoiceMaster.supplierTransactionCurrencyID
            LEFT JOIN erp_purchaseordermaster pom ON pom.purchaseOrderID = ep.purchaseOrderMasterID
            INNER JOIN
                companymaster AS cm ON cm.companySystemID = invoiceMaster.companySystemID
            INNER JOIN
                suppliermaster AS sm ON sm.supplierCodeSystem = invoiceMaster.supplierID
            INNER JOIN
                currencymaster AS curm ON curm.currencyID = invoiceMaster.supplierTransactionCurrencyID
            LEFT JOIN
                countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
            LEFT JOIN
                erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
            WHERE DATE(invoiceMaster.postedDate) BETWEEN ? AND ?
            AND invoiceMaster.companySystemID IN (' . implode(',', $companyID) . ')
            AND invoiceMaster.approved = -1
            AND invoiceMaster.cancelYN = 0
            AND invoiceMaster.documentType =  0
            AND details.logisticID = 0
            AND invoiceMaster.approved  = -1
            GROUP BY
                CASE WHEN ? = 1 THEN primaryKey ELSE id END
            HAVING (? != 1 OR taxTotalAmount > 0)
            UNION ALL

            SELECT
                invoiceMaster.bookingSuppMasInvAutoID AS primaryKey,
                invoiceMaster.createdDateAndTime,
                invoiceMaster.companyID AS companyID,
                invoiceMaster.bookingInvCode AS DocumentCode,
                DATE_FORMAT(invoiceMaster.bookingDate, "%d/%m/%Y") AS DocumentDate,
                invoiceMaster.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(invoiceMaster.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                invoiceMaster.bookingInvCode,
                invoiceMaster.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.CurrencyCode,
                DATE_FORMAT(invoiceMaster.postedDate, "%d/%m/%Y") AS postedDate,
                invoiceMaster.rcmActivated AS rcmActivated,
                ctm.countryName,
                CONCAT(ep.itemPrimaryCode," | ",ep.itemDescription) AS lineItemNumber,
                IFNULL((details.supplierInvoAmount - (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)), 0) AS value,
                IFNULL(((ep.GRVcostPerUnitComRptCur * ep.noQty) + (ep.VATAmountRpt * ep.noQty)), 0) AS valueRpt,
                (ep.discountAmount * (details.supplierInvoAmount/(ep.unitCost - ep.discountAmount + ep.VATAmount))) AS discount,
                (ep.discountAmount * (details.supplierInvoAmount/(ep.unitCost - ep.discountAmount + ep.VATAmount))) AS discountAmount,
                IFNULL(SUM(details.supplierInvoAmount - (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)), 0) AS bookingAmountTrans,
                IFNULL((invoiceMaster.bookingAmountRpt), 0) AS bookingAmountRpt,
                CASE 
                    WHEN invoiceMaster.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0 THEN
                         ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - SUM(IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                    ELSE
                     CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                                ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                            ELSE
                                ((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) - (((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) / 100 * invoiceMaster.retentionPercentage)) 
                        END
                END AS VATAmount,
                CASE 
                    WHEN invoiceMaster.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0 THEN
                         (SUM(details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - SUM(IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                    ELSE
                     CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                                SUM((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage))) 
                            ELSE
                                SUM((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage) - (IFNULL((details.exempt_vat_portion)/100,0) * (details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) - (((details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) / 100 * invoiceMaster.retentionPercentage)) 
                        END
                END AS taxTotalAmount,
                CASE 
                    WHEN (invoiceMaster.rcmActivated = 1 AND invoiceMaster.retentionPercentage > 0) THEN
                       IFNULL((SUM(details.supplierInvoAmount)/100*invoiceMaster.retentionPercentage),0)
                    ELSE
                        IFNULL((SUM(details.supplierInvoAmount)/100*invoiceMaster.retentionPercentage) - 
                        CASE
                            WHEN 
                                (
                                 SELECT subCatgeoryType 
                                FROM erp_tax_vat_sub_categories 
                                WHERE taxVatSubCategoriesAutoID = details.vatSubCategoryID
                                ) = 3
                            THEN
                            0
                            ELSE
                            (SUM(details.supplierInvoAmount/(100+ep.VATPercentage) * ep.VATPercentage)) / 100 * invoiceMaster.retentionPercentage
                        END
                        ,0)
                END AS retentionAmount,
                details.vatSubCategoryID,
                (IF(details.exempt_vat_portion IS NULL, NULL,details.exempt_vat_portion) * (details.VATAmount / 100) * details.grvRecivedQty) AS exempt_vat_portion,
                (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
                ) AS transcation,
                "Invoice For Direct GRV" AS documentType,
                (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
                (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptDecimalPlaces,
                (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = invoiceMaster.companyReportingCurrencyID) AS rptCurrencyCode,
                curm.DecimalPlaces,
                ep.companyReportingER,
                "Input VAT" AS vatCategory,
                ep.VATPercentage,
                IFNULL(etvsc1.subCategoryDescription,"-") AS subCategoryDescription,
                GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
                GROUP_CONCAT(DISTINCT CASE WHEN ep.VATPercentage != 0 THEN ep.VATPercentage ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
                GROUP_CONCAT(ep.itemPrimaryCode SEPARATOR ",") AS lineItemNumberALL,
                GROUP_CONCAT(ROUND(IFNULL(details.exempt_vat_portion,0) * (details.VATAmount / 100) * details.grvRecivedQty,curm.DecimalPlaces) SEPARATOR ",") AS exemptVATPortionALL,
                invoiceMaster.documentType as documentTypeID,
                invoiceMaster.retentionPercentage
            FROM
                erp_bookinvsuppmaster AS invoiceMaster
            LEFT JOIN erp_bookinvsupp_item_det details ON invoiceMaster.bookingSuppMasInvAutoID = details.bookingSuppMasInvAutoID
            LEFT JOIN erp_grvdetails ep ON ep.grvDetailsID = details.grvDetailsID
            INNER JOIN
                companymaster AS cm ON cm.companySystemID = invoiceMaster.companySystemID
            INNER JOIN
                suppliermaster AS sm ON sm.supplierCodeSystem = invoiceMaster.supplierID
            INNER JOIN
                currencymaster AS curm ON curm.currencyID = invoiceMaster.supplierTransactionCurrencyID
            LEFT JOIN
                countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
            LEFT JOIN
                erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
            WHERE DATE(invoiceMaster.postedDate) BETWEEN ? AND ?
            AND invoiceMaster.companySystemID IN (' . implode(',', $companyID) . ')
            AND invoiceMaster.approved = -1
            AND invoiceMaster.cancelYN = 0
            AND invoiceMaster.documentType =  2
            AND invoiceMaster.approved  = -1
            GROUP BY
                CASE WHEN ? = 1 THEN primaryKey ELSE details.id END
            HAVING (? != 1 OR taxTotalAmount > 0)
            ORDER BY primaryKey';

        $bindings = [];

        for ($i = 0; $i < 5; $i++) {
            $bindings[] = $fromDate;
            $bindings[] = $toDate;
            $bindings[] = $request->reportViewID;
            $bindings[] = $request->reportViewID;
        }


        $results = \DB::select($query, $bindings);


        if($request->reportViewID == 1)
        {

            $querry2 = '
            
SELECT 
                eb.createdDateAndTime,
                eb.companyID AS companyID,
                eb.bookingInvCode AS DocumentCode,
                DATE_FORMAT(eb.bookingDate, "%d/%m/%Y") AS DocumentDate,
                eb.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(eb.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                eb.bookingInvCode,
                eb.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.DecimalPlaces,
                curm.CurrencyCode,
                DATE_FORMAT(eb.postedDate, "%d/%m/%Y") AS postedDate,
                ctm.countryName,
                 "Input VAT" AS vatCategory,
                0 AS lineItemNumber,
                   "Supplier PO Invoice - Logistics" AS documentType,
                   0 AS discountAmount,
                        (eb.rcmActivated = 1 OR pom.rcmActivated = 1) AS rcmActivated,
                    0 AS grvDetailsID,
                    0 AS exempt_vat_portion,
                    SUM(details.supplierInvoAmount - (details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage))  as bookingAmountTrans,
                    CASE 
                    WHEN pom.rcmActivated = 1 AND eb.retentionPercentage > 0 THEN
                        (SUM(details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)) 
                    ELSE
                        (SUM(details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage) -  (SUM(details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)/100*eb.retentionPercentage)) 
                     END AS taxTotalAmount,
                CASE 
                    WHEN pom.rcmActivated = 1 AND eb.retentionPercentage > 0 THEN
                        IFNULL((SUM(details.supplierInvoAmount)/100*eb.retentionPercentage) ,0) 
                    ELSE
                        IFNULL((SUM(details.supplierInvoAmount)/100*eb.retentionPercentage) - (SUM(details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)/100*eb.retentionPercentage) ,0) 
                    END AS retentionAmount,    
    0 AS discount,
    (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptCurrencyCode,
    GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
    GROUP_CONCAT(DISTINCT CASE WHEN  erp_purchaseorderadvpayment.VATPercentage != 0 THEN ROUND( erp_purchaseorderadvpayment.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
        details.companyReportingER,
            (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptCurrencyCode,
    (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
     ) AS transcation,
    eb.documentType as documentTypeID,
    eb.retentionPercentage
FROM erp_purchaseorderadvpayment
LEFT JOIN erp_bookinvsupp_item_det details ON details.logisticID = erp_purchaseorderadvpayment.poAdvPaymentID 
LEFT JOIN erp_grvmaster 
    ON erp_purchaseorderadvpayment.grvAutoID = erp_grvmaster.grvAutoID
LEFT JOIN erp_tax_vat_sub_categories 
    ON erp_purchaseorderadvpayment.vatSubCategoryID = erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID
LEFT JOIN erp_purchaseordermaster 
    ON erp_purchaseorderadvpayment.poID = erp_purchaseordermaster.purchaseOrderID
JOIN erp_addoncostcategories 
    ON erp_purchaseorderadvpayment.logisticCategoryID = erp_addoncostcategories.idaddOnCostCategories
JOIN itemmaster 
    ON itemmaster.itemCodeSystem = erp_addoncostcategories.itemSystemCode
LEFT JOIN erp_bookinvsuppmaster eb ON eb.bookingSuppMasInvAutoID = details.bookingSuppMasInvAutoID
INNER JOIN companymaster AS cm ON cm.companySystemID = eb.companySystemID
INNER JOIN suppliermaster AS sm ON sm.supplierCodeSystem = eb.supplierID
INNER JOIN currencymaster AS curm ON curm.currencyID = eb.supplierTransactionCurrencyID
LEFT JOIN countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
LEFT JOIN erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = erp_purchaseorderadvpayment.vatSubCategoryID
LEFT JOIN erp_purchaseordermaster pom ON pom.purchaseOrderID  = details.purchaseOrderID 
WHERE DATE(eb.postedDate) BETWEEN ? AND ? AND details.logisticID > 0
AND eb.companySystemID IN (' . implode(',', $companyID) . ')
AND eb.approved = -1
AND eb.cancelYN = 0
AND eb.documentType =  0
GROUP BY details.bookingSuppMasInvAutoID 
 ';
        }
        else {
            $querry2 = 'SELECT 
  eb.createdDateAndTime,
                eb.companyID AS companyID,
                eb.bookingInvCode AS DocumentCode,
                DATE_FORMAT(eb.bookingDate, "%d/%m/%Y") AS DocumentDate,
                eb.supplierInvoiceNo AS invoiceNo,
                DATE_FORMAT(eb.supplierInvoiceDate, "%d/%m/%Y") AS invoiceDate,
                eb.bookingInvCode,
                eb.comments AS comments,
                sm.primarySupplierCode,
                sm.supplierName,
                sm.vatNumber,
                curm.CurrencyCode,
                curm.DecimalPlaces,
                 "Input VAT" AS vatCategory,
                "Supplier PO Invoice - Logistics" AS documentType,
                DATE_FORMAT(eb.postedDate, "%d/%m/%Y") AS postedDate,
                (eb.rcmActivated = 1 OR pom.rcmActivated = 1) AS rcmActivated,
                CONCAT(itemmaster.primaryCode," | ",itemmaster.itemDescription) AS lineItemNumber,
                ctm.countryName,
                CASE 
                    WHEN  (eb.rcmActivated = 1 OR pom.rcmActivated = 1) AND eb.retentionPercentage > 0 THEN
                        ((details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)) 
                    ELSE
                        ((details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage) -  ((details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)/100*eb.retentionPercentage)) 
                END AS VATAmount,
                CASE 
                    WHEN pom.rcmActivated = 1 AND eb.retentionPercentage > 0 THEN
                        IFNULL(((details.supplierInvoAmount)/100*eb.retentionPercentage) ,0) 
                    ELSE
                        IFNULL(((details.supplierInvoAmount)/100*eb.retentionPercentage) - ((details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage)/100*eb.retentionPercentage) ,0) 
                END AS retentionAmount, 
                erp_purchaseorderadvpayment.VATPercentage,
                erp_purchaseorderadvpayment.currencyID,
    erp_purchaseorderadvpayment.poAdvPaymentID AS logisticID,
    itemmaster.primaryCode AS itemPrimaryCode,
    itemmaster.itemDescription AS itemDescription,
    erp_tax_vat_sub_categories.mainCategory AS vatMasterCategoryID,
    erp_purchaseorderadvpayment.vatSubCategoryID,
    0 AS exempt_vat_portion,
    (details.supplierInvoAmount-(details.supplierInvoAmount/(100+erp_purchaseorderadvpayment.VATPercentage) * erp_purchaseorderadvpayment.VATPercentage))  as value,
    IFNULL(etvsc1.subCategoryDescription,"-") AS subCategoryDescription,
    details.companyReportingER,
    0 AS discount,
    (SELECT categoryDescription FROM suppliercategoryicvmaster WHERE supCategoryICVMasterID = sm.supCategoryICVMasterID) AS goodORService,
    (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptDecimalPlaces,
    (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = eb.companyReportingCurrencyID) AS rptCurrencyCode,
    (
                    SELECT
                        GROUP_CONCAT(DISTINCT scm.categoryName SEPARATOR ",") AS businessCategoryNames
                    FROM supplierbusinesscategoryassign sbca
                    LEFT JOIN suppliercategorymaster scm
                        ON sbca.supCategoryMasterID = scm.supCategoryMasterID
                    LEFT JOIN suppliersubcategoryassign ssa
                        ON ssa.supplierID = sbca.supplierID
                    LEFT JOIN suppliercategorysub scs
                        ON ssa.supSubCategoryID = scs.supCategorySubID
                        AND scs.supMasterCategoryID = sbca.supCategoryMasterID
                    WHERE sbca.supplierID = sm.supplierCodeSystem
     ) AS transcation,
eb.documentType as documentTypeID,
eb.retentionPercentage
FROM erp_purchaseorderadvpayment
LEFT JOIN erp_bookinvsupp_item_det details ON details.logisticID = erp_purchaseorderadvpayment.poAdvPaymentID 
LEFT JOIN erp_grvmaster 
    ON erp_purchaseorderadvpayment.grvAutoID = erp_grvmaster.grvAutoID
LEFT JOIN erp_tax_vat_sub_categories 
    ON erp_purchaseorderadvpayment.vatSubCategoryID = erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID
LEFT JOIN erp_purchaseordermaster 
    ON erp_purchaseorderadvpayment.poID = erp_purchaseordermaster.purchaseOrderID
JOIN erp_addoncostcategories 
    ON erp_purchaseorderadvpayment.logisticCategoryID = erp_addoncostcategories.idaddOnCostCategories
JOIN itemmaster 
    ON itemmaster.itemCodeSystem = erp_addoncostcategories.itemSystemCode
LEFT JOIN erp_bookinvsuppmaster eb ON eb.bookingSuppMasInvAutoID = details.bookingSuppMasInvAutoID
INNER JOIN companymaster AS cm ON cm.companySystemID = eb.companySystemID
INNER JOIN suppliermaster AS sm ON sm.supplierCodeSystem = eb.supplierID
INNER JOIN currencymaster AS curm ON curm.currencyID = eb.supplierTransactionCurrencyID
LEFT JOIN countrymaster AS ctm ON ctm.countryID = sm.supplierCountryID
LEFT JOIN erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = erp_purchaseorderadvpayment.vatSubCategoryID
LEFT JOIN erp_purchaseordermaster pom ON pom.purchaseOrderID  = details.purchaseOrderID 
WHERE DATE(eb.postedDate) BETWEEN ? AND ? AND details.logisticID > 0
AND eb.companySystemID IN (' . implode(',', $companyID) . ')
AND eb.approved = -1
AND eb.cancelYN = 0
AND eb.documentType =  0
GROUP BY id
';
        }




        $bindings2 = [];

        for ($i = 0; $i < 1; $i++) {
            $bindings2[] = $fromDate;
            $bindings2[] = $toDate;
        }


        $results2 = \DB::select($querry2,$bindings2);
        $mergedData = array_merge($results,$results2);

        if(!empty($mergedData))
        {
            usort($mergedData, function ($a, $b) {
                return strcmp($a->DocumentCode, $b->DocumentCode);
            });


            if($request->reportViewID == 2) {
                $adddedDocCode = array();
                foreach ($mergedData as $item) {
                    $docCode = $item->DocumentCode;

                    if(!in_array($docCode,$adddedDocCode))
                    {
                        $item->borderTop = true;
                        array_push($adddedDocCode,$docCode);
                    }else {
                        $item->borderTop = false;
                    }
                }
            }

        }

        return $mergedData;
    }



    public function getCustomerInvoiceDetailsQuery($request) {

        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $toDate = new Carbon($request->toDate);
        $toDate = $toDate->format('Y-m-d');

        $checkIsGroup = Company::find($request->companySystemID);
        if ($checkIsGroup->isGroup) {
            $companyID = \Helper::getGroupCompany($request->companySystemID);
        } else {
            $companyID = (array)$request->companySystemID;
        }

        $companyID = array_map('intval', $companyID);

        $query = '
        SELECT
            cm.companyID,
            details.discountAmountLine AS discountAmount1,
            ecid.custInvoiceDirectAutoID AS primaryID,
            ecid.bookingInvCode AS DocumentCode,
            DATE_FORMAT(ecid.bookingDate, "%d/%m/%Y") AS DocumentDate,
            ecid.customerInvoiceNo AS invoiceNo,
            DATE_FORMAT(ecid.customerInvoiceDate, "%d/%m/%Y") AS invoiceDate,
            ecid.comments AS comments,
            ecid.custTransactionCurrencyID AS custTransactionCurrencyID,
            curm.CurrencyCode,
            DATE_FORMAT(ecid.postedDate, "%d/%m/%Y") AS postedDate,
            ecid.customerID AS customerID,
            custm.customerShortCode,
            custm.CustomerName,
            NULL AS exempt_vat_portion,
            ctm.countryName,
            curm.DecimalPlaces,
            (ecid.bookingAmountRpt + ecid.VATAmountRpt) AS bookingAmountRpt,
            "Output VAT" AS vatCategory,
            (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = ecid.companyReportingCurrencyID) AS rptDecimalPlaces,
            (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = ecid.companyReportingCurrencyID) AS rptCurrencyCode,
            (details.invoiceQty * details.salesPrice) AS value,
            (details.invoiceQty * details.discountAmountLine) AS discount,
            IFNULL(SUM(details.invoiceQty * details.discountAmountLine),0) AS discountAmount,
            (details.VATAmount * details.invoiceQty) AS VATAmount,
            IFNULL(SUM(details.VATAmount * details.invoiceQty),0) AS taxTotalAmount,
            ecid.custInvoiceDirectAutoID,
            SUM(details.invoiceQty * details.salesPrice) AS bookingAmountTrans,
            details.custInvoiceDirectID,
            details.custInvDirDetAutoID,
            ecid.companyReportingER AS companyReportingER,
            NULL AS valueRpt,
            NULL AS customerItemDetailID,
            details.salesPrice AS salesPrice,
            ROUND(details.VATPercentage,4) AS VATPercentage,
            CONCAT(details.glCode," | ",details.glCodeDes) AS lineItemNumber,
            custm.CutomerCode AS CutomerCode,
            custm.vatNumber,
            "Direct Customer Invoice" AS documentType,
            etvsc1.subCategoryDescription AS subCategoryDescription,
            GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
            GROUP_CONCAT(DISTINCT CASE WHEN details.VATPercentage != 0 THEN ROUND(details.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
            GROUP_CONCAT(details.glCode SEPARATOR ",") AS lineItemNumberALL,
            GROUP_CONCAT(0 SEPARATOR ",") AS exemptVATPortionALL
        FROM
            erp_custinvoicedirect AS ecid
        INNER JOIN
            companymaster AS cm ON cm.companySystemID = ecid.companySystemID
        INNER JOIN
            currencymaster AS curm ON curm.currencyID = ecid.custTransactionCurrencyID
        INNER JOIN
            customermaster AS custm ON custm.customerCodeSystem = ecid.customerID
        LEFT JOIN
            countrymaster AS ctm ON ctm.countryID = custm.customerCountry
        LEFT JOIN erp_custinvoicedirectdet details ON details.custInvoiceDirectID = ecid.custInvoiceDirectAutoID
        LEFT JOIN erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
        WHERE  DATE(ecid.postedDate) BETWEEN :fromDate1 AND :toDate1
        AND ecid.companySystemID IN (' . implode(',', $companyID) . ')
        AND ecid.approved = - 1
        AND ecid.isPerforma = 0
        GROUP BY
            CASE WHEN :reportViewID1 = 1 THEN details.custInvoiceDirectID ELSE details.custInvDirDetAutoID END
        HAVING
            taxTotalAmount > 0
        UNION ALL
        
        SELECT
            cm.companyID AS companyCode,
            details.discountAmount AS discountAmount1,
            ecid.custInvoiceDirectAutoID AS primaryID,
            ecid.bookingInvCode AS DocumentCode,
            DATE_FORMAT(ecid.bookingDate, "%d/%m/%Y") AS DocumentDate,
            ecid.customerInvoiceNo AS invoiceNo,
            DATE_FORMAT(ecid.customerInvoiceDate, "%d/%m/%Y") AS invoiceDate,
            ecid.comments AS comments,
            ecid.custTransactionCurrencyID AS custTransactionCurrencyID,
            curm.CurrencyCode,
            DATE_FORMAT(ecid.postedDate, "%d/%m/%Y") AS postedDate,
            ecid.customerID AS customerID,
            custm.customerShortCode,
            custm.CustomerName,
            NULL AS exempt_vat_portion,
            ctm.countryName,
            curm.DecimalPlaces,
            (ecid.bookingAmountRpt + ecid.VATAmountRpt) AS bookingAmountRpt,
            "Output VAT" AS vatCategory,
            (SELECT DecimalPlaces FROM currencymaster WHERE currencymaster.currencyID = ecid.companyReportingCurrencyID) AS rptDecimalPlaces,
            (SELECT CurrencyCode FROM currencymaster WHERE currencymaster.currencyID = ecid.companyReportingCurrencyID) AS rptCurrencyCode,
            (details.qtyIssued * details.salesPrice) AS value,
            (details.qtyIssued * details.discountAmount) AS discount,
            IFNULL(SUM(details.qtyIssued * details.discountAmount),0) AS discountAmount,
            (details.VATAmount * details.qtyIssued) AS VATAmount,
            IFNULL(SUM(details.VATAmount * details.qtyIssued),0) AS taxTotalAmount,
            ecid.custInvoiceDirectAutoID,
            SUM(details.qtyIssued * details.salesPrice) AS bookingAmountTrans,
            details.custInvoiceDirectAutoID AS custInvoiceDirectID,
            details.customerItemDetailID AS custInvDirDetAutoID,
            ecid.companyReportingER AS companyReportingER,
            (0) AS valueRpt,
            details.customerItemDetailID,
            details.salesPrice,
            details.VATPercentage,
            CONCAT(details.itemPrimaryCode," | ",details.itemDescription) AS lineItemNumber,
            custm.CutomerCode AS CutomerCode,
            custm.vatNumber,
            CASE
                WHEN ecid.isPerforma = 2 THEN "Item Sales Invoice"
                WHEN ecid.isPerforma = 3 THEN "From Delivery Note"
                WHEN ecid.isPerforma = 4 THEN "From Sales Order"
                WHEN ecid.isPerforma = 5 THEN "From Quotation"
            END AS documentType,
            etvsc1.subCategoryDescription AS subCategoryDescription,
            GROUP_CONCAT(DISTINCT etvsc1.subCategoryDescription SEPARATOR ",") AS subCategoryDescriptionALL,
            GROUP_CONCAT(DISTINCT CASE WHEN details.VATPercentage != 0 THEN ROUND(details.VATPercentage,4) ELSE NULL END SEPARATOR ",") AS VATPercentageALL,
            GROUP_CONCAT(details.itemPrimaryCode SEPARATOR ",") AS lineItemNumberALL,
            GROUP_CONCAT(0 SEPARATOR ",") AS exemptVATPortionALL
        FROM
            erp_custinvoicedirect AS ecid
        INNER JOIN
            companymaster AS cm ON cm.companySystemID = ecid.companySystemID
        INNER JOIN
            currencymaster AS curm ON curm.currencyID = ecid.custTransactionCurrencyID
        INNER JOIN
            customermaster AS custm ON custm.customerCodeSystem = ecid.customerID
        LEFT JOIN
            countrymaster AS ctm ON ctm.countryID = custm.customerCountry
        LEFT JOIN erp_customerinvoiceitemdetails details ON details.custInvoiceDirectAutoID = ecid.custInvoiceDirectAutoID
        LEFT JOIN erp_tax_vat_sub_categories AS etvsc1 ON etvsc1.taxVatSubCategoriesAutoID = details.vatSubCategoryID
        WHERE  DATE(ecid.postedDate) BETWEEN :fromDate2 AND :toDate2
        AND ecid.companySystemID IN (' . implode(',', $companyID) . ')
        AND ecid.approved = - 1
        AND ecid.isPerforma IN (2,3,4,5)
        GROUP BY
            CASE WHEN :reportViewID2 = 1 THEN details.custInvoiceDirectAutoID ELSE details.customerItemDetailID END
        HAVING
        taxTotalAmount > 0
        ';

        $bindings = [
            'fromDate1' => $fromDate,
            'toDate1' => $toDate,
            'reportViewID1' => $request->reportViewID,
            'fromDate2' => $fromDate, // Same value, different parameter name
            'toDate2' => $toDate,     // Same value, different parameter name
            'reportViewID2' => $request->reportViewID, // Same value, different parameter name
        ];

        $results = \DB::select($query, $bindings);


        if(!empty($results))
        {
            usort($results, function ($a, $b) {
                return strcmp($a->DocumentCode, $b->DocumentCode);
            });


            if($request->reportViewID == 2) {
                $adddedDocCode = array();
                foreach ($results as $item) {
                    $docCode = $item->DocumentCode;

                    if(!in_array($docCode,$adddedDocCode))
                    {
                        $item->borderTop = true;
                        array_push($adddedDocCode,$docCode);
                    }else {
                        $item->borderTop = false;
                    }
                }
            }

        }
        return $results;
    }
    public function createCustomQueryWithSelectedColums($selectedColumns,$documentSystemID,$isCurrencyColumnEmpty)
    {
        switch ($documentSystemID)
        {
            case 4 :
            case 11 :
            case 15 :
            case 19 :
            case 20 :
                $query = array();
                $mastertables = array();
                foreach ($selectedColumns as $selectedColumn)
                {
                    $column_reference = json_decode($selectedColumn->column_reference);

                    if(!empty($column_reference))
                    {


                        $column_reference = collect($column_reference)->where('documentID',$documentSystemID)->first();

                        if(isset($column_reference->selectColumnAs))
                        {
                            $selectAs = $column_reference->selectColumnAs;
                        }else {
                            $selectAs = $column_reference->column;
                        }
                        if(collect(json_decode($selectedColumn->master_column_reference))->where('documentID',$documentSystemID)->count() > 1)
                        {
                            $lineItmsDetails = collect(json_decode($selectedColumn->master_column_reference))->where('documentID',$documentSystemID);

                            $sql = "CASE";
                            foreach($lineItmsDetails as $lineItmsDetail)
                            {
                                if(isset($column_reference->documentUniqueKey) && !empty($column_reference->documentUniqueKey))
                                {
                                    $sql .= " WHEN ".$column_reference->table.".".$column_reference->documentUniqueKey." = ".$lineItmsDetail->documentType." THEN ".$lineItmsDetail->table.'.'.$lineItmsDetail->selectQueryColumn;
                                }
                            }
                            $sql .= " END AS ".$column_reference->selectColumnAs;
                            array_push($query,$sql);
                        }else {
                            if($selectedColumn->isDate)
                            {
                                array_push($query,'DATE_FORMAT('.$column_reference->table.'.'.$column_reference->column.',"%d/%m/%Y") AS '.$selectAs);
                            }else {

                                array_push($query,$column_reference->table.'.'.$column_reference->column.' AS '.$selectAs);

                                if(!empty(json_decode($selectedColumn->master_column_reference)) && count(json_decode($selectedColumn->master_column_reference)) == 1)
                                {
                                    array_push($query,json_decode($selectedColumn->master_column_reference)[0]->table.'.'.json_decode($selectedColumn->master_column_reference)[0]->selectQueryColumn);

                                }
                            }

                        }



                        if(!empty(json_decode($selectedColumn->master_column_reference)))
                        {
                            if(collect(json_decode($selectedColumn->master_column_reference))->where('documentID',$documentSystemID)->count() > 1)
                            {
                                if($selectedColumn->isDetails)
                                {
                                    $parentTableReferences = collect(json_decode($selectedColumn->master_column_reference))->where('documentID',$documentSystemID);
                                    foreach($parentTableReferences as $parentTableReference)
                                    {
                                        array_push($mastertables,[
                                            "table" => $parentTableReference->table,
                                            "primaryKey" => $parentTableReference->column,
                                            "joinColumn" => $column_reference->table.'.'.$column_reference->column,
                                            "joinType" => "LEFT JOIN"
                                        ]);
                                    }
                                }

                            }else {

                                $record = array_values(collect(json_decode($selectedColumn->master_column_reference))->where('documentID',$documentSystemID)->toArray());
                                $checkJoinCondtion = isset($column_reference->joinKey) ? $column_reference->joinKey : $column_reference->column;
                                array_push($mastertables,[
                                    "table" => (!empty($record)) ? collect($record)->where('column',$checkJoinCondtion)->first()->table : null,
                                    "primaryKey" => (!empty($record)) ? collect($record)->where('column',$checkJoinCondtion)->first()->column : null,
                                    "joinColumn" => $column_reference->table.'.'.$column_reference->column,
                                    "joinType" => "INNER JOIN"
                                ]);
                            }

                        }


                        $data =   $this->buildSubQuery($selectedColumn,$query,$mastertables,$documentSystemID);
                        if(!empty($data))
                        {
                            $mastertables = $data['mastertables'];
                            $query = $data['query'];
                        }
                    }

                }

                $joinQuery = Array();

                if($isCurrencyColumnEmpty)
                {
                    $currencyColumnReference = ReportCustomColumn::where('id',9)->first();

                    if(!empty(json_decode($currencyColumnReference->column_reference)) && collect(json_decode($currencyColumnReference->column_reference))->where('documentID',$documentSystemID)->isNotEmpty())
                    {
                        $refeneceTable = collect(json_decode($currencyColumnReference->column_reference))->where('documentID',$documentSystemID)->first();
                        $masterTableReference =  (collect(json_decode($currencyColumnReference->master_column_reference))->isNotEmpty()) ? collect(json_decode($currencyColumnReference->master_column_reference))->first() : [];

                        if(!empty($masterTableReference))
                        {
                            array_push($joinQuery,"INNER JOIN ".$masterTableReference->table." ON ".$masterTableReference->table.".".$masterTableReference->column." = ".$refeneceTable->table.".".$refeneceTable->column);
                        }

                    }
                }

                if(!empty($mastertables))
                {
                    foreach ($mastertables as $mastertable)
                    {
                        if(!is_null($mastertable['table']) && !is_null($mastertable['primaryKey']))
                        {
                            array_push($joinQuery,$mastertable['joinType']." ".$mastertable['table']." ON ".$mastertable['table'].".".$mastertable['primaryKey']." = ".$mastertable['joinColumn']);
                        }
                    }
                }

                $joinQuery = $this->removeDuplicatedQueries($joinQuery);
                return [
                    'joinQuery' => implode(array_unique($joinQuery),' '),
                    'selectedQuery' => implode(',',$query)
                ];
        }
    }

    private function removeDuplicatedQueries($queries)
    {
        $uniqueJoinConditions = [];
        $uniqueQueries = [];

        foreach ($queries as $query) {
            // Extract the table and the ON condition
            if (preg_match('/^(INNER|LEFT) JOIN (\w+) ON (.+)$/', $query, $matches)) {
                $joinType = $matches[1];
                $table = $matches[2];
                $condition = trim($matches[3]);
                $identifier = $table . ' ON ' . $condition;

                if (!in_array($identifier, $uniqueJoinConditions)) {
                    $uniqueJoinConditions[] = $identifier;
                    $uniqueQueries[] = $query; // Keep the first encountered version (INNER or LEFT)
                }
            } else {
                // If the query doesn't match the expected pattern, keep it (though it shouldn't in this case)
                $uniqueQueries[] = $query;
            }
        }

        return ($uniqueQueries);
    }


    private function buildSubQuery($selectedColumn,$query,$mastertables,$documentSystemID)
    {
        $references = array_values(array_filter(json_decode($selectedColumn->master_column_reference), function ($item) use ($documentSystemID) {
            return $item->documentID == $documentSystemID;
        }));

        if (!empty($references)) {
            foreach($references as $reference)
            {
                $firstRef = $reference;
                if (isset($firstRef->masterTable)) {
                    $parentTable = $firstRef->masterTable;

                    // 23 - vat category type this join is conditional join with it's subcategory id, conditon based on document type
                    if($selectedColumn->id == 23) {
                        $target = 'AS vatSubCategoryID';
                        $index = array_search(true, array_map(function($item) use ($target) {
                            return strpos($item, $target) !== false;
                        }, $query));

                        $joinColumn = preg_replace('/\bEND\b.*/i', 'END', $query[$index]);
                    }else {
                        $joinColumn = $firstRef->table . '.' . $firstRef->joinColumn;
                    }

                    $mastertables[] = [
                        'table'       => $parentTable->table,
                        'primaryKey'  => $parentTable->column,
                        'joinColumn'  => $joinColumn,
                        "joinType" => "LEFT JOIN"
                    ];

                    $query[] = $parentTable->table . '.' . $parentTable->selectQueryColumn;
                }else {
                    $parentTable = $reference;
                    $firstRef = array_values(array_filter(json_decode($selectedColumn->column_reference), function ($item) use ($documentSystemID) {
                        return $item->documentID == $documentSystemID;
                    }))[0];

                    if (isset($firstRef->table)) {
                        // 23 - vat category type this join is conditional join with it's subcategory id, conditon based on document type
                        if($selectedColumn->id == 23) {
                            $target = 'AS vatSubCategoryID';
                            $index = array_search(true, array_map(function($item) use ($target) {
                                return strpos($item, $target) !== false;
                            }, $query));

                            $joinColumn = preg_replace('/\bEND\b.*/i', 'END', $query[$index]);
                        }else {
                            $joinColumn = $firstRef->table . '.' . $firstRef->column;
                        }

                        $mastertables[] = [
                            'table'       => $parentTable->table,
                            'primaryKey'  => $parentTable->column,
                            'joinColumn'  => $joinColumn,
                            "joinType" => "INNER JOIN"
                        ];

                        $query[] = $parentTable->table . '.' . $parentTable->selectQueryColumn;
                    }
                }


            }
        }

        return ['mastertables' => $mastertables, 'query' => $query];
    }
    public
    function pdfExportReport(Request $request)
    {
        ini_set('max_execution_time', 1800);
        ini_set('memory_limit', -1);
        $reportID = $request->reportID;
        switch ($reportID) {
            case 'FGL':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));

                $db = isset($request->db) ? $request->db : "";

                $employeeID = \Helper::getEmployeeSystemID();
                GeneralLedgerPdfJob::dispatch($db, $request, [$employeeID])->onQueue('reporting');

                return $this->sendResponse([], "General Ledger PDF report has been sent to queue");
                break;

            case 'FTB':
                $request = (object)$this->convertArrayToSelectedValue($request->all(), array('currencyID'));
                $db = isset($request->db) ? $request->db : "";

                $checkIsGroup = Company::find($request->companySystemID);
                $companyName = $checkIsGroup->CompanyName;
                $companyLogo = $checkIsGroup->logo_url;

                $currencyId =  $request->currencyID;

                $employeeID = \Helper::getEmployeeSystemID();
                $employeeData = Employee::where('employeeSystemID',$employeeID)->first();


                $output = $this->getTrialBalance($request);
                $companyCurrency = \Helper::companyCurrency($request->companySystemID);
                if($companyCurrency) {
                    $requestCurrencyLocal = $companyCurrency->localcurrency;
                    $requestCurrencyRpt = $companyCurrency->reportingcurrency;
                }

                $decimalPlaceLocal = !empty($requestCurrencyLocal) ? $requestCurrencyLocal->DecimalPlaces : 3;
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;


                $currencyLocal = $requestCurrencyLocal->CurrencyCode;
                $currencyRpt = $requestCurrencyRpt->CurrencyCode;

                $totalOpeningBalanceRpt = 0;
                $totalOpeningBalanceLocal = 0;
                $totaldocumentLocalAmountDebit = 0;
                $totaldocumentRptAmountDebit = 0;
                $totaldocumentLocalAmountCredit= 0;
                $totaldocumentRptAmountCredit = 0;
                $totalClosingBalanceRpt = 0;
                $totalClosingBalanceLocal= 0;

                if ($output) {
                    foreach ($output as $val) {

                        if ($checkIsGroup->isGroup == 0 && $currencyId ==1 || $currencyId ==3) {
                            $totalOpeningBalanceLocal = $totalOpeningBalanceLocal + $val->openingBalLocal;
                            $totaldocumentLocalAmountDebit = $totaldocumentLocalAmountDebit + $val->documentLocalAmountDebit;
                            $totaldocumentLocalAmountCredit = $totaldocumentLocalAmountCredit + $val->documentLocalAmountCredit;

                            $totalClosingBalanceLocal = $totalClosingBalanceLocal + $val->openingBalLocal + ($val->documentLocalAmountDebit - $val->documentLocalAmountCredit);
                        }
                        if($currencyId == 2 || $currencyId == 3) {
                            $totalOpeningBalanceRpt = $totalOpeningBalanceRpt + $val->openingBalRpt;
                            $totaldocumentRptAmountDebit = $totaldocumentRptAmountDebit + $val->documentRptAmountDebit;
                            $totalClosingBalanceRpt = $totalClosingBalanceRpt + ($val->openingBalRpt + ($val->documentRptAmountDebit - $val->documentRptAmountCredit));

                            $totaldocumentRptAmountCredit = $totaldocumentRptAmountCredit + $val->documentRptAmountCredit;
                        }
                    }
                }

                $dataArr = array(   'output'=>$output,
                                    'employeeData'=>$employeeData,
                                    'fromDate' => \Helper::dateFormat($request->fromDate),
                                    'toDate' => \Helper::dateFormat($request->toDate),
                                    'companyLogo'=>$companyLogo,
                                    'companyName'=>$companyName,
                                    'totalOpeningBalanceRpt'=>$totalOpeningBalanceRpt,
                                    'totalOpeningBalanceLocal'=>$totalOpeningBalanceLocal,
                                    'totaldocumentLocalAmountDebit'=>$totaldocumentLocalAmountDebit,
                                    'totaldocumentRptAmountDebit'=>$totaldocumentRptAmountDebit,
                                    'totaldocumentLocalAmountCredit'=>$totaldocumentLocalAmountCredit,
                                    'totaldocumentRptAmountCredit'=>$totaldocumentRptAmountCredit,
                                    'totalClosingBalanceRpt'=>$totalClosingBalanceRpt,
                                    'totalClosingBalanceLocal'=>$totalClosingBalanceLocal,
                                    'requestCurrencyLocal'=>$requestCurrencyLocal,
                                    'requestCurrencyRpt'=>$requestCurrencyRpt,
                                    'decimalPlaceLocal'=>$decimalPlaceLocal,
                                    'decimalPlaceRpt'=>$decimalPlaceRpt,
                                    'currencyId'=>$currencyId
                                );

                $html = view('print.financial_trial_balance', $dataArr);

                $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';

                $mpdf->WriteHTML($html);
                return $mpdf->Output('financial_trial_balance.pdf', 'I');   
                break;

            case 'FCT':

                $companyName = $request->companySystemID[0]['CompanyName'];
                $employeeID = \Helper::getEmployeeSystemID();
                $employeeData = Employee::where('employeeSystemID',$employeeID)->first();

                $reportData = $this->generateFRReport($request);

                $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
                if (isset($reportData['template']) && $reportData['template']['showDecimalPlaceYN']) {
                    if ($input['currency'] === 1) {
                        $reportData['decimalPlaces'] = $reportData['companyCurrency']['localcurrency']['DecimalPlaces'];
                    } else {
                        $reportData['decimalPlaces'] = $reportData['companyCurrency']['reportingcurrency']['DecimalPlaces'];
                    }
                } else {
                    $reportData['decimalPlaces'] = 0;
                }

                if ($input['currency'] === 1) {
                    $reportData['currencyCode'] = $reportData['companyCurrency']['localcurrency']['CurrencyCode'];
                } else {
                    $reportData['currencyCode'] = $reportData['companyCurrency']['reportingcurrency']['CurrencyCode'];
                }

                $reportData['accountType'] = $input['accountType'];

                if (is_array($reportData['uncategorize']) && $reportData['columnTemplateID'] == null) {
                    $reportData['isUncategorize'] = false;
                } else {
                    $reportData['isUncategorize'] = true;
                }

                if ($reportData['columnTemplateID'] == 1 || $reportData['columnTemplateID'] == 2) {
                    $templateName = "print.finance_column_template_one";
                } else {
                    $templateName = $reportData['accountType'] == 4? "print.equity_finance":"print.finance";
                }

                $month = '';
                if ($request->dateType != 1) {
                    $period = CompanyFinancePeriod::find($request->month);
                    $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                    $month = Carbon::parse($toDate)->format('Y-m-d');
                }
                if($month){
                    $reportData['month'] = ((new Carbon($month))->format('d/m/Y'));
                }
                $reportData['report_tittle'] = 'Finance Report';
                $reportData['from_date'] = $input['fromDate'];
                $reportData['to_date'] = $input['toDate'];

                if ($request->dateType == 1) {
                    $toDate = new Carbon($input['toDate']);
                    $reportData['to_date'] = $toDate->format('d/m/Y');
                    $fromDate = new Carbon($input['fromDate']);
                    $reportData['from_date'] = $fromDate->format('d/m/Y');
                } else {
                    $period = CompanyFinancePeriod::find($request->month);
                    $reportData['to_date'] = Carbon::parse($period->dateTo)->format('d/m/Y');
                    $reportData['from_date'] = Carbon::parse($period->dateFrom)->format('d/m/Y');
                }

                $reportData['employeeData'] = $employeeData;
                $reportData['CompanyName'] = $companyName;

                $html = view($templateName, $reportData);

                if (count($input['companySystemID']) > 1) {
                    $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A3-L', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                } else {
                    $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
                }
                $mpdf->AddPage('P');
                $mpdf->setAutoBottomMargin = 'stretch';
        
                $mpdf->WriteHTML($html);
                return $mpdf->Output($templateName, 'I');

                break;

            default:
                return $this->sendError('No report ID found');
        }
    }

    function getCustomizeFinancialRptQry($request, $linkedcolumnQry, $linkedcolumnQry2, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $showZeroGL, $cominedColumnKey)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();



        $isExpand = 0;
        $divisionValue = 1;
        $templateMaster = ReportTemplate::find($request->templateType);
        if ($templateMaster) {
            if ($templateMaster->showNumbersIn !== 1) {
                $numbers = ReportTemplateNumbers::find($templateMaster->showNumbersIn);
                $divisionValue = (float)$numbers->value;
            }

            if ($templateMaster->presentationType == 2) {
                $isExpand = 1;
            } else {
                $isExpand = 0;
            }
        }

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        $servicelineQryForElimination = '';
        if ($request->dateType == 1) {
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForElimination = 'AND erp_elimination_ledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';
        $secondLinkedcolumnQry = '';
        //$thirdLinkedcolumnQry = '';
        $fourthLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';
        $fifthLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $fifthLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $fifthLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTDFULL") {
                $fifthLinkedcolumnQry .= 'IFNULL( bAmountYearFull,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "ELMN") {
                $fifthLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else if ($coloumnShortCode == "CONS") {
                $fifthLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else {
                $fifthLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            }
            $secondLinkedcolumnQry .= '((IFNULL(IFNULL( c.`' . $val . '`, e.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';

            //$thirdLinkedcolumnQry .= 'IFNULL(SUM(d.`' . $val . '`),0) AS `' . $val . '`,';
            //$fourthLinkedcolumnQry .= 'IFNULL(SUM(`' . $val . '`),0) AS `' . $val . '`,';
            $whereQry[] .= 'IF(masterID is not null AND isFinalLevel = 1 , d.`' . $val . '` != 0,d.`' . $val . '` IS NOT NULL)';
        }

        $budgetJoin = '';
        $whereNonZero = '';
        $generalLedgerGroup = '';
        $templateGroup = '';
        if ($columnTemplateID == 1) {
            $secondLinkedcolumnQry .= ' ((IFNULL(IFNULL(c.compID, e.compID),0))) AS CompanyID,';
            $fourthLinkedcolumnQry .= ' compID,';
            $fifthLinkedcolumnQry .= ' compID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $budgetJoin = ' AND g.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $templateGroup = ', compID';
        } else if ($columnTemplateID == 2) {
            $secondLinkedcolumnQry .= ' ((IFNULL(IFNULL(c.serviceLineID, e.serviceLineID),0))) AS ServiceLineSystemID,';
            $fourthLinkedcolumnQry .= ' serviceLineID,';
            $fifthLinkedcolumnQry .= ' serviceLineID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.serviceLineSystemID AS serviceLineID,';
            $budgetJoin = ' AND g.serviceLineID = budget.serviceLineSystemID';
            $generalLedgerGroup = ' ,erp_generalledger.serviceLineSystemID';
            $templateGroup = ', serviceLineID';
        }

        // if (!$showZeroGL) {
        //     $whereNonZero = ' WHERE (' . join(' OR ', $whereQry) . ')';
        // }
        $sql = 'SELECT * FROM (SELECT
    c.detDescription,
    c.detID,
    ' . $secondLinkedcolumnQry . '
    c.sortOrder,
    c.masterID,
    c.isFinalLevel,
    c.bgColor,
    c.fontColor,
    c.itemType,
    c.netProfitStatus,
    c.hideHeader,
    ' . $isExpand . ' as expanded  
FROM
    (
SELECT
    b.*,
    erp_companyreporttemplatedetails.detID,
    erp_companyreporttemplatedetails.description AS detDescription,
    erp_companyreporttemplatedetails.sortOrder,
    erp_companyreporttemplatedetails.masterID,
    erp_companyreporttemplatedetails.isFinalLevel,
    erp_companyreporttemplatedetails.bgColor,
    erp_companyreporttemplatedetails.fontColor,
    erp_companyreporttemplatedetails.hideHeader,
    erp_companyreporttemplatedetails.itemType,
    erp_companyreporttemplatedetails.netProfitStatus,
    erp_companyreporttemplatedetails.controlAccountType as controlAccountType
FROM
    erp_companyreporttemplatedetails
    LEFT JOIN (
SELECT
    ' . $fourthLinkedcolumnQry . ' 
    templateDetailID,
    description
FROM
    (
        SELECT
            ' . $fifthLinkedcolumnQry . ' 
            templateDetailID,
            description
            FROM
            (
                (
                    SELECT
                        ' . $firstLinkedcolumnQry . ' 
                        erp_generalledger.chartOfAccountSystemID
                    FROM
                        erp_generalledger
                    INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                    WHERE
                        erp_generalledger.companySystemID IN (
                            ' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
                    GROUP BY
                        erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
                ) g
                INNER JOIN (
                    SELECT
                        erp_companyreporttemplatelinks.glAutoID,
                        erp_companyreporttemplatelinks.templateDetailID,
                        erp_companyreporttemplatelinks.categoryType AS linkCatType,
                        erp_companyreporttemplatedetails.description,
                        erp_companyreporttemplatedetails.categoryType AS templateCatType,
                        erp_companyreporttemplatedetails.controlAccountType as controlAccountType
                    FROM
                        erp_companyreporttemplatelinks
                    INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
                    WHERE
                        erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
                    ORDER BY
                        erp_companyreporttemplatedetails.sortOrder
                ) AS a ON a.glAutoID = g.chartOfAccountSystemID
            )
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin . '
    ) f
GROUP BY
    templateDetailID ' . $templateGroup . '
    ) AS b ON b.templateDetailID = erp_companyreporttemplatedetails.detID 
WHERE
    erp_companyreporttemplatedetails.companyReportTemplateID = ' . $request->templateType . ' 
    ) c
    LEFT JOIN (
SELECT
    ' . $fourthLinkedcolumnQry . '
    erp_companyreporttemplatelinks.templateDetailID 
FROM
    erp_companyreporttemplatelinks
    LEFT JOIN (
            SELECT
    ' . $fourthLinkedcolumnQry . ' 
    templateDetailID,
    description
FROM
    (
        SELECT
            ' . $fifthLinkedcolumnQry . ' 
            templateDetailID,
            description
            FROM
            (
                (
                    SELECT
                        ' . $firstLinkedcolumnQry . ' 
                        erp_generalledger.chartOfAccountSystemID
                    FROM
                        erp_generalledger
                    
                    INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                    WHERE
                        erp_generalledger.companySystemID IN (
                            ' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
                    GROUP BY
                        erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
                ) g
                INNER JOIN (
                    SELECT
                        erp_companyreporttemplatelinks.glAutoID,
                        erp_companyreporttemplatelinks.templateDetailID,
                        erp_companyreporttemplatelinks.categoryType AS linkCatType,
                        erp_companyreporttemplatedetails.description,
                        erp_companyreporttemplatedetails.categoryType AS templateCatType,
                        erp_companyreporttemplatedetails.controlAccountType as controlAccountType
                    FROM
                        erp_companyreporttemplatelinks
                    INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
                    WHERE
                        erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
                    ORDER BY
                        erp_companyreporttemplatedetails.sortOrder
                ) AS a ON a.glAutoID = g.chartOfAccountSystemID
            )
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin . '
    ) g
GROUP BY
    templateDetailID ' . $templateGroup . '
    ) d ON d.templateDetailID = erp_companyreporttemplatelinks.subCategory 
WHERE
    erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' 
    AND subCategory IS NOT NULL 
GROUP BY
    erp_companyreporttemplatelinks.templateDetailID ' . $templateGroup . '
    ) e ON e.templateDetailID = c.detID) d '.$whereNonZero;

        $output = \DB::select($sql);
        return $output;
    }

    function getCustomizeFinancialDetailRptQry($request, $linkedcolumnQry, $columnKeys, $financeYear, $period, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $showZeroGL, $cominedColumnKey,$linkedcolumnQry2)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();


        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        $servicelineQryForElimination = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForElimination = 'AND erp_elimination_ledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $divisionValue = 1;
        $templateMaster = ReportTemplate::find($request->templateType);
        if ($templateMaster) {
            if ($templateMaster->showNumbersIn !== 1) {
                $numbers = ReportTemplateNumbers::find($templateMaster->showNumbersIn);
                $divisionValue = (float)$numbers->value;
            }

        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';
        $secondLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $secondLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $secondLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTDFULL") {
                $secondLinkedcolumnQry .= 'IFNULL( bAmountYearFull,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "ELMN") {
                $secondLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else if ($coloumnShortCode == "CONS") {
                $secondLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else {
                $secondLinkedcolumnQry .= '((IFNULL(IF(erp_companyreporttemplatelinks.categoryType != erp_companyreporttemplatedetails.categoryType,gl.`' . $val . '`*-1,gl.`' . $val . '`),0))/' . $divisionValue . ') AS `' . $val . '`,';
            }
            $whereQry[] .= 'a.`' . $val . '` != 0';
        }

        $budgetJoin = '';
        $whereNonZero = '';

        if (!$showZeroGL) {
            $whereNonZero = ' WHERE (' . join(' OR ', $whereQry) . ')';
        }


        $generalLedgerGroup = '';
        $linkedcolumnQry2WithoutSum = preg_replace('/SUM\((.*?)\)/', '$1', $linkedcolumnQry2);

        if ($columnTemplateID == 1) {
            $linkedcolumnQry2WithoutSum .= ',b.compID';
            $secondLinkedcolumnQry .= ' gl.compID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $budgetJoin = ' AND gl.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
        } else if ($columnTemplateID == 2) {
            $linkedcolumnQry2WithoutSum .= ',b.serviceLineID';
            $secondLinkedcolumnQry .= ' gl.serviceLineID,';
            $firstLinkedcolumnQry .= ' erp_generalledger.serviceLineSystemID AS serviceLineID,';
            $budgetJoin = ' AND gl.serviceLineID = budget.serviceLineSystemID';
            $generalLedgerGroup = ' ,erp_generalledger.serviceLineSystemID';
        }

        $sql = 'SELECT * FROM (select 
        '.$linkedcolumnQry2WithoutSum.',
        b.glCode,
        b.glDescription,
        b.glAutoID,
        b.templateDetailID,
        b.linkCatType,
        b.templateCatType,
        b.controlAccountType,
        b.sortOrder
    from (SELECT
    ' . $secondLinkedcolumnQry . '
    erp_companyreporttemplatelinks.glCode,
    erp_companyreporttemplatelinks.glDescription,
    erp_companyreporttemplatelinks.glAutoID,
    erp_companyreporttemplatelinks.templateDetailID,
    erp_companyreporttemplatelinks.categoryType AS linkCatType,
    erp_companyreporttemplatedetails.categoryType AS templateCatType,
    erp_companyreporttemplatedetails.controlAccountType as controlAccountType,
    erp_companyreporttemplatelinks.sortOrder
FROM
    erp_companyreporttemplatelinks
    INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
    LEFT JOIN (
        SELECT
        ' . $firstLinkedcolumnQry . '
        erp_generalledger.chartOfAccountSystemID
    FROM
        erp_generalledger
        INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
        WHERE
        erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') 
        ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') AS gl ON erp_companyreporttemplatelinks.glAutoID = gl.chartOfAccountSystemID
    LEFT JOIN(
                SELECT
                    ' . $budgetQuery . ' 
                FROM
                    erp_budjetdetails
                INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                WHERE
                    erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                ', $companyID) . '
            ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
            ) AS budget
        ON
            budget.chartOfAccountID = erp_companyreporttemplatelinks.glAutoID ' . $budgetJoin . '
WHERE
    erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . ' AND erp_companyreporttemplatelinks.glAutoID IS NOT NULL
ORDER BY
    erp_companyreporttemplatelinks.sortOrder) b ) a '.$whereNonZero;

//        dd($sql);
        $output = \DB::select($sql);
        return $output;
    }

    function getCashflowOpeningBalanceQry($request, $currency, $columnTemplateID)
    {
        $fromDate = new Carbon($request->fromDate);
        $fromDate = $fromDate->format('Y-m-d');

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        //$serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $glCodes = ReportTemplateLinks::where('templateMasterID', $request->templateType)->whereNotNull('glAutoID')->pluck('glAutoID')->toArray();

        if ($columnTemplateID == 1) {
            $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance, companySystemID')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->groupBy('companySystemID')->get();
        } else if ($columnTemplateID == 2) {
            $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance, companySystemID, serviceLineSystemID')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->groupBy('serviceLineSystemID')->get();
        } else {
            $output = GeneralLedger::selectRaw('SUM(' . $currency . ') as openingBalance')->whereIN('companySystemID', $companyID)->whereIN('documentSystemID', $documents)->whereIN('chartOfAccountSystemID', $glCodes)->whereRaw('(DATE(erp_generalledger.documentDate) < "' . $fromDate . '")')->first();
        }


        return $output;
    }

    function getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $changeSelect, $cominedColumnKey)
    {
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        $servicelineQryForElimination = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForElimination = 'AND erp_elimination_ledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }
        $secondLinkedcolumnQry = '';
        $thirdLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $key)[0];
            $secondLinkedcolumnQry .= 'IFNULL(SUM(`' . $key . '`),0) AS `' . $key . '`,';
            if ($coloumnShortCode == "BCM" && !$changeSelect) {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $key . '`,';
            } else if ($coloumnShortCode == "BYTD" && !$changeSelect) {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $key . '`,';
            } else if ($coloumnShortCode == "BYTDFULL" && !$changeSelect) {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYearFull,  0 ) AS `' . $key . '`,';
            } else if ($coloumnShortCode == "ELMN" && !$changeSelect) {
                $thirdLinkedcolumnQry .= '0 AS `' . $key . '`,';
            } else if ($coloumnShortCode == "CONS" && !$changeSelect) {
                $thirdLinkedcolumnQry .= '0 AS `' . $key . '`,';
            } else {
                $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $key . '` * -1,`' . $key . '`),0) AS `' . $key . '`,';
            }
            $whereQry[] .= 'b.`' . $key . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $budgetJoinQuery1 = '';
        $budgetJoinQuery2 = '';
        if ($changeSelect) {
            $budgetJoinQuery2 = ' LEFT JOIN(
                            SELECT
                                ' . $budgetQuery . ' 
                            FROM
                                erp_budjetdetails
                            WHERE
                                erp_budjetdetails.companySystemID IN(' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                        ) AS budget
                    ON
                        budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID
            ';
        } else {
            $budgetJoinQuery1 = ' LEFT JOIN(
                            SELECT
                                ' . $budgetQuery . ' 
                            FROM
                                erp_budjetdetails
                            WHERE
                                erp_budjetdetails.companySystemID IN(' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                        ) AS budget
                    ON
                        budget.chartOfAccountID = a.glAutoID
            ';
        }


        $sql = 'SELECT * FROM (SELECT
    ' . $secondLinkedcolumnQry . ' 
    templateDetailID,
    description
FROM
    (
        SELECT
            ' . $thirdLinkedcolumnQry . ' 
            templateDetailID,
            description
            FROM
            (
                (
                    SELECT
                        ' . $firstLinkedcolumnQry . ' 
                        erp_generalledger.chartOfAccountSystemID
                    FROM
                        erp_generalledger
                    ' . $budgetJoinQuery2 . '
                    INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                    WHERE
                        erp_generalledger.companySystemID IN (
                            ' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
                    GROUP BY
                        erp_generalledger.chartOfAccountSystemID
                ) g
                INNER JOIN (
                    SELECT
                        erp_companyreporttemplatelinks.glAutoID,
                        erp_companyreporttemplatelinks.templateDetailID,
                        erp_companyreporttemplatelinks.categoryType AS linkCatType,
                        erp_companyreporttemplatedetails.description,
                        erp_companyreporttemplatedetails.categoryType AS templateCatType
                    FROM
                        erp_companyreporttemplatelinks
                    INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
                    WHERE
                        erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
                    ORDER BY
                        erp_companyreporttemplatedetails.sortOrder
                ) AS a ON a.glAutoID = g.chartOfAccountSystemID
                ' . $budgetJoinQuery1 . '
        )
    ) f
GROUP BY
    templateDetailID) b WHERE (' . join(' OR ', $whereQry) . ')';

        $output = \DB::select($sql);
        return $output;
    }


    function getCustomizeFinancialUncategorizeQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $cominedColumnKey, $companyWiseTemplate = false)
    {

        $reportTemplateMaster = ReportTemplate::find($request->templateType);
        $uncategorizeGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->whereNotExists(function ($query) use ($request) {
            $query->selectRaw('*')
                ->from('erp_companyreporttemplatelinks')
                ->where('templateMasterID', $request->templateType)
                ->whereRaw('chartofaccounts.chartOfAccountSystemID = erp_companyreporttemplatelinks.glAutoID');
        })->pluck('chartOfAccountSystemID')->toArray();

        if (count($uncategorizeGL) > 0) {
            $newColumData = $this->getFinancialCustomizeRptColumnQry($request, true, $companyWiseTemplate);
            $linkedcolumnQry = $newColumData['linkedcolumnQry'];
            $linkedcolumnQry2 = $newColumData['linkedcolumnQry2'];
            $columnKeys = $newColumData['columnKeys'];
            $budgetQuery = $newColumData['budgetQuery'];
            $budgetWhereQuery = $newColumData['budgetWhereQuery'];
        }

        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        $servicelineQryForElimination = '';
        if ($request->dateType == 1) {
            $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForElimination = 'AND erp_elimination_ledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $secondLinkedcolumnQry = '';
        $whereQry = [];
        foreach ($columnKeys as $key => $val) {
            $secondLinkedcolumnQry .= 'IFNULL(`' . $val . '`,0) AS `' . $val . '`,';
            $whereQry[] .= 'a.`' . $val . '` != 0';
        }

        $thirdLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 . ',' : '';

        $budgetJoin = '';
        $groupByCompID = '';
        $generalLedgerGroup = '';
        if ($columnTemplateID == 1) {
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $thirdLinkedcolumnQry .= ' compID,';
            $secondLinkedcolumnQry .= ' compID,';
            $budgetJoin = ' AND erp_generalledger.companySystemID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $groupByCompID = ' GROUP BY compID';
        } else if ($columnTemplateID == 2) {
            $firstLinkedcolumnQry .= ' erp_generalledger.serviceLineSystemID AS serviceLineID,';
            $thirdLinkedcolumnQry .= ' serviceLineID,';
            $secondLinkedcolumnQry .= ' serviceLineID,';
            $budgetJoin = ' AND erp_generalledger.serviceLineSystemID = budget.serviceLineSystemID';
            $generalLedgerGroup = ' ,erp_generalledger.serviceLineSystemID';
            $groupByCompID = ' GROUP BY serviceLineID';
        }


        $output = [];
        $outputDetail = [];
        if (count($uncategorizeGL) > 0) {
            $sql = 'SELECT  ' . $thirdLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription,glAutoID FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            chartofaccounts.AccountCode as glCode,
            chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a WHERE ' . join(' OR ', $whereQry) . $groupByCompID;

            $output = \DB::select($sql);

            $sql = 'SELECT  ' . $secondLinkedcolumnQry . ' chartOfAccountSystemID,glCode,glDescription,glAutoID FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID,
            erp_generalledger.chartOfAccountSystemID as glAutoID,
            chartofaccounts.AccountCode as glCode,
            chartofaccounts.AccountDescription as glDescription 
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a WHERE ' . join(' OR ', $whereQry);

            $outputDetail = \DB::select($sql);
        }


        return ['output' => $output, 'outputDetail' => $outputDetail];
    }

    function getEliminationCompanyGroup($request): array
    {
        $eliminationCompanyGroup = [];
        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $templateMaster = ReportTemplate::find($request->templateType);

        if ($templateMaster) {

            $companyGroupID = null;
            if($templateMaster->columnTemplateID == null && $templateMaster->isConsolidation == 1) {
                $companySubID = [];

                $groupCompanySystemID = $request->groupCompanySystemID[0] ?? null;
                foreach($request->companySystemID as $company) {

                    $latestStructure = GroupCompanyStructure::where('company_system_id',$company['companySystemID'])->where('isActive',1)->first();
                    if ($latestStructure) {
                        $groupParents = GroupParents::where('structure_id',$latestStructure->id)->where('company_system_id', $company['companySystemID'])->where('parent_company_system_id', $groupCompanySystemID)->first();

                        if($groupParents && $groupParents->group_type == 1) {
                            $companySubID[] = $groupParents->company_system_id;

                        }
                    }
                }

                $companyGroupID = collect($request->groupCompanySystemID)->pluck('companySystemID')->toArray();

                $subGroupCompanyIDs = array_unique(array_merge($companySubID, $companyGroupID));
            } else {
                $subGroupCompanyIDs = $companyID;
            }

            if(isset($companyGroupID)) {
                $eliminationCompanyGroup = array_values(collect($subGroupCompanyIDs)->diff($companyGroupID)->toArray());
            }
            else {
                $eliminationCompanyGroup = collect($subGroupCompanyIDs)->toArray();
            }
        }

        if(count($eliminationCompanyGroup) == 0) $eliminationCompanyGroup[] = 0;

        return $eliminationCompanyGroup;
    }

    function getCustomizeFinancialGrandTotalQry($request, $linkedcolumnQry, $linkedcolumnQry2, $financeYear, $period, $columnKeys, $budgetQuery, $budgetWhereQuery, $columnTemplateID, $cominedColumnKey)
    {

        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();
        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';
        $servicelineQryForElimination = '';
        if ($request->dateType == 1) {
            //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
            // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
        } else {
            if ($request->accountType == 2) {
                //$dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '") OR (DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $lastYearEndDate . '"))';
                // $dateFilter = 'AND ((DATE(erp_generalledger.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '"))';
            } else {
                $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                // $dateFilter = 'AND (DATE(erp_generalledger.documentDate) <= "' . $toDate . '")';
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND erp_generalledger.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND erp_generalledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForElimination = 'AND erp_elimination_ledger.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        $reportTemplateMaster = ReportTemplate::find($request->templateType);
        $uncategorizeGL = ChartOfAccount::where('catogaryBLorPL', $reportTemplateMaster->categoryBLorPL)->where('isActive', 1)->where('isApproved', 1)->whereNotExists(function ($query) use ($request) {
            $query->selectRaw('*')
                ->from('erp_companyreporttemplatelinks')
                ->where('templateMasterID', $request->templateType)
                ->whereRaw('chartofaccounts.chartOfAccountSystemID = erp_companyreporttemplatelinks.glAutoID');
        })->pluck('chartOfAccountSystemID')->toArray();

        $thirdLinkedcolumnQry = '';
        $whereQry = [];
        $secondLinkedcolumnQry = !empty($linkedcolumnQry2) ? $linkedcolumnQry2 : '';
        foreach ($columnKeys as $key => $val) {
            $coloumnShortCode = explode('-', $val)[0];
            if ($coloumnShortCode == "BCM") {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountMonth,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTD") {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYear,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "BYTDFULL") {
                $thirdLinkedcolumnQry .= 'IFNULL( bAmountYearFull,  0 ) AS `' . $val . '`,';
            } else if ($coloumnShortCode == "ELMN") {
                $thirdLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else if ($coloumnShortCode == "CONS") {
                $thirdLinkedcolumnQry .= '0 AS `' . $val . '`,';
            } else {
                $thirdLinkedcolumnQry .= 'IFNULL(IF(linkCatType != templateCatType,`' . $val . '` * -1,`' . $val . '`),0) AS `' . $val . '`,';
            }
            $whereQry[] .= 'b.`' . $val . '` != 0';
        }

        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $budgetJoin1 = '';
        $budgetJoin2 = '';
        $generalLedgerGroup = '';
        $unionGroupBy = '';
        $templateGroupBY = '';
        if ($columnTemplateID == 1) {
            $firstLinkedcolumnQry .= ' erp_generalledger.companySystemID AS compID,';
            $secondLinkedcolumnQry .= ' ,compID';
            $templateGroupBY .= ' ,compID';
            $thirdLinkedcolumnQry .= ' compID,';
            $budgetJoin1 = ' AND erp_generalledger.companySystemID = budget.companySystemID';
            $budgetJoin2 = ' AND g.compID = budget.companySystemID';
            $generalLedgerGroup = ' ,erp_generalledger.companySystemID';
            $unionGroupBy = ' GROUP BY compID';
        } else if ($columnTemplateID == 2) {
            $firstLinkedcolumnQry .= ' erp_generalledger.serviceLineSystemID AS serviceLineID,';
            $secondLinkedcolumnQry .= ' ,serviceLineID';
            $templateGroupBY .= ' ,serviceLineID';
            $thirdLinkedcolumnQry .= ' serviceLineID,';
            $budgetJoin1 = ' AND erp_generalledger.serviceLineSystemID = budget.serviceLineSystemID';
            $budgetJoin2 = ' AND g.serviceLineID = budget.serviceLineSystemID';
            $generalLedgerGroup = ' ,erp_generalledger.serviceLineSystemID';
            $unionGroupBy = ' GROUP BY serviceLineID';
        }


        $unionQry = '';
        if (count($uncategorizeGL) > 0) {
            $unionQry = ' UNION SELECT  ' . $secondLinkedcolumnQry . ' FROM (SELECT
            ' . $firstLinkedcolumnQry . '
            erp_generalledger.chartOfAccountSystemID
        FROM
            erp_generalledger
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = erp_generalledger.chartOfAccountSystemID ' . $budgetJoin1 . '
            INNER JOIN chartofaccounts ON erp_generalledger.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
        WHERE
            erp_generalledger.companySystemID IN (' . join(',', $companyID) . ') AND
            erp_generalledger.chartOfAccountSystemID IN (' . join(',', $uncategorizeGL) . ')
            ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
        GROUP BY
            erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . ') a';
        }

        $sql = 'SELECT ' . $secondLinkedcolumnQry . ' FROM (SELECT * FROM (SELECT
    ' . $secondLinkedcolumnQry . '
FROM
    (
        SELECT
            ' . $thirdLinkedcolumnQry . ' 
            templateDetailID,
            description
            FROM
            (
                (
                    SELECT
                        ' . $firstLinkedcolumnQry . ' 
                        erp_generalledger.chartOfAccountSystemID
                    FROM
                        erp_generalledger
                    INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = erp_generalledger.chartOfAccountSystemID
                    WHERE
                        erp_generalledger.companySystemID IN (
                            ' . join(',
                            ', $companyID) . '
                        ) ' . $servicelineQry . ' ' . $dateFilter . ' ' . $documentQry . '
                    GROUP BY
                        erp_generalledger.chartOfAccountSystemID ' . $generalLedgerGroup . '
                ) g
                INNER JOIN (
                    SELECT
                        erp_companyreporttemplatelinks.glAutoID,
                        erp_companyreporttemplatelinks.templateDetailID,
                        erp_companyreporttemplatelinks.categoryType AS linkCatType,
                        erp_companyreporttemplatedetails.description,
                        erp_companyreporttemplatedetails.categoryType AS templateCatType
                    FROM
                        erp_companyreporttemplatelinks
                    INNER JOIN erp_companyreporttemplatedetails ON erp_companyreporttemplatelinks.templateDetailID = erp_companyreporttemplatedetails.detID
                    WHERE
                        erp_companyreporttemplatelinks.templateMasterID = ' . $request->templateType . '
                    ORDER BY
                        erp_companyreporttemplatedetails.sortOrder
                ) AS a ON a.glAutoID = g.chartOfAccountSystemID
            )
            LEFT JOIN(
                    SELECT
                        ' . $budgetQuery . ' 
                    FROM
                        erp_budjetdetails
                    INNER JOIN erp_budgetmaster ON erp_budgetmaster.budgetmasterID = erp_budjetdetails.budgetmasterID
                    WHERE
                        erp_budgetmaster.approvedYN = -1 AND erp_budjetdetails.companySystemID IN(' . join(',
                    ', $companyID) . '
                ) ' . $servicelineQryForBudget . ' ' . $budgetWhereQuery . '
                ) AS budget
            ON
                budget.chartOfAccountID = a.glAutoID ' . $budgetJoin2 . '
    ) f
GROUP BY
    templateDetailID' . $templateGroupBY . ') b WHERE (' . join(' OR ', $whereQry) . ') ' . $unionQry . ') b' . $unionGroupBy;

        $output = \DB::select($sql);
        return $output;
    }

    /**
     * function to decode tax formula to multiple combined formula
     * @param $columnLinkID
     * @param $rowValues
     * @param $columnArray
     * @param $linkedRowHead
     * @param $type
     * @return string
     */
    public function columnFormulaDecode($columnLinkID, $rowValues, $columnArray, $linkedRowHead = false, $type)
    {
        global $globalFormula;
        $finalFormula = '';
        $taxFormula = ReportTemplateColumnLink::find($columnLinkID);
        $globalFormula = $taxFormula->formula;
        $linkedColumns = $taxFormula->formulaColumnID;
        $linkedRows = '';
        if ($linkedRowHead) {
            $linkedRows = $taxFormula->formulaRowID;
        }
        $sepFormulaArr = $this->decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray, $type);
        $globalFormula = '';
        if ($sepFormulaArr) {
            $fomulaFinal = '';
            $formulaArr = explode('~', $sepFormulaArr);
            if ($formulaArr) {
                foreach ($formulaArr as $val2) {
                    $removedFirstChar = substr($val2, 1);
                    $fomulaFinal .= $removedFirstChar;
                }
                $finalFormula = $fomulaFinal;
            }
        }

        return $finalFormula;
    }


    /**
     * function to decode customize report
     * @param $linkedColumns - connected formulas
     * @param $rowValues
     * @param $columnArray
     * @param $linkedRows
     * @param $type
     * @return mixed
     */
    public function decodeColumnFormula($linkedColumns, $linkedRows, $rowValues, $columnArray, $type)
    {
        global $globalFormula;
        $taxFormula = ReportTemplateColumnLink::whereIn('columnLinkID', explode(',', $linkedColumns))->get();
        if ($taxFormula) {
            foreach ($taxFormula as $val) {
                $searchVal = '#' . $val['columnLinkID'];
                if (!empty($val['formulaColumnID'])) {
                    $replaceVal = '|(~' . $val['formula'] . '~|)';
                    $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    $return = $this->decodeColumnFormula($val['formulaColumnID'], $val['formulaRowID'], $rowValues, $columnArray, $type);
                    if (is_array($return)) {
                        if ($return[0] == 'e') {
                            return $return;
                            break;
                        }
                    }
                } else {
                    $replaceVal = '';
                    if ($type == 1) {
                        $replaceVal = '#' . $columnArray[$val['shortCode']];
                    } else {
                        $replaceVal = '#IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                    }

                    $globalFormula = str_replace_first($searchVal, $replaceVal, $globalFormula);
                    /*$replaceVal = '/'.$columnArray[$val['shortCode']].'/';
                    $globalFormula = preg_replace($searchVal, $replaceVal, $globalFormula,1);*/
                }
            }
        }

        // if there is a row linked to the formula calculation will be done here
        if ($linkedRows) {
            $explodedLinkedColumns = explode(',', $linkedColumns);
            $linkedColumnsShortCode = ReportTemplateColumnLink::whereIN('columnLinkID', $explodedLinkedColumns)->get();
            foreach ($linkedColumnsShortCode as $column) {
                $columnCustomeCode = $column->shortCode . '-' . $column->columnLinkID;
                $explodeLinkedRows = explode(',', $linkedRows);
                if ($explodeLinkedRows) {
                    foreach ($explodeLinkedRows as $val) {
                        $searchVal = '$' . $val;
                        $filtered = $rowValues->where('templateDetailID', $val);
                        $detValues = $filtered->values();
                        $replaceVal = '';
                        if (count($detValues) > 0) {
                            $replaceVal = '$' . $detValues[0]->$columnCustomeCode;
                        } else {
                            $replaceVal = '$0';
                        }
                        $globalFormula = str_replace($searchVal, $replaceVal, $globalFormula);
                    }
                }
            }
        }
        return $globalFormula;
    }

    public function reportTemplateGLDrillDown(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $output = $this->reportTemplateGLDrillDownQry($request);

        $total = collect($output)->pluck($input['selectedColumn'])->toArray();
        $total = array_sum($total);

        $request->request->remove('search.value');

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('total', $total)
            ->make(true);
    }


    public function reportTemplateGLDrillDownQry($request)
    {

        $input = $request->all();
        $fromDateObj = new Carbon($request->fromDate);
        $fromDate = $fromDateObj->format('Y-m-d');

        $toDateObj = new Carbon($request->toDate);
        $toDate = $toDateObj->format('Y-m-d');

        $reportTemplate = ReportTemplate::where('companyReportTemplateID',$input['templateType'])->first();

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);
        $period = CompanyFinancePeriod::find($request->month);

        // get generated customized column
        $generatedColumn = $this->getFinancialCustomizeRptColumnQry($request);
        $linkedcolumnQry = $generatedColumn['linkedcolumnQry'];
        $budgetQuery = $generatedColumn['budgetQuery'];
        $budgetWhereQuery = $generatedColumn['budgetWhereQuery'];


        $firstLinkedcolumnQry = !empty($linkedcolumnQry) ? $linkedcolumnQry . ',' : '';

        $companyID = collect($request->companySystemID)->pluck('companySystemID')->toArray();
        $groupCompanyID = collect($request->groupCompanySystemID)->pluck('companySystemID')->toArray();
        $serviceline = collect($request->serviceLineSystemID)->pluck('serviceLineSystemID')->toArray();

        if ($request->columnTemplateID == 2) {
            $selectedSegmentData = SegmentMaster::where('ServiceLineCode', $request->selectedCompany)->first();

            $serviceline = collect($selectedSegmentData->serviceLineSystemID)->toArray();
        }

        $documents = ReportTemplateDocument::pluck('documentSystemID')->toArray();

        $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
        $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
        $lastYearEndDate = Carbon::parse($financeYear->endingDate);
        $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');


        $currency = $input['currency'][0] ?? $input['currency'];
        $currencyColumn = $currency == 1 ? "documentLocalAmount" : "documentRptAmount";

        $columnCode = explode('-', $input['selectedColumn'])[0] ?? null;

        $dateFilter = '';
        $documentQry = '';
        $servicelineQry = '';
        $servicelineQryForBudget = '';

        if ($columnCode != 'ELMN') {

            if ($request->dateType == 1) {
                $dateFilter = 'AND (DATE(gl.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '")';
                if ($request->accountType == 1) {
                    $dateFilter = '';
                }
            } else {
                if ($request->accountType == 2) {
                    $dateFilter = 'AND (DATE(gl.documentDate) BETWEEN "' . $lastYearStartDate . '" AND "' . $toDate . '")';
                } else {
                    $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                    $dateFilter = 'AND (DATE(gl.documentDate) <= "' . $toDate . '")';
                }
            }
        }

        if ($request->accountType == 3) {
            if (count($documents) > 0) {
                $documentQry = 'AND gl.documentSystemID IN (' . join(',', $documents) . ')';
            }
        }

        if ($request->accountType == 2 || $request->columnTemplateID == 2) {
            if (count($serviceline) > 0) {
                $servicelineQry = 'AND gl.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
                $servicelineQryForBudget = 'AND erp_budjetdetails.serviceLineSystemID IN (' . join(',', $serviceline) . ')';
            }
        }

        if ($request->columnTemplateID == 1) {
            $selectedCompanyData = Company::where('CompanyID', $request->selectedCompany)->first();

            $companyID = collect($selectedCompanyData->companySystemID)->toArray();
        }

        if ($columnCode != 'ELMN') {
            if ($columnCode == 'CMB') {
                $dateFilter = "";
                if(is_array($groupCompanyID) && isset($groupCompanyID[0])) {
                    $firstLinkedcolumnQry = ConsolidationReportService::generateFilterQuery($fromDateObj, $toDateObj, $groupCompanyID[0], $companyID, $currencyColumn, [1], true, "gl", true);
                    $firstLinkedcolumnQry .= " AS `" . $input['selectedColumn'] . "`,";
                }
            }

            $drillDownData = $this->reportTemplateGLDrillDownQryData($input, $firstLinkedcolumnQry, $companyID, $servicelineQry, $dateFilter, $documentQry);

            if (($reportTemplate->reportID == 1) && ($reportTemplate->isConsolidation == 1) && ($columnCode == 'CMB')) {
                foreach ($drillDownData as $data) {
                    if(is_array($groupCompanyID) && isset($groupCompanyID[0])) {
                        $data->type = ($data->companySystemID == $groupCompanyID[0]) ? "Parent" : "Subsidiary";
                    }
                    $data->companyProfit = $data->{$input['selectedColumn']};
                    $data->holdingPercentage = "100";
                }

                // Add Joint venture & Associate document data
                $groupTypes = [2,3];
                $periods = ConsolidationReportService::getCompanyOwnershipPeriods($groupCompanyID[0], $companyID, $groupTypes);

                foreach ($periods as $period) {
                    $rowStartDate = Carbon::parse($period->start_date);
                    $rowEndDate = ($period->end_date == null) ? $toDateObj : Carbon::parse($period->end_date);

                    if ($rowStartDate->isBetween($fromDateObj, $toDateObj) || $rowEndDate->isBetween($fromDateObj, $toDateObj)) {
                        $queryStartDate = ($fromDateObj >= $rowStartDate) ? $fromDateObj : $rowStartDate;
                        $queryEndDate = ($toDateObj <= $rowEndDate) ? $toDateObj : $rowEndDate;

                        $filter = "WHEN gl.companySystemID = " . $period->company_system_id;
                        $filter .= " AND (DATE(gl.documentDate) BETWEEN '" . $queryStartDate->format("Y-m-d") . "' AND '" . $queryEndDate->format("Y-m-d") . "')";
                        $filter .= " THEN (gl." . $currencyColumn . " * -1) * " . ($period->holding_percentage / 100);
                        $filter = "IFNULL(CASE " . $filter . " ELSE 0 END, 0) AS `" . $input['selectedColumn'] . "`,";

                        $drillDownDataOtherTypes = $this->reportTemplateGLDrillDownQryData($input, $filter, $companyID, $servicelineQry, $dateFilter, $documentQry);

                        foreach ($drillDownDataOtherTypes as $data) {
                            $data->type = ConsolidationReportService::getCompanyType($period->group_type);
                            $data->holdingPercentage = $period->holding_percentage;
                            // Reverse calculate initial amount using company portion & holding percentage value
                            $data->companyProfit = $data->{$input['selectedColumn']} / ($period->holding_percentage / 100);

                            $drillDownData[] = $data;
                        }
                    }
                }
            }

            return $drillDownData;
        }
        else {
            $companyID = array_values(array_diff($companyID,$groupCompanyID));

            $subsidiaryCompanyPeriodsFilterForEL = ConsolidationReportService::generateFilterQuery($fromDateObj, $toDateObj, $groupCompanyID[0], $companyID, $currencyColumn, [1], false, "el", true);

            $sql = "SELECT 
                        glCode,
                        AccountDescription,
                        documentCode,
                        documentDate,
                        ServiceLineDes,
                        documentNarration,
                        clientContractID,
                        partyName,
                        documentSystemCode,
                        documentSystemID,
                        IFNULL(CASE WHEN controlAccountsSystemID = 2 THEN `" . $input['selectedColumn'] . "` * - 1 ELSE `" . $input['selectedColumn'] . "` END, 0) AS `" . $input['selectedColumn'] . "`
                    FROM 
                        (
                            SELECT
                                glCode,
                                AccountDescription,
                                documentCode,
                                documentDate,
                                serviceline.ServiceLineDes,
                                consoleJVNarration AS documentNarration,
                                el.clientContractID,
                                suppliermaster.supplierName AS partyName,
                                el.documentSystemCode,
                                el.documentSystemID,
                                controlAccountsSystemID,
                                " . $subsidiaryCompanyPeriodsFilterForEL . " AS `" . $input['selectedColumn'] . "`
                            FROM
                                erp_elimination_ledger AS el
                                INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = el.chartOfAccountSystemID
                                LEFT JOIN serviceline ON serviceline.serviceLineSystemID = el.serviceLineSystemID
                                LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = el.supplierCodeSystem
                                INNER JOIN erp_consolejvmaster ON erp_consolejvmaster.consoleJvMasterAutoId = el.documentSystemCode
                            WHERE
                                el.chartOfAccountSystemID = " . $input['glAutoID'] . " 
                                AND el.companySystemID IN (" . join(",", $companyID) . ") 
                                AND el.serviceLineSystemID IN (" . join(",", $serviceline) . ")
                        ) el
                    WHERE el.`" . $input['selectedColumn'] . "` != 0";

            return DB::select($sql);
        }
    }

    public function reportTemplateGLDrillDownQryData($input, $linkedColumnQry, $companyID, $servicelineQry, $dateFilter, $documentQry) {
        $sql = "SELECT 
                        `" . $input['selectedColumn'] . "`,
                        glCode,
                        AccountDescription,
                        documentCode,
                        documentDate,
                        ServiceLineDes,
                        partyName,
                        documentNarration,
                        clientContractID,
                        documentSystemCode,
                        documentSystemID,
                        CompanyName,
                        companySystemID 
                    FROM 
                        (
                            SELECT
                                " . $linkedColumnQry . "
                                glCode,
                                AccountDescription,
                                documentCode,
                                documentDate,
                                serviceline.ServiceLineDes,
                                gl.documentNarration,
                                gl.clientContractID,
                                IF(
                                    gl.documentSystemID = 87 OR 
                                    gl.documentSystemID = 20 OR 
                                    gl.documentSystemID = 21 OR 
                                    gl.documentSystemID = 19 OR 
                                    gl.documentSystemID = 71, 
                                        customermaster.CustomerName, 
                                        suppliermaster.supplierName 
                                ) AS partyName, 
                                gl.documentSystemCode,
                                gl.documentSystemID,
                                companymaster.CompanyName,
                                companymaster.companySystemID
                            FROM
                                erp_generalledger AS gl
                            INNER JOIN chartofaccounts ON chartofaccounts.chartOfAccountSystemID = gl.chartOfAccountSystemID
                            LEFT JOIN serviceline ON serviceline.serviceLineSystemID = gl.serviceLineSystemID
                            LEFT JOIN suppliermaster ON suppliermaster.supplierCodeSystem = gl.supplierCodeSystem
                            LEFT JOIN customermaster ON customermaster.customerCodeSystem = gl.supplierCodeSystem 
                            LEFT JOIN companymaster ON companymaster.companySystemID = gl.companySystemID 
                            WHERE
                                gl.chartOfAccountSystemID = " . $input['glAutoID'] . " AND
                                gl.companySystemID IN (" . join(",",$companyID) . ")
                                " . $servicelineQry . " " . $dateFilter . " " . $documentQry . "
                            GROUP BY GeneralLedgerID
                        ) a 
                    WHERE a.`" . $input['selectedColumn'] . "` != 0";

        return DB::select($sql);
    }

    public function reportTemplateGLDrillDownExport(Request $request)
    {

        $input = $request->all();
        $type = $request->type;
        $data = array();
        $output = $request->accountType == 4 ? $this->reportTemplateEquityGLDrillDownQry($request):$this->reportTemplateGLDrillDownQry($request);
        $columName =  $request->accountType == 4 ? 'Amount':$input['selectedColumn'];

        if ($output) {
            $total = collect($output)->pluck($input['selectedColumn'])->toArray();
            $total = array_sum($total);
            $x = 0;
            foreach ($output as $val) {
                $tem = (array)$val;

                $data[$x]['Document Number'] = $val->documentCode;
                $data[$x]['Date'] = \Helper::dateFormat($val->documentDate);
                $data[$x]['Document Narration'] = $val->documentNarration;
                $data[$x]['Segment'] = $val->ServiceLineDes;
                $data[$x]['Contract'] = $val->clientContractID;
                $data[$x]['Supplier/Customer'] = $val->partyName;
                $data[$x][$columName] = $tem[$input['selectedColumn']];
                $x++;
            }

            $data[$x]['Document Number'] = '';
            $data[$x]['Date'] = '';
            $data[$x]['Document Narration'] = '';
            $data[$x]['Segment'] = '';
            $data[$x]['Contract'] = '';
            $data[$x]['Supplier/Customer'] = 'Total';
            $data[$x][$columName] = $total;
        }


        \Excel::create('trial_balance', function ($excel) use ($data) {
            $excel->sheet('sheet name', function ($sheet) use ($data) {
                $sheet->fromArray($data, null, 'A1', true);
                $sheet->setAutoSize(true);
                $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
            });
            $lastrow = $excel->getActiveSheet()->getHighestRow();
            $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
        })->download($type);

        return $this->sendResponse(array(), 'successfully export');
    }

    function getFinancialCustomizeRptColumnQry($request, $changeSelect = false, $companyWiseTemplate = false)
    {

        $toDate = '';
        $fromDate = '';
        $month = '';
        $period = '';
        $currencyColumn = '';
        $budgetColumn = '';
        $columnArray = [];
        $columnHeaderArray = [];
        $columnHeader = [];
        $columnHeaderMapping = [];
        $linkedcolumnArray = [];
        $linkedcolumnArray2 = [];
        $linkedcolumnArray3 = [];

        $financeYear = CompanyFinanceYear::find($request->companyFinanceYearID);
        if ($request->dateType == 1) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = ($period) ? Carbon::parse($period->dateTo)->format('Y-m-d') : null;
            $month = Carbon::parse($toDate)->format('Y-m-d');
            $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
        }

        //assign db currency column
        if ($request->currency == 1) {
            $currencyColumn = 'documentLocalAmount';
            $budgetColumn = 'budjetAmtLocal';
        } else {
            $currencyColumn = 'documentRptAmount';
            $budgetColumn = 'budjetAmtRpt';
        }

        $reportTemplateMasterData = ReportTemplate::find($request->templateType);

        $columnTemplateID = ($companyWiseTemplate) ? 1 : $reportTemplateMasterData->columnTemplateID;

        $columns = ReportTemplateColumns::all();
        $linkedColumn = ReportTemplateColumnLink::ofTemplate($request->templateType)->where('hideColumn', 0)->orderBy('sortOrder')->get();
        if ((count($columns) > 0) && isset($financeYear)) {
            $currentYearPeriod = CarbonPeriod::create($financeYear->bigginingDate, '1 month', $financeYear->endingDate);
            $currentYearPeriodArr = [];
            $lastYearStartDate = Carbon::parse($financeYear->bigginingDate);
            $lastYearStartDate = $lastYearStartDate->subYear()->format('Y-m-d');
            $lastYearEndDate = Carbon::parse($financeYear->endingDate);
            $lastYearEndDate = $lastYearEndDate->subYear()->format('Y-m-d');
            $lastYearPeriod = CarbonPeriod::create($lastYearStartDate, '1 month', $lastYearEndDate);
            $lastYearPeriodArr = [];
            $linkedcolumnArrayFinal = [];
            $linkedcolumnArrayFinal2 = [];
            $linkedcolumnArrayFinal3 = [];
            $currentMonth = Carbon::parse($toDate)->format('Y-m');
            $currentYear = Carbon::parse($toDate)->format('Y');
            $currentYearCurrentMonthOnly = (int)Carbon::parse($toDate)->format('m');
            $prevMonth = Carbon::parse($currentMonth)->subMonth()->format('Y-m');
            $prevMonth2 = Carbon::parse($currentMonth)->subMonth(2)->format('Y-m');
            $LCurrentMonth = Carbon::parse($toDate)->subYear()->format('Y-m');
            $LYear = Carbon::parse($toDate)->subYear()->format('Y');
            $LYear1 = Carbon::parse($toDate)->subYear(2)->format('Y');
            $LYear2 = Carbon::parse($toDate)->subYear(3)->format('Y');
            $LYear3 = Carbon::parse($toDate)->subYear(4)->format('Y');
            $LPrevMonth = Carbon::parse($LCurrentMonth)->subMonth()->format('Y-m');
            $LPrevMonth2 = Carbon::parse($LCurrentMonth)->subMonth(2)->format('Y-m');
            foreach ($currentYearPeriod as $val) {
                $currentYearPeriodArr[] = $val->format('Y-m');
            }

            foreach ($lastYearPeriod as $val) {
                $lastYearPeriodArr[] = $val->format('Y-m');
            }

            // link queries to selected column
            $currentMonthColumn = collect($columns)->where('type', 3)->values();
            $prevMonthColumn = collect($columns)->where('type', 6)->values();
            if (count($currentMonthColumn) > 0) {
                foreach ($currentMonthColumn as $key => $val) {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $currentYearPeriodArr[$key] . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $currentYearPeriodArr[$key];
                }
            }


            if (count($prevMonthColumn) > 0) {
                foreach ($prevMonthColumn as $key => $val) {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $lastYearPeriodArr[$key] . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $lastYearPeriodArr[$key];
                }
            }

            $cominedColumnData =  collect($linkedColumn)->where('shortCode', "CMB")->first();
            $cominedColumnKey = $cominedColumnData ? $cominedColumnData->shortCode."-".$cominedColumnData->columnLinkID : "";

            $cvtdColumnData =  collect($linkedColumn)->where('shortCode', "CYYTD")->first();
            $CYYTDColumnKey = $cvtdColumnData ? $cvtdColumnData->shortCode."-".$cvtdColumnData->columnLinkID : "";

            $consColumnData =  collect($linkedColumn)->where('shortCode', "CONS")->first();
            $CONSColumnKey = $consColumnData ? $consColumnData->shortCode."-".$consColumnData->columnLinkID : "";

            foreach ($columns as $val) {

                if ($request->dateType == 1) {
                    $toDate = new Carbon($request->toDate);
                    $toDate = $toDate->format('Y-m-d');
                    $fromDate = new Carbon($request->fromDate);
                    $fromDate = $fromDate->format('Y-m-d');
                } else {
                    $period = CompanyFinancePeriod::find($request->month);
                    $toDate = ($period) ? Carbon::parse($period->dateTo)->format('Y-m-d') : null;
                    $month = Carbon::parse($toDate)->format('Y-m-d');
                    $fromDate = Carbon::parse($period->dateFrom)->format('Y-m-d');
                }

                if ($val->shortCode == 'CM') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $currentMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $currentMonth;
                }
                if ($val->shortCode == 'CM-1') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $prevMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $prevMonth;
                }
                if ($val->shortCode == 'CM-2') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $prevMonth2 . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $prevMonth2;
                }
                if ($val->shortCode == 'LYCM') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LCurrentMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LCurrentMonth;
                }
                if ($val->shortCode == 'LYCM-1') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LPrevMonth . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LPrevMonth;
                }
                if ($val->shortCode == 'LYCM-2') {
                    $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m') = '" . $LPrevMonth2 . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    $columnHeaderArray[$val->shortCode] = $LPrevMonth2;
                }
                if ($val->shortCode == 'CYYTD') {
                    if ($request->accountType == 2) {
                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }

                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $currentYear;
                }
                if ($val->shortCode == 'LYYTDFULL') {
                    if ($request->accountType == 2) {
                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear()->format('Y-m-d');
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                            // $toDate = Carbon::parse($period->dateTo)->subYear()->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear()->format('Y-m-d');
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            // $toDate = Carbon::parse($period->dateTo)->subYear()->format('Y-m-d');
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear()->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear;
                }
                if ($val->shortCode == 'LYYTD') {
                    if ($request->accountType == 2) {
                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear()->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->subYear()->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($toDate)->subYear()->format('Y-m-d');
                            $fromDate = Carbon::parse($fromDate)->subYear()->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($period->dateTo)->subYear()->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($toDate)->subYear()->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }

                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear;
                }


                if ($val->shortCode == 'CY-2') {
                    if ($request->accountType == 2) {

                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear(2)->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->subYear(2)->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($request->toDate)->subYear(2)->format('Y-m-d');
                            $fromDate = Carbon::parse($request->fromDate)->subYear(2)->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear(2)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear1;
                }

                if ($val->shortCode == 'CY-3') {
                    if ($request->accountType == 2) {

                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear(3)->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->subYear(3)->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($request->toDate)->subYear(3)->format('Y-m-d');
                            $fromDate = Carbon::parse($request->fromDate)->subYear(3)->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear(3)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear2;
                }

                if ($val->shortCode == 'CY-4') {
                    if ($request->accountType == 2) {

                        if ($request->dateType == 2) {
                            $fromDate = Carbon::parse($financeYear->bigginingDate)->subYear(4)->format('Y-m-d');
                            $toDate = Carbon::parse($period->dateTo)->subYear(4)->format('Y-m-d');
                        }
                        else if ($request->dateType == 1) {
                            $toDate = Carbon::parse($request->toDate)->subYear(4)->format('Y-m-d');
                            $fromDate = Carbon::parse($request->fromDate)->subYear(4)->format('Y-m-d');
                        }

                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 1) {
                        if ($request->dateType == 2) {
                            $toDate = Carbon::parse($financeYear->endingDate)->subYear(4)->format('Y-m-d');
                        }
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    } else if ($request->accountType == 3) {
                        $columnArray[$val->shortCode] = "IFNULL(SUM(if(DATE_FORMAT(documentDate,'%Y-%m-%d') >= '" . $fromDate . "' AND DATE_FORMAT(documentDate,'%Y-%m-%d') <= '" . $toDate . "',IF(chartofaccounts.catogaryBLorPL = 'PL',
    $currencyColumn * - 1,IF(chartofaccounts.catogaryBLorPL = 'BS' && (chartofaccounts.controlAccounts = 'BSL' OR chartofaccounts.controlAccounts = 'BSE'),$currencyColumn * - 1,$currencyColumn)), 0) ), 0 )";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode . '-' . $LYear3;
                }


                if ($val->shortCode == 'BCM') {

                    if ($changeSelect) {
                        $columnArray[$val->shortCode] = "IFNULL(bAmountMonth, 0)";
                    } else {
                        $columnArray[$val->shortCode] = "0";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }

                if ($val->shortCode == 'BYTD') {
                    if ($changeSelect) {
                        $columnArray[$val->shortCode] = "IFNULL(bAmountYear, 0)";
                    } else {
                        $columnArray[$val->shortCode] = "0";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }

                if ($val->shortCode == 'BYTDFULL') {
                    if ($changeSelect) {
                        $columnArray[$val->shortCode] = "IFNULL(bAmountYearFull, 0)";
                    } else {
                        $columnArray[$val->shortCode] = "0";
                    }
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }

                if (($val->shortCode == 'CMB') || ($val->shortCode == 'ELMN') || ($val->shortCode == 'CONS')) {
                    $columnArray[$val->shortCode] = "0";
                    $columnHeaderArray[$val->shortCode] = $val->shortCode;
                }
            }
        }


        // formatting queries
        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    if ($val->formula == null) {
                        $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = 0;
                    } else {
                        // if column has a formula value decoding process is done here
                        $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, [], $columnArray, false, 1);
                    }
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD' || $val->shortCode == 'CY-2' || $val->shortCode == 'CY-3' || $val->shortCode == 'CY-4' || $val->shortCode == 'CMB') {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                } else {
                    $linkedcolumnArray2[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                }
            }
        }


        // formatting queries
        if (count($linkedcolumnArray2)) {
            foreach ($linkedcolumnArray2 as $key => $val) {
                if ($key == 'FCA') {
                    $linkedcolumnArrayFinal2[$key] = '(' . $val . ') as ' . '`' . $key . '`';
                } else if ($key == 'FCP') {
                    $linkedcolumnArrayFinal2[$key] = 'ROUND(' . $val . ') as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal2[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal2);

        $budgetQuery = "chartOfAccountID,
                        erp_budjetdetails.companySystemID,
                        erp_budjetdetails.serviceLineSystemID,
                        IFNULL(
                            SUM(
                                IF(
                                    erp_budjetdetails.Year = '" . $currentYear . "' && erp_budjetdetails.month <= '" . $currentYearCurrentMonthOnly . "',
                                    $budgetColumn, 
                                    0
                                )
                            ),
                            0
                        ) AS `bAmountYear`,
                        IFNULL(
                            SUM(
                                IF(
                                    erp_budjetdetails.Year = '" . $currentYear . "' && erp_budjetdetails.month = '" . $currentYearCurrentMonthOnly . "',
                                    $budgetColumn, 
                                    0
                                )
                            ),
                            0
                        ) AS `bAmountMonth`,
                        IFNULL(
                            SUM(
                                IF(
                                    erp_budjetdetails.Year = '" . $currentYear . "',
                                    $budgetColumn, 
                                    0
                                )
                            ),
                            0
                        ) AS `bAmountYearFull`";



        $budgetWhereQuery = " AND erp_budjetdetails.Year = " . $currentYear . " GROUP BY erp_budjetdetails.`chartOfAccountID`";

        if ($columnTemplateID == 1) {
            $budgetWhereQuery .= ', erp_budjetdetails.companySystemID';
        } else if ($columnTemplateID == 2) {
            $budgetWhereQuery .= ', erp_budjetdetails.serviceLineSystemID';
        }

        //get linked row sum amount to the formula
        $detTotCollect = collect($this->getCustomizeFinancialDetailTOTQry($request, $linkedcolumnQry2, $financeYear, $period, $linkedcolumnArray2, $budgetQuery, $budgetWhereQuery, $changeSelect, $cominedColumnKey));

        // formatting queries
        if (count($linkedColumn) > 0) {
            foreach ($linkedColumn as $val) {
                if ($val->shortCode == 'FCA' || $val->shortCode == 'FCP') {
                    if ($val->formula == null) {
                        $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = 0;
                        $columnHeader[] = ['description' => $val->description, 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $val->description, 'width' => $val->width];
                        $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $val->description;
                        $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 0;
                    } else {
                        $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true, 1);
                        $columnHeader[] = ['description' => $val->description, 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $val->description, 'width' => $val->width];
                        $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $val->description;
                        $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = $this->columnFormulaDecode($val->columnLinkID, $detTotCollect, $columnArray, true, 2);
                    }
                } else if ($val->shortCode == 'CYYTD' || $val->shortCode == 'LYYTD' || $val->shortCode == 'LYYTDFULL' || $val->shortCode == 'CY-2' || $val->shortCode == 'CY-3' || $val->shortCode == 'CY-4' || $val->shortCode == 'CMB') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode], 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                } else if ($val->shortCode == 'BYTD' || $val->shortCode == 'BCM' || $val->shortCode == 'BYTDFULL' ) {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode], 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                }  else if ($val->shortCode == 'ELMN' || $val->shortCode == 'CONS') {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => $columnHeaderArray[$val->shortCode], 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => $columnHeaderArray[$val->shortCode], 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = $columnHeaderArray[$val->shortCode];
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                } else {
                    $linkedcolumnArray[$val->shortCode . '-' . $val->columnLinkID] = $columnArray[$val->shortCode];
                    $columnHeader[] = ['description' => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'bgColor' => $val->bgColor, $val->shortCode . '-' . $val->columnLinkID => Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M'), 'width' => $val->width];
                    $columnHeaderMapping[$val->shortCode . '-' . $val->columnLinkID] = Carbon::parse($columnHeaderArray[$val->shortCode])->format('Y-M');
                    $linkedcolumnArray3[$val->shortCode . '-' . $val->columnLinkID] = 'IFNULL(SUM(`' . $val->shortCode . '-' . $val->columnLinkID . '`),0)';
                }
            }
        }

        $columnKeys = collect($linkedcolumnArray)->keys()->all();
        // formatting queries
        if (count($linkedcolumnArray)) {
            foreach ($linkedcolumnArray as $key => $val) {
                $explodedKey = explode('-', $key);
                if ($explodedKey[0] == 'FCA') {
                    $linkedcolumnArrayFinal[$key] = 'IFNULL(' . $val . ',0) as ' . '`' . $key . '`';
                } else if ($explodedKey[0] == 'FCP') {
                    $linkedcolumnArrayFinal[$key] = 'ROUND(IFNULL(' . $val . ',0)) as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }
        // formatting queries
        if (count($linkedcolumnArray3)) {
            foreach ($linkedcolumnArray3 as $key => $val) {
                $explodedKey = explode('-', $key);
                if ($explodedKey[0] == 'FCA') {
                    $linkedcolumnArrayFinal3[$key] = 'IFNULL(' . $val . ',0) as ' . '`' . $key . '`';
                } else if ($explodedKey[0] == 'FCP') {
                    $linkedcolumnArrayFinal3[$key] = 'ROUND(IFNULL(' . $val . ',0)) as ' . '`' . $key . '`';
                } else {
                    $linkedcolumnArrayFinal3[$key] = $val . ' as ' . '`' . $key . '`';
                }
            }
        }

        // final select statements
        $linkedcolumnQry = implode(',', $linkedcolumnArrayFinal);
        $linkedcolumnQry2 = implode(',', $linkedcolumnArrayFinal3);

        return [
            'linkedcolumnQry' => $linkedcolumnQry,
            'linkedcolumnQry2' => $linkedcolumnQry2,
            'columnArray' => $columnArray,
            'columnKeys' => $columnKeys,
            'columnHeader' => $columnHeader,
            'columnHeaderMapping' => $columnHeaderMapping,
            'budgetQuery' => $budgetQuery,
            'budgetWhereQuery' => $budgetWhereQuery,
            'cominedColumnKey' => $cominedColumnKey,
            'CONSColumnKey' => $CONSColumnKey,
            'columnTemplateID' => $columnTemplateID,
            'CYYTDColumnKey' => $CYYTDColumnKey,
            'currencyColumn' => $currencyColumn
        ];
    }

    public function getTBUnmatchedData(Request $request)
    {
        $input = $request->all();
        $toDate = new Carbon($input['toDate']);
        $toDate = $toDate->format('Y-m-d');

        $unmatchedData = GeneralLedger::selectRaw('documentCode, round( sum( erp_generalledger.documentLocalAmount ), 3 ), round( sum( erp_generalledger.documentRptAmount ), 2 ), documentSystemCode, documentSystemID')
            ->where('companySystemID', $input['companySystemID'])
            ->whereDate('documentDate', '<=', $toDate)
            ->whereHas('charofaccount')
            ->havingRaw('round( sum( erp_generalledger.documentRptAmount ), 2 ) != 0 OR round( sum( erp_generalledger.documentLocalAmount ), 3 ) != 0')
            ->groupBy('companySystemID', 'documentSystemCode', 'documentSystemID')
            ->get();

        $unmatchedData1 = GeneralLedger::selectRaw('documentCode, round( sum( erp_generalledger.documentLocalAmount ), 3 ), round( sum( erp_generalledger.documentRptAmount ), 2 ), documentSystemCode, documentSystemID')
            ->where('companySystemID', $input['companySystemID'])
            ->whereDate('documentDate', '<=', $toDate)
            ->whereDoesntHave('charofaccount')
            ->havingRaw('round( sum( erp_generalledger.documentRptAmount ), 2 ) != 0 OR round( sum( erp_generalledger.documentLocalAmount ), 3 ) != 0')
            ->groupBy('companySystemID', 'documentSystemCode', 'documentSystemID')
            ->get();


        $unmatchedData2 = GeneralLedger::selectRaw('documentCode, round( sum( erp_generalledger.documentLocalAmount ), 3 ), round( sum( erp_generalledger.documentRptAmount ), 2 ), documentSystemCode, documentSystemID')
            ->where('companySystemID', $input['companySystemID'])
            ->whereDate('documentDate', '<=', $toDate)
            ->whereDoesntHave('charofaccount')
            ->groupBy('companySystemID', 'documentSystemCode', 'documentSystemID')
            ->havingRaw('round( sum( erp_generalledger.documentRptAmount ), 2 ) = 0 AND round( sum( erp_generalledger.documentLocalAmount ), 3 ) = 0')
            ->get();


        $meregedResultOne = collect($unmatchedData)->merge(collect($unmatchedData2));

        $meregedResultTwo = collect($meregedResultOne)->merge(collect($unmatchedData1));

        $respondData = [
            'unMatchedData' => collect($meregedResultTwo)->unique('documentCode')->values()->all()
        ];

        return $this->sendResponse($respondData, "Unmatched data retrived successfully.");
    }

    public function getICFilterFormData(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);
        if ($isGroup) {
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $companiesByGroup = (array)$selectedCompanyId;
        }

        $companies = Company::whereIN('companySystemID', $companiesByGroup)->where('isGroup', 0)->get();

        $years = CompanyFinanceYear::select(DB::raw("YEAR(bigginingDate) as year"))
            ->whereIn('companySystemID', $companiesByGroup)
            ->groupBy('year')
            ->orderBy('year', 'DESC')
            ->get();

        $expenseClaimTypes = ExpenseClaimType::all();

        $months = Months::all();

        $output = array(
            'years' => $years,
            'months' => $months,
            'companies' => $companies,
            'expenseClaimTypes' => $expenseClaimTypes
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function validateICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;
        switch ($reportTypeID) {

            case 'ICR':
            case 'ICST':
            case 'ICAT':
                $validator = \Validator::make($request->all(), [
                    'reportID' => 'required',
                    'companies' => 'required|array',
                    'months' => 'required|array',
                    'years' => 'required|array'
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                break;
            case 'ICFT':
                $validator = \Validator::make($request->all(), [
                    'reportID' => 'required',
                    'companies' => 'required|array',
                    'transferType' => 'required|array',
                    'fromDate' => 'required_if:dateType,1|nullable|date',
                    'toDate' => 'required_if:dateType,1|nullable|date|after_or_equal:fromDate',
                ]);
                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function generateICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;
        $company = Company::find($request->companySystemID);

        $output = $this->getICReportQuery($request);

        $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
        $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

        $total = array();

        switch ($reportTypeID) {
            case 'ICR': // Inter Company Report

                $total['poTotalComRptCurrency'] = array_sum(collect($output)->pluck('poTotalComRptCurrency')->toArray());
                $total['GRVAmount'] = array_sum(collect($output)->pluck('GRVAmount')->toArray());
                $total['APAmount'] = array_sum(collect($output)->pluck('APAmount')->toArray());
                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['receiveAmountRpt'] = array_sum(collect($output)->pluck('receiveAmountRpt')->toArray());

            case 'ICST':

                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['supplierAmountRpt'] = array_sum(collect($output)->pluck('supplierAmountRpt')->toArray());

            case 'ICAT':

                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());
                $total['GrvAmount'] = array_sum(collect($output)->pluck('GrvAmount')->toArray());

                break;

            case 'ICFT':

                $total['payAmountCompRpt'] = array_sum(collect($output)->pluck('payAmountCompRpt')->toArray());
                $total['companyRptAmount'] = array_sum(collect($output)->pluck('companyRptAmount')->toArray());

                break;
            default:
                return $this->sendError('No report ID found');
        }

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('total', $total)
            ->with('companyName', $company->CompanyName)
            ->with('decimalPlaceRpt', $decimalPlaceRpt)
            ->with('currencyCodeRpt', $currencyCodeRpt)
            ->addIndexColumn()
            ->make(true);
    }

    public function getICReportQuery($request)
    {
        $reportTypeID = $request->reportTypeID;

        $where = '';
        $companyIDs = [];
        $yearIDs = [];
        $monthsIDs = [];
        $transferTypes = [];
        $fromDate = null;
        $toDate = null;
        if (isset($request->companies)) {
            $companies = (array)$request->companies;
            $companyIDs = array_filter(collect($companies)->pluck('companySystemID')->toArray());
        }

        if (isset($request->years)) {
            $years = (array)$request->years;
            $yearIDs = array_filter(collect($years)->pluck('year')->toArray());
        }

        if (isset($request->months)) {
            $months = (array)$request->months;
            $monthsIDs = array_filter(collect($months)->pluck('monthID')->toArray());
        }

        if (isset($request->transferType)) {
            $transferType = (array)$request->transferType;
            $transferTypes = array_filter(collect($transferType)->pluck('expenseClaimTypeID')->toArray());
        }

        if (isset($request->fromDate)) {
            $fromDate = new Carbon($request->fromDate);
            $fromDate = $fromDate->format('Y-m-d');
        }

        if (isset($request->toDate)) {
            $toDate = new Carbon($request->toDate);
            $toDate = $toDate->format('Y-m-d');
        }

        switch ($reportTypeID) {

            case 'ICR':

                if (count($companyIDs)) {
                    $where .= 'AND erp_purchaseordermaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(det.grvDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(det.grvDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = 'SELECT
                        erp_purchaseordermaster.purchaseOrderID,
                        erp_purchaseordermaster.companyID,
                        erp_purchaseordermaster.companySystemID,
                        purchaseOrderCode AS POCode,
                        DATE(
                            erp_purchaseordermaster.approvedDate
                        ) AS PODate,
                        sum(IFNULL(poTotalComRptCurrency,0)) AS poTotalComRptCurrency,
                         IFNULL(det.netAmount,0) AS GRVAmount,
                        IFNULL(bsi.totRptAmount,0) AS APAmount,
                        suppliermaster.supplierName,
                        IFNULL(det.bookingAmountRpt,0) AS bookingAmountRpt,
                        IFNULL(det.receiveAmountRpt,0) AS receiveAmountRpt,
                    det.bookingInvCodeSystem as bookinkcode,
                    det.grvDate as grvDate,
                    erp_purchaseordermaster.companyReportingCurrencyID
                    FROM
                        erp_purchaseordermaster
                    LEFT JOIN (
                        SELECT
                            SUM(netAmount) AS netAmount,
                            purchaseOrderMastertID,
                            bookingAmountRpt,
                            custInvoiceDirectAutoID,
                            receiveAmountRpt,
                    bookingInvCodeSystem,
                    grvDate
                        FROM
                            (
                                SELECT
                                    *
                                FROM
                                    (
                                        SELECT
                                            purchaseOrderMastertID,
                                            erp_grvdetails.grvAutoID,
                                            SUM(
                                                netAmount / erp_grvdetails.companyReportingER
                                            ) AS netAmount,
                                            erp_grvdetails.companyReportingER,
                                            erp_grvmaster.grvDate
                                        FROM
                                            erp_grvdetails
                                        LEFT JOIN erp_grvmaster ON erp_grvdetails.grvAutoID = erp_grvmaster.grvAutoID
                                        GROUP BY
                                            erp_grvdetails.purchaseOrderMastertID,
                                            erp_grvdetails.grvAutoID
                                    ) grvID
                                LEFT JOIN (
                                    SELECT
                                        SUM(
                                            erp_custinvoicedirect.bookingAmountRpt
                                        ) AS bookingAmountRpt,
                                        custInvoiceDirectAutoID,
                                        customerGRVAutoID,
                                        SUM(receipt.receiveAmountRpt) AS receiveAmountRpt,
                                        GROUP_CONCAT(receipt.bookingInvCodeSystem) as bookingInvCodeSystem
                                    FROM
                                        erp_custinvoicedirect
                                    LEFT JOIN (
                                        SELECT
                                            erp_custreceivepaymentdet.bookingInvCodeSystem,
                                            SUM(
                                                erp_custreceivepaymentdet.receiveAmountRpt
                                            ) AS receiveAmountRpt
                    
                                        FROM
                                            erp_custreceivepaymentdet
                                        LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                        WHERE
                                            (
                                                (
                                                    (
                                                        erp_customerreceivepayment.confirmedYN
                                                    ) = 1
                                                )
                                                AND (
                                                    (
                                                        erp_customerreceivepayment.approved
                                                    ) =- 1
                                                )
                                                AND (
                                                    erp_custreceivepaymentdet.addedDocumentID = "INV"
                                                )
                                            )
                                        GROUP BY
                                            bookingInvCodeSystem
                                    ) receipt ON receipt.bookingInvCodeSystem = erp_custinvoicedirect.custInvoiceDirectAutoID
                                    GROUP BY
                                        erp_custinvoicedirect.customerGRVAutoID
                                ) cgrv ON grvID.grvAutoID = cgrv.customerGRVAutoID
                            ) ful
                        GROUP BY
                            purchaseOrderMastertID
                    ) det ON (
                        det.purchaseOrderMastertID = erp_purchaseordermaster.purchaseOrderID
                    )
                    LEFT JOIN (
                        SELECT
                            purchaseOrderID,
                            bookingSuppMasInvAutoID,
                            SUM(totRptAmount) AS totRptAmount
                        FROM
                            erp_bookinvsuppdet
                        GROUP BY
                            erp_bookinvsuppdet.purchaseOrderID
                    ) bsi ON (
                        bsi.purchaseOrderID = erp_purchaseordermaster.purchaseOrderID
                    )
                    INNER JOIN supplierassigned ON erp_purchaseordermaster.supplierID = supplierassigned.supplierCodeSytem
                    AND erp_purchaseordermaster.companySystemID = supplierassigned.companySystemID
                    LEFT JOIN suppliermaster ON erp_purchaseordermaster.supplierID = suppliermaster.supplierCodeSystem
                    WHERE 
                        erp_purchaseordermaster.approved = - 1
                    AND supplierassigned.liabilityAccount = 9999963
                    ' . $where . '
                    GROUP BY
                        erp_purchaseordermaster.purchaseOrderID
                    ORDER BY erp_purchaseordermaster.companyID,erp_purchaseordermaster.purchaseOrderID ASC';
                break;

            case 'ICST':

                if (count($companyIDs)) {
                    $where .= 'AND erp_stocktransfer.companyFromSystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(erp_stocktransfer.tranferDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(erp_stocktransfer.tranferDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = "SELECT
                                erp_stocktransfer.stockTransferAutoID,
                                erp_stocktransfer.companyFrom,
                                erp_stocktransfer.companyTo,
                                erp_stocktransfer.stockTransferCode,
                                DATE( erp_stocktransfer.tranferDate ) AS tranferDate,
                                erp_custinvoicedirect.bookingInvCode,
                                DATE( erp_custinvoicedirect.bookingDate ) AS bookingDate,
                                erp_custinvoicedirect.bookingAmountRpt,
                                str.stockReceiveAutoID,
                                str.bookingSuppMasInvAutoID,
                                str.stockReceiveCode,
                                str.receivedDate,
                                str.Approved,
                                str.bookingInvCode AS supplierInvCode,
                                DATE( str.bookingDate ) AS supplierInvDate,
                                str.bookingAmountRpt AS supplierAmountRpt,
                                erp_custinvoicedirect.companyReportingCurrencyID,
                                erp_custinvoicedirectdet.glCode as invGLCode,
                                erp_custinvoicedirectdet.glCodeDes as invGLCodeDes,
                                invService.ServiceLineDes as invDepartment,
                                str.ServiceLineDes as strDepartment,
                                str.AccountCode as strGLCode,
                                str.AccountDescription as strGLCodeDes
                            FROM
                                ( erp_stocktransfer 
                                LEFT JOIN erp_custinvoicedirect ON erp_stocktransfer.stockTransferCode = erp_custinvoicedirect.customerInvoiceNo )
                                LEFT JOIN
                                erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID
                                LEFT JOIN
                                serviceline invService ON erp_custinvoicedirect.serviceLineSystemID = invService.serviceLineSystemID
                                LEFT JOIN (
                            SELECT
                                erp_stockreceive.stockReceiveAutoID,
                                erp_bookinvsuppmaster.bookingSuppMasInvAutoID,
                                erp_stockreceive.stockReceiveCode,
                                erp_stockreceive.receivedDate,
                            IF
                                ( erp_stockreceive.approved =- 1, 'Yes', 'No' ) AS Approved,
                                erp_bookinvsuppmaster.bookingInvCode,
                                erp_bookinvsuppmaster.bookingDate,
                                erp_stockreceive.refNo,
                                erp_bookinvsuppmaster.supplierInvoiceNo,
                                erp_bookinvsuppmaster.bookingAmountRpt,
                                erp_bookinvsuppmaster.companyReportingCurrencyID as companyReportingCurrencyID,
                                serviceline.ServiceLineDes,
                                chartofaccounts.AccountCode as AccountCode,
                                chartofaccounts.AccountDescription as AccountDescription
                            FROM
                                erp_stockreceive
                                LEFT JOIN 
                                erp_bookinvsuppmaster ON erp_stockreceive.refNo = erp_bookinvsuppmaster.supplierInvoiceNo
                                LEFT JOIN
                                erp_stockreceivedetails ON erp_stockreceive.stockReceiveAutoID = erp_stockreceivedetails.stockReceiveAutoID
                                LEFT JOIN
                                chartofaccounts ON erp_stockreceivedetails.financeGLcodebBSSystemID = chartofaccounts.chartOfAccountSystemID
                                LEFT JOIN
                                serviceline ON erp_stockreceive.serviceLineSystemID = serviceline.serviceLineSystemID 
                            WHERE
                                 erp_stockreceive.interCompanyTransferYN =- 1   
                                ) AS str ON erp_custinvoicedirect.bookingInvCode = str.refNo 
                            WHERE erp_stocktransfer.interCompanyTransferYN  =- 1  AND erp_stocktransfer.approved =- 1 " . $where . "
                            GROUP BY erp_stocktransfer.stockTransferAutoID,str.stockReceiveAutoID,str.bookingSuppMasInvAutoID
                            ORDER BY erp_stocktransfer.companyFrom,stockTransferCode ASC";
                break;

            case 'ICAT':

                if (count($companyIDs)) {
                    $where .= 'AND erp_fa_asset_disposalmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($yearIDs)) {
                    $where .= 'AND YEAR(erp_fa_asset_disposalmaster.disposalDocumentDate) IN (' . join(',', $yearIDs) . ')';
                }
                if (count($monthsIDs)) {
                    $where .= 'AND MONTH(erp_fa_asset_disposalmaster.disposalDocumentDate) IN (' . join(',', $monthsIDs) . ')';
                }

                $query = "SELECT
                            erp_fa_asset_disposalmaster.assetdisposalMasterAutoID,
                            erp_fa_asset_disposalmaster.companyID AS Company,
                            erp_fa_asset_disposalmaster.toCompanyID AS toCompanyID,
                            erp_fa_asset_disposalmaster.disposalDocumentCode,
                            DATE(erp_fa_asset_disposalmaster.disposalDocumentDate) as disposalDocumentDate,
                            erp_custinvoicedirect.bookingInvCode,
                            DATE(erp_custinvoicedirect.bookingDate) as bookingDate,
                            erp_custinvoicedirect.bookingAmountRpt,
                            erp_custinvoicedirect.companyReportingCurrencyID,
                            erp_grvmaster.grvPrimaryCode,
                            DATE(erp_grvmaster.grvDate) AS grvDate,
                            erp_grvmaster.interCompanyTransferYN,
                            IF (
                                erp_grvmaster.approved =- 1,
                                'Yes',
                                'No'
                            ) AS Approved,
                            dtl.GrvAmount
                        FROM
                            erp_fa_asset_disposalmaster
                            INNER JOIN erp_custinvoicedirect
                            ON erp_fa_asset_disposalmaster.disposalDocumentCode = erp_custinvoicedirect.customerInvoiceNo
                            INNER JOIN erp_grvmaster
                            ON erp_custinvoicedirect.bookingInvCode = erp_grvmaster.grvDoRefNo
                            LEFT JOIN (
                            select
                                grvAutoID,
                                SUM(GRVcostPerUnitComRptCur*noQty) as GrvAmount
                            FROM
                                erp_grvdetails
                                GROUP BY grvAutoID
                        ) AS dtl ON erp_grvmaster.grvAutoID = dtl.grvAutoID
                        WHERE 
                            erp_fa_asset_disposalmaster.disposalType=1 AND erp_grvmaster.interCompanyTransferYN=-1 " . $where;
                break;

            case 'ICFT':
                $whereCompany2 = '';
                $whereDate = '';
                if (count($companyIDs)) {
                    $where .= 'AND erp_paysupplierinvoicemaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                    $whereCompany2 .= 'AND erp_jvmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
                }
                if (count($transferTypes)) {
                    $where .= 'AND erp_paysupplierinvoicemaster.expenseClaimOrPettyCash IN (' . join(',', $transferTypes) . ')';
                }
                if ($fromDate != null && $toDate != null) {
                    $where .= ' AND DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
                    $whereDate .= 'DATE(erp_jvmaster.JVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '"';
                }

                $query = "SELECT
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.companyID,
                                erp_paysupplierinvoicemaster.companySystemID as companySystemID,
                                erp_expenseclaimtype.expenseClaimTypeDescription,
                                erp_paysupplierinvoicemaster.BPVcode,
                                DATE(erp_paysupplierinvoicemaster.BPVdate) as BPVdate,
                                erp_paysupplierinvoicemaster.payAmountCompRpt,
                                erp_customerreceivepayment.companyID as companyIdTo,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                DATE(erp_customerreceivepayment.custPaymentReceiveDate) as custPaymentReceiveDate,
                                erp_customerreceivepayment.companyRptAmount,
                                IF (
                                    erp_customerreceivepayment.approved =- 1,
                                    'Yes',
                                    'No'
                                ) AS Approved,
                                erp_paysupplierinvoicemaster.companyRptCurrencyID as companyRptCurrencyID 
                            FROM
                                (
                                    erp_paysupplierinvoicemaster
                                    LEFT JOIN erp_expenseclaimtype ON erp_paysupplierinvoicemaster.expenseClaimOrPettyCash = erp_expenseclaimtype.expenseClaimTypeID
                                )
                            INNER JOIN erp_customerreceivepayment ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_customerreceivepayment.intercompanyPaymentID
                            WHERE
                            erp_paysupplierinvoicemaster.approved =- 1 " . $where . "
                            UNION
                            
                            SELECT
                                erp_jvmaster.jvMasterAutoId as PayMasterAutoId,
                                erp_jvmaster.companyID as companyID,
                                erp_jvmaster.companySystemID as companySystemID,
                                erp_jvmaster.JVNarration as expenseClaimTypeDescription,
                                erp_jvmaster.JVcode as BPVcode,
                                DATE(erp_jvmaster.JVdate) as BPVdate,
                                '0' AS payAmountCompRpt,
                                CONCAT(erp_jvdetail.glAccountDescription,' ',erp_jvdetail.glAccount) as companyIdTo,
                                '-' as custPaymentReceiveCode,
                                '-' as custPaymentReceiveDate,
                                if(Sum(erp_jvdetail.debitAmount) > 0,Sum(erp_jvdetail.debitAmount),Sum(erp_jvdetail.creditAmount)*-1) AS companyRptAmount,
                                IF (
                                    erp_jvmaster.approved =- 1,
                                    'Yes',
                                    'No'
                                ) AS Approved,
                                erp_jvmaster.rptCurrencyID as companyRptCurrencyID 
                            FROM ((companymaster
                                    INNER JOIN erp_jvmaster
                                    ON companymaster.companySystemID = erp_jvmaster.companySystemID)
                                    INNER JOIN erp_jvdetail
                                    ON erp_jvmaster.jvMasterAutoId = erp_jvdetail.jvMasterAutoId)
                                    INNER JOIN chartofaccounts
                                    ON erp_jvdetail.chartOfAccountSystemID = chartofaccounts.chartOfAccountSystemID
                            GROUP BY
                                erp_jvmaster.companySystemID,
                                erp_jvmaster.JVcode,
                                erp_jvmaster.JVNarration,
                                erp_jvmaster.JVdate,
                                erp_jvdetail.glAccount,
                                erp_jvdetail.glAccountDescription,
                                erp_jvmaster.confirmedByEmpID,
                                erp_jvmaster.confirmedByName,
                                companymaster.masterComapanyIDReporting,
                                erp_jvmaster.approved,
                                chartofaccounts.relatedPartyYN
                            
                            HAVING (" . $whereDate . ")
                                AND erp_jvmaster.approved = - 1
                                AND chartofaccounts.relatedPartyYN = 1 " . $whereCompany2;
                break;
            default:
                return '';
        }


        return  \DB::select($query);
    }

    public function getICReportDumpQuery($request)
    {
        $where = '';
        $companies = (array)$request->companies;
        $companyIDs = array_filter(collect($companies)->pluck('companySystemID')->toArray());
        if (count($companyIDs)) {
            $where .= 'AND erp_grvmaster.companySystemID IN (' . join(',', $companyIDs) . ')';
        }

        $years = (array)$request->years;
        $yearIDs = array_filter(collect($years)->pluck('year')->toArray());
        if (count($yearIDs)) {
            $where .= 'AND YEAR(grvDate) IN (' . join(',', $yearIDs) . ')';
        }

        $months = (array)$request->months;
        $monthsIDs = array_filter(collect($months)->pluck('monthID')->toArray());
        if (count($monthsIDs)) {
            $where .= 'AND MONTH(grvDate) IN (' . join(',', $monthsIDs) . ')';
        }

        $query = 'SELECT
                    det.grvDetailsID,
                    erp_grvmaster.companyID AS grvCompany,
                    erp_grvmaster.grvPrimaryCode AS grvCode,
                    det.GRVAmount AS GRVAmount,
                    DATE(erp_grvmaster.grvDate) AS grvDate,
                    erp_custinvoicedirect.companyID AS customer,
                    erp_custinvoicedirect.bookingInvCode,
                    DATE(erp_custinvoicedirect.bookingDate) AS BookingDate,
                    erp_custinvoicedirect.bookingAmountRpt,
                    erp_custinvoicedirectdet.glCode as invGLCode,
                    erp_custinvoicedirectdet.glCodeDes as invGLCodeDes,
                    invService.ServiceLineDes as invDepartment,
                    det.AccountCode as grvGLCode,
                    det.AccountDescription as grvGLCodeDes,
                    grvService.ServiceLineDes as grvDepartment
                FROM
                    erp_grvmaster
                    LEFT JOIN
                        (SELECT
                            grvAutoID,
                                grvDetailsID,
                                purchaseOrderMastertID,
                                companyID,
                                SUM((erp_grvdetails.netAmount / erp_grvdetails.companyReportingER)) AS GRVAmount,
                                chartofaccounts.AccountCode as AccountCode,
                                chartofaccounts.AccountDescription as AccountDescription
                        FROM
                            erp_grvdetails
                        LEFT JOIN
                        chartofaccounts ON erp_grvdetails.financeGLcodePLSystemID = chartofaccounts.chartOfAccountSystemID
                        GROUP BY erp_grvdetails.grvAutoID,erp_grvdetails.financeGLcodePLSystemID) det ON det.grvAutoID = erp_grvmaster.grvAutoID
                    LEFT JOIN
                        erp_custinvoicedirect ON erp_grvmaster.grvAutoID = erp_custinvoicedirect.customerGRVAutoID
                    LEFT JOIN
                        erp_custinvoicedirectdet ON erp_custinvoicedirect.custInvoiceDirectAutoID = erp_custinvoicedirectdet.custInvoiceDirectID 
                    LEFT JOIN
                        serviceline invService ON erp_custinvoicedirectdet.serviceLineSystemID = invService.serviceLineSystemID
                    LEFT JOIN
                        serviceline grvService ON erp_grvmaster.serviceLineSystemID = grvService.serviceLineSystemID 
                    INNER JOIN
                        supplierassigned ON erp_grvmaster.supplierID = supplierassigned.supplierCodeSytem
                        AND erp_grvmaster.companySystemID = supplierassigned.companySystemID
                    WHERE  supplierassigned.liabilityAccount = 9999963 ' . $where . ' GROUP BY erp_grvmaster.grvAutoID,erp_custinvoicedirect.custInvoiceDirectAutoID,erp_custinvoicedirectdet.glCode
                    ORDER BY erp_grvmaster.grvAutoID DESC';

        return  \DB::select($query);
    }

    public function exportICReport(Request $request)
    {
        $reportTypeID = $request->reportTypeID;

        $output = $this->getICReportQuery($request);

        $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

        $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;

        $type = $request->type;

        switch ($reportTypeID) {

            case 'ICR':

                $reportSD = $request->reportSD;


                if ($reportSD == "report") {

                    if (!empty($output)) {
                        $x = 0;

                        $subTotalPOAmount = 0;
                        $subTotalGRVAmount = 0;
                        $subTotalAPInvoiceAmount = 0;
                        $subTotalARInvoiceAmount = 0;
                        $subTotalAdjAmount = 0;

                        foreach ($output as $val) {
                            $x++;

                            $data[$x]['Company ID'] = $val->companyID;
                            $data[$x]['PO Code'] = $val->companyID;
                            $data[$x]['PO Date'] = Helper::dateFormat($val->PODate);
                            $data[$x]['Supplier Name'] = $val->supplierName;
                            $data[$x]['PO Amount'] = round($val->poTotalComRptCurrency, $decimalPlaceRpt);
                            $data[$x]['GRV Amount'] = round($val->GRVAmount, $decimalPlaceRpt);
                            $data[$x]['AP Invoice Amount'] = round($val->APAmount, $decimalPlaceRpt);
                            $data[$x]['AR Invoice Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                            $data[$x]['Receipts/Adjustments'] = round($val->receiveAmountRpt, $decimalPlaceRpt);

                            $subTotalPOAmount += $val->poTotalComRptCurrency;
                            $subTotalGRVAmount += $val->GRVAmount;
                            $subTotalAPInvoiceAmount += $val->APAmount;
                            $subTotalARInvoiceAmount += $val->bookingAmountRpt;
                            $subTotalAdjAmount += $val->receiveAmountRpt;
                        }

                        $x++;
                        $data[$x]['Company ID'] = '';
                        $data[$x]['PO Code'] = '';
                        $data[$x]['PO Date'] = '';
                        $data[$x]['Supplier Name'] = 'Total';
                        $data[$x]['PO Amount'] = round($subTotalPOAmount, $decimalPlaceRpt);
                        $data[$x]['GRV Amount'] = round($subTotalGRVAmount, $decimalPlaceRpt);
                        $data[$x]['AP Invoice Amount'] = round($subTotalAPInvoiceAmount, $decimalPlaceRpt);
                        $data[$x]['AR Invoice Amount'] = round($subTotalARInvoiceAmount, $decimalPlaceRpt);
                        $data[$x]['Receipts/Adjustments'] = round($subTotalAdjAmount, $decimalPlaceRpt);
                    }
                } else { // DUMP

                    if ($reportSD == "dump") {

                        $output = $this->getICReportDumpQuery($request);

                        if (!empty($output)) {
                            $x = 0;

                            $subTotalGRVAmount = 0;
                            $subTotalBookingAmount = 0;

                            foreach ($output as $val) {
                                $x++;

                                $data[$x]['GRV Company'] = $val->grvCompany;
                                $data[$x]['GRV Code'] = $val->grvCode;
                                $data[$x]['GRV Amount'] = round($val->GRVAmount, $decimalPlaceRpt);
                                $data[$x]['GRV Date'] = Helper::dateFormat($val->grvDate);
                                $data[$x]['GL Code'] = $val->grvGLCode;
                                $data[$x]['GL Description'] = $val->grvGLCodeDes;
                                $data[$x]['Department'] = $val->grvDepartment;

                                $data[$x]['Customer'] = $val->customer;
                                $data[$x]['Booking Invoice Code'] = $val->bookingInvCode;
                                $data[$x]['Booking Date'] = Helper::dateFormat($val->BookingDate);
                                $data[$x]['Booking Amount Rpt'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                                $data[$x]['INV GL Code'] = $val->invGLCode;
                                $data[$x]['INV GL Description'] = $val->invGLCodeDes;
                                $data[$x]['INV Department'] = $val->invDepartment;

                                $subTotalGRVAmount += $val->GRVAmount;
                                $subTotalBookingAmount += $val->bookingAmountRpt;
                            }

                            $x++;

                            //                            $data[$x]['GRV Company'] = '';
                            //                            $data[$x]['GRV Code'] = '';
                            //                            $data[$x]['GRV Amount'] = round($subTotalGRVAmount,$decimalPlaceRpt);
                            //                            $data[$x]['GRV Date'] = '';
                            //                            $data[$x]['GL Code'] = '';
                            //                            $data[$x]['GL Description'] = '';
                            //                            $data[$x]['Department'] = '';
                            //                            $data[$x]['Customer'] = '';
                            //                            $data[$x]['Booking Invoice Code'] = '';
                            //                            $data[$x]['Booking Date'] = '';
                            //                            $data[$x]['Booking Amount Rpt'] = round($subTotalBookingAmount,$decimalPlaceRpt);
                            //                            $data[$x]['INV GL Code'] = '';
                            //                            $data[$x]['INV GL Description'] = '';
                            //                            $data[$x]['INV Department'] = '';
                        }
                    }
                }

                \Excel::create('inter_company', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;

            case 'ICST':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalBookingAmountRpt = 0;
                    $subTotalSupplierAmountRpt = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->companyFrom;
                        $data[$x]['Stock Transfer Code'] = $val->stockTransferCode;
                        $data[$x]['Stock Transfer Date'] = Helper::dateFormat($val->tranferDate);
                        $data[$x]['Customer INV Code'] = $val->bookingInvCode;
                        $data[$x]['Customer INV Date'] = Helper::dateFormat($val->bookingDate);
                        $data[$x]['Customer INV Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);

                        $data[$x]['INV GL Code'] = $val->invGLCode;
                        $data[$x]['INV GL Description'] = $val->invGLCodeDes;
                        $data[$x]['INV Department'] = $val->invDepartment;

                        $data[$x]['Company To'] = $val->companyTo;
                        $data[$x]['Stock Receive Code'] = $val->stockReceiveCode;
                        $data[$x]['Approved'] = $val->Approved;
                        $data[$x]['Supplier Invoice Code'] = $val->supplierInvCode;
                        $data[$x]['Supplier Invoice Date'] = Helper::dateFormat($val->supplierInvDate);
                        $data[$x]['Supplier Invoice Amount'] = round($val->supplierAmountRpt, $decimalPlaceRpt);

                        $data[$x]['GL Code'] = $val->strGLCode;
                        $data[$x]['GL Description'] = $val->strGLCodeDes;
                        $data[$x]['Department'] = $val->strDepartment;

                        $subTotalBookingAmountRpt += $val->bookingAmountRpt;
                        $subTotalSupplierAmountRpt += $val->supplierAmountRpt;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Stock Transfer Code'] = '';
                    $data[$x]['Stock Transfer Date'] = '';
                    $data[$x]['Customer INV Code'] = '';
                    $data[$x]['Customer INV Date'] = 'Total';
                    $data[$x]['Customer INV Amount'] = round($subTotalBookingAmountRpt, $decimalPlaceRpt);
                    $data[$x]['INV GL Code'] = '';
                    $data[$x]['INV GL Description'] = '';
                    $data[$x]['INV Department'] = '';
                    $data[$x]['Company To'] = '';
                    $data[$x]['Stock Receive Code'] = '';
                    $data[$x]['Approved'] = '';
                    $data[$x]['Supplier Invoice Code'] = '';
                    $data[$x]['Supplier Invoice Date'] = '';
                    $data[$x]['Supplier Invoice Amount'] = round($subTotalSupplierAmountRpt, $decimalPlaceRpt);
                    $data[$x]['GL Code'] = '';
                    $data[$x]['GL Description'] = '';
                    $data[$x]['Department'] = '';
                }

                \Excel::create('inter_company_stock_transfer', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;

            case 'ICAT':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalBookingAmountRpt = 0;
                    $subTotalGrvAmount = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->Company;
                        $data[$x]['Disposal Document'] = $val->disposalDocumentCode;
                        $data[$x]['Disposal Date'] = Helper::dateFormat($val->disposalDocumentDate);
                        $data[$x]['Customer INV Code'] = $val->bookingInvCode;
                        $data[$x]['Customer INV Date'] = Helper::dateFormat($val->bookingDate);
                        $data[$x]['Customer INV Amount'] = round($val->bookingAmountRpt, $decimalPlaceRpt);
                        $data[$x]['Company To'] = $val->toCompanyID;
                        $data[$x]['Direct GRV Code'] = $val->grvPrimaryCode;
                        $data[$x]['GRV Date'] = Helper::dateFormat($val->grvDate);
                        $data[$x]['Direct GRV Approved'] = $val->Approved;
                        $data[$x]['GRV Amount'] = round($val->GrvAmount, $decimalPlaceRpt);

                        $subTotalBookingAmountRpt += $val->bookingAmountRpt;
                        $subTotalGrvAmount += $val->GrvAmount;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Disposal Document'] = '';
                    $data[$x]['Disposal Date'] = '';
                    $data[$x]['Customer INV Code'] = '';
                    $data[$x]['Customer INV Date'] = 'Total';
                    $data[$x]['Customer INV Amount'] = round($subTotalBookingAmountRpt, $decimalPlaceRpt);
                    $data[$x]['Company To'] = '';
                    $data[$x]['Direct GRV Code'] = '';
                    $data[$x]['GRV Date'] = '';
                    $data[$x]['Direct GRV Approved'] = '';
                    $data[$x]['GRV Amount'] = round($subTotalGrvAmount, $decimalPlaceRpt);
                }

                \Excel::create('inter_company_Asset_transfer', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;

            case 'ICFT':

                if (!empty($output)) {
                    $x = 0;

                    $subTotalPayAmountCompRpt = 0;
                    $subTotalCompanyRptAmount = 0;

                    foreach ($output as $val) {
                        $x++;

                        $data[$x]['Company From'] = $val->companyID;
                        $data[$x]['Transfer Type'] = $val->expenseClaimTypeDescription;
                        $data[$x]['PV Code'] = $val->BPVcode;
                        $data[$x]['PV Date'] = Helper::dateFormat($val->BPVdate);
                        $data[$x]['PV Amount'] = round($val->payAmountCompRpt, $decimalPlaceRpt);
                        $data[$x]['Company To'] = $val->companyIdTo;
                        $data[$x]['Receipt Code'] = $val->custPaymentReceiveCode;
                        $data[$x]['Receipt Date'] = Helper::dateFormat($val->custPaymentReceiveDate);
                        $data[$x]['Receipt Approved'] = $val->Approved;
                        $data[$x]['Receipt Amount'] = round($val->companyRptAmount, $decimalPlaceRpt);

                        $subTotalPayAmountCompRpt += $val->payAmountCompRpt;
                        $subTotalCompanyRptAmount += $val->companyRptAmount;
                    }

                    $x++;
                    $data[$x]['Company From'] = '';
                    $data[$x]['Transfer Type'] = '';
                    $data[$x]['PV Code'] = '';
                    $data[$x]['PV Date'] = 'Total';
                    $data[$x]['PV Amount'] = round($subTotalPayAmountCompRpt, $decimalPlaceRpt);
                    $data[$x]['Company To'] = '';
                    $data[$x]['Receipt Code'] = '';
                    $data[$x]['Receipt Date'] = '';
                    $data[$x]['Receipt Approved'] = '';
                    $data[$x]['Receipt Amount'] = round($subTotalCompanyRptAmount, $decimalPlaceRpt);
                }

                \Excel::create('inter_company_Fund_transfer', function ($excel) use ($data) {
                    $excel->sheet('sheet name', function ($sheet) use ($data) {
                        $sheet->fromArray($data, null, 'A1', true);
                        $sheet->setAutoSize(true);
                        $sheet->getStyle('C1:C2')->getAlignment()->setWrapText(true);
                    });
                    $lastrow = $excel->getActiveSheet()->getHighestRow();
                    $excel->getActiveSheet()->getStyle('A1:J' . $lastrow)->getAlignment()->setWrapText(true);
                })->download($type);

                return $this->sendResponse(array(), 'successfully export');
                break;
            default:
                return $this->sendError('No report ID found');
        }
    }

    public function getICDrillDownData(Request $request)
    {

        $type = $request->type;
        $purchaseOrderID = $request->purchaseOrderID;
        $companySystemID = $request->companySystemID;

        switch ($type) {
            case 'grv':

                $query = "SELECT
                                det.grvDetailsID,
                                DATE(erp_grvmaster.grvDate) AS grvDate,
                                erp_grvmaster.grvPrimaryCode,
                                erp_grvmaster.companyReportingCurrencyID,
                                det.GRVAmount,
                             DATE(
                                    erp_custinvoicedirect.bookingDate
                                ) AS BookingDate,
                                erp_custinvoicedirect.bookingInvCode,
                                erp_custinvoicedirect.bookingAmountRpt
                            FROM
                                erp_grvmaster
                            LEFT JOIN (
                                SELECT
                                    grvAutoID,
                                    grvDetailsID,
                                    purchaseOrderMastertID,
                                    companySystemID,
                                    companyID,
                                    SUM(
                                        (
                                            erp_grvdetails.netAmount / erp_grvdetails.companyReportingER
                                        )
                                    ) AS GRVAmount
                                FROM
                                    erp_grvdetails
                                GROUP BY
                                    erp_grvdetails.grvAutoID
                            ) det ON det.grvAutoID = erp_grvmaster.grvAutoID
                            LEFT JOIN erp_custinvoicedirect ON erp_grvmaster.grvAutoID = erp_custinvoicedirect.customerGRVAutoID
                            WHERE
                                det.purchaseOrderMastertID = $purchaseOrderID
                            AND det.companySystemID = $companySystemID";

                $output = \DB::select($query);

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['GRVAmount'] = array_sum(collect($output)->pluck('GRVAmount')->toArray());
                $total['bookingAmountRpt'] = array_sum(collect($output)->pluck('bookingAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );
                break;
            case 'ap':

                $output = BookInvSuppDet::where('companySystemID', $companySystemID)
                    ->where('purchaseOrderID', $purchaseOrderID)
                    ->groupBy('bookingSuppMasInvAutoID')
                    ->select('bookingSuppMasInvAutoID', 'companyReportingCurrencyID', DB::raw('SUM(erp_bookinvsuppdet.totRptAmount) AS totRptAmount'))
                    ->whereHas('suppinvmaster')
                    ->with(['suppinvmaster' => function ($query) {
                        $query->select('bookingDate', 'bookingInvCode', 'bookingSuppMasInvAutoID');
                    }])

                    ->get();

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');
                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['totRptAmount'] = array_sum(collect($output)->pluck('totRptAmount')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );

                break;
            case 'receipt':

                $bookingInvCodeSystem = (isset($request->bookinkcode) && $request->bookinkcode > 0) ? $request->bookinkcode : 0;

                $query = "SELECT
                                erp_custreceivepaymentdet.custReceivePaymentAutoID,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                erp_custreceivepaymentdet.bookingInvCodeSystem,
                        
                            IF (
                                erp_custreceivepaymentdet.matchingDocID = 0
                                OR erp_custreceivepaymentdet.matchingDocID IS NULL,
                                erp_customerreceivepayment.custPaymentReceiveCode,
                                erp_matchdocumentmaster.matchingDocCode
                            ) AS docCode,
                        
                        DATE(IF (
                                erp_custreceivepaymentdet.matchingDocID = 0
                            OR erp_custreceivepaymentdet.matchingDocID IS NULL,
                            erp_customerreceivepayment.custPaymentReceiveDate,
                            erp_matchdocumentmaster.matchingDocdate
                        
                        )) AS docDate,
                            erp_custreceivepaymentdet.receiveAmountRpt,
                            erp_custreceivepaymentdet.companyReportingCurrencyID as companyReportingCurrencyID,
                            erp_creditnote.creditNoteCode,
                            erp_creditnote.comments
                        FROM
                            (
                                (
                                    erp_custreceivepaymentdet
                                    LEFT JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                                )
                                LEFT JOIN erp_matchdocumentmaster ON erp_custreceivepaymentdet.matchingDocID = erp_matchdocumentmaster.matchDocumentMasterAutoID
                            )
                        INNER JOIN currencymaster ON erp_custreceivepaymentdet.companyReportingCurrencyID = currencymaster.currencyID
                        LEFT JOIN erp_creditnote ON erp_creditnote.creditNoteCode = erp_matchdocumentmaster.BPVcode
                        WHERE bookingInvCodeSystem = $bookingInvCodeSystem
                        AND erp_customerreceivepayment.confirmedYN = 1
                        AND erp_customerreceivepayment.approved = -1
                        AND erp_custreceivepaymentdet.addedDocumentID = 'INV'";

                $output = \DB::select($query);

                $requestCurrencyRpt = $this->getReportingCurrencyDetail($output, 'companyReportingCurrencyID');

                $decimalPlaceRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->DecimalPlaces : 2;
                $currencyCodeRpt = !empty($requestCurrencyRpt) ? $requestCurrencyRpt->CurrencyCode : 'USD';

                $total = array();
                $total['receiveAmountRpt'] = array_sum(collect($output)->pluck('receiveAmountRpt')->toArray());

                return array(
                    'reportData' => $output,
                    'total' => $total,
                    'decimalPlaceRpt' => $decimalPlaceRpt,
                    'currencyCodeRpt' => $currencyCodeRpt,
                );
                break;
            default:
                return $this->sendError('Drill down type not found');
        }
    }

    private function getReportingCurrencyDetail($result, $column)
    {
        $currencyIdRpt = 2;

        $decimalPlaceCollectRpt = collect($result)->pluck($column)->toArray();
        $decimalPlaceUniqueRpt = array_unique($decimalPlaceCollectRpt);

        if (!empty($decimalPlaceUniqueRpt)) {
            $currencyIdRpt = $decimalPlaceUniqueRpt[0];
        }

        return CurrencyMaster::where('currencyID', $currencyIdRpt)->first();
    }

    public function exportFinanceReport(Request $request)
    {
        $reportID = $request->get('reportID');
        if(!isset($reportID) && $reportID == null)
        {
            return $this->sendError('No report ID found');
        }
        $reportData = $this->generateFRReport($request);

        $input = $this->convertArrayToSelectedValue($request->all(), array('currency'));
        if (isset($reportData['template']) && $reportData['template']['showDecimalPlaceYN']) {
            if ($input['currency'] === 1) {
                $reportData['decimalPlaces'] = $reportData['companyCurrency']['localcurrency']['DecimalPlaces'];
            } else {
                $reportData['decimalPlaces'] = $reportData['companyCurrency']['reportingcurrency']['DecimalPlaces'];
            }
        } else {
            $reportData['decimalPlaces'] = 0;
        }

        if ($input['currency'] === 1) {
            $reportData['currencyCode'] = $reportData['companyCurrency']['localcurrency']['CurrencyCode'];
        } else {
            $reportData['currencyCode'] = $reportData['companyCurrency']['reportingcurrency']['CurrencyCode'];
        }

        $reportData['accountType'] = $input['accountType'];

        if (is_array($reportData['uncategorize']) && $reportData['columnTemplateID'] == null) {
            $reportData['isUncategorize'] = false;
        } else {
            $reportData['isUncategorize'] = true;
        }

        if ($reportData['columnTemplateID'] == 1 || $reportData['columnTemplateID'] == 2) {
            $templateName = "export_report.finance_coloumn_template_one";
        } else {
            $templateName = $reportData['accountType'] == 4? "export_report.equity_finance":"export_report.finance";
        }

        $month = '';
        if ($request->dateType != 1) {
            $period = CompanyFinancePeriod::find($request->month);
            $toDate = Carbon::parse($period->dateTo)->format('Y-m-d');
            $month = Carbon::parse($toDate)->format('Y-m-d');
        }
        if($month){
            $reportData['month'] = ((new Carbon($month))->format('d/m/Y'));
        }
        $reportData['report_tittle'] = 'Finance Report';
        $reportData['from_date'] = $input['fromDate'];
        $reportData['to_date'] = $input['toDate'];

        if ($request->dateType == 1) {
            $toDate = new Carbon($input['toDate']);
            $reportData['to_date'] = $toDate->format('d/m/Y');
            $fromDate = new Carbon($input['fromDate']);
            $reportData['from_date'] = $fromDate->format('d/m/Y');
        } else {
            $period = CompanyFinancePeriod::find($request->month);
            $reportData['to_date'] = Carbon::parse($period->dateTo)->format('d/m/Y');
            $reportData['from_date'] = Carbon::parse($period->dateFrom)->format('d/m/Y');
        }

        $excelColumnFormat = ExcelColumnFormat::getExcelColumnFormat($reportData['reportData'],$request['reportID']);

        return \Excel::create('finance', function ($excel) use ($reportData, $templateName, $excelColumnFormat) {
            $excel->sheet('New sheet', function ($sheet) use ($reportData, $templateName, $excelColumnFormat) {
                $sheet->setColumnFormat($excelColumnFormat);
                $sheet->loadView($templateName, $reportData);
            });
        })->download('xlsx');
    }

    public function getOpeningBalanceData($fromDate,$typeID,$companyID,$employeeID) {

        $typeIDs = join(',', json_decode($typeID));
        $companyID = join(',', json_decode($companyID));

        $exchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($companyID, 4 , "exchange-gainloss-gl");

        return DB::select('
SELECT SUM(amountLocal) AS amountLocal,SUM(amountRpt) AS amountRpt FROM (
    SELECT
        IF(SUM(bookingAmountLocal) < 0, -SUM(bookingAmountLocal), SUM(bookingAmountLocal)) AS amountLocal,
        IF(SUM(bookingAmountRpt) < 0, -SUM(bookingAmountRpt), SUM(bookingAmountRpt)) AS amountRpt,
        4 AS type
    FROM
        erp_bookinvsuppmaster
    WHERE
        DATE(bookingDate) < "' . $fromDate . '" AND 
        approved = -1 AND
        documentType = 4 AND
        employeeID = '.$employeeID.' AND
        4 IN (' . $typeIDs . ') AND
        companySystemID IN ('.$companyID.')
    UNION ALL
    SELECT
        -SUM((payAmountCompLocal + VATAmountLocal)) AS amountLocal,
        -SUM((payAmountCompRpt + VATAmountRpt)) AS amountRpt,
        5 AS type
    FROM
        erp_paysupplierinvoicemaster
    WHERE
        invoiceType = 6 AND 
        DATE(BPVdate) < "' . $fromDate . '" AND 
        approved = -1 AND
        directPaymentPayeeEmpID = '.$employeeID.' AND
        5 IN (' . $typeIDs . ') AND
        companySystemID IN ('.$companyID.')
    UNION ALL
    SELECT
        -SUM((payAmountCompLocal + VATAmountLocal)) AS amountLocal,
        -SUM((payAmountCompRpt + VATAmountRpt)) AS amountRpt,
        6 AS type
    FROM
        erp_paysupplierinvoicemaster
    WHERE
        invoiceType = 7 AND 
        DATE(BPVdate) < "' . $fromDate . '" AND 
        approved = -1 AND
        directPaymentPayeeEmpID = '.$employeeID.' AND
        6 IN (' . $typeIDs . ') AND
        companySystemID IN ('.$companyID.')
    UNION ALL
    SELECT
        IF(SUM(expense_employee_allocation.amountLocal) < 0, -SUM(expense_employee_allocation.amountLocal), SUM(expense_employee_allocation.amountLocal)) AS amountLocal,
        IF(SUM(expense_employee_allocation.amountRpt) < 0, -SUM(expense_employee_allocation.amountRpt), SUM(expense_employee_allocation.amountRpt)) AS amountRpt,
        1 AS type
    FROM
        erp_bookinvsuppmaster
        LEFT JOIN expense_employee_allocation ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = expense_employee_allocation.documentSystemCode
    WHERE
        DATE(erp_bookinvsuppmaster.bookingDate) < "' . $fromDate . '" AND 
        erp_bookinvsuppmaster.approved = -1 AND
        erp_bookinvsuppmaster.documentType = 1 AND
        expense_employee_allocation.employeeSystemID = '.$employeeID.' AND
        expense_employee_allocation.documentSystemID = 11 AND
        1 IN (' . $typeIDs . ') AND
        erp_bookinvsuppmaster.companySystemID IN ('.$companyID.')
    UNION ALL
    SELECT
        -SUM(companyLocalAmount) AS amountLocal,
        -SUM(companyReportingAmount) AS amountRpt,
        3 AS type
    FROM
        srp_erp_iouvouchers
    WHERE
        DATE(voucherDate) < "' . $fromDate . '" AND
        3 IN (' . $typeIDs . ') AND
        empID = '.$employeeID.' AND
        companyID IN ('.$companyID.') AND
        approvedYN = 1
    UNION ALL  
    SELECT
        -SUM(netAmountLocal) AS amountLocal,
        -SUM(netAmountRpt) AS amountRpt,
        7 AS type
    FROM
        erp_debitnote
    WHERE
        DATE(debitNoteDate) < "' . $fromDate . '" AND
        7 IN (' . $typeIDs . ') AND
        empID = '.$employeeID.' AND
        companySystemID IN ('.$companyID.') AND
        approved = -1
    UNION ALL
    SELECT
        -SUM(erp_generalledger.documentLocalAmount*-1) AS amountLocal,
        -SUM(erp_generalledger.documentRptAmount*-1) AS amountRpt,
        5 AS type
    FROM
        erp_paysupplierinvoicemaster
        LEFT JOIN erp_generalledger ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_generalledger.documentSystemCode
    WHERE
        erp_paysupplierinvoicemaster.invoiceType = 6 AND 
        DATE(erp_paysupplierinvoicemaster.BPVdate) < "' . $fromDate . '" AND 
        erp_paysupplierinvoicemaster.approved = -1 AND
        erp_paysupplierinvoicemaster.directPaymentPayeeEmpID = '.$employeeID.' AND
        5 IN (' . $typeIDs . ') AND
        erp_paysupplierinvoicemaster.companySystemID IN ('.$companyID.') AND
        erp_generalledger.documentSystemID = 4 AND 
        erp_generalledger.chartOfAccountSystemID = "'.$exchangeGainLossAccount.'"
) AS t');
    }

    public function getGeneralLedgerQueryData($fromDate,$toDate,$typeID,$companyID,$employeeIDs) {
        try {
            $typeIDs = join(',', json_decode($typeID));
            $employeeIDs = join(',', json_decode($employeeIDs));
            $companyID = join(',', json_decode($companyID));

            $exchangeGainLossAccount = SystemGlCodeScenarioDetail::getGlByScenario($companyID, 4 , "exchange-gainloss-gl");

            return DB::select('SELECT * FROM ( 
                    SELECT
                        erp_bookinvsuppmaster.bookingDate AS documentDate,
                        erp_bookinvsuppmaster.bookingInvCode AS documentCode,
                        erp_bookinvsuppmaster.comments AS description,
                        erp_bookinvsuppmaster.employeeID AS employeeID,
                        erp_bookinvsuppmaster.bookingAmountLocal AS amountLocal,
                        erp_bookinvsuppmaster.bookingAmountRpt AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        4 AS type
                    FROM
                        erp_bookinvsuppmaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = srp_erp_pay_monthlydeductionmaster.supplierInvoiceID
                        LEFT JOIN currencymaster ON erp_bookinvsuppmaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_bookinvsuppmaster.companyReportingCurrencyID = currencymasterRpt.currencyID
                    WHERE
                        DATE(erp_bookinvsuppmaster.bookingDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_bookinvsuppmaster.approved = -1 AND
                        erp_bookinvsuppmaster.documentType = 4 AND
                        4 IN (' . $typeIDs . ') AND
                        erp_bookinvsuppmaster.companySystemID IN ('.$companyID.')
                        UNION ALL
                        SELECT
                        erp_paysupplierinvoicemaster.BPVdate AS documentDate,
                        erp_paysupplierinvoicemaster.BPVcode AS documentCode,
                        erp_paysupplierinvoicemaster.BPVNarration AS description,
                        erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
                        (erp_paysupplierinvoicemaster.payAmountCompLocal + erp_paysupplierinvoicemaster.VATAmountLocal) AS amountLocal,
                        (erp_paysupplierinvoicemaster.payAmountCompRpt + erp_paysupplierinvoicemaster.VATAmountRpt) AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        5 AS type
                    FROM
                        erp_paysupplierinvoicemaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = srp_erp_pay_monthlydeductionmaster.pv_id
                        LEFT JOIN currencymaster ON erp_paysupplierinvoicemaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_paysupplierinvoicemaster.companyRptCurrencyID = currencymasterRpt.currencyID
                    WHERE
                        erp_paysupplierinvoicemaster.invoiceType = 6 AND 
                        DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_paysupplierinvoicemaster.approved = -1 AND
                        5 IN (' . $typeIDs . ') AND
                        erp_paysupplierinvoicemaster.companySystemID IN ('.$companyID.')
                        UNION ALL
                        SELECT
                        erp_paysupplierinvoicemaster.BPVdate AS documentDate,
                        erp_paysupplierinvoicemaster.BPVcode AS documentCode,
                        erp_paysupplierinvoicemaster.BPVNarration AS description,
                        erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
                        (erp_paysupplierinvoicemaster.payAmountCompLocal + erp_paysupplierinvoicemaster.VATAmountLocal) AS amountLocal,
                        (erp_paysupplierinvoicemaster.payAmountCompRpt + erp_paysupplierinvoicemaster.VATAmountRpt) AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        6 AS type
                    FROM
                        erp_paysupplierinvoicemaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = srp_erp_pay_monthlydeductionmaster.pv_id
                        LEFT JOIN currencymaster ON erp_paysupplierinvoicemaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_paysupplierinvoicemaster.companyRptCurrencyID = currencymasterRpt.currencyID
                    WHERE
                        erp_paysupplierinvoicemaster.invoiceType = 7 AND 
                        DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_paysupplierinvoicemaster.approved = -1 AND
                        6 IN (' . $typeIDs . ') AND
                        erp_paysupplierinvoicemaster.companySystemID IN ('.$companyID.')
                        UNION ALL
                        SELECT
                        erp_bookinvsuppmaster.bookingDate AS documentDate,
                        erp_bookinvsuppmaster.bookingInvCode AS documentCode,
                        erp_bookinvsuppmaster.comments AS description,
                        expense_employee_allocation.employeeSystemID AS employeeID,
                        expense_employee_allocation.amountLocal AS amountLocal,
                        expense_employee_allocation.amountRpt AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        1 AS type
                    FROM
                        erp_bookinvsuppmaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = srp_erp_pay_monthlydeductionmaster.supplierInvoiceID
                        LEFT JOIN expense_employee_allocation ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = expense_employee_allocation.documentSystemCode
                        LEFT JOIN currencymaster ON erp_bookinvsuppmaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_bookinvsuppmaster.companyReportingCurrencyID = currencymasterRpt.currencyID
                    WHERE
                        DATE(erp_bookinvsuppmaster.bookingDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_bookinvsuppmaster.approved = -1 AND
                        erp_bookinvsuppmaster.documentType = 1 AND
                        expense_employee_allocation.documentSystemID = 11 AND
                        1 IN (' . $typeIDs . ') AND
                        erp_bookinvsuppmaster.companySystemID IN ('.$companyID.')
                        UNION ALL
                        SELECT
                        erp_paysupplierinvoicemaster.BPVdate AS documentDate,
                        erp_paysupplierinvoicemaster.BPVcode AS documentCode,
                        erp_paysupplierinvoicemaster.BPVNarration AS description,
                        erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
                        (erp_paysupplierinvoicemaster.payAmountCompLocal + erp_paysupplierinvoicemaster.VATAmountLocal) AS amountLocal,
                        (erp_paysupplierinvoicemaster.payAmountCompRpt + erp_paysupplierinvoicemaster.VATAmountRpt) AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        2 AS type
                    FROM
                        erp_paysupplierinvoicemaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = srp_erp_pay_monthlydeductionmaster.pv_id
                        LEFT JOIN currencymaster ON erp_paysupplierinvoicemaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_paysupplierinvoicemaster.companyRptCurrencyID = currencymasterRpt.currencyID
                    WHERE
                        erp_paysupplierinvoicemaster.invoiceType = 3 AND 
                        DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_paysupplierinvoicemaster.approved = -1 AND
                        2 IN (' . $typeIDs . ') AND
                        erp_paysupplierinvoicemaster.companySystemID IN ('.$companyID.')
                        UNION ALL 
                        SELECT
                         srp_erp_iouvouchers.voucherDate AS documentDate,
                        srp_erp_iouvouchers.iouCode AS documentCode,
                        srp_erp_iouvouchers.narration AS description,
                        srp_erp_iouvouchers.empID AS employeeID,
                        srp_erp_iouvouchers.companyLocalAmount AS amountLocal,
                        srp_erp_iouvouchers.companyReportingAmount AS amountRpt,
                        srp_erp_ioubookingmaster.bookingCode AS referenceDoc,
                        srp_erp_ioubookingmaster.bookingDate AS referenceDocDate,
                        srp_erp_ioubookingmaster.bookingMasterID AS masterID,
                        srp_erp_iouvouchers.companyLocalCurrencyDecimalPlaces AS localCurrencyDecimals,
                        srp_erp_iouvouchers.companyReportingCurrencyDecimalPlaces AS rptCurrencyDecimals,
                        3 AS type
                    FROM
                        srp_erp_iouvouchers
                        LEFT JOIN srp_erp_ioubookingmaster ON srp_erp_iouvouchers.voucherAutoID = srp_erp_ioubookingmaster.iouVoucherAutoID
                    WHERE
                        DATE(srp_erp_iouvouchers.voucherDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND
                        3 IN (' . $typeIDs . ') AND
                        srp_erp_iouvouchers.companyID IN ('.$companyID.') AND
                        srp_erp_iouvouchers.approvedYN = 1
                        UNION ALL  
                        SELECT
                        erp_debitnote.debitNoteDate AS documentDate,
                        erp_debitnote.debitNoteCode AS documentCode,
                        erp_debitnote.comments AS description,
                        erp_debitnote.empID AS employeeID,
                        erp_debitnote.netAmountLocal AS amountLocal,
                        erp_debitnote.netAmountRpt AS amountRpt,
                        erp_debitnote.invoiceNumber AS referenceDoc,
                        erp_debitnote.postedDate AS referenceDocDate,
                        erp_debitnote.debitNoteAutoID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        rptCurrency.DecimalPlaces AS rptCurrencyDecimals,
                        7 AS type
                    FROM
                    erp_debitnote
                        LEFT JOIN currencymaster ON erp_debitnote.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster as rptCurrency ON erp_debitnote.companyReportingCurrencyID = rptCurrency.currencyID
                    WHERE
                        DATE(erp_debitnote.debitNoteDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND
                        7 IN (' . $typeIDs . ') AND
                        erp_debitnote.companySystemID IN ('.$companyID.') AND
                        erp_debitnote.approved = -1
                        UNION ALL
                        SELECT
                        erp_paysupplierinvoicemaster.BPVdate AS documentDate,
                        erp_paysupplierinvoicemaster.BPVcode AS documentCode,
                        "Exchange Gain or Loss" AS description,
                        erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
                        erp_generalledger.documentLocalAmount*-1 AS amountLocal,
                        erp_generalledger.documentRptAmount*-1 AS amountRpt,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionCode AS referenceDoc,
                        srp_erp_pay_monthlydeductionmaster.dateMD AS referenceDocDate,
                        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
                        currencymaster.DecimalPlaces AS localCurrencyDecimals,
                        currencymasterRpt.DecimalPlaces As rptCurrencyDecimals,
                        5 AS type
                    FROM
                        erp_paysupplierinvoicemaster
                        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = srp_erp_pay_monthlydeductionmaster.pv_id
                        LEFT JOIN currencymaster ON erp_paysupplierinvoicemaster.localCurrencyID = currencymaster.currencyID
                        LEFT JOIN currencymaster AS currencymasterRpt ON erp_paysupplierinvoicemaster.companyRptCurrencyID = currencymasterRpt.currencyID
                        LEFT JOIN erp_generalledger ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_generalledger.documentSystemCode
                    WHERE
                        erp_paysupplierinvoicemaster.invoiceType = 6 AND 
                        DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" AND 
                        erp_paysupplierinvoicemaster.approved = -1 AND
                        5 IN (' . $typeIDs . ') AND
                        erp_paysupplierinvoicemaster.companySystemID IN ('.$companyID.') AND
                        erp_generalledger.documentSystemID = 4 AND 
                        erp_generalledger.chartOfAccountSystemID = "'.$exchangeGainLossAccount.'"
                        ) AS t1 WHERE t1.employeeID IN (' . $employeeIDs . ')');

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }


    public function getGeneralLedgerRefAmount() {
        return DB::select("SELECT * FROM (SELECT
        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
        srp_erp_payrolldetail.companyLocalAmount as referenceAmountLocal,
        srp_erp_payrolldetail.companyReportingAmount as referenceAmountRpt,
        srp_erp_payrolldetail.empID as employeeID,
        1 as refType
    FROM
        erp_bookinvsuppmaster
        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = srp_erp_pay_monthlydeductionmaster.supplierInvoiceID
        LEFT JOIN srp_erp_pay_monthlydeductiondetail ON srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID = srp_erp_pay_monthlydeductiondetail.monthlyDeductionMasterID
        LEFT JOIN srp_erp_payrolldetail ON srp_erp_pay_monthlydeductiondetail.monthlyDeductionDetailID = srp_erp_payrolldetail.detailTBID
        LEFT JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID
    WHERE
        srp_erp_payrolldetail.fromTB = 'MD' AND
        srp_erp_payrollmaster.approvedYN = 1 AND
        erp_bookinvsuppmaster.documentType = 4 
        UNION ALL
        SELECT
        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
        srp_erp_payrolldetail.companyLocalAmount as referenceAmountLocal,
        srp_erp_payrolldetail.companyReportingAmount as referenceAmountRpt,
        srp_erp_payrolldetail.empID as employeeID,
        1 as refType
    FROM
        expense_employee_allocation
        LEFT JOIN employees ON expense_employee_allocation.employeeSystemID = employees.employeeSystemID
        LEFT JOIN erp_bookinvsuppmaster ON expense_employee_allocation.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
            LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = srp_erp_pay_monthlydeductionmaster.supplierInvoiceID
        LEFT JOIN srp_erp_pay_monthlydeductiondetail ON srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID = srp_erp_pay_monthlydeductiondetail.monthlyDeductionMasterID
        LEFT JOIN srp_erp_payrolldetail ON srp_erp_pay_monthlydeductiondetail.monthlyDeductionDetailID = srp_erp_payrolldetail.detailTBID
        LEFT JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID
    WHERE
        srp_erp_payrolldetail.fromTB = 'MD' AND
        srp_erp_payrollmaster.approvedYN = 1 AND
        expense_employee_allocation.documentSystemID = 11
        UNION ALL
        SELECT
        srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID AS masterID,
        SUM(srp_erp_payrolldetail.companyLocalAmount) as referenceAmountLocal,
        SUM(srp_erp_payrolldetail.companyReportingAmount) as referenceAmountRpt,
        0 as employeeID,
        1 as refType
    FROM
        erp_paysupplierinvoicemaster
        LEFT JOIN srp_erp_pay_monthlydeductionmaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = srp_erp_pay_monthlydeductionmaster.pv_id
        LEFT JOIN srp_erp_pay_monthlydeductiondetail ON srp_erp_pay_monthlydeductionmaster.monthlyDeductionMasterID = srp_erp_pay_monthlydeductiondetail.monthlyDeductionMasterID
        LEFT JOIN srp_erp_payrolldetail ON srp_erp_pay_monthlydeductiondetail.monthlyDeductionDetailID = srp_erp_payrolldetail.detailTBID
        LEFT JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID
    WHERE
        erp_paysupplierinvoicemaster.invoiceType = 3 AND 
        srp_erp_payrolldetail.fromTB = 'MD' AND
        srp_erp_payrollmaster.approvedYN = 1 GROUP BY masterID
       ) AS t1");
    }

    public function getGeneralLedgerSelectedEmployees($fromDate,$toDate,$typeID,$companyID,$employeeDatas) {
        $typeID = join(",",json_decode($typeID));
        $employeeDatas = join(",",json_decode($employeeDatas));
        $companyID = join(",",json_decode($companyID));

        return DB::select('SELECT * FROM (
            SELECT
           erp_bookinvsuppmaster.employeeID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
           erp_bookinvsuppmaster
           LEFT JOIN employees ON erp_bookinvsuppmaster.employeeID = employees.employeeSystemID
       WHERE
           (DATE(erp_bookinvsuppmaster.bookingDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" 
    OR DATE(erp_bookinvsuppmaster.bookingDate) < "' . $fromDate . '") AND 
           erp_bookinvsuppmaster.approved = -1 AND
           erp_bookinvsuppmaster.employeeID IN (' . $employeeDatas . ') AND
           erp_bookinvsuppmaster.documentType = 4 AND
           4 IN (' . $typeID . ')
       UNION ALL
       SELECT
           expense_employee_allocation.employeeSystemID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
           expense_employee_allocation
       LEFT JOIN employees ON expense_employee_allocation.employeeSystemID = employees.employeeSystemID
       LEFT JOIN erp_bookinvsuppmaster ON expense_employee_allocation.documentSystemCode = erp_bookinvsuppmaster.bookingSuppMasInvAutoID
       WHERE
           (DATE(erp_bookinvsuppmaster.bookingDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(erp_bookinvsuppmaster.bookingDate) < "' . $fromDate . '") AND 
           expense_employee_allocation.documentSystemID = 11 AND
           erp_bookinvsuppmaster.approved = -1 AND
           expense_employee_allocation.employeeSystemID IN (' . $employeeDatas . ') AND
           1 IN (' . $typeID . ')  
           UNION ALL
           SELECT
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
           erp_paysupplierinvoicemaster
       LEFT JOIN employees ON erp_paysupplierinvoicemaster.directPaymentPayeeEmpID = employees.employeeSystemID
       WHERE
           erp_paysupplierinvoicemaster.invoiceType = 3 AND 
           (DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(erp_paysupplierinvoicemaster.BPVdate) < "' . $fromDate . '") AND 
           erp_paysupplierinvoicemaster.approved = -1 AND
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID IN (' . $employeeDatas . ') AND
           2 IN (' . $typeID . ')  
           UNION ALL
           SELECT
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
           erp_paysupplierinvoicemaster
       LEFT JOIN employees ON erp_paysupplierinvoicemaster.directPaymentPayeeEmpID = employees.employeeSystemID
       WHERE
           erp_paysupplierinvoicemaster.invoiceType = 6 AND 
           (DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(erp_paysupplierinvoicemaster.BPVdate) < "' . $fromDate . '") AND 
           erp_paysupplierinvoicemaster.approved = -1 AND
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID IN (' . $employeeDatas . ') AND
           5 IN (' . $typeID . ') 
           UNION ALL
           SELECT
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
           erp_paysupplierinvoicemaster
       LEFT JOIN employees ON erp_paysupplierinvoicemaster.directPaymentPayeeEmpID = employees.employeeSystemID
       WHERE
           erp_paysupplierinvoicemaster.invoiceType = 7 AND 
           (DATE(erp_paysupplierinvoicemaster.BPVdate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(erp_paysupplierinvoicemaster.BPVdate) < "' . $fromDate . '") AND 
           erp_paysupplierinvoicemaster.approved = -1 AND
           erp_paysupplierinvoicemaster.directPaymentPayeeEmpID IN (' . $employeeDatas . ') AND
           6 IN (' . $typeID . ') 
           UNION ALL 
           SELECT
           srp_erp_iouvouchers.empID AS employeeID,
           srp_erp_iouvouchers.empName AS employeeName,
           employees.empID AS empID
       FROM
           srp_erp_iouvouchers
       LEFT JOIN employees ON srp_erp_iouvouchers.empID = employees.employeeSystemID
       WHERE
           (DATE(srp_erp_iouvouchers.voucherDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(srp_erp_iouvouchers.voucherDate) < "' . $fromDate . '") AND
           srp_erp_iouvouchers.empID IN (' . $employeeDatas . ') AND
           3 IN (' . $typeID . ') AND
           srp_erp_iouvouchers.approvedYN = 1
           UNION ALL 
           SELECT
           erp_debitnote.empID AS employeeID,
           employees.empName AS employeeName,
           employees.empID AS empID
       FROM
       erp_debitnote
       LEFT JOIN employees ON erp_debitnote.empID = employees.employeeSystemID
       WHERE
           (DATE(erp_debitnote.debitNoteDate) BETWEEN "' . $fromDate . '" AND "' . $toDate . '" OR DATE(erp_debitnote.debitNoteDate) < "' . $fromDate . '") AND
           7 IN (' . $typeID . ') AND
           erp_debitnote.companySystemID IN (' . $companyID . ') AND
           erp_debitnote.empID IN (' . $employeeDatas . ') AND
           erp_debitnote.approved = -1
           ) t GROUP BY t.employeeID');
    }

    public function reportTemplateEquityGLDrillDown(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $output = $this->reportTemplateEquityGLDrillDownQry($request);

        $total = collect($output)->pluck($input['selectedColumn'])->toArray();
        $total = array_sum($total);

        return \DataTables::of($output)
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('total', $total)
            ->filter(function ($query) {
            })
            ->make(true);
    }

    public function reportTemplateEquityGLDrillDownQry(Request $request)
    {

        $fromDate = Carbon::parse($request->fromDate)->startOfDay()->format('Y-m-d H:i:s');
        $toDate = Carbon::parse($request->toDate)->endOfDay()->format('Y-m-d H:i:s');
        $selectedGL = $request->details;
        $selectedColumn = $request->selectedColumn;
        $currency = isset($request->currency[0]) ? $request->currency[0]: $request->currency;
        $amountColumn = ($currency == 1) ? 'documentLocalAmount' : 'documentRptAmount';
        $search = ($request->search['value'] ?? '');
        $output = DB::table('erp_generalledger')
                ->selectRaw("documentSystemCode,documentCode,erp_generalledger.documentSystemID,documentDate,documentNarration,(-1 *$amountColumn) as `$selectedColumn` ,serviceline.ServiceLineDes,clientContractID,
                            CASE 
                                WHEN customermaster.CustomerName IS NOT NULL THEN customermaster.CustomerName
                                ELSE suppliermaster.SupplierName 
                            END AS partyName")
                ->leftJoin('serviceline', 'erp_generalledger.serviceLineSystemID', '=', 'serviceline.serviceLineSystemID')
                ->leftJoin('suppliermaster', 'erp_generalledger.supplierCodeSystem', '=', 'suppliermaster.supplierCodeSystem')
                ->leftJoin('customermaster', 'erp_generalledger.supplierCodeSystem', '=', 'customermaster.customerCodeSystem')
                ->whereBetween('documentDate', [$fromDate, $toDate])
                ->where('erp_generalledger.companySystemID', $request->selectedCompany)
                ->whereIn('chartOfAccountSystemID',$selectedGL);


                if (!empty($search)) {
                    $search = strtolower($search);
                    $output->where(function ($query) use ($search) {
                        $query->where('documentNarration', 'LIKE', "%{$search}%")
                              ->orWhereRaw("REPLACE(documentCode, '\\\\', '') LIKE ?", ["%" . str_replace("\\", "", $search) . "%"]);
                    });
                }
                return $output->get();
    }

    public function reportTemplateConsolidationDrillDown(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $output = ConsolidationReportService::processConsolidationDataForDrillDownAndReport($input);

        if ($search) {
            $output['data'] = array_filter($output['data'], function ($item) use ($search) {
                return strpos(strtolower($item['company']), strtolower($search)) !== false;
            });
        }

        $data['order'] = [];
        $data['search']['value'] = '';
        $request->merge($data);
        $request->request->remove('search.value');

        return \DataTables::of($output['data'])
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->with('total', $output['total'])
            ->make(true);
    }   
    
}

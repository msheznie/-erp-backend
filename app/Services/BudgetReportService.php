<?php

namespace App\Services;

use App\Classes\AccountsPayable\Reports\BudgetCommitmentsDetailsReport;
use App\helper\BudgetConsumptionService;
use App\Models\BudgetMaster;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CompanyFinanceYear;
use App\Models\CurrencyMaster;
use App\Models\ProcumentOrder;
use App\Models\ServiceLine;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BudgetReportService
{
    public function generateBudgetCommitmentDetailsReport(Request  $request)
    {
        $serviceLineSystemIDs = collect($request->input('selectedServicelines'))->pluck('serviceLineSystemID')->toArray();
        $chartOfAccountIDs = collect($request->input('glCodes'))->pluck('chartOfAccountSystemID')->toArray();
        $fromDate = new Carbon($request->input('fromDate'));
        $toDate = new Carbon($request->input('toDate'));
        $currentFinanicalYear = CompanyFinanceYear::currentFinanceYear($request->input('companySystemID'));

        $previosYear = CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate")
            ->where('companySystemID', $request->input('companySystemID'))
            ->whereDate('bigginingDate', '<', $currentFinanicalYear->startDate)
            ->orderBy('bigginingDate', 'desc')
            ->first();


        $data= [];
        $errorBudgetZero = [];

            $chartOfAccountDataArray = array();
            $total =0;
            foreach ($chartOfAccountIDs as $chartOfAccountID)
            {

                $currentBudgetAmount = Budjetdetails::whereHas('budget_master.segment_by', function ($query) use ($serviceLineSystemIDs,$currentFinanicalYear) {
                    $query->where('companyFinanceYearID',$currentFinanicalYear->companyFinanceYearID);
                    $query->whereIn('serviceLineSystemID', $serviceLineSystemIDs);
                })->whereHas('budget_master.company', function($query) use ($request) {
                    $query->where('companySystemID',$request->input('companySystemID'));
                })->whereHas('chart_of_account', function ($query) use ($chartOfAccountID) {
                    $query->where('chartOfAccountSystemID',$chartOfAccountID);
                })->whereBetween('erp_budjetdetails.createdDateTime', [$fromDate, $toDate])->sum('budjetAmtLocal');

                $chartOfAccount = ChartOfAccount::find($chartOfAccountID);

                if($currentBudgetAmount == 0)
                {
                    array_push($errorBudgetZero,$chartOfAccount->AccountCode);
                }
                else {
                    $commitments = ProcumentOrder::whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)->where('approved',-1)
                        ->where('budgetYear','<',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->sum('poTotalLocalCurrency');

                    $currentOpenPOs = ProcumentOrder::whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)
                        ->where('approved',-1)
                        ->where('budgetYear',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->sum('poTotalLocalCurrency');

                    $prvOpenPOs = ProcumentOrder::whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)
                        ->where('approved',-1)
                        ->where('budgetYear','<',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->sum('poTotalLocalCurrency');

                    $grvTotalAmountPreYear = ProcumentOrder::whereIn('serviceLineSystemID',$serviceLineSystemIDs)->where('poConfirmedYN', 1)
                        ->Where('approved', -1)
                        ->join('erp_grvdetails', 'erp_grvdetails.purchaseOrderMastertID', '=', 'erp_purchaseordermaster.purchaseOrderID') // Join the grv_details table
                        ->where('budgetYear','<', Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->selectRaw('SUM(poTotalLocalCurrency) as total') // Sum netAmount from grv_details
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->first();

                    $grvTotalAmountCurrYear = ProcumentOrder::whereIn('serviceLineSystemID',$serviceLineSystemIDs)->where('poConfirmedYN', 1)
                        ->Where('approved', -1)
                        ->where('budgetYear',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->join('erp_grvdetails', 'erp_grvdetails.purchaseOrderMastertID', '=', 'erp_purchaseordermaster.purchaseOrderID') // Join the grv_details table
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereHas('grv_details')
                        ->selectRaw('SUM(poTotalLocalCurrency) as total1') // Sum netAmount from grv_details
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->first();

                    $budgetCommitmentsDetailsReport = new BudgetCommitmentsDetailsReport();
                    $budgetCommitmentsDetailsReport->setGlCode($chartOfAccount->AccountCode);
                    $budgetCommitmentsDetailsReport->setAccountsDescription($chartOfAccount->AccountDescription);
                    $budgetCommitmentsDetailsReport->setGlTypes($chartOfAccount->catogaryBLorPL);
                    $budgetCommitmentsDetailsReport->setBudgetAmount($currentBudgetAmount);
                    $budgetCommitmentsDetailsReport->setCommitments($commitments);
                    $budgetCommitmentsDetailsReport->setTotalAvailableBudget($currentBudgetAmount + $commitments);
                    $budgetCommitmentsDetailsReport->setActualAmountSpentTillDateCB($grvTotalAmountCurrYear->total1);
                    $budgetCommitmentsDetailsReport->setActualAmountSpentTillDatePC($grvTotalAmountPreYear->total);
                    $budgetCommitmentsDetailsReport->setCommitmentsForCurrentYear($currentOpenPOs - $grvTotalAmountCurrYear->total1);
                    $budgetCommitmentsDetailsReport->setCommitmentsFromPreviosYear($prvOpenPOs - $grvTotalAmountPreYear->total);
                    $budgetCommitmentsDetailsReport->setBalance();
                    $total += $budgetCommitmentsDetailsReport->getTotal();

                    array_push($data,$budgetCommitmentsDetailsReport);
                }

            }

        return ['data' => $data, 'total' => $total];

    }


}

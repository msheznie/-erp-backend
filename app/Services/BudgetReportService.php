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
        $toDate = $toDate->setTime(23, 59, 59);
        $currentFinanicalYear = CompanyFinanceYear::currentFinanceYear($request->input('companySystemID'));
        $currencyID = $request->currencyID[0];
        $previosYear = CompanyFinanceYear::selectRaw("companyFinanceYearID, DATE(bigginingDate) AS startDate, DATE(endingDate) AS endDate")
            ->where('companySystemID', $request->input('companySystemID'))
            ->whereDate('bigginingDate', '<', $currentFinanicalYear->startDate)
            ->orderBy('bigginingDate', 'desc')
            ->first();


        $data= [];
        $errorBudgetZero = [];

            $chartOfAccountDataArray = array();
            $total =0;
            $totalBudgetAmount = 0;
            $totalCommitments = 0;
            $totalAvailableBudget = 0;
            $totalActualAmountSpentTillDateCB = 0;
            $totalActualAmountSpentTillDatePC = 0;
            $totalCommitmentsForCurrentYear = 0;
            $totalCommitmentsFromPreviousYear = 0;

        $idWithValue = Budjetdetails::with(['budget_master.segment_by', 'budget_master.company', 'chart_of_account'])
            ->whereHas('budget_master.segment_by', function ($query) use ($serviceLineSystemIDs) {
                $query->whereIn('serviceLineSystemID', $serviceLineSystemIDs);
            })
            ->whereHas('budget_master.company', function($query) use ($request) {
                $query->where('companySystemID', $request->input('companySystemID'));
            })
            ->whereHas('chart_of_account', function ($query) use ($chartOfAccountIDs) {
                $query->whereIn('chartOfAccountSystemID', $chartOfAccountIDs);
            })
            ->whereHas('budget_master', function ($query) use ($currentFinanicalYear) {
                $query->where('companyFinanceYearID', $currentFinanicalYear->companyFinanceYearID);
            })
            ->whereBetween('createdDateTime', [$fromDate, $toDate])
            ->where('budjetAmtLocal', '!=', 0)
            ->groupBy('chartOfAccountID')
            ->pluck('chartOfAccountID')
            ->toArray();

        foreach (array_flatten($idWithValue) as $chartOfAccountID)
            {

                $currentBudgetAmount = Budjetdetails::with(['budget_master.segment_by', 'budget_master.company', 'chart_of_account'])
                ->whereHas('budget_master.segment_by', function ($query) use ($serviceLineSystemIDs,$currentFinanicalYear) {
                    $query->whereIn('serviceLineSystemID', $serviceLineSystemIDs);
                })->whereHas('budget_master.company', function($query) use ($request) {
                    $query->where('companySystemID',$request->input('companySystemID'));
                })->whereHas('chart_of_account', function ($query) use ($chartOfAccountID) {
                    $query->where('chartOfAccountSystemID',$chartOfAccountID);
                })->whereHas('budget_master',function ($query) use ($currentFinanicalYear) {
                    $query->where('companyFinanceYearID',$currentFinanicalYear->companyFinanceYearID);
                })->whereBetween('erp_budjetdetails.createdDateTime', [$fromDate, $toDate]);

                $currentBudgetAmount = ($currencyID == 1) ? $currentBudgetAmount->sum('budjetAmtLocal') : $currentBudgetAmount->sum('budjetAmtRpt');

                $chartOfAccount = ChartOfAccount::find($chartOfAccountID);

                if($currentBudgetAmount == 0)
                {
                    array_push($errorBudgetZero,$chartOfAccount->AccountCode);
                }
                else {
                    $commitments = ProcumentOrder::with(['detail'])->whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)->where('approved',-1)
                        ->where('budgetYear','<',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate]);

                    $commitments = ($currencyID == 1) ? $commitments->sum('poTotalLocalCurrency') : $commitments->sum('poTotalComRptCurrency');


                    $currentOpenPOs = ProcumentOrder::with(['detail'])->whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)
                        ->where('approved',-1)
                        ->where('budgetYear',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate]);
                    $currentOpenPOs = ($currencyID == 1) ? $currentOpenPOs->sum('poTotalLocalCurrency') : $currentOpenPOs->sum('poTotalComRptCurrency');


                    $prvOpenPOs = ProcumentOrder::with(['detail'])->whereIn('serviceLineSystemID',$serviceLineSystemIDs)
                        ->where('poConfirmedYN',1)
                        ->where('approved',-1)
                        ->where('budgetYear','<',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate]);

                    $prvOpenPOs = ($currencyID == 1) ? $prvOpenPOs->sum('poTotalLocalCurrency') : $prvOpenPOs->sum('poTotalComRptCurrency');

                    $grvTotalAmountPreYear = ProcumentOrder::with(['detail'])->whereIn('erp_purchaseordermaster.serviceLineSystemID',$serviceLineSystemIDs)->where('poConfirmedYN', 1)
                        ->Where('erp_purchaseordermaster.approved', -1)
                        ->join('erp_grvdetails', 'erp_grvdetails.purchaseOrderMastertID', '=', 'erp_purchaseordermaster.purchaseOrderID') // Join the grv_details table
                        ->join('erp_grvmaster', 'erp_grvmaster.grvAutoID', '=', 'erp_grvdetails.grvAutoID') // Ensure the GRV master exists
                        ->where('budgetYear','<', Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->where('erp_grvmaster.approved',-1)
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->selectRaw(
                            $currencyID == 1
                                ? 'SUM(poTotalLocalCurrency) as total'
                                : 'SUM(poTotalComRptCurrency) as total'
                        )
                        ->whereBetween('erp_purchaseordermaster.createdDateTime', [$fromDate, $toDate])
                        ->first();

                    $grvTotalAmountCurrYear = ProcumentOrder::with(['detail'])->whereIn('erp_purchaseordermaster.serviceLineSystemID',$serviceLineSystemIDs)->where('poConfirmedYN', 1)
                        ->Where('erp_purchaseordermaster.approved', -1)
                        ->where('budgetYear',Carbon::parse($currentFinanicalYear->startDate)->year)
                        ->join('erp_grvdetails', 'erp_grvdetails.purchaseOrderMastertID', '=', 'erp_purchaseordermaster.purchaseOrderID') // Join the grv_details table
                        ->join('erp_grvmaster', 'erp_grvmaster.grvAutoID', '=', 'erp_grvdetails.grvAutoID') // Ensure the GRV master exists
                        ->whereHas('detail', function ($query) use ($chartOfAccountID) {
                            $query->where('financeGLcodebBSSystemID',$chartOfAccountID)->orWhere('financeGLcodePLSystemID',$chartOfAccountID);
                        })
                        ->where('erp_grvmaster.approved',-1)
                        ->whereHas('grv_details')
                        ->selectRaw(
                            $currencyID == 1
                                ? 'SUM(poTotalLocalCurrency) as total1'
                                : 'SUM(poTotalComRptCurrency) as total1'
                        )
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
                    $totalBudgetAmount += $currentBudgetAmount;
                    $totalCommitments += $commitments;
                    $totalAvailableBudget += ($currentBudgetAmount + $commitments);
                    $totalActualAmountSpentTillDateCB += $grvTotalAmountCurrYear->total1;
                    $totalActualAmountSpentTillDatePC += $grvTotalAmountPreYear->total;
                    $totalCommitmentsForCurrentYear += ($currentOpenPOs - $grvTotalAmountCurrYear->total1);
                    $totalCommitmentsFromPreviousYear += ($prvOpenPOs - $grvTotalAmountPreYear->total);

                    array_push($data,$budgetCommitmentsDetailsReport);
                }

            }

        $totals = [
            'totalBudgetAmount' => $totalBudgetAmount,
            'totalCommitments' => $totalCommitments,
            'totalAvailableBudget' => $totalAvailableBudget,
            'totalActualAmountSpentTillDateCB' => $totalActualAmountSpentTillDateCB,
            'totalActualAmountSpentTillDatePC' => $totalActualAmountSpentTillDatePC,
            'totalCommitmentsForCurrentYear' => $totalCommitmentsForCurrentYear,
            'totalCommitmentsFromPreviousYear' => $totalCommitmentsFromPreviousYear,
            'total' => $total, // Total of the reports if applicable
        ];


        return ['data' => $data, 'total' => $totals];

    }


}

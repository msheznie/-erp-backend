<?php

namespace App\Jobs;

use App\Models\BudgetTransferForm;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\CompanyFinanceYear;
use App\Models\Company;
use App\Models\TemplatesDetails;
use App\Repositories\AuditTrailRepository;
use App\Repositories\BudgetAdjustmentRepository;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BudgetAdjustment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $budgetTransfer;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($budgetTransfer)
    {
        $this->budgetTransfer = $budgetTransfer;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AuditTrailRepository $auditTrailRepo, BudgetAdjustmentRepository $budgetAdjustmentRepo,
                           BudjetdetailsRepository $budjetdetailsRepo)
    {
        $bt = $this->budgetTransfer;
        $budgetTransfer = BudgetTransferForm::with(['detail'])->find($bt->budgetTransferFormAutoID);
        Log::useFiles(storage_path() . '/logs/budget_adjustment_jobs.log');
        if (!empty($budgetTransfer)) {
            DB::beginTransaction();

            $companyData = Company::with(['localcurrency', 'reportingcurrency'])->where('companySystemID', $budgetTransfer->companySystemID)
                                  ->first();

            $localDecimalPlaces = 3; // set default value as 3, since old code hard coded as 3
            $reportingDecimalPlaces = 2;

            if ($companyData) {
                $localDecimalPlaces = $companyData->localcurrency ? $companyData->localcurrency->DecimalPlaces : 3;
                $reportingDecimalPlaces = $companyData->reportingcurrency ? $companyData->reportingcurrency->DecimalPlaces : 2;
            }

            try {

                foreach ($budgetTransfer->detail as $item) {

                    // template detail
                    $conversionFrom  = 1;
                    $conversionTo  = 1;
                    //$templateDetail = TemplatesDetails::find($item['fromTemplateDetailID']);
                    $templateDetail = ChartOfAccount::find($item['fromChartOfAccountSystemID']);

                    if(!empty($templateDetail) && $templateDetail->controlAccountsSystemID == 2){
                        $conversionFrom = -1;
                    }

                    //$templateDetail = TemplatesDetails::find($item['toTemplateDetailID']);
                    $templateDetail = ChartOfAccount::find($item['toChartOfAccountSystemID']);

                    if(!empty($templateDetail) && $templateDetail->controlAccountsSystemID == 2){
                        $conversionTo = -1;
                    }

                    // Audit Trail Insert start
                    $fromValue = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->sum('budjetAmtRpt');


                    $fromValueBudget = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->first();

                    $newFromValue = ($fromValue * $conversionFrom)  - $item['adjustmentAmountRpt'];

                    $toValue = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->sum('budjetAmtRpt');

                     $toValueBudget = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->first();

                    $newToValue = ($toValue + ($item['adjustmentAmountRpt'] * $conversionTo)) * $conversionTo;

                    $fromTotalMonth = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->count();

                    $toTotalMonth = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                        ->whereHas('budget_master', function($query) use ($item) {
                            $query->where('Year', $item['year']);
                        })
                        ->count();

                    $auditTrail = array('companySystemID' => $budgetTransfer->companySystemID,
                        'companyID' => $budgetTransfer->companyID,
                        'serviceLineSystemID' => $item['fromServiceLineSystemID'],
                        'serviceLineCode' => $item['fromServiceLineCode'],
                        'documentSystemID' => $budgetTransfer->documentSystemID,
                        'documentID' => $budgetTransfer->documentID,
                        'documentSystemCode' => $budgetTransfer->budgetTransferFormAutoID,
                        'valueFrom' => $fromValue ,
                        'valueTo' => $newFromValue * $conversionFrom,
                        'valueFromSystemID' => $item['fromChartOfAccountSystemID'],
                        'valueFromText' => $item['FromGLCode'],
                        'valueToSystemID' => $item['toChartOfAccountSystemID'],
                        'valueToText' => $item['toGLCode'],
                        'description' => 'Adjusting from ' . $item['FromGLCode'] . ' to ' . $item['toGLCode'] . '. Current amount of ' . $item['FromGLCode'] . ' is ' . $fromValue . '. Current amount of ' . $item['toGLCode'] . ' is ' . $toValue . '. New amount of ' . $item['FromGLCode'] . ' is ' . ($newFromValue * $conversionFrom) . '. New amount of ' . $item['toGLCode'] . ' is ' . ($newToValue * $conversionTo) . '. Total months of ' . $item['FromGLCode'] . ' is ' . $fromTotalMonth . '. Total month of ' . $item['toGLCode'] . ' is ' . $toTotalMonth,
                        'modifiedUserSystemID' => $budgetTransfer->modifiedUserSystemID,
                        'modifiedUserID' => $budgetTransfer->modifiedUser
                    );
                     $auditTrailRepo->create($auditTrail);

                    // Audit Trail Insert End

                    //Start update From Budget in budget details

                    $fromBudgetDetails = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                                                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                                                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                                                        ->whereHas('budget_master', function($query) use ($item) {
                                                            $query->where('Year', $item['year']);
                                                        })
                                                        ->where(function($query) {
                                                            $query->where('month','>=', date("m"))
                                                                  ->orWhere('Year','>', date("Y"));
                                                        })
                                                        ->get();

                    $fromTotalCount = count($fromBudgetDetails);

                    if($fromTotalCount > 0){
                        $fromMinusAmountRpt = round(($item['adjustmentAmountRpt']/$fromTotalCount),$reportingDecimalPlaces);
                        $fromMinusAmountLocal = round(($item['adjustmentAmountLocal']/$fromTotalCount),$localDecimalPlaces);

                        $countFrom = 1;
                        foreach ($fromBudgetDetails as $fromBudgetDetail){

                            if (count($fromBudgetDetails) == $countFrom) {
                                $diffRpt = $item['adjustmentAmountRpt'] - ($fromMinusAmountRpt * $fromTotalCount);
                                $diffLocal = $item['adjustmentAmountLocal'] - ($fromMinusAmountLocal * $fromTotalCount);

                                $fromMinusAmountLocal = $fromMinusAmountLocal + $diffLocal;
                                $fromMinusAmountRpt = $fromMinusAmountRpt + $diffRpt;
                            }
                            
                            $budjetdetailsRepo->update([
                                'budjetAmtLocal' => ((($fromBudgetDetail['budjetAmtLocal'] * $conversionFrom) - $fromMinusAmountLocal)* $conversionFrom),
                                'budjetAmtRpt' => ((($fromBudgetDetail['budjetAmtRpt']* $conversionFrom) - $fromMinusAmountRpt) * $conversionFrom)
                            ],
                                $fromBudgetDetail['budjetDetailsID']);

                            $countFrom++;
                        }
                    }


                    $toTotalBudgetDetails = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                                                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                                                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                                                        ->whereHas('budget_master', function($query) use ($item) {
                                                            $query->where('Year', $item['year']);
                                                        })
                                                        ->where(function($query) {
                                                            $query->where('month','>=', date("m"))
                                                                  ->orWhere('Year','>', date("Y"));
                                                        })
                                                        ->get();

                    $toTotalCount = count($toTotalBudgetDetails);

                    if($toTotalCount > 0){
                        $toAddAmountRpt = round(($item['adjustmentAmountRpt']/$toTotalCount),$reportingDecimalPlaces);
                        $toAddAmountLocal = round(($item['adjustmentAmountLocal']/$toTotalCount),$localDecimalPlaces);
                        $count = 1;
                        foreach ($toTotalBudgetDetails as $toBudgetDetail){

                            if (count($toTotalBudgetDetails) == $count) {
                                $diffRpt = $item['adjustmentAmountRpt'] - ($toAddAmountRpt * $toTotalCount);
                                $diffLocal = $item['adjustmentAmountLocal'] - ($toAddAmountLocal * $toTotalCount);

                                $toAddAmountLocal = $toAddAmountLocal + $diffLocal;
                                $toAddAmountRpt = $toAddAmountRpt + $diffRpt;
                            }

                            $budjetdetailsRepo->update([
                                'budjetAmtLocal' => ((($toBudgetDetail['budjetAmtLocal'] * $conversionTo)  + $toAddAmountLocal) * $conversionTo) ,
                                'budjetAmtRpt' => ((($toBudgetDetail['budjetAmtRpt'] * $conversionTo) + $toAddAmountRpt) * $conversionTo)
                            ],
                            $toBudgetDetail['budjetDetailsID']);


                            $count++;
                        }
                    }


                    //End update From Budget in budget details

                    // Budget Adjustment Start

                    $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $budgetTransfer->companySystemID)
                        ->whereYear('bigginingDate', '=', $budgetTransfer->year)
                        ->first();
                    $financeYearId = 0;
                    if (!empty($companyFinanceYear)) {
                        $financeYearId = $companyFinanceYear->companyFinanceYearID;
                    }

                    $budgetAdjustmentData = array(
                        'companySystemID' => $budgetTransfer->companySystemID,
                        'companyId' => $budgetTransfer->companyID,
                        'companyFinanceYearID' => $financeYearId,
                        'serviceLineSystemID' => $item['fromServiceLineSystemID'],
                        'serviceLine' => $item['fromServiceLineCode'],
                        'fromGLCodeSystemID' => $item['fromChartOfAccountSystemID'],
                        'fromGLCode' => $item['FromGLCode'],
                        'toGLCodeSystemID' => $item['toChartOfAccountSystemID'],
                        'toGLCode' => $item['toGLCode'],
                        'Year' => $budgetTransfer->year,
                        'createdUserSystemID' => $budgetTransfer->modifiedUserSystemID,
                        'createdByUserID' => $budgetTransfer->modifiedUser
                    );

                    $fromAdjustment = $budgetAdjustmentData;
                    $toAdjustment   = $budgetAdjustmentData;

                    if ($fromAdjustment) {
                        $fromAdjustment['adjustedGLCodeSystemID'] = $item['fromChartOfAccountSystemID'];
                        $fromAdjustment['budgetMasterID'] = ($fromValueBudget) ? $fromValueBudget->budgetmasterID : null;
                        $fromAdjustment['adjustedGLCode'] = $item['FromGLCode'];
                        $fromAdjustment['adjustmedLocalAmount'] = $item['adjustmentAmountLocal'] * -1;
                        $fromAdjustment['adjustmentRptAmount'] = $item['adjustmentAmountRpt'] * -1;
                    }

                    if ($toAdjustment) {
                        $toAdjustment['adjustedGLCodeSystemID'] = $item['toChartOfAccountSystemID'];
                        $toAdjustment['budgetMasterID'] = ($toValueBudget) ? $toValueBudget->budgetmasterID : null;
                        $toAdjustment['adjustedGLCode'] = $item['toGLCode'];
                        $toAdjustment['adjustmedLocalAmount'] = $item['adjustmentAmountLocal'];
                        $toAdjustment['adjustmentRptAmount'] = $item['adjustmentAmountRpt'];
                    }


                    if($item['isFromContingency'] == 0){
                        $budgetAdjustmentRepo->create($fromAdjustment);
                    }
                    $budgetAdjustmentRepo->create($toAdjustment);

                    // Budget Adjustment End

                }

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        } else {
            Log::error('Budget Transfer not found' . date('H:i:s'));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

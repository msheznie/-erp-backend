<?php

namespace App\Jobs;

use App\Models\BudgetTransferForm;
use App\Models\Budjetdetails;
use App\Models\CompanyFinanceYear;
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
            try {
                Log::info('Successfully start  budget_adjustment' . date('H:i:s'));
                Log::info($budgetTransfer);

                foreach ($budgetTransfer->detail as $item) {

                    // Audit Trail Insert start
                    $fromValue = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                        ->where('Year', $item['year'])
                        ->sum('budjetAmtRpt');

                    $newFromValue = $fromValue - $item['adjustmentAmountRpt'];

                    $toValue = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                        ->where('Year', $item['year'])
                        ->sum('budjetAmtRpt');

                    $newToValue = $toValue + $item['adjustmentAmountRpt'];

                    $fromTotalMonth = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                        ->where('Year', $item['year'])
                        ->count();

                    $toTotalMonth = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                        ->where('Year', $item['year'])
                        ->count();

                    $auditTrail = array('companySystemID' => $budgetTransfer->companySystemID,
                        'companyID' => $budgetTransfer->companyID,
                        'serviceLineSystemID' => $item['fromServiceLineSystemID'],
                        'serviceLineCode' => $item['fromServiceLineCode'],
                        'documentSystemID' => $budgetTransfer->documentSystemID,
                        'documentID' => $budgetTransfer->documentID,
                        'documentSystemCode' => $budgetTransfer->budgetTransferFormAutoID,
                        'valueFrom' => $fromValue,
                        'valueTo' => $newFromValue,
                        'valueFromSystemID' => $item['fromChartOfAccountSystemID'],
                        'valueFromText' => $item['FromGLCode'],
                        'valueToSystemID' => $item['toChartOfAccountSystemID'],
                        'valueToText' => $item['toGLCode'],
                        'description' => 'Adjusting from ' . $item['FromGLCode'] . ' to ' . $item['toGLCode'] . '. Current amount of ' . $item['FromGLCode'] . ' is ' . $fromValue . '. Current amount of ' . $item['toGLCode'] . ' is ' . $toValue . '. New amount of ' . $item['FromGLCode'] . ' is ' . $newFromValue . '. New amount of ' . $item['toGLCode'] . ' is ' . $newToValue . '. Total months of ' . $item['FromGLCode'] . ' is ' . $fromTotalMonth . '. Total month of ' . $item['toGLCode'] . ' is ' . $toTotalMonth,
                        'modifiedUserSystemID' => $budgetTransfer->modifiedUserSystemID,
                        'modifiedUserID' => $budgetTransfer->modifiedUser
                    );
                     $auditTrailRepo->create($auditTrail);
                    //Log::info($auditTrail);

                    // Audit Trail Insert End

                    //Start update From Budget in budget details

                    $fromBudgetDetails = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                                                        ->where('serviceLineSystemID', $item['fromServiceLineSystemID'])
                                                        ->where('chartOfAccountID', $item['fromChartOfAccountSystemID'])
                                                        ->where('Year', $item['year'])
                                                        ->where('month','>=', date("m"))
                                                        ->get();

                    $fromTotalCount = count($fromBudgetDetails);

                    if($fromTotalCount > 0){
                        $fromMinusAmountRpt = round(($item['adjustmentAmountRpt']/$fromTotalCount),2);
                        $fromMinusAmountLocal = round(($item['adjustmentAmountLocal']/$fromTotalCount),3);

                        foreach ($fromBudgetDetails as $fromBudgetDetail){
                            $budjetdetailsRepo->update(['budjetAmtLocal' => ($fromBudgetDetail['budjetAmtLocal'] - $fromMinusAmountLocal),
                                'budjetAmtRpt' => ($fromBudgetDetail['budjetAmtRpt'] - $fromMinusAmountRpt)],$fromBudgetDetail['budjetDetailsID']);
                        }
                    }

                    Log::info('fromBudgetDetails' . $fromTotalCount );
                    Log::info($fromBudgetDetails);

                    $toTotalBudgetDetails = Budjetdetails::where('companySystemID', $budgetTransfer->companySystemID)
                                                        ->where('serviceLineSystemID', $item['toServiceLineSystemID'])
                                                        ->where('chartOfAccountID', $item['toChartOfAccountSystemID'])
                                                        ->where('Year', $item['year'])
                                                        ->where('month','>=', date("m"))
                                                        ->get();

                    $toTotalCount = count($fromBudgetDetails);

                    if($toTotalCount > 0){
                        $toAddAmountRpt = round(($item['adjustmentAmountRpt']/$toTotalCount),2);
                        $toAddAmountLocal = round(($item['adjustmentAmountLocal']/$toTotalCount),3);

                        foreach ($toTotalBudgetDetails as $toBudgetDetail){
                            $budjetdetailsRepo->update(['budjetAmtLocal' => ($toBudgetDetail['budjetAmtLocal'] + $toAddAmountLocal),
                                'budjetAmtRpt' => ($toBudgetDetail['budjetAmtRpt'] + $toAddAmountRpt)],$toBudgetDetail['budjetDetailsID']);
                        }
                    }

                    //Log::info('toTotalBudgetDetails - ' . $toTotalCount);
                    //Log::info($toTotalBudgetDetails);

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
                        $fromAdjustment['adjustedGLCode'] = $item['FromGLCode'];
                        $fromAdjustment['adjustmedLocalAmount'] = $item['adjustmentAmountLocal'] * -1;
                        $fromAdjustment['adjustmentRptAmount'] = $item['adjustmentAmountRpt'] * -1;
                    }

                    if ($toAdjustment) {
                        $toAdjustment['adjustedGLCodeSystemID'] = $item['toChartOfAccountSystemID'];
                        $toAdjustment['adjustedGLCode'] = $item['toGLCode'];
                        $toAdjustment['adjustmedLocalAmount'] = $item['adjustmentAmountLocal'];
                        $toAdjustment['adjustmentRptAmount'] = $item['adjustmentAmountRpt'];
                    }
                    //Log::info('fromAdjustment');
                    //Log::info($fromAdjustment);

                    //Log::info('toAdjustment');
                    //Log::info($toAdjustment);

                    $budgetAdjustmentRepo->create($fromAdjustment);
                    $budgetAdjustmentRepo->create($toAdjustment);

                    // Budget Adjustment End

                }

                Log::info('Successfully end  budget_adjustment' . date('H:i:s'));
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        } else {
            Log::info('Error' . date('H:i:s'));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

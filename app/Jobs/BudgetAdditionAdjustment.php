<?php

namespace App\Jobs;

use App\Models\ErpBudgetAddition;
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


class BudgetAdditionAdjustment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $budgetAddition;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($budgetAddition)
    {
        $this->budgetAddition = $budgetAddition;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(AuditTrailRepository $auditTrailRepo,BudgetAdjustmentRepository $budgetAdjustmentRepo,BudjetdetailsRepository $budjetdetailsRepo) {
        $bat = $this->budgetAddition;
         $budgetAddition = ErpBudgetAddition::with(['detail'])->find($bat->id);
        Log::useFiles(storage_path() . '/logs/budget_addition_adjustment_jobs.log');
        if (!empty($budgetAddition)) {
            DB::beginTransaction();

            $companyData = Company::with(['localcurrency', 'reportingcurrency'])->where('companySystemID', $budgetAddition->companySystemID)
                                  ->first();

            $localDecimalPlaces = 3; // set default value as 3, since old code hard coded as 3
            $reportingDecimalPlaces = 2;

            if ($companyData) {
                $localDecimalPlaces = $companyData->localcurrency ? $companyData->localcurrency->DecimalPlaces : 3;
                $reportingDecimalPlaces = $companyData->reportingcurrency ? $companyData->reportingcurrency->DecimalPlaces : 2;
            }

            try {
                foreach ($budgetAddition->detail as $item) {
                    $conversion  = 1;
                   // $templateDetail = TemplatesDetails::find($item['templateDetailID']);
                    $templateDetail = ChartOfAccount::find($item['chartOfAccountSystemID']);
                    if(!empty($templateDetail) && $templateDetail->controlAccountsSystemID == 2){
                        $conversion = -1;
                    }

                     // Audit Trail Insert start
                     $Value = Budjetdetails::where('companySystemID', $budgetAddition->companySystemID)
                     ->where('serviceLineSystemID', $item['serviceLineSystemID'])
                     ->where('chartOfAccountID', $item['chartOfAccountSystemID'])
                     ->whereHas('budget_master', function($query) use ($item) {
                        $query->where('Year', $item['year']);
                     })
                     ->sum('budjetAmtRpt');
                    
                    $newValue = ($Value + ($item['adjustmentAmountRpt'] * $conversion)) * $conversion;
                  

                     $auditTrail = array('companySystemID' => $budgetAddition->companySystemID,
                        'companyID' => $budgetAddition->companyID,
                        'serviceLineSystemID' => $item['serviceLineSystemID'],
                        'serviceLineCode' => $item['serviceLineCode'],
                        'documentSystemID' => $budgetAddition->documentSystemID,
                        'documentID' => $budgetAddition->documentID,
                        'documentSystemCode' => $budgetAddition->id,
                        'valueFrom' => $Value,
                        'valueTo' => $newValue * $conversion,
                        'description' => 'Addition of '.$item['gLCode'].' with amount of '.$newValue.'',
                        'modifiedUserSystemID' => $budgetAddition->modifiedUserSystemID,
                        'modifiedUserID' => $budgetAddition->modifiedUser
                    );

                    $auditTrailRepo->create($auditTrail);
                   
                     $BudgetDetails = Budjetdetails::where('companySystemID', $budgetAddition->companySystemID)
                                                 ->where('serviceLineSystemID', $item['serviceLineSystemID'])
                                                 ->where('chartOfAccountID', $item['chartOfAccountSystemID'])
                                                 ->whereHas('budget_master', function($query) use ($budgetAddition) {
                                                        $query->where('Year', $budgetAddition->year);
                                                    })
                                                    ->where(function($query) {
                                                        $query->where('month','>=', date("m"))
                                                              ->orWhere('Year','>', date("Y"));
                                                    })
                                                 ->get();

                    $TotalCount = count($BudgetDetails); 

                    $budgetmasterID = null;
                    if($TotalCount > 0){
                        $toAddAmountRpt = round(($item['adjustmentAmountRpt']/$TotalCount),$reportingDecimalPlaces);
                        $toAddAmountLocal = round(($item['adjustmentAmountLocal']/$TotalCount),$localDecimalPlaces);

                        $count = 1;
                        foreach ($BudgetDetails as $BudgetDetailVal){
                            $budgetmasterID = $BudgetDetailVal['budgetmasterID'];

                            if (count($BudgetDetails) == $count) {
                                $diffRpt = $item['adjustmentAmountRpt'] - ($toAddAmountRpt * $TotalCount);
                                $diffLocal = $item['adjustmentAmountLocal'] - ($toAddAmountLocal * $TotalCount);

                                $toAddAmountLocal = $toAddAmountLocal + $diffLocal;
                                $toAddAmountRpt = $toAddAmountRpt + $diffRpt;
                            }

                            $budjetdetailsRepo->update([
                                'budjetAmtLocal' => ((($BudgetDetailVal['budjetAmtLocal'] * $conversion)  + $toAddAmountLocal) * $conversion) ,
                                'budjetAmtRpt' => ((($BudgetDetailVal['budjetAmtRpt'] * $conversion) + $toAddAmountRpt) * $conversion)
                            ],
                                $BudgetDetailVal['budjetDetailsID']);


                            $count++;
                        }
                    }
                    
                    $companyFinanceYear = CompanyFinanceYear::where('companySystemID', $budgetAddition->companySystemID)
                    ->whereYear('bigginingDate', '=', $budgetAddition->year)
                    ->first();
                
                $financeYearId = 0; 
                
                if (!empty($companyFinanceYear)) {
                 $financeYearId = $companyFinanceYear->companyFinanceYearID;
                }

                $budgetAdjustmentData = array(
                    'companySystemID' => $budgetAddition->companySystemID,
                    'companyId' => $budgetAddition->companyID,
                    'companyFinanceYearID' => $financeYearId,
                    'budgetMasterID' => $budgetmasterID,
                    'serviceLineSystemID' => $item['serviceLineSystemID'],
                    'serviceLine' => $item['serviceLineCode'],
                    'toGLCodeSystemID' => $item['chartOfAccountSystemID'],
                    'toGLCode' => $item['gLCode'],
                    'Year' => $budgetAddition->year,
                    'createdUserSystemID' => $budgetAddition->modifiedUserSystemID,
                    'createdByUserID' => $budgetAddition->modifiedUser
                );
            
                $Adjustment = $budgetAdjustmentData;
                if ($Adjustment) {
                    $Adjustment['adjustedGLCodeSystemID'] = $item['chartOfAccountSystemID'];
                    $Adjustment['adjustedGLCode'] = $item['gLCode'];
                    $Adjustment['adjustmedLocalAmount'] = $item['adjustmentAmountLocal'];
                    $Adjustment['adjustmentRptAmount'] = $item['adjustmentAmountRpt'];
                }
                
                $budgetAdjustmentRepo->create($Adjustment);
                
            }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                Log::error($this->failed($e));
            }
        
        } else {
            Log::error('Budget Addition not found' . date('H:i:s'));
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

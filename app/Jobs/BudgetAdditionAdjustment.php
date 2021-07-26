<?php

namespace App\Jobs;

use App\Models\ErpBudgetAddition;
use App\Models\Budjetdetails;
use App\Models\ChartOfAccount;
use App\Models\CompanyFinanceYear;
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
            try {
                Log::info('Successfully start  budget_addition_adjustment' . date('H:i:s'));
                Log::info($budgetAddition);
                foreach ($budgetAddition->detail as $item) {
                    $conversion  = 1;
                   // $templateDetail = TemplatesDetails::find($item['templateDetailID']);
                    $templateDetail = ChartOfAccount::find($item['chartOfAccountSystemID']);
                    Log::info('Control Account System ID ' .$templateDetail->controlAccountsSystemID );
                    if(!empty($templateDetail) && $templateDetail->controlAccountsSystemID == 2){
                        $conversion = -1;
                    }

                     // Audit Trail Insert start
                     $Value = Budjetdetails::where('companySystemID', $budgetAddition->companySystemID)
                     ->where('serviceLineSystemID', $item['serviceLineSystemID'])
                     ->where('chartOfAccountID', $item['chartOfAccountSystemID'])
                     ->where('Year', $item['year'])
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
                     ->where('Year', $budgetAddition->year)
                     ->where('month','>=', date("m"))
                     ->get();

                    $TotalCount = count($BudgetDetails); 

                 
                    Log::info('Budget Details Total Count ' .$TotalCount);
                    Log::info('serviceLineSystemID ' .$item['serviceLineSystemID']);
                    Log::info('chartOfAccountID ' .$item['chartOfAccountSystemID']);
                    Log::info('Year ' .$budgetAddition->year);
                    Log::info('month ' . date("m"));
                    Log::info('conversion ' .  $conversion);

                    if($TotalCount > 0){
                        $toAddAmountRpt = round(($item['adjustmentAmountRpt']/$TotalCount),2);
                        $toAddAmountLocal = round(($item['adjustmentAmountLocal']/$TotalCount),3);

                        foreach ($BudgetDetails as $BudgetDetailVal){
                            Log::info('budjetAmtLocal conversion ' .  ((($BudgetDetailVal['budjetAmtLocal'] * $conversion)  + $toAddAmountLocal) * $conversion));
                            Log::info('To Amount Local ' .  $toAddAmountLocal);

                            $budjetdetailsRepo->update([
                                'budjetAmtLocal' => ((($BudgetDetailVal['budjetAmtLocal'] * $conversion)  + $toAddAmountLocal) * $conversion) ,
                                'budjetAmtRpt' => ((($BudgetDetailVal['budjetAmtRpt'] * $conversion) + $toAddAmountRpt) * $conversion)
                            ],
                                $BudgetDetailVal['budjetDetailsID']);
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
                Log::info('Successfully end  budget_addition_adjustment' . date('H:i:s'));
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

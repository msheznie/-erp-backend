<?php

namespace App\Jobs;

use App\Models\Months;
use App\Repositories\BudgetMasterRepository;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class AddBudgetDetails implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $budget;
    protected $glData;
    protected $monthArray;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($budget, $glData, $monthArray)
    {
        $this->budget = $budget;
        $this->glData = $glData;
        $this->monthArray = $monthArray;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(BudjetdetailsRepository $budgetDetailsRepo, BudgetMasterRepository $budgetMasterRepo)
    {
        $budgetMasters = $this->budget;
        $glData = $this->glData;
        $months = $this->monthArray;
        Log::useFiles(storage_path() . '/logs/budget_details_jobs.log');
        if ($budgetMasters) {

            foreach ($months as $month) {
                foreach ($glData as $gl) {
                    $detail = array(
                        'budgetmasterID' => $budgetMasters->budgetmasterID,
                        'companySystemID' => $budgetMasters->companySystemID,
                        'companyId' => $budgetMasters->companyID,
                        'companyFinanceYearID' => $budgetMasters->companyFinanceYearID,
                        'serviceLineSystemID' => $budgetMasters->serviceLineSystemID,
                        'serviceLine' => $budgetMasters->serviceLineCode,
                        'templateDetailID' => $gl['templateDetailID'],
                        'chartOfAccountID' => $gl['glAutoID'],
                        'glCode' => $gl['chart_of_account']['AccountCode'],
                        'glCodeType' => $gl['chart_of_account']['controlAccounts'],
                        'Year' => $month['year'],
                        'month' => $month['monthID'], //$budgetMasters->month,
                        'budjetAmtLocal' => 0,
                        'budjetAmtRpt' => 0,
                        'createdByUserSystemID' => $budgetMasters->createdByUserSystemID,
                        'createdByUserID' => $budgetMasters->createdByUserID
                    );
                    $budgetDetailsRepo->create($detail);
                }

                $budgets = $budgetMasterRepo->findWhere(['companySystemID' => $budgetMasters->companySystemID,
                    'serviceLineSystemID' => $budgetMasters->serviceLineSystemID,
                    'Year' => $budgetMasters->Year,
                    'templateMasterID' => $budgetMasters->templateMasterID,
                ]);

                $percentage = 8; //100 / 12;
                foreach ($budgets as $budget){
                    $updatedPercentage = ($budget->generateStatus + $percentage);

                    if( $updatedPercentage > 100){
                        $updatedPercentage = 100;
                    }

                    if($updatedPercentage == 96){
                        $updatedPercentage = 100;
                    }

                    $budgetMasterRepo->update(['generateStatus' => $updatedPercentage],$budget->budgetmasterID);
                }
            }

        }
    }
}

<?php

namespace App\Services;

use App\Models\DepartmentBudgetPlanning;
use App\Models\CompanyBudgetPlanningGenerate;
use App\Models\ReportTemplateDetails;
use App\Models\Company;
use App\Models\ReportTemplate;
use App\Models\BudgetMaster;
use Auth;
use App\Models\ChartOfAccount;
use App\Models\ReportTemplateLinks;
use App\Models\Budjetdetails;
use Illuminate\Support\Facades\DB;

/**
 * Class GenerateCompanyBudgetPlanningService
 * @package App\Services
 */
class GenerateCompanyBudgetPlanningService
{
    /**
     * Generate company budget planning for the given department budget planning.
     *
     * @param int $id Department budget planning ID
     * @param int $departmentID Department system ID
     * @return array
     */
    public function generate(string $rowId)
    {
        $cached = $this->validation($rowId);
        $payload = $cached->payload ?? [];

        $this->generateBudgetMasterData($payload);
    }

    private function generateBudgetMasterData(array $payload)
    {
        $company = Company::where('companySystemID', $payload['master_budget_plannings']['companySystemID'])->first();

        $budgetType = $payload['budgetType'] ?? '';
        $reportID = 2; // default OPEX
        if (stripos($budgetType, 'CAPEX') !== false) {
            $reportID = 1;
        } elseif (stripos($budgetType, 'OPEX') !== false) {
            $reportID = 2;
        }

        $budgetTemplate = ReportTemplate::where('companySystemID', $payload['master_budget_plannings']['companySystemID'])
                          ->where('isActive', 1)
                          ->where('isDefault', 1)
                          ->where('reportID', $reportID)
                          ->first();

        if(!$budgetTemplate) {
            throw new \Exception('Budget template not found');
        }

        try {
            DB::beginTransaction();

            $budgetArray = array(
                'documentSystemID' => 65,
                'documentID' => 'BUD',
                'companySystemID' => $payload['master_budget_plannings']['companySystemID'],
                'companyID' => $company->CompanyID,
                'companyFinanceYearID' => $payload['financeYear']['companyFinanceYearID'],
                'serviceLineSystemID' => $payload['segmentInfo']['serviceLineSystemID'],
                'serviceLineCode' => $payload['segmentInfo']['ServiceLineCode'],
                'templateMasterID' => $budgetTemplate->companyReportTemplateID,
                'Year' => $payload['yearID'],
                'month' => 1,
                'generateStatus' => 100,
                'createdByUserSystemID' => Auth::user()->employee->employeeSystemID,
                'createdByUserID' => Auth::user()->id,
                'createdDateTime' => \Helper::currentDateTime(),
                'sentNotificationAt' => 30,
                'cutOffPeriod' => 3,
                'budgetUploadID' => null
            );

            $budget = BudgetMaster::create($budgetArray);

            $glCodes = $payload['glAmounts'] ?? [];
            $glCodeData = [];

            foreach ($glCodes as $glCode) {

                $companyCurrencyConversion = \Helper::currencyConversion($company->companySystemID, $company->reportingCurrency, $company->reportingCurrency, ($glCode['request_amount'] /12));

                $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $glCode['chartOfAccountSystemID'])->first();
                $reportTemplateLink = ReportTemplateLinks::where('templateMasterID', $budget->templateMasterID)->where('glAutoID', $glCode['chartOfAccountSystemID'])->first();
                if ($reportTemplateLink) {
                    for ($i = 1; $i <= 12; $i++) {
                        $glCodeData[] = [
                            'budgetmasterID' => $budget->budgetmasterID,
                            'companySystemID' => $payload['master_budget_plannings']['companySystemID'],
                            'companyID' => $company->CompanyID,
                            'companyFinanceYearID' => $payload['financeYear']['companyFinanceYearID'],
                            'serviceLineSystemID' => $payload['segmentInfo']['serviceLineSystemID'],
                            'serviceLine' => $payload['segmentInfo']['ServiceLineCode'],
                            'templateDetailID' => $reportTemplateLink->templateDetailID,
                            'chartOfAccountID' => $glCode['chartOfAccountSystemID'],
                            'glCode' => $chartOfAccount->AccountCode,
                            'glCodeType' => $chartOfAccount->controlAccounts,
                            'Year' => $payload['yearID'],
                            'month' => $i,
                            'budjetAmtLocal' => ($chartOfAccount->controlAccountsSystemID == 3 || $chartOfAccount->controlAccountsSystemID == 2) ? (-1 * \Helper::formatNumberWithPrecision($companyCurrencyConversion['localAmount'])) / 12 : (\Helper::formatNumberWithPrecision($companyCurrencyConversion['localAmount'] / 12)),
                            'budjetAmtRpt' => ($chartOfAccount->controlAccountsSystemID == 3 || $chartOfAccount->controlAccountsSystemID == 2) ? (-1 * \Helper::formatNumberWithPrecision($glCode['request_amount'])) : (\Helper::formatNumberWithPrecision($glCode['request_amount'] / 12)),
                        ];
                    }
                }
            }

            if (!empty($glCodeData)) {
                Budjetdetails::insert($glCodeData);
            }

            $this->confirmDoument($budget);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * @return CompanyBudgetPlanningGenerate
     * @throws \Exception
     */
    private function validation(string $rowId): CompanyBudgetPlanningGenerate
    {
        $detailsTogenerate = CompanyBudgetPlanningGenerate::where('row_id', $rowId)->first();
        if (!$detailsTogenerate) {
            throw new \Exception('Budget generate detail row not found');
        }

        $payload = $detailsTogenerate->payload;

        $reportID = 2; // default OPEX
        if (stripos($payload['budgetType'], 'CAPEX') !== false) {
            $reportID = 1;
        } elseif (stripos($payload['budgetType'], 'OPEX') !== false) {
            $reportID = 2;
        }

        $budgetTemplate = ReportTemplate::where('companySystemID', $payload['master_budget_plannings']['companySystemID'])
                          ->where('isActive', 1)
                          ->where('isDefault', 1)
                          ->where('reportID', $reportID)
                          ->first();

        $checkBudgetMsaterExists = BudgetMaster::where('companySystemID', $payload['master_budget_plannings']['companySystemID'])
                                    ->where('documentSystemID', 65) 
                                    ->where('serviceLineSystemID', $payload['segmentInfo']['serviceLineSystemID'])
                                    ->where('templateMasterID', $budgetTemplate->companyReportTemplateID)
                                    ->where('Year', $payload['yearID'])
                                    ->where('month', 1)
                                    ->exists();

        
        if ($checkBudgetMsaterExists) {
            throw new \Exception('Budget master already exists');
        }

        return $detailsTogenerate;
    }

    private function confirmDoument(BudgetMaster $budget)
    {
        $params = array('autoID' => $budget->budgetmasterID,
            'company' => $budget->companySystemID,
            'document' => $budget->documentSystemID,
            'segment' => $budget->serviceLineSystemID,
            'category' => 0,
            'amount' => 0
        );

        $confirm = \Helper::confirmDocument($params);
        if (!$confirm["success"]) {
            throw new \Exception($confirm["message"]);
        }
    }
}

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
use App\Models\DocumentApproved;
use Illuminate\Support\Facades\DB;
use App\helper\Workflow\DocumentConfirm;
use App\helper\Workflow\DocumentApprove;
use App\helper\Helper;
use Carbon\Carbon;
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

        $this->generateBudgetMasterData($payload, $rowId);
    }

    /**
     * Run validation only for a row (no generate). Throws on failure.
     *
     * @param string $rowId
     * @return void
     * @throws \Exception
     */
    public function validateRow(string $rowId): void
    {
        $this->validation($rowId);
    }

    private function generateBudgetMasterData(array $payload, string $rowId)
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
                'Year' => Carbon::parse($payload['financeYear']['bigginingDate'])->year,
                'month' => 1,
                'generateStatus' => 100,
                'createdByUserSystemID' => Auth::user()->employee->employeeSystemID,
                'createdByUserID' => Auth::user()->id,
                'createdDateTime' => Helper::currentDateTime(),
                'sentNotificationAt' => 30,
                'cutOffPeriod' => 3,
                'budgetUploadID' => null
            );

            $budget = BudgetMaster::create($budgetArray);

            $glCodes = $payload['glAmounts'] ?? [];
            $glCodeData = [];

            foreach ($glCodes as $glCode) {

                $companyCurrencyConversion = Helper::currencyConversion($company->companySystemID, $company->localCurrencyID, $company->localCurrencyID, $glCode['request_amount']);

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
                            'Year' => Carbon::parse($payload['financeYear']['bigginingDate'])->year,
                            'month' => $i,
                            'budjetAmtLocal' => ($chartOfAccount->controlAccountsSystemID == 3 || $chartOfAccount->controlAccountsSystemID == 2) ? (-1 * Helper::formatNumberWithPrecision($companyCurrencyConversion['localAmount']))  : (Helper::formatNumberWithPrecision($companyCurrencyConversion['localAmount'])),
                            'budjetAmtRpt' => ($chartOfAccount->controlAccountsSystemID == 3 || $chartOfAccount->controlAccountsSystemID == 2) ? (-1 * Helper::formatNumberWithPrecision($companyCurrencyConversion['reportingAmount'])) : (Helper::formatNumberWithPrecision($companyCurrencyConversion['reportingAmount'])),
                        ];
                    }
                }
            }

            if (!empty($glCodeData)) {
                Budjetdetails::insert($glCodeData);
            }

            $this->confirmDoument($budget);

            $this->apporveDocument($budget);

            $detailsTogenerate = CompanyBudgetPlanningGenerate::where('row_id', $rowId)->first();
            $detailsTogenerate->budget_master_id = $budget->budgetmasterID;
            $detailsTogenerate->save();
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

        $typeMap = [
            1 => "OPEX",
            2 => "CAPEX",
            3 => (str_contains($payload['budgetType'], '-')) ? explode(' - ', $payload['budgetType'])[1] : null ,
        ];

       
        $type = $typeMap[(int) $payload['typeID']] ?? null;

        $budgetTemplate = ReportTemplate::where('companySystemID', $payload['master_budget_plannings']['companySystemID'])
                          ->where('isActive', 1)
                          ->where('isDefault', 1)
                          ->where('reportID', $reportID)
                          ->first();

        if(empty($budgetTemplate)) {
            throw new \Exception('Budget default template not found for '.$type);
        }

        $companySystemID = $payload['master_budget_plannings']['companySystemID'];
        $yearID = $payload['yearID'];
        $serviceLineSystemID = $payload['segmentInfo']['serviceLineSystemID'];
        $templateMasterID = $budgetTemplate->companyReportTemplateID;

        $existsForSegmentAndType = BudgetMaster::where('companySystemID', $companySystemID)
            ->where('documentSystemID', 65)
            ->where('companyFinanceYearID', $yearID)
            ->where('serviceLineSystemID', $serviceLineSystemID)
            ->where('templateMasterID', $templateMasterID)
            ->exists();

        if ($existsForSegmentAndType) {
            $errorMsg= 'A budget already exists in Draft/Open status for '.$payload['segment'].' - '.$payload['templateDescription'].' - '.$payload['financeYearDisplay'].'. Please review or delete the existing budget before generating';
            throw new \Exception($errorMsg);
        }
   

        $existsForBudgetYear = BudgetMaster::where('companySystemID', $companySystemID)
            ->where('documentSystemID', 65)
            ->where('serviceLineSystemID', $serviceLineSystemID)
            ->where('companyFinanceYearID', $yearID)
            ->exists();

        if ($existsForBudgetYear) {
            throw new \Exception('A budget for '.$type.' already exists for the financial year '.$payload['financeYearDisplay'].'. Common budget type cannot be initiated for the same period.');
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
            'amount' => 0,
            'isAutoCreateDocument' => true,
        );

        $confirm = DocumentConfirm::confirmDocument($params);
        if (!$confirm["success"]) {
            throw new \Exception($confirm["message"]);
        }
    }

    private function apporveDocument(BudgetMaster $budget)
    {
        $documentApproveds = DocumentApproved::where('documentSystemCode', $budget->budgetmasterID)->where('documentSystemID', $budget->documentSystemID)->get();

        foreach ($documentApproveds as $documentApproved)
        {
            $documentApproved["approvedComments"] = "Generated budget automatically through system";
            $documentApproved['documentSystemID'] = $budget->documentSystemID;
            $documentApproved['approvedDate'] = Helper::currentDateTime();
            $documentApproved['sendMail'] = false;
            $documentApproved['sendNotication'] = false;
            $documentApproved['isCheckPrivilages'] = false;
            $documentApproved['isAutoCreateDocument'] = true;
            $approval = DocumentApprove::approveDocument($documentApproved);
            
            if(!$approval['success'])
            {
                throw new \Exception('Document approval failed: ' . ($approval['message'] ?? 'Unknown error'));
            }
        }
    }
}

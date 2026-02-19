<?php

namespace App\Http\Controllers\API;

use App\Jobs\GenerateBudget;
use App\Services\GenerateCompanyBudgetPlanningService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CompanyBudgetPlanningGenerate;
use Response;

/**
 * Class CompanyBudgetPlanningGenerateAPIController
 * @package App\Http\Controllers\API
 */
class CompanyBudgetPlanningGenerateAPIController extends AppBaseController
{
    /** @var GenerateCompanyBudgetPlanningService */
    private $generateCompanyBudgetPlanningService;

    public function __construct(GenerateCompanyBudgetPlanningService $generateCompanyBudgetPlanningService)
    {
        $this->generateCompanyBudgetPlanningService = $generateCompanyBudgetPlanningService;
    }

    /**
     * Generate company budget planning.
     * POST generate-company-budget-planning
     *
     * @param Request $request
     * @return Response
     */
    public function generate(Request $request)
    {

        if (filter_var($request->input('bulkGenerate'), FILTER_VALIDATE_BOOLEAN)) {
            $budgetPlanningId =  null;

            $query = CompanyBudgetPlanningGenerate::where('is_generated', false);
            if ($budgetPlanningId !== null) {
                $query->where('company_budget_planning_id', $budgetPlanningId);
            }
            $pending = $query->orderBy('id')->get();

            $validationErrors = [];
            foreach ($pending as $row) {
                try {
                    $this->generateCompanyBudgetPlanningService->validateRow($row->row_id);
                } catch (\Exception $e) {
                    $payload = $row->payload ?? [];
                    $validationErrors[] = [
                        'rowId'   => $row->row_id,
                        'segment' => $payload['segment'] ?? '-',
                        'year'    => $payload['financeYearDisplay'] ?? ($payload['yearID'] ?? '-'),
                        'message' => $e->getMessage(),
                    ];
                }
            }

            if (!empty($validationErrors)) {
                return $this->sendAPIError('Validation failed', 422, $validationErrors);
            }

            GenerateBudget::dispatch($budgetPlanningId);
            return $this->sendResponse([], 'Budget generation job dispatched successfully.');
        }
        
        $validated = $request->validate([
            'id' => 'required',
        ]);

       $rowId = (string) $validated['id'];

       try {
            $this->generateCompanyBudgetPlanningService->generate($rowId);
            $data = CompanyBudgetPlanningGenerate::where('row_id', $rowId)->first();
            $data->is_generated = true;
            $data->save();
            return $this->sendResponse($data, 'Generate company budget planning request received.');

       } catch (\Exception $e) {
            return $this->sendError($e->getMessage(), 500);
       }
    }
}

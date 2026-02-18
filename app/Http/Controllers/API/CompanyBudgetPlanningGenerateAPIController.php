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
            $budgetPlanningId = null;
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

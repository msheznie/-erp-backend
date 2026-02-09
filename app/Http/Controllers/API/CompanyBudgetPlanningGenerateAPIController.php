<?php

namespace App\Http\Controllers\API;

use App\Services\GenerateCompanyBudgetPlanningService;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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
        $validated = $request->validate([
            'id' => 'required|integer',
            'departmentID' => 'required|integer',
        ]);

        $id = (int) $validated['id'];
        $departmentID = (int) $validated['departmentID'];

        $result = $this->generateCompanyBudgetPlanningService->generate($id, $departmentID);

        return $this->sendResponse($result, 'Generate company budget planning request received.');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTemplateAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateAPIRequest;
use App\Models\BudgetTemplate;
use App\Models\DepartmentBudgetTemplate;
use App\Repositories\BudgetTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BudgetTemplateController
 * @package App\Http\Controllers\API
 */

class BudgetTemplateAPIController extends AppBaseController
{
    /** @var  BudgetTemplateRepository */
    private $budgetTemplateRepository;

    public function __construct(BudgetTemplateRepository $budgetTemplateRepo)
    {
        $this->budgetTemplateRepository = $budgetTemplateRepo;
    }

    /**
     * Display a listing of the BudgetTemplate.
     * GET|HEAD /budget_templates
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->budgetTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetTemplates = $this->budgetTemplateRepository->all();

        return $this->sendResponse($budgetTemplates->toArray(), 'Budget Templates retrieved successfully');
    }

    /**
     * Store a newly created BudgetTemplate in storage.
     * POST /budget_templates
     *
     * @param CreateBudgetTemplateAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetTemplateAPIRequest $request)
    {
        $input = $request->all();
        
        // Set user ID for audit trail
        $input['createdUserSystemID'] = auth()->id();
        
        $budgetTemplate = $this->budgetTemplateRepository->create($input);

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template saved successfully');
    }

    /**
     * Display the specified BudgetTemplate.
     * GET|HEAD /budget_templates/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template retrieved successfully');
    }

    /**
     * Update the specified BudgetTemplate in storage.
     * PUT/PATCH /budget_templates/{id}
     *
     * @param  int $id
     * @param UpdateBudgetTemplateAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetTemplateAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        $companyDepartmentTemplate = DepartmentBudgetTemplate::where('budgetTemplateID', $id)->first();
        if ($companyDepartmentTemplate) {
            return $this->sendError('The template already assigned to the department cannot be amended');
        }

        // Set user ID for audit trail
        $input['modifiedUserSystemID'] = auth()->id();

        $budgetTemplate = $this->budgetTemplateRepository->update($input, $id);

        return $this->sendResponse($budgetTemplate->toArray(), 'Budget Template updated successfully');
    }

    /**
     * Remove the specified BudgetTemplate from storage.
     * DELETE /budget_templates/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BudgetTemplate $budgetTemplate */
        $budgetTemplate = $this->budgetTemplateRepository->find($id);

        if (empty($budgetTemplate)) {
            return $this->sendError('Budget Template not found');
        }

        $budgetTemplate->delete();

        return $this->sendResponse($id, 'Budget Template deleted successfully');
    }

    /**
     * Get all budget templates for DataTables
     *
     * @param Request $request
     * @return Response
     */
    public function getAllBudgetTemplates(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('isActive', 'type'));
        
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $budgetTemplates = $this->budgetTemplateRepository->budgetTemplateListQuery($request, $input, $search);

        return \DataTables::eloquent($budgetTemplates)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetTemplateID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    /**
     * Get form data for budget template
     *
     * @param Request $request
     * @return Response
     */
    public function getBudgetTemplateFormData(Request $request)
    {
        $data = [
            'typeOptions' => [
                ['value' => 1, 'label' => 'OPEX'],
                ['value' => 2, 'label' => 'CAPEX'],
                ['value' => 3, 'label' => 'Common']
            ]
        ];

        return $this->sendResponse($data, 'Budget Template form data retrieved successfully');
    }

    /**
     * Get budget templates by type (active only)
     *
     * @param string $type
     * @return Response
     */
    public function getBudgetTemplatesByType($type)
    {
        try {
            $templates = $this->budgetTemplateRepository->findWhere([
                'type' => $type,
                'isActive' => 1
            ]);

            return $this->sendResponse($templates, 'Budget templates retrieved successfully');
        } catch (Exception $e) {
            return $this->sendError('Error occurred while fetching budget templates', $e->getMessage());
        }
    }

    /**
     * Export budget templates
     *
     * @param Request $request
     * @return Response
     */
    public function exportBudgetTemplates(Request $request)
    {
        $input = $request->all();
        $search = $request->input('search.value');

        $budgetTemplates = $this->budgetTemplateRepository->budgetTemplateListQuery($request, $input, $search)->get();

        // Convert to export format
        $exportData = $budgetTemplates->map(function ($template) {
            $typeLabel = '';
            switch ($template->type) {
                case 1: $typeLabel = 'OPEX'; break;
                case 2: $typeLabel = 'CAPEX'; break;
                case 3: $typeLabel = 'Common'; break;
            }
            
            return [
                'Description' => $template->description,
                'Type' => $typeLabel,
                'Is Active' => $template->isActive ? 'Yes' : 'No',
                'Is Default' => $template->isDefault ? 'Yes' : 'No',
                'Created At' => $template->created_at ? $template->created_at->format('Y-m-d H:i:s') : '',
                'Updated At' => $template->updated_at ? $template->updated_at->format('Y-m-d H:i:s') : '',
            ];
        });

        return $this->sendResponse($exportData->toArray(), 'Budget Templates exported successfully');
    }
} 
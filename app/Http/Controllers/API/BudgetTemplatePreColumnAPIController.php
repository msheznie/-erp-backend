<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTemplatePreColumnAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplatePreColumnAPIRequest;
use App\Models\BudgetTemplatePreColumn;
use App\Repositories\BudgetTemplatePreColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

class BudgetTemplatePreColumnAPIController extends AppBaseController
{
    private $budgetTemplatePreColumnRepository;

    public function __construct(BudgetTemplatePreColumnRepository $budgetTemplatePreColumnRepo)
    {
        $this->budgetTemplatePreColumnRepository = $budgetTemplatePreColumnRepo;
    }

    /**
     * Display a listing of the BudgetTemplatePreColumn.
     * GET|HEAD /budgetTemplatePreColumns
     */
    public function index(Request $request)
    {
        $budgetTemplatePreColumns = $this->budgetTemplatePreColumnRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($budgetTemplatePreColumns->toArray(), trans('custom.budget_template_pre_columns_retrieved_successfully'));
    }

    /**
     * Store a newly created BudgetTemplatePreColumn in storage.
     * POST /budgetTemplatePreColumns
     */
    public function store(CreateBudgetTemplatePreColumnAPIRequest $request)
    {
        $input = $request->all();

        // Generate slug from column name if not provided
        if (empty($input['slug'])) {
            $input['slug'] = str_slug($input['columnName'], '_');
        }

        $input['isSystemPredefined'] = 0; // User created columns are not system predefined

        $budgetTemplatePreColumn = $this->budgetTemplatePreColumnRepository->create($input);

        return $this->sendResponse($budgetTemplatePreColumn->toArray(), trans('custom.budget_template_pre_column_saved_successfully'));
    }

    /**
     * Display the specified BudgetTemplatePreColumn.
     * GET|HEAD /budgetTemplatePreColumns/{id}
     */
    public function show($id)
    {
        $budgetTemplatePreColumn = $this->budgetTemplatePreColumnRepository->find($id);

        if (empty($budgetTemplatePreColumn)) {
            return $this->sendError(trans('custom.budget_template_pre_column_not_found'));
        }

        return $this->sendResponse($budgetTemplatePreColumn->toArray(), trans('custom.budget_template_pre_column_retrieved_successfully'));
    }

    /**
     * Update the specified BudgetTemplatePreColumn in storage.
     * PUT/PATCH /budgetTemplatePreColumns/{id}
     */
    public function update($id, UpdateBudgetTemplatePreColumnAPIRequest $request)
    {
        $budgetTemplatePreColumn = $this->budgetTemplatePreColumnRepository->find($id);

        if (empty($budgetTemplatePreColumn)) {
            return $this->sendError(trans('custom.budget_template_pre_column_not_found'));
        }

        // Prevent editing system predefined columns
        if ($budgetTemplatePreColumn->isSystemPredefined) {
            return $this->sendError(trans('custom.system_predefined_columns_cannot_be_edited'));
        }

        $input = $request->all();

        // Generate slug from column name if not provided
        if (empty($input['slug'])) {
            $input['slug'] = str_slug($input['columnName'], '_');
        }

        $budgetTemplatePreColumn = $this->budgetTemplatePreColumnRepository->update($input, $id);

        return $this->sendResponse($budgetTemplatePreColumn->toArray(), trans('custom.budgettemplateprecolumn_updated_successfully'));
    }

    /**
     * Remove the specified BudgetTemplatePreColumn from storage.
     * DELETE /budgetTemplatePreColumns/{id}
     */
    public function destroy($id)
    {
        $budgetTemplatePreColumn = $this->budgetTemplatePreColumnRepository->find($id);

        if (empty($budgetTemplatePreColumn)) {
            return $this->sendError(trans('custom.budget_template_pre_column_not_found'));
        }

        // Prevent deleting system predefined columns
        if ($budgetTemplatePreColumn->isSystemPredefined) {
            return $this->sendError(trans('custom.system_predefined_columns_cannot_be_deleted'));
        }

        $budgetTemplatePreColumn->delete();

        return $this->sendResponse($id, trans('custom.budget_template_pre_column_deleted_successfully'));
    }

    /**
     * Get available columns grouped by type
     * GET /budgetTemplatePreColumns/grouped
     */
    public function getAvailableColumnsGrouped()
    {
        $grouped = $this->budgetTemplatePreColumnRepository->getAvailableColumnsGrouped();

        return $this->sendResponse($grouped, trans('custom.available_columns_retrieved_successfully'));
    }

    /**
     * Get unassigned columns for a template
     * GET /budgetTemplatePreColumns/unassigned/{templateId}
     */
    public function getUnassignedColumns($templateId)
    {
        $columns = $this->budgetTemplatePreColumnRepository->getUnassignedColumns($templateId);

        return $this->sendResponse($columns->toArray(), trans('custom.unassigned_columns_retrieved_successfully'));
    }

    /**
     * Get column type options
     * GET /budgetTemplatePreColumns/column-types
     */
    public function getColumnTypeOptions()
    {
        $options = $this->budgetTemplatePreColumnRepository->getColumnTypeOptions();

        return $this->sendResponse($options, trans('custom.column_type_options_retrieved_successfully'));
    }
} 
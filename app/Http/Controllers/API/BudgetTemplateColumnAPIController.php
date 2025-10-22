<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTemplateColumnAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateColumnAPIRequest;
use App\Models\BudgetTemplateColumn;
use App\Repositories\BudgetTemplateColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use App\Traits\AuditLogsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BudgetTemplateColumnAPIController extends AppBaseController
{
    use AuditLogsTrait;
    
    private $budgetTemplateColumnRepository;

    public function __construct(BudgetTemplateColumnRepository $budgetTemplateColumnRepo)
    {
        $this->budgetTemplateColumnRepository = $budgetTemplateColumnRepo;
    }

    /**
     * Display a listing of the BudgetTemplateColumn.
     * GET|HEAD /budgetTemplateColumns
     */
    public function index(Request $request)
    {
        $budgetTemplateColumns = $this->budgetTemplateColumnRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($budgetTemplateColumns->toArray(), trans('custom.budget_template_columns_retrieved_successfully'));
    }

    /**
     * Store a newly created BudgetTemplateColumn in storage.
     * POST /budgetTemplateColumns
     */
    public function store(CreateBudgetTemplateColumnAPIRequest $request)
    {
        $input = $request->all();

        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->addToTemplate($input);

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $budgetTemplateColumn->templateColumnID, $uuid, "budget_template_columns", "Budget template column added", "C", $budgetTemplateColumn->toArray(), [], $budgetTemplateColumn->budgetTemplateID, 'budget_templates');

        return $this->sendResponse($budgetTemplateColumn->toArray(), trans('custom.budget_template_column_saved_successfully'));
    }

    /**
     * Display the specified BudgetTemplateColumn.
     * GET|HEAD /budgetTemplateColumns/{id}
     */
    public function show($id)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError(trans('custom.budget_template_column_not_found'));
        }

        $budgetTemplateColumn->load('preColumn');

        return $this->sendResponse($budgetTemplateColumn->toArray(), trans('custom.budget_template_column_retrieved_successfully'));
    }

    /**
     * Update the specified BudgetTemplateColumn in storage.
     * PUT/PATCH /budgetTemplateColumns/{id}
     */
    public function update($id, UpdateBudgetTemplateColumnAPIRequest $request)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError(trans('custom.budget_template_column_not_found'));
        }

        $oldValues = $budgetTemplateColumn->toArray();

        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->update($request->all(), $id);

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "budget_template_columns", "Budget template column updated", "U", $budgetTemplateColumn->toArray(), $oldValues, $budgetTemplateColumn->budgetTemplateID, 'budget_templates');

        return $this->sendResponse($budgetTemplateColumn->toArray(), trans('custom.budget_template_column_updated_successfully'));
    }

    /**
     * Remove the specified BudgetTemplateColumn from storage.
     * DELETE /budgetTemplateColumns/{id}
     */
    public function destroy($id, Request $request)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError(trans('custom.budget_template_column_not_found'));
        }

        $previousValue = $budgetTemplateColumn->toArray();

        // Check if this column is referenced in any formulas within the same template
        $referencingColumns = $this->budgetTemplateColumnRepository->getColumnsReferencingColumn(
            $budgetTemplateColumn->budgetTemplateID, 
            $id
        );

        if ($referencingColumns->count() > 0) {
            $columnNames = $referencingColumns->pluck('pre_column.columnName')->toArray();
            $columnNamesList = implode(', ', $columnNames);
            
            return $this->sendError(
                'Cannot delete this column because it is referenced in formulas by the following columns: ' . $columnNamesList . 
                '. Please remove the references from these formulas first.'
            );
        }

        $budgetTemplateColumn->delete();

        // Audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog($db, $id, $uuid, "budget_template_columns", "Budget template column deleted", "D", [], $previousValue, $previousValue['budgetTemplateID'], 'budget_templates');

        return $this->sendResponse($id, trans('custom.budget_template_column_deleted_successfully'));
    }

    /**
     * Get template columns with pre-column details
     * GET /budgetTemplateColumns/template/{templateId}
     */
    public function getTemplateColumns($templateId)
    {
        $columns = $this->budgetTemplateColumnRepository->getTemplateColumns($templateId);

        return $this->sendResponse($columns->toArray(), trans('custom.template_columns_retrieved_successfully'));
    }

    /**
     * Update sort order for template columns
     * POST /budgetTemplateColumns/template/{templateId}/sort-order
     */
    public function updateSortOrder($templateId, Request $request)
    {
        $sortOrderData = $request->get('sortOrder', []);

        $this->budgetTemplateColumnRepository->updateSortOrder($templateId, $sortOrderData);

        return $this->sendResponse([], trans('custom.sort_order_updated_successfully'));
    }

    /**
     * Remove column from template
     * DELETE /budgetTemplateColumns/template/{templateId}/column/{preColumnId}
     */
    public function removeFromTemplate($templateId, $preColumnId, Request $request)
    {
        // First, find the template column to get its templateColumnID
        $templateColumn = $this->budgetTemplateColumnRepository->getModel()
            ->where('budgetTemplateID', $templateId)
            ->where('preColumnID', $preColumnId)
            ->first();

        if (!$templateColumn) {
            return $this->sendError(trans('custom.column_not_found_in_template'));
        }

        // Check if this column is referenced in any formulas within the same template
        $referencingColumns = $this->budgetTemplateColumnRepository->getColumnsReferencingColumn(
            $templateId, 
            $templateColumn->templateColumnID
        );

        if ($referencingColumns->count() > 0) {
            $columnNames = $referencingColumns->pluck('pre_column.columnName')->toArray();
            $columnNamesList = implode(', ', $columnNames);
            
            return $this->sendError(
                'Cannot delete this column because it is referenced in formulas by the following columns: ' . $columnNamesList . 
                '. Please remove the references from these formulas first.'
            );
        }

        // Check if this column is linked to any link request amount, check in the budget template table
        $budgetTemplate = \App\Models\BudgetTemplate::where('linkRequestAmount', $templateColumn->templateColumnID)
                                        ->first();

        if ($budgetTemplate) {
            return $this->sendError(trans('custom.cannot_delete_this_column_because_it_is_linked_to_'));
        }

        $result = $this->budgetTemplateColumnRepository->removeFromTemplate($templateId, $preColumnId);

        if ($result) {
            // Audit log for remove from template
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $templateColumn->templateColumnID, $uuid, "budget_template_columns", "Budget template column removed from template", "D", [], $templateColumn->toArray(), $templateColumn->budgetTemplateID, 'budget_templates');

            return $this->sendResponse([], trans('custom.column_removed_from_template_successfully'));
        }

        return $this->sendError(trans('custom.failed_to_remove_column_from_template'));
    }

    /**
     * Get columns available for formula reference
     * GET /budgetTemplateColumns/template/{templateId}/formula-references/{excludeColumnId?}
     */
    public function getFormulaReferenceColumns($templateId, $excludeColumnId = null)
    {
        $columns = $this->budgetTemplateColumnRepository->getFormulaReferenceColumns($templateId, $excludeColumnId);

        return $this->sendResponse($columns->toArray(), trans('custom.formula_reference_columns_retrieved_successfully'));
    }
} 
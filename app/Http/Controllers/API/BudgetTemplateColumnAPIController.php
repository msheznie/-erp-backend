<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetTemplateColumnAPIRequest;
use App\Http\Requests\API\UpdateBudgetTemplateColumnAPIRequest;
use App\Models\BudgetTemplateColumn;
use App\Repositories\BudgetTemplateColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

class BudgetTemplateColumnAPIController extends AppBaseController
{
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

        return $this->sendResponse($budgetTemplateColumns->toArray(), 'Budget Template Columns retrieved successfully');
    }

    /**
     * Store a newly created BudgetTemplateColumn in storage.
     * POST /budgetTemplateColumns
     */
    public function store(CreateBudgetTemplateColumnAPIRequest $request)
    {
        $input = $request->all();

        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->addToTemplate($input);

        return $this->sendResponse($budgetTemplateColumn->toArray(), 'Budget Template Column saved successfully');
    }

    /**
     * Display the specified BudgetTemplateColumn.
     * GET|HEAD /budgetTemplateColumns/{id}
     */
    public function show($id)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError('Budget Template Column not found');
        }

        $budgetTemplateColumn->load('preColumn');

        return $this->sendResponse($budgetTemplateColumn->toArray(), 'Budget Template Column retrieved successfully');
    }

    /**
     * Update the specified BudgetTemplateColumn in storage.
     * PUT/PATCH /budgetTemplateColumns/{id}
     */
    public function update($id, UpdateBudgetTemplateColumnAPIRequest $request)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError('Budget Template Column not found');
        }

        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->update($request->all(), $id);

        return $this->sendResponse($budgetTemplateColumn->toArray(), 'Budget Template Column updated successfully');
    }

    /**
     * Remove the specified BudgetTemplateColumn from storage.
     * DELETE /budgetTemplateColumns/{id}
     */
    public function destroy($id)
    {
        $budgetTemplateColumn = $this->budgetTemplateColumnRepository->find($id);

        if (empty($budgetTemplateColumn)) {
            return $this->sendError('Budget Template Column not found');
        }

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

        return $this->sendResponse($id, 'Budget Template Column deleted successfully');
    }

    /**
     * Get template columns with pre-column details
     * GET /budgetTemplateColumns/template/{templateId}
     */
    public function getTemplateColumns($templateId)
    {
        $columns = $this->budgetTemplateColumnRepository->getTemplateColumns($templateId);

        return $this->sendResponse($columns->toArray(), 'Template columns retrieved successfully');
    }

    /**
     * Update sort order for template columns
     * POST /budgetTemplateColumns/template/{templateId}/sort-order
     */
    public function updateSortOrder($templateId, Request $request)
    {
        $sortOrderData = $request->get('sortOrder', []);

        $this->budgetTemplateColumnRepository->updateSortOrder($templateId, $sortOrderData);

        return $this->sendResponse([], 'Sort order updated successfully');
    }

    /**
     * Remove column from template
     * DELETE /budgetTemplateColumns/template/{templateId}/column/{preColumnId}
     */
    public function removeFromTemplate($templateId, $preColumnId)
    {
        // First, find the template column to get its templateColumnID
        $templateColumn = $this->budgetTemplateColumnRepository->getModel()
            ->where('budgetTemplateID', $templateId)
            ->where('preColumnID', $preColumnId)
            ->first();

        if (!$templateColumn) {
            return $this->sendError('Column not found in template');
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

        $result = $this->budgetTemplateColumnRepository->removeFromTemplate($templateId, $preColumnId);

        if ($result) {
            return $this->sendResponse([], 'Column removed from template successfully');
        }

        return $this->sendError('Failed to remove column from template');
    }

    /**
     * Get columns available for formula reference
     * GET /budgetTemplateColumns/template/{templateId}/formula-references/{excludeColumnId?}
     */
    public function getFormulaReferenceColumns($templateId, $excludeColumnId = null)
    {
        $columns = $this->budgetTemplateColumnRepository->getFormulaReferenceColumns($templateId, $excludeColumnId);

        return $this->sendResponse($columns->toArray(), 'Formula reference columns retrieved successfully');
    }
} 
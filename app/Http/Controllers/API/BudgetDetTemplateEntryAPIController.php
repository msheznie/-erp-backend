<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetDetTemplateEntryAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetTemplateEntryAPIRequest;
use App\Models\BudgetDetTemplateEntry;
use App\Repositories\BudgetDetTemplateEntryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class BudgetDetTemplateEntryController
 * @package App\Http\Controllers\API
 */

class BudgetDetTemplateEntryAPIController extends AppBaseController
{
    /** @var  BudgetDetTemplateEntryRepository */
    private $budgetDetTemplateEntryRepository;

    public function __construct(BudgetDetTemplateEntryRepository $budgetDetTemplateEntryRepo)
    {
        $this->budgetDetTemplateEntryRepository = $budgetDetTemplateEntryRepo;
    }

    /**
     * Display a listing of the BudgetDetTemplateEntry.
     * GET|HEAD /budgetDetTemplateEntries
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $budgetDetTemplateEntries = $this->budgetDetTemplateEntryRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($budgetDetTemplateEntries->toArray(), 'Budget Det Template Entries retrieved successfully');
    }

    /**
     * Store a newly created BudgetDetTemplateEntry in storage.
     * POST /budgetDetTemplateEntries
     *
     * @param CreateBudgetDetTemplateEntryAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetDetTemplateEntryAPIRequest $request)
    {
        $input = $request->all();

        $budgetDetTemplateEntry = $this->budgetDetTemplateEntryRepository->create($input);

        return $this->sendResponse($budgetDetTemplateEntry->toArray(), 'Budget Det Template Entry saved successfully');
    }

    /**
     * Display the specified BudgetDetTemplateEntry.
     * GET|HEAD /budgetDetTemplateEntries/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BudgetDetTemplateEntry $budgetDetTemplateEntry */
        $budgetDetTemplateEntry = $this->budgetDetTemplateEntryRepository->find($id);

        if (empty($budgetDetTemplateEntry)) {
            return $this->sendError('Budget Det Template Entry not found');
        }

        return $this->sendResponse($budgetDetTemplateEntry->toArray(), 'Budget Det Template Entry retrieved successfully');
    }

    /**
     * Update the specified BudgetDetTemplateEntry in storage.
     * PUT/PATCH /budgetDetTemplateEntries/{id}
     *
     * @param int $id
     * @param UpdateBudgetDetTemplateEntryAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetDetTemplateEntryAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetDetTemplateEntry $budgetDetTemplateEntry */
        $budgetDetTemplateEntry = $this->budgetDetTemplateEntryRepository->find($id);

        if (empty($budgetDetTemplateEntry)) {
            return $this->sendError('Budget Det Template Entry not found');
        }

        $budgetDetTemplateEntry = $this->budgetDetTemplateEntryRepository->update($input, $id);

        return $this->sendResponse($budgetDetTemplateEntry->toArray(), 'BudgetDetTemplateEntry updated successfully');
    }

    /**
     * Remove the specified BudgetDetTemplateEntry from storage.
     * DELETE /budgetDetTemplateEntries/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BudgetDetTemplateEntry $budgetDetTemplateEntry */
        $budgetDetTemplateEntry = $this->budgetDetTemplateEntryRepository->find($id);

        if (empty($budgetDetTemplateEntry)) {
            return $this->sendError('Budget Det Template Entry not found');
        }

        $budgetDetTemplateEntry->delete();

        return $this->sendSuccess('Budget Det Template Entry deleted successfully');
    }

    /**
     * Get entries by budget detail ID
     * GET /budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}
     *
     * @param int $budgetDetailId
     * @return Response
     */
    public function getByBudgetDetail($budgetDetailId)
    {
        $entries = $this->budgetDetTemplateEntryRepository->getByBudgetDetailId($budgetDetailId);

        return $this->sendResponse($entries->toArray(), 'Budget Det Template Entries retrieved successfully');
    }

    /**
     * Get entries by budget detail ID with pagination
     * GET /budgetDetTemplateEntries/byBudgetDetailPaginated/{budgetDetailId}
     *
     * @param int $budgetDetailId
     * @param Request $request
     * @return Response
     */
    public function getByBudgetDetailPaginated($budgetDetailId, Request $request)
    {
        $perPage = $request->get('per_page', 15);
        $entries = $this->budgetDetTemplateEntryRepository->getByBudgetDetailIdPaginated($budgetDetailId, $perPage);

        return $this->sendResponse($entries->toArray(), 'Budget Det Template Entries retrieved successfully');
    }

    /**
     * Delete entries by budget detail ID
     * DELETE /budgetDetTemplateEntries/byBudgetDetail/{budgetDetailId}
     *
     * @param int $budgetDetailId
     * @return Response
     */
    public function deleteByBudgetDetail($budgetDetailId)
    {
        $deleted = $this->budgetDetTemplateEntryRepository->deleteByBudgetDetailId($budgetDetailId);

        if ($deleted) {
            return $this->sendSuccess('Budget Det Template Entries deleted successfully');
        }

        return $this->sendError('Failed to delete Budget Det Template Entries');
    }
} 
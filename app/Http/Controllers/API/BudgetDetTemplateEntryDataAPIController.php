<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetDetTemplateEntryDataAPIRequest;
use App\Http\Requests\API\UpdateBudgetDetTemplateEntryDataAPIRequest;
use App\Models\BudgetDetTemplateEntryData;
use App\Models\ItemMaster;
use App\Repositories\BudgetDetTemplateEntryDataRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;

/**
 * Class BudgetDetTemplateEntryDataController
 * @package App\Http\Controllers\API
 */

class BudgetDetTemplateEntryDataAPIController extends AppBaseController
{
    /** @var  BudgetDetTemplateEntryDataRepository */
    private $budgetDetTemplateEntryDataRepository;

    public function __construct(BudgetDetTemplateEntryDataRepository $budgetDetTemplateEntryDataRepo)
    {
        $this->budgetDetTemplateEntryDataRepository = $budgetDetTemplateEntryDataRepo;
    }

    /**
     * Display a listing of the BudgetDetTemplateEntryData.
     * GET|HEAD /budgetDetTemplateEntryData
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->all(
            $request->except(['skip', 'limit']),
            $request->get('skip'),
            $request->get('limit')
        );

        return $this->sendResponse($budgetDetTemplateEntryData->toArray(), trans('custom.budget_det_template_entry_data_retrieved_successfu'));
    }

    /**
     * Store a newly created BudgetDetTemplateEntryData in storage.
     * POST /budgetDetTemplateEntryData
     *
     * @param CreateBudgetDetTemplateEntryDataAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateBudgetDetTemplateEntryDataAPIRequest $request)
    {
        $input = $request->all();

        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->create($input);

        return $this->sendResponse($budgetDetTemplateEntryData->toArray(), trans('custom.budget_det_template_entry_data_saved_successfully'));
    }

    /**
     * Display the specified BudgetDetTemplateEntryData.
     * GET|HEAD /budgetDetTemplateEntryData/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var BudgetDetTemplateEntryData $budgetDetTemplateEntryData */
        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->find($id);

        if (empty($budgetDetTemplateEntryData)) {
            return $this->sendError(trans('custom.budget_det_template_entry_data_not_found'));
        }

        return $this->sendResponse($budgetDetTemplateEntryData->toArray(), trans('custom.budget_det_template_entry_data_retrieved_successfu'));
    }

    /**
     * Update the specified BudgetDetTemplateEntryData in storage.
     * PUT/PATCH /budgetDetTemplateEntryData/{id}
     *
     * @param int $id
     * @param UpdateBudgetDetTemplateEntryDataAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBudgetDetTemplateEntryDataAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetDetTemplateEntryData $budgetDetTemplateEntryData */
        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->find($id);

        if (empty($budgetDetTemplateEntryData)) {
            return $this->sendError(trans('custom.budget_det_template_entry_data_not_found'));
        }

        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->update($input, $id);

        return $this->sendResponse($budgetDetTemplateEntryData->toArray(), trans('custom.budgetdettemplateentrydata_updated_successfully'));
    }

    /**
     * Remove the specified BudgetDetTemplateEntryData from storage.
     * DELETE /budgetDetTemplateEntryData/{id}
     *
     * @param int $id
     *
     * @throws \Exception
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var BudgetDetTemplateEntryData $budgetDetTemplateEntryData */
        $budgetDetTemplateEntryData = $this->budgetDetTemplateEntryDataRepository->find($id);

        if (empty($budgetDetTemplateEntryData)) {
            return $this->sendError(trans('custom.budget_det_template_entry_data_not_found'));
        }

        $budgetDetTemplateEntryData->delete();

        return $this->sendSuccess('Budget Det Template Entry Data deleted successfully');
    }

    /**
     * Get data by entry ID
     * GET /budgetDetTemplateEntryData/byEntry/{entryID}
     *
     * @param int $entryID
     * @return Response
     */
    public function getByEntry($entryID)
    {
        $data = $this->budgetDetTemplateEntryDataRepository->getByEntryId($entryID);

        return $this->sendResponse($data->toArray(), trans('custom.budget_det_template_entry_data_retrieved_successfu'));
    }

    /**
     * Get data by template column ID
     * GET /budgetDetTemplateEntryData/byTemplateColumn/{templateColumnID}
     *
     * @param int $templateColumnID
     * @return Response
     */
    public function getByTemplateColumn($templateColumnID)
    {
        $data = $this->budgetDetTemplateEntryDataRepository->getByTemplateColumnId($templateColumnID);

        return $this->sendResponse($data->toArray(), trans('custom.budget_det_template_entry_data_retrieved_successfu'));
    }

    /**
     * Update or create data for a specific entry and template column
     * POST /budgetDetTemplateEntryData/updateOrCreate
     *
     * @param Request $request
     * @return Response
     */
    public function updateOrCreate(Request $request)
    {
        $request->validate([
            'entryID' => 'required|integer|exists:budget_det_template_entries,entryID',
            'templateColumnID' => 'required|integer|exists:budget_template_columns,templateColumnID',
            'value' => 'nullable|string'
        ]);

        $data = $this->budgetDetTemplateEntryDataRepository->updateOrCreate(
            $request->entryID,
            $request->templateColumnID,
            $request->value
        );

        return $this->sendResponse($data->toArray(), trans('custom.budget_det_template_entry_data_updatedcreated_succ'));
    }

    /**
     * Get data by multiple entry IDs
     * POST /budgetDetTemplateEntryData/byEntryIds
     *
     * @param Request $request
     * @return Response
     */
    public function getByEntryIds(Request $request)
    {
        $request->validate([
            'entryIDs' => 'required|array',
            'entryIDs.*' => 'integer|exists:budget_det_template_entries,entryID'
        ]);

        $data = $this->budgetDetTemplateEntryDataRepository->getByEntryIds($request->entryIDs);

        return $this->sendResponse($data->toArray(), trans('custom.budget_det_template_entry_data_retrieved_successfu'));
    }

    public function getItemsForBudgetPlanningTemplateDetails(Request $request) {
        $input = $request->all();

        $items = ItemMaster::where('primaryCompanySystemID', $input['companyId'])->where('unit', $input['unitId'])->where('isActive', 1)->where('itemApprovedYN', 1)->get();

        return $this->sendResponse($items->toArray(), trans('custom.items_retrieved_successfully'));
    }
} 

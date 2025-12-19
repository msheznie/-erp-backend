<?php
/**
=============================================
-- File Name : YesNoSelectionAPIController.php
-- Project Name : ERP
-- Module Name :  Yes No Selection
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for  Yes No Selection
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateYesNoSelectionAPIRequest;
use App\Http\Requests\API\UpdateYesNoSelectionAPIRequest;
use App\Models\YesNoSelection;
use App\Repositories\YesNoSelectionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class YesNoSelectionController
 * @package App\Http\Controllers\API
 */

class YesNoSelectionAPIController extends AppBaseController
{
    /** @var  YesNoSelectionRepository */
    private $yesNoSelectionRepository;

    public function __construct(YesNoSelectionRepository $yesNoSelectionRepo)
    {
        $this->yesNoSelectionRepository = $yesNoSelectionRepo;
    }

    /**
     * Display a listing of the YesNoSelection.
     * GET|HEAD /yesNoSelections
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->yesNoSelectionRepository->pushCriteria(new RequestCriteria($request));
        $this->yesNoSelectionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $yesNoSelections = $this->yesNoSelectionRepository->all();

        return $this->sendResponse($yesNoSelections->toArray(), trans('custom.yes_no_selections_retrieved_successfully'));
    }

    /**
     * Store a newly created YesNoSelection in storage.
     * POST /yesNoSelections
     *
     * @param CreateYesNoSelectionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateYesNoSelectionAPIRequest $request)
    {
        $input = $request->all();

        $yesNoSelections = $this->yesNoSelectionRepository->create($input);

        return $this->sendResponse($yesNoSelections->toArray(), trans('custom.yes_no_selection_saved_successfully'));
    }

    /**
     * Display the specified YesNoSelection.
     * GET|HEAD /yesNoSelections/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var YesNoSelection $yesNoSelection */
        $yesNoSelection = $this->yesNoSelectionRepository->findWithoutFail($id);

        if (empty($yesNoSelection)) {
            return $this->sendError(trans('custom.yes_no_selection_not_found'));
        }

        return $this->sendResponse($yesNoSelection->toArray(), trans('custom.yes_no_selection_retrieved_successfully'));
    }

    /**
     * Update the specified YesNoSelection in storage.
     * PUT/PATCH /yesNoSelections/{id}
     *
     * @param  int $id
     * @param UpdateYesNoSelectionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateYesNoSelectionAPIRequest $request)
    {
        $input = $request->all();

        /** @var YesNoSelection $yesNoSelection */
        $yesNoSelection = $this->yesNoSelectionRepository->findWithoutFail($id);

        if (empty($yesNoSelection)) {
            return $this->sendError(trans('custom.yes_no_selection_not_found'));
        }

        $yesNoSelection = $this->yesNoSelectionRepository->update($input, $id);

        return $this->sendResponse($yesNoSelection->toArray(), trans('custom.yesnoselection_updated_successfully'));
    }

    /**
     * Remove the specified YesNoSelection from storage.
     * DELETE /yesNoSelections/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var YesNoSelection $yesNoSelection */
        $yesNoSelection = $this->yesNoSelectionRepository->findWithoutFail($id);

        if (empty($yesNoSelection)) {
            return $this->sendError(trans('custom.yes_no_selection_not_found'));
        }

        $yesNoSelection->delete();

        return $this->sendResponse($id, trans('custom.yes_no_selection_deleted_successfully'));
    }
}

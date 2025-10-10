<?php
/**
 * =============================================
 * -- File Name : YesNoSelectionForMinusAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  YesNoSelectionForMinus
 * -- Author : Mohamed Fayas
 * -- Create date : 27 - March 2018
 * -- Description : This file contains the all CRUD for  YesNoSelectionForMinus
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateYesNoSelectionForMinusAPIRequest;
use App\Http\Requests\API\UpdateYesNoSelectionForMinusAPIRequest;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\YesNoSelectionForMinusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class YesNoSelectionForMinusController
 * @package App\Http\Controllers\API
 */

class YesNoSelectionForMinusAPIController extends AppBaseController
{
    /** @var  YesNoSelectionForMinusRepository */
    private $yesNoSelectionForMinusRepository;

    public function __construct(YesNoSelectionForMinusRepository $yesNoSelectionForMinusRepo)
    {
        $this->yesNoSelectionForMinusRepository = $yesNoSelectionForMinusRepo;
    }

    /**
     * Display a listing of the YesNoSelectionForMinus.
     * GET|HEAD /yesNoSelectionForMinuses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->yesNoSelectionForMinusRepository->pushCriteria(new RequestCriteria($request));
        $this->yesNoSelectionForMinusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $yesNoSelectionForMinuses = $this->yesNoSelectionForMinusRepository->all();

        return $this->sendResponse($yesNoSelectionForMinuses->toArray(), trans('custom.yes_no_selection_for_minuses_retrieved_successfull'));
    }

    /**
     * Store a newly created YesNoSelectionForMinus in storage.
     * POST /yesNoSelectionForMinuses
     *
     * @param CreateYesNoSelectionForMinusAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateYesNoSelectionForMinusAPIRequest $request)
    {
        $input = $request->all();

        $yesNoSelectionForMinuses = $this->yesNoSelectionForMinusRepository->create($input);

        return $this->sendResponse($yesNoSelectionForMinuses->toArray(), trans('custom.yes_no_selection_for_minus_saved_successfully'));
    }

    /**
     * Display the specified YesNoSelectionForMinus.
     * GET|HEAD /yesNoSelectionForMinuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var YesNoSelectionForMinus $yesNoSelectionForMinus */
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            return $this->sendError(trans('custom.yes_no_selection_for_minus_not_found'));
        }

        return $this->sendResponse($yesNoSelectionForMinus->toArray(), trans('custom.yes_no_selection_for_minus_retrieved_successfully'));
    }

    /**
     * Update the specified YesNoSelectionForMinus in storage.
     * PUT/PATCH /yesNoSelectionForMinuses/{id}
     *
     * @param  int $id
     * @param UpdateYesNoSelectionForMinusAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateYesNoSelectionForMinusAPIRequest $request)
    {
        $input = $request->all();

        /** @var YesNoSelectionForMinus $yesNoSelectionForMinus */
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            return $this->sendError(trans('custom.yes_no_selection_for_minus_not_found'));
        }

        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->update($input, $id);

        return $this->sendResponse($yesNoSelectionForMinus->toArray(), trans('custom.yesnoselectionforminus_updated_successfully'));
    }

    /**
     * Remove the specified YesNoSelectionForMinus from storage.
     * DELETE /yesNoSelectionForMinuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var YesNoSelectionForMinus $yesNoSelectionForMinus */
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            return $this->sendError(trans('custom.yes_no_selection_for_minus_not_found'));
        }

        $yesNoSelectionForMinus->delete();

        return $this->sendResponse($id, trans('custom.yes_no_selection_for_minus_deleted_successfully'));
    }
}

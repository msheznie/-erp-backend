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

        return $this->sendResponse($yesNoSelectionForMinuses->toArray(), 'Yes No Selection For Minuses retrieved successfully');
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

        return $this->sendResponse($yesNoSelectionForMinuses->toArray(), 'Yes No Selection For Minus saved successfully');
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
            return $this->sendError('Yes No Selection For Minus not found');
        }

        return $this->sendResponse($yesNoSelectionForMinus->toArray(), 'Yes No Selection For Minus retrieved successfully');
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
            return $this->sendError('Yes No Selection For Minus not found');
        }

        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->update($input, $id);

        return $this->sendResponse($yesNoSelectionForMinus->toArray(), 'YesNoSelectionForMinus updated successfully');
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
            return $this->sendError('Yes No Selection For Minus not found');
        }

        $yesNoSelectionForMinus->delete();

        return $this->sendResponse($id, 'Yes No Selection For Minus deleted successfully');
    }
}

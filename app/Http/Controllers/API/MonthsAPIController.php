<?php
/**
 * =============================================
 * -- File Name : MonthsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Months
 * -- Author : Mohamed Fayas
 * -- Create date : 27 - March 2018
 * -- Description : This file contains the all CRUD for  Months
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateMonthsAPIRequest;
use App\Http\Requests\API\UpdateMonthsAPIRequest;
use App\Models\Months;
use App\Repositories\MonthsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MonthsController
 * @package App\Http\Controllers\API
 */

class MonthsAPIController extends AppBaseController
{
    /** @var  MonthsRepository */
    private $monthsRepository;

    public function __construct(MonthsRepository $monthsRepo)
    {
        $this->monthsRepository = $monthsRepo;
    }

    /**
     * Display a listing of the Months.
     * GET|HEAD /months
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->monthsRepository->pushCriteria(new RequestCriteria($request));
        $this->monthsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $months = $this->monthsRepository->all();

        return $this->sendResponse($months->toArray(), trans('custom.months_retrieved_successfully'));
    }

    /**
     * Store a newly created Months in storage.
     * POST /months
     *
     * @param CreateMonthsAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateMonthsAPIRequest $request)
    {
        $input = $request->all();

        $months = $this->monthsRepository->create($input);

        return $this->sendResponse($months->toArray(), trans('custom.months_saved_successfully'));
    }

    /**
     * Display the specified Months.
     * GET|HEAD /months/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var Months $months */
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            return $this->sendError(trans('custom.months_not_found'));
        }

        return $this->sendResponse($months->toArray(), trans('custom.months_retrieved_successfully'));
    }

    /**
     * Update the specified Months in storage.
     * PUT/PATCH /months/{id}
     *
     * @param  int $id
     * @param UpdateMonthsAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateMonthsAPIRequest $request)
    {
        $input = $request->all();

        /** @var Months $months */
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            return $this->sendError(trans('custom.months_not_found'));
        }

        $months = $this->monthsRepository->update($input, $id);

        return $this->sendResponse($months->toArray(), trans('custom.months_updated_successfully'));
    }

    /**
     * Remove the specified Months from storage.
     * DELETE /months/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var Months $months */
        $months = $this->monthsRepository->findWithoutFail($id);

        if (empty($months)) {
            return $this->sendError(trans('custom.months_not_found'));
        }

        $months->delete();

        return $this->sendResponse($id, trans('custom.months_deleted_successfully'));
    }
}

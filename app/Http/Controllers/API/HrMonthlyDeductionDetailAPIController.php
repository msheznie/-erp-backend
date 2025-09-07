<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateHrMonthlyDeductionDetailAPIRequest;
use App\Http\Requests\API\UpdateHrMonthlyDeductionDetailAPIRequest;
use App\Models\HrMonthlyDeductionDetail;
use App\Repositories\HrMonthlyDeductionDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class HrMonthlyDeductionDetailController
 * @package App\Http\Controllers\API
 */

class HrMonthlyDeductionDetailAPIController extends AppBaseController
{
    /** @var  HrMonthlyDeductionDetailRepository */
    private $hrMonthlyDeductionDetailRepository;

    public function __construct(HrMonthlyDeductionDetailRepository $hrMonthlyDeductionDetailRepo)
    {
        $this->hrMonthlyDeductionDetailRepository = $hrMonthlyDeductionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrMonthlyDeductionDetails",
     *      summary="Get a listing of the HrMonthlyDeductionDetails.",
     *      tags={"HrMonthlyDeductionDetail"},
     *      description="Get all HrMonthlyDeductionDetails",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/HrMonthlyDeductionDetail")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->hrMonthlyDeductionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->hrMonthlyDeductionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $hrMonthlyDeductionDetails = $this->hrMonthlyDeductionDetailRepository->all();

        return $this->sendResponse($hrMonthlyDeductionDetails->toArray(), trans('custom.hr_monthly_deduction_details_retrieved_successfull'));
    }

    /**
     * @param CreateHrMonthlyDeductionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/hrMonthlyDeductionDetails",
     *      summary="Store a newly created HrMonthlyDeductionDetail in storage",
     *      tags={"HrMonthlyDeductionDetail"},
     *      description="Store HrMonthlyDeductionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrMonthlyDeductionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrMonthlyDeductionDetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/HrMonthlyDeductionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateHrMonthlyDeductionDetailAPIRequest $request)
    {
        $input = $request->all();

        $hrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepository->create($input);

        return $this->sendResponse($hrMonthlyDeductionDetail->toArray(), trans('custom.hr_monthly_deduction_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/hrMonthlyDeductionDetails/{id}",
     *      summary="Display the specified HrMonthlyDeductionDetail",
     *      tags={"HrMonthlyDeductionDetail"},
     *      description="Get HrMonthlyDeductionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/HrMonthlyDeductionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var HrMonthlyDeductionDetail $hrMonthlyDeductionDetail */
        $hrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionDetail)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_detail_not_found'));
        }

        return $this->sendResponse($hrMonthlyDeductionDetail->toArray(), trans('custom.hr_monthly_deduction_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateHrMonthlyDeductionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/hrMonthlyDeductionDetails/{id}",
     *      summary="Update the specified HrMonthlyDeductionDetail in storage",
     *      tags={"HrMonthlyDeductionDetail"},
     *      description="Update HrMonthlyDeductionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="HrMonthlyDeductionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/HrMonthlyDeductionDetail")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/HrMonthlyDeductionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateHrMonthlyDeductionDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var HrMonthlyDeductionDetail $hrMonthlyDeductionDetail */
        $hrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionDetail)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_detail_not_found'));
        }

        $hrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepository->update($input, $id);

        return $this->sendResponse($hrMonthlyDeductionDetail->toArray(), trans('custom.hrmonthlydeductiondetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/hrMonthlyDeductionDetails/{id}",
     *      summary="Remove the specified HrMonthlyDeductionDetail from storage",
     *      tags={"HrMonthlyDeductionDetail"},
     *      description="Delete HrMonthlyDeductionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of HrMonthlyDeductionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var HrMonthlyDeductionDetail $hrMonthlyDeductionDetail */
        $hrMonthlyDeductionDetail = $this->hrMonthlyDeductionDetailRepository->findWithoutFail($id);

        if (empty($hrMonthlyDeductionDetail)) {
            return $this->sendError(trans('custom.hr_monthly_deduction_detail_not_found'));
        }

        $hrMonthlyDeductionDetail->delete();

        return $this->sendSuccess('Hr Monthly Deduction Detail deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowReportDetailAPIRequest;
use App\Http\Requests\API\UpdateCashFlowReportDetailAPIRequest;
use App\Models\CashFlowReportDetail;
use App\Repositories\CashFlowReportDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CashFlowReportDetailController
 * @package App\Http\Controllers\API
 */

class CashFlowReportDetailAPIController extends AppBaseController
{
    /** @var  CashFlowReportDetailRepository */
    private $cashFlowReportDetailRepository;

    public function __construct(CashFlowReportDetailRepository $cashFlowReportDetailRepo)
    {
        $this->cashFlowReportDetailRepository = $cashFlowReportDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReportDetails",
     *      summary="Get a listing of the CashFlowReportDetails.",
     *      tags={"CashFlowReportDetail"},
     *      description="Get all CashFlowReportDetails",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowReportDetail")
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
        $this->cashFlowReportDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowReportDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowReportDetails = $this->cashFlowReportDetailRepository->all();

        return $this->sendResponse($cashFlowReportDetails->toArray(), trans('custom.cash_flow_report_details_retrieved_successfully'));
    }

    /**
     * @param CreateCashFlowReportDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowReportDetails",
     *      summary="Store a newly created CashFlowReportDetail in storage",
     *      tags={"CashFlowReportDetail"},
     *      description="Store CashFlowReportDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReportDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReportDetail")
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
     *                  ref="#/definitions/CashFlowReportDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowReportDetailAPIRequest $request)
    {
        $input = $request->all();

        $cashFlowReportDetail = $this->cashFlowReportDetailRepository->create($input);

        return $this->sendResponse($cashFlowReportDetail->toArray(), trans('custom.cash_flow_report_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowReportDetails/{id}",
     *      summary="Display the specified CashFlowReportDetail",
     *      tags={"CashFlowReportDetail"},
     *      description="Get CashFlowReportDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReportDetail",
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
     *                  ref="#/definitions/CashFlowReportDetail"
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
        /** @var CashFlowReportDetail $cashFlowReportDetail */
        $cashFlowReportDetail = $this->cashFlowReportDetailRepository->findWithoutFail($id);

        if (empty($cashFlowReportDetail)) {
            return $this->sendError(trans('custom.cash_flow_report_detail_not_found'));
        }

        return $this->sendResponse($cashFlowReportDetail->toArray(), trans('custom.cash_flow_report_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCashFlowReportDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowReportDetails/{id}",
     *      summary="Update the specified CashFlowReportDetail in storage",
     *      tags={"CashFlowReportDetail"},
     *      description="Update CashFlowReportDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReportDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowReportDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowReportDetail")
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
     *                  ref="#/definitions/CashFlowReportDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowReportDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowReportDetail $cashFlowReportDetail */
        $cashFlowReportDetail = $this->cashFlowReportDetailRepository->findWithoutFail($id);

        if (empty($cashFlowReportDetail)) {
            return $this->sendError(trans('custom.cash_flow_report_detail_not_found'));
        }

        $cashFlowReportDetail = $this->cashFlowReportDetailRepository->update($input, $id);

        return $this->sendResponse($cashFlowReportDetail->toArray(), trans('custom.cashflowreportdetail_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowReportDetails/{id}",
     *      summary="Remove the specified CashFlowReportDetail from storage",
     *      tags={"CashFlowReportDetail"},
     *      description="Delete CashFlowReportDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowReportDetail",
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
        /** @var CashFlowReportDetail $cashFlowReportDetail */
        $cashFlowReportDetail = $this->cashFlowReportDetailRepository->findWithoutFail($id);

        if (empty($cashFlowReportDetail)) {
            return $this->sendError(trans('custom.cash_flow_report_detail_not_found'));
        }

        $cashFlowReportDetail->delete();

        return $this->sendSuccess('Cash Flow Report Detail deleted successfully');
    }
}

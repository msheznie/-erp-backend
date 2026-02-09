<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChartOfAccountAllocationDetailHistoryAPIRequest;
use App\Http\Requests\API\UpdateChartOfAccountAllocationDetailHistoryAPIRequest;
use App\Models\ChartOfAccountAllocationDetailHistory;
use App\Repositories\ChartOfAccountAllocationDetailHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChartOfAccountAllocationDetailHistoryController
 * @package App\Http\Controllers\API
 */

class ChartOfAccountAllocationDetailHistoryAPIController extends AppBaseController
{
    /** @var  ChartOfAccountAllocationDetailHistoryRepository */
    private $chartOfAccountAllocationDetailHistoryRepository;

    public function __construct(ChartOfAccountAllocationDetailHistoryRepository $chartOfAccountAllocationDetailHistoryRepo)
    {
        $this->chartOfAccountAllocationDetailHistoryRepository = $chartOfAccountAllocationDetailHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationDetailHistories",
     *      summary="Get a listing of the ChartOfAccountAllocationDetailHistories.",
     *      tags={"ChartOfAccountAllocationDetailHistory"},
     *      description="Get all ChartOfAccountAllocationDetailHistories",
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
     *                  @SWG\Items(ref="#/definitions/ChartOfAccountAllocationDetailHistory")
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
        $this->chartOfAccountAllocationDetailHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->chartOfAccountAllocationDetailHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chartOfAccountAllocationDetailHistories = $this->chartOfAccountAllocationDetailHistoryRepository->all();

        return $this->sendResponse($chartOfAccountAllocationDetailHistories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
    }

    /**
     * @param CreateChartOfAccountAllocationDetailHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chartOfAccountAllocationDetailHistories",
     *      summary="Store a newly created ChartOfAccountAllocationDetailHistory in storage",
     *      tags={"ChartOfAccountAllocationDetailHistory"},
     *      description="Store ChartOfAccountAllocationDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationDetailHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationDetailHistory")
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetailHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChartOfAccountAllocationDetailHistoryAPIRequest $request)
    {
        $input = $request->all();

        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->create($input);

        return $this->sendResponse($chartOfAccountAllocationDetailHistory->toArray(), trans('custom.save', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chartOfAccountAllocationDetailHistories/{id}",
     *      summary="Display the specified ChartOfAccountAllocationDetailHistory",
     *      tags={"ChartOfAccountAllocationDetailHistory"},
     *      description="Get ChartOfAccountAllocationDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetailHistory",
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetailHistory"
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
        /** @var ChartOfAccountAllocationDetailHistory $chartOfAccountAllocationDetailHistory */
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
        }

        return $this->sendResponse($chartOfAccountAllocationDetailHistory->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
    }

    /**
     * @param int $id
     * @param UpdateChartOfAccountAllocationDetailHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chartOfAccountAllocationDetailHistories/{id}",
     *      summary="Update the specified ChartOfAccountAllocationDetailHistory in storage",
     *      tags={"ChartOfAccountAllocationDetailHistory"},
     *      description="Update ChartOfAccountAllocationDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetailHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChartOfAccountAllocationDetailHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChartOfAccountAllocationDetailHistory")
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
     *                  ref="#/definitions/ChartOfAccountAllocationDetailHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChartOfAccountAllocationDetailHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChartOfAccountAllocationDetailHistory $chartOfAccountAllocationDetailHistory */
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
        }

        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->update($input, $id);

        return $this->sendResponse($chartOfAccountAllocationDetailHistory->toArray(), trans('custom.update', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chartOfAccountAllocationDetailHistories/{id}",
     *      summary="Remove the specified ChartOfAccountAllocationDetailHistory from storage",
     *      tags={"ChartOfAccountAllocationDetailHistory"},
     *      description="Delete ChartOfAccountAllocationDetailHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChartOfAccountAllocationDetailHistory",
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
        /** @var ChartOfAccountAllocationDetailHistory $chartOfAccountAllocationDetailHistory */
        $chartOfAccountAllocationDetailHistory = $this->chartOfAccountAllocationDetailHistoryRepository->findWithoutFail($id);

        if (empty($chartOfAccountAllocationDetailHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
        }

        $chartOfAccountAllocationDetailHistory->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.chart_of_account_allocation_detail_histories')]));
    }
}

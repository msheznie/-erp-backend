<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesReturnDetailRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateSalesReturnDetailRefferedBackAPIRequest;
use App\Models\SalesReturnDetailRefferedBack;
use App\Repositories\SalesReturnDetailRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalesReturnDetailRefferedBackController
 * @package App\Http\Controllers\API
 */

class SalesReturnDetailRefferedBackAPIController extends AppBaseController
{
    /** @var  SalesReturnDetailRefferedBackRepository */
    private $salesReturnDetailRefferedBackRepository;

    public function __construct(SalesReturnDetailRefferedBackRepository $salesReturnDetailRefferedBackRepo)
    {
        $this->salesReturnDetailRefferedBackRepository = $salesReturnDetailRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnDetailRefferedBacks",
     *      summary="Get a listing of the SalesReturnDetailRefferedBacks.",
     *      tags={"SalesReturnDetailRefferedBack"},
     *      description="Get all SalesReturnDetailRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/SalesReturnDetailRefferedBack")
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
        $this->salesReturnDetailRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->salesReturnDetailRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesReturnDetailRefferedBacks = $this->salesReturnDetailRefferedBackRepository->all();

        return $this->sendResponse($salesReturnDetailRefferedBacks->toArray(), trans('custom.sales_return_detail_reffered_backs_retrieved_succe'));
    }

    /**
     * @param CreateSalesReturnDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesReturnDetailRefferedBacks",
     *      summary="Store a newly created SalesReturnDetailRefferedBack in storage",
     *      tags={"SalesReturnDetailRefferedBack"},
     *      description="Store SalesReturnDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnDetailRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnDetailRefferedBack")
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
     *                  ref="#/definitions/SalesReturnDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesReturnDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $salesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepository->create($input);

        return $this->sendResponse($salesReturnDetailRefferedBack->toArray(), trans('custom.sales_return_detail_reffered_back_saved_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnDetailRefferedBacks/{id}",
     *      summary="Display the specified SalesReturnDetailRefferedBack",
     *      tags={"SalesReturnDetailRefferedBack"},
     *      description="Get SalesReturnDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetailRefferedBack",
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
     *                  ref="#/definitions/SalesReturnDetailRefferedBack"
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
        /** @var SalesReturnDetailRefferedBack $salesReturnDetailRefferedBack */
        $salesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnDetailRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_detail_reffered_back_not_found'));
        }

        return $this->sendResponse($salesReturnDetailRefferedBack->toArray(), trans('custom.sales_return_detail_reffered_back_retrieved_succes'));
    }

    /**
     * @param int $id
     * @param UpdateSalesReturnDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesReturnDetailRefferedBacks/{id}",
     *      summary="Update the specified SalesReturnDetailRefferedBack in storage",
     *      tags={"SalesReturnDetailRefferedBack"},
     *      description="Update SalesReturnDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetailRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnDetailRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnDetailRefferedBack")
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
     *                  ref="#/definitions/SalesReturnDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesReturnDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesReturnDetailRefferedBack $salesReturnDetailRefferedBack */
        $salesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnDetailRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_detail_reffered_back_not_found'));
        }

        $salesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepository->update($input, $id);

        return $this->sendResponse($salesReturnDetailRefferedBack->toArray(), trans('custom.salesreturndetailrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesReturnDetailRefferedBacks/{id}",
     *      summary="Remove the specified SalesReturnDetailRefferedBack from storage",
     *      tags={"SalesReturnDetailRefferedBack"},
     *      description="Delete SalesReturnDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnDetailRefferedBack",
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
        /** @var SalesReturnDetailRefferedBack $salesReturnDetailRefferedBack */
        $salesReturnDetailRefferedBack = $this->salesReturnDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnDetailRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_detail_reffered_back_not_found'));
        }

        $salesReturnDetailRefferedBack->delete();

        return $this->sendSuccess('Sales Return Detail Reffered Back deleted successfully');
    }
}

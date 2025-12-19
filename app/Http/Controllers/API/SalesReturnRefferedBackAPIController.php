<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSalesReturnRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateSalesReturnRefferedBackAPIRequest;
use App\Models\SalesReturnRefferedBack;
use App\Repositories\SalesReturnRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalesReturnRefferedBackController
 * @package App\Http\Controllers\API
 */

class SalesReturnRefferedBackAPIController extends AppBaseController
{
    /** @var  SalesReturnRefferedBackRepository */
    private $salesReturnRefferedBackRepository;

    public function __construct(SalesReturnRefferedBackRepository $salesReturnRefferedBackRepo)
    {
        $this->salesReturnRefferedBackRepository = $salesReturnRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnRefferedBacks",
     *      summary="Get a listing of the SalesReturnRefferedBacks.",
     *      tags={"SalesReturnRefferedBack"},
     *      description="Get all SalesReturnRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/SalesReturnRefferedBack")
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
        $this->salesReturnRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->salesReturnRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesReturnRefferedBacks = $this->salesReturnRefferedBackRepository->all();

        return $this->sendResponse($salesReturnRefferedBacks->toArray(), trans('custom.sales_return_reffered_backs_retrieved_successfully'));
    }

    /**
     * @param CreateSalesReturnRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesReturnRefferedBacks",
     *      summary="Store a newly created SalesReturnRefferedBack in storage",
     *      tags={"SalesReturnRefferedBack"},
     *      description="Store SalesReturnRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnRefferedBack")
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
     *                  ref="#/definitions/SalesReturnRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesReturnRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $salesReturnRefferedBack = $this->salesReturnRefferedBackRepository->create($input);

        return $this->sendResponse($salesReturnRefferedBack->toArray(), trans('custom.sales_return_reffered_back_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesReturnRefferedBacks/{id}",
     *      summary="Display the specified SalesReturnRefferedBack",
     *      tags={"SalesReturnRefferedBack"},
     *      description="Get SalesReturnRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnRefferedBack",
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
     *                  ref="#/definitions/SalesReturnRefferedBack"
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
        /** @var SalesReturnRefferedBack $salesReturnRefferedBack */
        $salesReturnRefferedBack = $this->salesReturnRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_reffered_back_not_found'));
        }

        return $this->sendResponse($salesReturnRefferedBack->toArray(), trans('custom.sales_return_reffered_back_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesReturnRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesReturnRefferedBacks/{id}",
     *      summary="Update the specified SalesReturnRefferedBack in storage",
     *      tags={"SalesReturnRefferedBack"},
     *      description="Update SalesReturnRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesReturnRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesReturnRefferedBack")
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
     *                  ref="#/definitions/SalesReturnRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesReturnRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesReturnRefferedBack $salesReturnRefferedBack */
        $salesReturnRefferedBack = $this->salesReturnRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_reffered_back_not_found'));
        }

        $salesReturnRefferedBack = $this->salesReturnRefferedBackRepository->update($input, $id);

        return $this->sendResponse($salesReturnRefferedBack->toArray(), trans('custom.salesreturnrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesReturnRefferedBacks/{id}",
     *      summary="Remove the specified SalesReturnRefferedBack from storage",
     *      tags={"SalesReturnRefferedBack"},
     *      description="Delete SalesReturnRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesReturnRefferedBack",
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
        /** @var SalesReturnRefferedBack $salesReturnRefferedBack */
        $salesReturnRefferedBack = $this->salesReturnRefferedBackRepository->findWithoutFail($id);

        if (empty($salesReturnRefferedBack)) {
            return $this->sendError(trans('custom.sales_return_reffered_back_not_found'));
        }

        $salesReturnRefferedBack->delete();

        return $this->sendSuccess('Sales Return Reffered Back deleted successfully');
    }
}

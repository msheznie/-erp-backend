<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCashFlowTemplateDetailAPIRequest;
use App\Http\Requests\API\UpdateCashFlowTemplateDetailAPIRequest;
use App\Models\CashFlowTemplateDetail;
use App\Repositories\CashFlowTemplateDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CashFlowTemplateDetailController
 * @package App\Http\Controllers\API
 */

class CashFlowTemplateDetailAPIController extends AppBaseController
{
    /** @var  CashFlowTemplateDetailRepository */
    private $cashFlowTemplateDetailRepository;

    public function __construct(CashFlowTemplateDetailRepository $cashFlowTemplateDetailRepo)
    {
        $this->cashFlowTemplateDetailRepository = $cashFlowTemplateDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateDetails",
     *      summary="Get a listing of the CashFlowTemplateDetails.",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Get all CashFlowTemplateDetails",
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
     *                  @SWG\Items(ref="#/definitions/CashFlowTemplateDetail")
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
        $this->cashFlowTemplateDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->cashFlowTemplateDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $cashFlowTemplateDetails = $this->cashFlowTemplateDetailRepository->all();

        return $this->sendResponse($cashFlowTemplateDetails->toArray(), 'Cash Flow Template Details retrieved successfully');
    }

    /**
     * @param CreateCashFlowTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/cashFlowTemplateDetails",
     *      summary="Store a newly created CashFlowTemplateDetail in storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Store CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateDetail")
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCashFlowTemplateDetailAPIRequest $request)
    {
        $input = $request->all();

        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->create($input);

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), 'Cash Flow Template Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Display the specified CashFlowTemplateDetail",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Get CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
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
        /** @var CashFlowTemplateDetail $cashFlowTemplateDetail */
        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateDetail)) {
            return $this->sendError('Cash Flow Template Detail not found');
        }

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), 'Cash Flow Template Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCashFlowTemplateDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Update the specified CashFlowTemplateDetail in storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Update CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CashFlowTemplateDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CashFlowTemplateDetail")
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
     *                  ref="#/definitions/CashFlowTemplateDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCashFlowTemplateDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CashFlowTemplateDetail $cashFlowTemplateDetail */
        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateDetail)) {
            return $this->sendError('Cash Flow Template Detail not found');
        }

        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->update($input, $id);

        return $this->sendResponse($cashFlowTemplateDetail->toArray(), 'CashFlowTemplateDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/cashFlowTemplateDetails/{id}",
     *      summary="Remove the specified CashFlowTemplateDetail from storage",
     *      tags={"CashFlowTemplateDetail"},
     *      description="Delete CashFlowTemplateDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CashFlowTemplateDetail",
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
        /** @var CashFlowTemplateDetail $cashFlowTemplateDetail */
        $cashFlowTemplateDetail = $this->cashFlowTemplateDetailRepository->findWithoutFail($id);

        if (empty($cashFlowTemplateDetail)) {
            return $this->sendError('Cash Flow Template Detail not found');
        }

        $cashFlowTemplateDetail->delete();

        return $this->sendSuccess('Cash Flow Template Detail deleted successfully');
    }
}

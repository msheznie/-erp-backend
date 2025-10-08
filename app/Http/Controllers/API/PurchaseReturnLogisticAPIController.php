<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnLogisticAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnLogisticAPIRequest;
use App\Models\PurchaseReturnLogistic;
use App\Repositories\PurchaseReturnLogisticRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnLogisticController
 * @package App\Http\Controllers\API
 */

class PurchaseReturnLogisticAPIController extends AppBaseController
{
    /** @var  PurchaseReturnLogisticRepository */
    private $purchaseReturnLogisticRepository;

    public function __construct(PurchaseReturnLogisticRepository $purchaseReturnLogisticRepo)
    {
        $this->purchaseReturnLogisticRepository = $purchaseReturnLogisticRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnLogistics",
     *      summary="Get a listing of the PurchaseReturnLogistics.",
     *      tags={"PurchaseReturnLogistic"},
     *      description="Get all PurchaseReturnLogistics",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnLogistic")
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
        $this->purchaseReturnLogisticRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnLogisticRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnLogistics = $this->purchaseReturnLogisticRepository->all();

        return $this->sendResponse($purchaseReturnLogistics->toArray(), trans('custom.purchase_return_logistics_retrieved_successfully'));
    }

    /**
     * @param CreatePurchaseReturnLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnLogistics",
     *      summary="Store a newly created PurchaseReturnLogistic in storage",
     *      tags={"PurchaseReturnLogistic"},
     *      description="Store PurchaseReturnLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnLogistic that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnLogistic")
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
     *                  ref="#/definitions/PurchaseReturnLogistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnLogisticAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnLogistic = $this->purchaseReturnLogisticRepository->create($input);

        return $this->sendResponse($purchaseReturnLogistic->toArray(), trans('custom.purchase_return_logistic_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnLogistics/{id}",
     *      summary="Display the specified PurchaseReturnLogistic",
     *      tags={"PurchaseReturnLogistic"},
     *      description="Get PurchaseReturnLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnLogistic",
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
     *                  ref="#/definitions/PurchaseReturnLogistic"
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
        /** @var PurchaseReturnLogistic $purchaseReturnLogistic */
        $purchaseReturnLogistic = $this->purchaseReturnLogisticRepository->findWithoutFail($id);

        if (empty($purchaseReturnLogistic)) {
            return $this->sendError(trans('custom.purchase_return_logistic_not_found'));
        }

        return $this->sendResponse($purchaseReturnLogistic->toArray(), trans('custom.purchase_return_logistic_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnLogistics/{id}",
     *      summary="Update the specified PurchaseReturnLogistic in storage",
     *      tags={"PurchaseReturnLogistic"},
     *      description="Update PurchaseReturnLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnLogistic",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnLogistic that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnLogistic")
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
     *                  ref="#/definitions/PurchaseReturnLogistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnLogisticAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturnLogistic $purchaseReturnLogistic */
        $purchaseReturnLogistic = $this->purchaseReturnLogisticRepository->findWithoutFail($id);

        if (empty($purchaseReturnLogistic)) {
            return $this->sendError(trans('custom.purchase_return_logistic_not_found'));
        }

        $purchaseReturnLogistic = $this->purchaseReturnLogisticRepository->update($input, $id);

        return $this->sendResponse($purchaseReturnLogistic->toArray(), trans('custom.purchasereturnlogistic_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnLogistics/{id}",
     *      summary="Remove the specified PurchaseReturnLogistic from storage",
     *      tags={"PurchaseReturnLogistic"},
     *      description="Delete PurchaseReturnLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnLogistic",
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
        /** @var PurchaseReturnLogistic $purchaseReturnLogistic */
        $purchaseReturnLogistic = $this->purchaseReturnLogisticRepository->findWithoutFail($id);

        if (empty($purchaseReturnLogistic)) {
            return $this->sendError(trans('custom.purchase_return_logistic_not_found'));
        }

        $purchaseReturnLogistic->delete();

        return $this->sendSuccess(trans('custom.purchase_return_logistic_deleted_successfully'));
    }
}

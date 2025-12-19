<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDeliveryOrderDetailRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateDeliveryOrderDetailRefferedbackAPIRequest;
use App\Models\DeliveryOrderDetailRefferedback;
use App\Repositories\DeliveryOrderDetailRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DeliveryOrderDetailRefferedbackController
 * @package App\Http\Controllers\API
 */

class DeliveryOrderDetailRefferedbackAPIController extends AppBaseController
{
    /** @var  DeliveryOrderDetailRefferedbackRepository */
    private $deliveryOrderDetailRefferedbackRepository;

    public function __construct(DeliveryOrderDetailRefferedbackRepository $deliveryOrderDetailRefferedbackRepo)
    {
        $this->deliveryOrderDetailRefferedbackRepository = $deliveryOrderDetailRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderDetailRefferedbacks",
     *      summary="Get a listing of the DeliveryOrderDetailRefferedbacks.",
     *      tags={"DeliveryOrderDetailRefferedback"},
     *      description="Get all DeliveryOrderDetailRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/DeliveryOrderDetailRefferedback")
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
        $this->deliveryOrderDetailRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->deliveryOrderDetailRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryOrderDetailRefferedbacks = $this->deliveryOrderDetailRefferedbackRepository->all();

        return $this->sendResponse($deliveryOrderDetailRefferedbacks->toArray(), trans('custom.delivery_order_detail_refferedbacks_retrieved_succ'));
    }

    /**
     * @param CreateDeliveryOrderDetailRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryOrderDetailRefferedbacks",
     *      summary="Store a newly created DeliveryOrderDetailRefferedback in storage",
     *      tags={"DeliveryOrderDetailRefferedback"},
     *      description="Store DeliveryOrderDetailRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderDetailRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderDetailRefferedback")
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
     *                  ref="#/definitions/DeliveryOrderDetailRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliveryOrderDetailRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $deliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepository->create($input);

        return $this->sendResponse($deliveryOrderDetailRefferedback->toArray(), trans('custom.delivery_order_detail_refferedback_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderDetailRefferedbacks/{id}",
     *      summary="Display the specified DeliveryOrderDetailRefferedback",
     *      tags={"DeliveryOrderDetailRefferedback"},
     *      description="Get DeliveryOrderDetailRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetailRefferedback",
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
     *                  ref="#/definitions/DeliveryOrderDetailRefferedback"
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
        /** @var DeliveryOrderDetailRefferedback $deliveryOrderDetailRefferedback */
        $deliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetailRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_detail_refferedback_not_found'));
        }

        return $this->sendResponse($deliveryOrderDetailRefferedback->toArray(), trans('custom.delivery_order_detail_refferedback_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param UpdateDeliveryOrderDetailRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryOrderDetailRefferedbacks/{id}",
     *      summary="Update the specified DeliveryOrderDetailRefferedback in storage",
     *      tags={"DeliveryOrderDetailRefferedback"},
     *      description="Update DeliveryOrderDetailRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetailRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderDetailRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderDetailRefferedback")
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
     *                  ref="#/definitions/DeliveryOrderDetailRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliveryOrderDetailRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DeliveryOrderDetailRefferedback $deliveryOrderDetailRefferedback */
        $deliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetailRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_detail_refferedback_not_found'));
        }

        $deliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepository->update($input, $id);

        return $this->sendResponse($deliveryOrderDetailRefferedback->toArray(), trans('custom.deliveryorderdetailrefferedback_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryOrderDetailRefferedbacks/{id}",
     *      summary="Remove the specified DeliveryOrderDetailRefferedback from storage",
     *      tags={"DeliveryOrderDetailRefferedback"},
     *      description="Delete DeliveryOrderDetailRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderDetailRefferedback",
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
        /** @var DeliveryOrderDetailRefferedback $deliveryOrderDetailRefferedback */
        $deliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepository->findWithoutFail($id);

        if (empty($deliveryOrderDetailRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_detail_refferedback_not_found'));
        }

        $deliveryOrderDetailRefferedback->delete();

        return $this->sendSuccess('Delivery Order Detail Refferedback deleted successfully');
    }
}

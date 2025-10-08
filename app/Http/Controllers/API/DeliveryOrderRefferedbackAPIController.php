<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDeliveryOrderRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateDeliveryOrderRefferedbackAPIRequest;
use App\Models\DeliveryOrderRefferedback;
use App\Repositories\DeliveryOrderRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DeliveryOrderRefferedbackController
 * @package App\Http\Controllers\API
 */

class DeliveryOrderRefferedbackAPIController extends AppBaseController
{
    /** @var  DeliveryOrderRefferedbackRepository */
    private $deliveryOrderRefferedbackRepository;

    public function __construct(DeliveryOrderRefferedbackRepository $deliveryOrderRefferedbackRepo)
    {
        $this->deliveryOrderRefferedbackRepository = $deliveryOrderRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderRefferedbacks",
     *      summary="Get a listing of the DeliveryOrderRefferedbacks.",
     *      tags={"DeliveryOrderRefferedback"},
     *      description="Get all DeliveryOrderRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/DeliveryOrderRefferedback")
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
        $this->deliveryOrderRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->deliveryOrderRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $deliveryOrderRefferedbacks = $this->deliveryOrderRefferedbackRepository->all();

        return $this->sendResponse($deliveryOrderRefferedbacks->toArray(), trans('custom.delivery_order_refferedbacks_retrieved_successfull'));
    }

    /**
     * @param CreateDeliveryOrderRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/deliveryOrderRefferedbacks",
     *      summary="Store a newly created DeliveryOrderRefferedback in storage",
     *      tags={"DeliveryOrderRefferedback"},
     *      description="Store DeliveryOrderRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderRefferedback")
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
     *                  ref="#/definitions/DeliveryOrderRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDeliveryOrderRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $deliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepository->create($input);

        return $this->sendResponse($deliveryOrderRefferedback->toArray(), trans('custom.delivery_order_refferedback_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/deliveryOrderRefferedbacks/{id}",
     *      summary="Display the specified DeliveryOrderRefferedback",
     *      tags={"DeliveryOrderRefferedback"},
     *      description="Get DeliveryOrderRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderRefferedback",
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
     *                  ref="#/definitions/DeliveryOrderRefferedback"
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
        /** @var DeliveryOrderRefferedback $deliveryOrderRefferedback */
        $deliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepository->with(['customer','currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'detail' => function($query){
            $query->with(['uom_default']);
        }])->findWithoutFail($id);

        if (empty($deliveryOrderRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_refferedback_not_found'));
        }

        return $this->sendResponse($deliveryOrderRefferedback->toArray(), trans('custom.delivery_order_refferedback_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateDeliveryOrderRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/deliveryOrderRefferedbacks/{id}",
     *      summary="Update the specified DeliveryOrderRefferedback in storage",
     *      tags={"DeliveryOrderRefferedback"},
     *      description="Update DeliveryOrderRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DeliveryOrderRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DeliveryOrderRefferedback")
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
     *                  ref="#/definitions/DeliveryOrderRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDeliveryOrderRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var DeliveryOrderRefferedback $deliveryOrderRefferedback */
        $deliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepository->findWithoutFail($id);

        if (empty($deliveryOrderRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_refferedback_not_found'));
        }

        $deliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepository->update($input, $id);

        return $this->sendResponse($deliveryOrderRefferedback->toArray(), trans('custom.deliveryorderrefferedback_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/deliveryOrderRefferedbacks/{id}",
     *      summary="Remove the specified DeliveryOrderRefferedback from storage",
     *      tags={"DeliveryOrderRefferedback"},
     *      description="Delete DeliveryOrderRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DeliveryOrderRefferedback",
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
        /** @var DeliveryOrderRefferedback $deliveryOrderRefferedback */
        $deliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepository->findWithoutFail($id);

        if (empty($deliveryOrderRefferedback)) {
            return $this->sendError(trans('custom.delivery_order_refferedback_not_found'));
        }

        $deliveryOrderRefferedback->delete();

        return $this->sendSuccess('Delivery Order Refferedback deleted successfully');
    }

    public function getDeliveryOrderAmendHistory(Request $request)
    {
        $input = $request->all();

        $deliveryOrderHistory = DeliveryOrderRefferedback::where('deliveryOrderID', $input['deliveryOrderID'])
            ->with(['created_by', 'confirmed_by', 'modified_by', 'customer', 'approved_by', 'currency','segment'])
            ->get();

        return $this->sendResponse($deliveryOrderHistory, trans('custom.delivery_order_history_retrieved_successfully'));
    }
}

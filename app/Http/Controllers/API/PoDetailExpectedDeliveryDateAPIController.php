<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoDetailExpectedDeliveryDateAPIRequest;
use App\Http\Requests\API\UpdatePoDetailExpectedDeliveryDateAPIRequest;
use App\Models\PoDetailExpectedDeliveryDate;
use App\Repositories\PoDetailExpectedDeliveryDateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\PurchaseOrderDetails;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;


/**
 * Class PoDetailExpectedDeliveryDateController
 * @package App\Http\Controllers\API
 */

class PoDetailExpectedDeliveryDateAPIController extends AppBaseController
{
    /** @var  PoDetailExpectedDeliveryDateRepository */
    private $poDetailExpectedDeliveryDateRepository;

    public function __construct(PoDetailExpectedDeliveryDateRepository $poDetailExpectedDeliveryDateRepo)
    {
        $this->poDetailExpectedDeliveryDateRepository = $poDetailExpectedDeliveryDateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/poDetailExpectedDeliveryDates",
     *      summary="Get a listing of the PoDetailExpectedDeliveryDates.",
     *      tags={"PoDetailExpectedDeliveryDate"},
     *      description="Get all PoDetailExpectedDeliveryDates",
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
     *                  @SWG\Items(ref="#/definitions/PoDetailExpectedDeliveryDate")
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
        $this->poDetailExpectedDeliveryDateRepository->pushCriteria(new RequestCriteria($request));
        $this->poDetailExpectedDeliveryDateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poDetailExpectedDeliveryDates = $this->poDetailExpectedDeliveryDateRepository->all();

        return $this->sendResponse($poDetailExpectedDeliveryDates->toArray(), trans('custom.po_detail_expected_delivery_dates_retrieved_succes'));
    }

    /**
     * @param CreatePoDetailExpectedDeliveryDateAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/poDetailExpectedDeliveryDates",
     *      summary="Store a newly created PoDetailExpectedDeliveryDate in storage",
     *      tags={"PoDetailExpectedDeliveryDate"},
     *      description="Store PoDetailExpectedDeliveryDate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoDetailExpectedDeliveryDate that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoDetailExpectedDeliveryDate")
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
     *                  ref="#/definitions/PoDetailExpectedDeliveryDate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePoDetailExpectedDeliveryDateAPIRequest $request)
    {
        $input = $request->all();

        $poDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepository->create($input);

        return $this->sendResponse($poDetailExpectedDeliveryDate->toArray(), trans('custom.po_detail_expected_delivery_date_saved_successfull'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/poDetailExpectedDeliveryDates/{id}",
     *      summary="Display the specified PoDetailExpectedDeliveryDate",
     *      tags={"PoDetailExpectedDeliveryDate"},
     *      description="Get PoDetailExpectedDeliveryDate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoDetailExpectedDeliveryDate",
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
     *                  ref="#/definitions/PoDetailExpectedDeliveryDate"
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
        /** @var PoDetailExpectedDeliveryDate $poDetailExpectedDeliveryDate */
        $poDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepository->findWithoutFail($id);

        if (empty($poDetailExpectedDeliveryDate)) {
            return $this->sendError(trans('custom.po_detail_expected_delivery_date_not_found'));
        }

        return $this->sendResponse($poDetailExpectedDeliveryDate->toArray(), trans('custom.po_detail_expected_delivery_date_retrieved_success'));
    }

    /**
     * @param int $id
     * @param UpdatePoDetailExpectedDeliveryDateAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/poDetailExpectedDeliveryDates/{id}",
     *      summary="Update the specified PoDetailExpectedDeliveryDate in storage",
     *      tags={"PoDetailExpectedDeliveryDate"},
     *      description="Update PoDetailExpectedDeliveryDate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoDetailExpectedDeliveryDate",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PoDetailExpectedDeliveryDate that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PoDetailExpectedDeliveryDate")
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
     *                  ref="#/definitions/PoDetailExpectedDeliveryDate"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePoDetailExpectedDeliveryDateAPIRequest $request)
    {
        $input = $request->all();

        if(isset($input['po_detail'])){
            unset($input['po_detail']);
        }

        if(isset($input['po_detail_auto_id'])){
            unset($input['po_detail_auto_id']);
        }

        if(isset($input['id'])){
            unset($input['id']);
        }

        if(isset($input['created_at'])){
            unset($input['created_at']);
        }

        if (isset($input['expected_delivery_date']) && $input['expected_delivery_date']) {
            $input['expected_delivery_date'] = new Carbon($input['expected_delivery_date']);
        }



        /** @var PoDetailExpectedDeliveryDate $poDetailExpectedDeliveryDate */
        $poDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepository->findWithoutFail($id);

        if (empty($poDetailExpectedDeliveryDate)) {
            return $this->sendError(trans('custom.po_detail_expected_delivery_date_not_found'));
        }

        $poDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepository->update($input, $id);

        if ($input['allocated_qty'] == 0) {
            $poDetailExpectedDeliveryDate->delete();
        }

        return $this->sendResponse($poDetailExpectedDeliveryDate->toArray(), trans('custom.podetailexpecteddeliverydate_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/poDetailExpectedDeliveryDates/{id}",
     *      summary="Remove the specified PoDetailExpectedDeliveryDate from storage",
     *      tags={"PoDetailExpectedDeliveryDate"},
     *      description="Delete PoDetailExpectedDeliveryDate",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PoDetailExpectedDeliveryDate",
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
        /** @var PoDetailExpectedDeliveryDate $poDetailExpectedDeliveryDate */
        $poDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepository->findWithoutFail($id);

        if (empty($poDetailExpectedDeliveryDate)) {
            return $this->sendError(trans('custom.po_detail_expected_delivery_date_not_found'));
        }

        $poDetailExpectedDeliveryDate->delete();

        return $this->sendResponse($poDetailExpectedDeliveryDate,trans('custom.po_detail_expected_delivery_date_deleted_successfu'));
    }

    public function getAllocatedExpectedDeliveryDates(Request $request)
    {
        $input = $request->all();

        $allocatedItems = PoDetailExpectedDeliveryDate::with(['po_detail'])
                                                        ->where('po_detail_auto_id', $input['docDetailID'])
                                                        ->get();
        
        return $this->sendResponse($allocatedItems, trans('custom.po_detail_expected_delivery_dates_retrieved_succes'));
    }

    public function allocateExpectedDeliveryDates(Request $request)
    {
        $input = $request->all();

        if (!isset($input['docDetailID'])) {
            return $this->sendError(trans('custom.item_line_not_found'));

        }

        if (isset($input['expected_delivery_date']) && $input['expected_delivery_date']) {
            $input['expected_delivery_date'] = new Carbon($input['expected_delivery_date']);
        }

        $checkAlreadyAllocated = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $input['docDetailID'])
                                                     ->where('expected_delivery_date', $input['expected_delivery_date'])
                                                     ->first();

        if ($checkAlreadyAllocated) {
            return $this->sendError(trans('custom.this_expected_delivery_date_already_allocated'));
        }

        $itemData = PurchaseOrderDetails::find($input['docDetailID']);
        if (!$itemData) {
            return $this->sendError(trans('custom.item_detail_not_found'));
        }

        $allocatedQty = PoDetailExpectedDeliveryDate::where('po_detail_auto_id', $input['docDetailID'])
                                                     ->sum('allocated_qty');


        if ($allocatedQty == $itemData->noQty) {
            return $this->sendError('No remaining quantity to allocate');
        }

        $allocationData = [
            'po_detail_auto_id' => $input['docDetailID'],
            'allocated_qty' => $itemData->noQty - $allocatedQty,
            'expected_delivery_date' => $input['expected_delivery_date']
        ];

        $createExpectedDeliveryDate = PoDetailExpectedDeliveryDate::create($allocationData);
        if (!$createExpectedDeliveryDate) {
            return $this->sendError(trans('custom.error_occured_while_allocating'));
        }

        return $this->sendResponse($createExpectedDeliveryDate, trans('custom.po_detail_expected_delivery_date_created_successfu'));

    }

}

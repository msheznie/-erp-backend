<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderDetailsRefferedHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderDetailsRefferedHistory
 * -- Author : Nazir
 * -- Create date : 24 - July 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 24-July 2018 By: Nazir Description: Added new function getPoItemsForAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderDetailsRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderDetailsRefferedHistoryAPIRequest;
use App\Models\PurchaseOrderDetailsRefferedHistory;
use App\Repositories\PurchaseOrderDetailsRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderDetailsRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderDetailsRefferedHistoryAPIController extends AppBaseController
{
    /** @var  PurchaseOrderDetailsRefferedHistoryRepository */
    private $purchaseOrderDetailsRefferedHistoryRepository;

    public function __construct(PurchaseOrderDetailsRefferedHistoryRepository $purchaseOrderDetailsRefferedHistoryRepo)
    {
        $this->purchaseOrderDetailsRefferedHistoryRepository = $purchaseOrderDetailsRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderDetailsRefferedHistories",
     *      summary="Get a listing of the PurchaseOrderDetailsRefferedHistories.",
     *      tags={"PurchaseOrderDetailsRefferedHistory"},
     *      description="Get all PurchaseOrderDetailsRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseOrderDetailsRefferedHistory")
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
        $this->purchaseOrderDetailsRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderDetailsRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderDetailsRefferedHistories = $this->purchaseOrderDetailsRefferedHistoryRepository->all();

        return $this->sendResponse($purchaseOrderDetailsRefferedHistories->toArray(), trans('custom.purchase_order_details_reffered_histories_retrieve'));
    }

    /**
     * @param CreatePurchaseOrderDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseOrderDetailsRefferedHistories",
     *      summary="Store a newly created PurchaseOrderDetailsRefferedHistory in storage",
     *      tags={"PurchaseOrderDetailsRefferedHistory"},
     *      description="Store PurchaseOrderDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderDetailsRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderDetailsRefferedHistory")
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
     *                  ref="#/definitions/PurchaseOrderDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseOrderDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderDetailsRefferedHistories = $this->purchaseOrderDetailsRefferedHistoryRepository->create($input);

        return $this->sendResponse($purchaseOrderDetailsRefferedHistories->toArray(), trans('custom.purchase_order_details_reffered_history_saved_succ'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderDetailsRefferedHistories/{id}",
     *      summary="Display the specified PurchaseOrderDetailsRefferedHistory",
     *      tags={"PurchaseOrderDetailsRefferedHistory"},
     *      description="Get PurchaseOrderDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderDetailsRefferedHistory",
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
     *                  ref="#/definitions/PurchaseOrderDetailsRefferedHistory"
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
        /** @var PurchaseOrderDetailsRefferedHistory $purchaseOrderDetailsRefferedHistory */
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.purchase_order_details_reffered_history_not_found'));
        }

        return $this->sendResponse($purchaseOrderDetailsRefferedHistory->toArray(), trans('custom.purchase_order_details_reffered_history_retrieved_'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseOrderDetailsRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseOrderDetailsRefferedHistories/{id}",
     *      summary="Update the specified PurchaseOrderDetailsRefferedHistory in storage",
     *      tags={"PurchaseOrderDetailsRefferedHistory"},
     *      description="Update PurchaseOrderDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderDetailsRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderDetailsRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderDetailsRefferedHistory")
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
     *                  ref="#/definitions/PurchaseOrderDetailsRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseOrderDetailsRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderDetailsRefferedHistory $purchaseOrderDetailsRefferedHistory */
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.purchase_order_details_reffered_history_not_found'));
        }

        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderDetailsRefferedHistory->toArray(), trans('custom.purchaseorderdetailsrefferedhistory_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseOrderDetailsRefferedHistories/{id}",
     *      summary="Remove the specified PurchaseOrderDetailsRefferedHistory from storage",
     *      tags={"PurchaseOrderDetailsRefferedHistory"},
     *      description="Delete PurchaseOrderDetailsRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderDetailsRefferedHistory",
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
        /** @var PurchaseOrderDetailsRefferedHistory $purchaseOrderDetailsRefferedHistory */
        $purchaseOrderDetailsRefferedHistory = $this->purchaseOrderDetailsRefferedHistoryRepository->findWithoutFail($id);

        if (empty($purchaseOrderDetailsRefferedHistory)) {
            return $this->sendError(trans('custom.purchase_order_details_reffered_history_not_found'));
        }

        $purchaseOrderDetailsRefferedHistory->delete();

        return $this->sendResponse($id, trans('custom.purchase_order_details_reffered_history_deleted_su'));
    }

    public function getPoItemsForAmendHistory(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];
        $timesReferred = $input['timesReferred'];

        $items = PurchaseOrderDetailsRefferedHistory::where('purchaseOrderMasterID', $poID)
            ->where('timesReferred', $timesReferred)
            ->with(['unit' => function ($query) {
            }])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.purchase_order_details_reffered_history_retrieved_'));
    }
}

<?php
/**
 * =============================================
 * -- File Name : PurchaseOrderAdvPaymentRefferedbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PurchaseOrderAdvPaymentRefferedback
 * -- Author : Nazir
 * -- Create date : 23 - July 2018
 * -- Description : This file is used to interact with database table and it contains relationships to the tables.
 * -- REVISION HISTORY
 * --
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseOrderAdvPaymentRefferedbackAPIRequest;
use App\Http\Requests\API\UpdatePurchaseOrderAdvPaymentRefferedbackAPIRequest;
use App\Models\PurchaseOrderAdvPaymentRefferedback;
use App\Repositories\PurchaseOrderAdvPaymentRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseOrderAdvPaymentRefferedbackController
 * @package App\Http\Controllers\API
 */

class PurchaseOrderAdvPaymentRefferedbackAPIController extends AppBaseController
{
    /** @var  PurchaseOrderAdvPaymentRefferedbackRepository */
    private $purchaseOrderAdvPaymentRefferedbackRepository;

    public function __construct(PurchaseOrderAdvPaymentRefferedbackRepository $purchaseOrderAdvPaymentRefferedbackRepo)
    {
        $this->purchaseOrderAdvPaymentRefferedbackRepository = $purchaseOrderAdvPaymentRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderAdvPaymentRefferedbacks",
     *      summary="Get a listing of the PurchaseOrderAdvPaymentRefferedbacks.",
     *      tags={"PurchaseOrderAdvPaymentRefferedback"},
     *      description="Get all PurchaseOrderAdvPaymentRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseOrderAdvPaymentRefferedback")
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
        $this->purchaseOrderAdvPaymentRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseOrderAdvPaymentRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseOrderAdvPaymentRefferedbacks = $this->purchaseOrderAdvPaymentRefferedbackRepository->all();

        return $this->sendResponse($purchaseOrderAdvPaymentRefferedbacks->toArray(), trans('custom.purchase_order_adv_payment_refferedbacks_retrieved'));
    }

    /**
     * @param CreatePurchaseOrderAdvPaymentRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseOrderAdvPaymentRefferedbacks",
     *      summary="Store a newly created PurchaseOrderAdvPaymentRefferedback in storage",
     *      tags={"PurchaseOrderAdvPaymentRefferedback"},
     *      description="Store PurchaseOrderAdvPaymentRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderAdvPaymentRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderAdvPaymentRefferedback")
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
     *                  ref="#/definitions/PurchaseOrderAdvPaymentRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseOrderAdvPaymentRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $purchaseOrderAdvPaymentRefferedbacks = $this->purchaseOrderAdvPaymentRefferedbackRepository->create($input);

        return $this->sendResponse($purchaseOrderAdvPaymentRefferedbacks->toArray(), trans('custom.purchase_order_adv_payment_refferedback_saved_succ'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseOrderAdvPaymentRefferedbacks/{id}",
     *      summary="Display the specified PurchaseOrderAdvPaymentRefferedback",
     *      tags={"PurchaseOrderAdvPaymentRefferedback"},
     *      description="Get PurchaseOrderAdvPaymentRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderAdvPaymentRefferedback",
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
     *                  ref="#/definitions/PurchaseOrderAdvPaymentRefferedback"
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
        /** @var PurchaseOrderAdvPaymentRefferedback $purchaseOrderAdvPaymentRefferedback */
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            return $this->sendError(trans('custom.purchase_order_adv_payment_refferedback_not_found'));
        }

        return $this->sendResponse($purchaseOrderAdvPaymentRefferedback->toArray(), trans('custom.purchase_order_adv_payment_refferedback_retrieved_'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseOrderAdvPaymentRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseOrderAdvPaymentRefferedbacks/{id}",
     *      summary="Update the specified PurchaseOrderAdvPaymentRefferedback in storage",
     *      tags={"PurchaseOrderAdvPaymentRefferedback"},
     *      description="Update PurchaseOrderAdvPaymentRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderAdvPaymentRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseOrderAdvPaymentRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseOrderAdvPaymentRefferedback")
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
     *                  ref="#/definitions/PurchaseOrderAdvPaymentRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseOrderAdvPaymentRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseOrderAdvPaymentRefferedback $purchaseOrderAdvPaymentRefferedback */
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            return $this->sendError(trans('custom.purchase_order_adv_payment_refferedback_not_found'));
        }

        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->update($input, $id);

        return $this->sendResponse($purchaseOrderAdvPaymentRefferedback->toArray(), trans('custom.purchaseorderadvpaymentrefferedback_updated_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseOrderAdvPaymentRefferedbacks/{id}",
     *      summary="Remove the specified PurchaseOrderAdvPaymentRefferedback from storage",
     *      tags={"PurchaseOrderAdvPaymentRefferedback"},
     *      description="Delete PurchaseOrderAdvPaymentRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseOrderAdvPaymentRefferedback",
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
        /** @var PurchaseOrderAdvPaymentRefferedback $purchaseOrderAdvPaymentRefferedback */
        $purchaseOrderAdvPaymentRefferedback = $this->purchaseOrderAdvPaymentRefferedbackRepository->findWithoutFail($id);

        if (empty($purchaseOrderAdvPaymentRefferedback)) {
            return $this->sendError(trans('custom.purchase_order_adv_payment_refferedback_not_found'));
        }

        $purchaseOrderAdvPaymentRefferedback->delete();

        return $this->sendResponse($id, trans('custom.purchase_order_adv_payment_refferedback_deleted_su'));
    }


    public function getPoLogisticsItemsForAmendHistory(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];
        $timesReferred = $input['timesReferred'];

        $items = PurchaseOrderAdvPaymentRefferedback::where('poID', $poID)
            ->where('timesReferred', $timesReferred)
            ->where('poTermID', 0)
            ->where('confirmedYN', 1)
            ->where('isAdvancePaymentYN', 1)
            ->where('approvedYN', -1)
            ->with(['currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), trans('custom.data_retrieved_successfully'));
    }
}

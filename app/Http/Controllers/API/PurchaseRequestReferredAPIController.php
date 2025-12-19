<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestReferredAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Request Referred
 * -- Author : Mohamed Nazir
 * -- Create date : 01 - August  2018
 * -- Description : This file contains the all CRUD for Purchase Request Referred
 * -- REVISION HISTORY
 * -- Date: 01-August 2018 By: Nazir Description: Added new function getPrMasterAmendHistory(),
 */


namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseRequestReferredAPIRequest;
use App\Http\Requests\API\UpdatePurchaseRequestReferredAPIRequest;
use App\Models\PurchaseRequestReferred;
use App\Repositories\PurchaseRequestReferredRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseRequestReferredController
 * @package App\Http\Controllers\API
 */

class PurchaseRequestReferredAPIController extends AppBaseController
{
    /** @var  PurchaseRequestReferredRepository */
    private $purchaseRequestReferredRepository;

    public function __construct(PurchaseRequestReferredRepository $purchaseRequestReferredRepo)
    {
        $this->purchaseRequestReferredRepository = $purchaseRequestReferredRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseRequestReferreds",
     *      summary="Get a listing of the PurchaseRequestReferreds.",
     *      tags={"PurchaseRequestReferred"},
     *      description="Get all PurchaseRequestReferreds",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseRequestReferred")
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
        $this->purchaseRequestReferredRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseRequestReferredRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseRequestReferreds = $this->purchaseRequestReferredRepository->all();

        return $this->sendResponse($purchaseRequestReferreds->toArray(), trans('custom.purchase_request_referreds_retrieved_successfully'));
    }

    /**
     * @param CreatePurchaseRequestReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseRequestReferreds",
     *      summary="Store a newly created PurchaseRequestReferred in storage",
     *      tags={"PurchaseRequestReferred"},
     *      description="Store PurchaseRequestReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseRequestReferred that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseRequestReferred")
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
     *                  ref="#/definitions/PurchaseRequestReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseRequestReferredAPIRequest $request)
    {
        $input = $request->all();

        $purchaseRequestReferreds = $this->purchaseRequestReferredRepository->create($input);

        return $this->sendResponse($purchaseRequestReferreds->toArray(), trans('custom.purchase_request_referred_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseRequestReferreds/{id}",
     *      summary="Display the specified PurchaseRequestReferred",
     *      tags={"PurchaseRequestReferred"},
     *      description="Get PurchaseRequestReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseRequestReferred",
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
     *                  ref="#/definitions/PurchaseRequestReferred"
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
        /** @var PurchaseRequestReferred $purchaseRequestReferred */
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->with(['created_by', 'confirmed_by', 'segment'])->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            return $this->sendError(trans('custom.purchase_request_referred_not_found'));
        }

        return $this->sendResponse($purchaseRequestReferred->toArray(), trans('custom.purchase_request_referred_retrieved_successfully'));
    }


    public function get_purchase_request_referreds(Request $request)
    {

        $id = $request['id'];
        /** @var PurchaseRequestReferred $purchaseRequestReferred */
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->with(['created_by', 'confirmed_by', 'segment'])->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            return $this->sendError(trans('custom.purchase_request_referred_not_found'));
        }

        return $this->sendResponse($purchaseRequestReferred, trans('custom.purchase_request_referred_retrieved_successfully'));

    }

    /**
     * @param int $id
     * @param UpdatePurchaseRequestReferredAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseRequestReferreds/{id}",
     *      summary="Update the specified PurchaseRequestReferred in storage",
     *      tags={"PurchaseRequestReferred"},
     *      description="Update PurchaseRequestReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseRequestReferred",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseRequestReferred that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseRequestReferred")
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
     *                  ref="#/definitions/PurchaseRequestReferred"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseRequestReferredAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseRequestReferred $purchaseRequestReferred */
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            return $this->sendError(trans('custom.purchase_request_referred_not_found'));
        }

        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->update($input, $id);

        return $this->sendResponse($purchaseRequestReferred->toArray(), trans('custom.purchaserequestreferred_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseRequestReferreds/{id}",
     *      summary="Remove the specified PurchaseRequestReferred from storage",
     *      tags={"PurchaseRequestReferred"},
     *      description="Delete PurchaseRequestReferred",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseRequestReferred",
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
        /** @var PurchaseRequestReferred $purchaseRequestReferred */
        $purchaseRequestReferred = $this->purchaseRequestReferredRepository->findWithoutFail($id);

        if (empty($purchaseRequestReferred)) {
            return $this->sendError(trans('custom.purchase_request_referred_not_found'));
        }

        $purchaseRequestReferred->delete();

        return $this->sendResponse($id, trans('custom.purchase_request_referred_deleted_successfully'));
    }

    public function getPrMasterAmendHistory(Request $request)
    {
        $input = $request->all();

        $procumentOrderHistory = PurchaseRequestReferred::where('purchaseRequestID', $input['purchaseRequestID'])
            ->with(['created_by','location','financeCategory','segment','approved_by', 'priority'])
            ->get();

        return $this->sendResponse($procumentOrderHistory, trans('custom.purchase_request_master_retrieved_successfully'));
    }
}

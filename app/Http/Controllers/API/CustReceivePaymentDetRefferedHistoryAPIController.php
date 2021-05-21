<?php
/**
 * =============================================
 * -- File Name : BookInvSuppMasterRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  BookInvSuppMasterRefferedBack
 * -- Author : Mohamed Nazir
 * -- Create date : 21 - November 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 21-November 2018 By: Nazir Description: Added new function getRVDetailAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustReceivePaymentDetRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateCustReceivePaymentDetRefferedHistoryAPIRequest;
use App\Models\CustReceivePaymentDetRefferedHistory;
use App\Repositories\CustReceivePaymentDetRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustReceivePaymentDetRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class CustReceivePaymentDetRefferedHistoryAPIController extends AppBaseController
{
    /** @var  CustReceivePaymentDetRefferedHistoryRepository */
    private $custReceivePaymentDetRefferedHistoryRepository;

    public function __construct(CustReceivePaymentDetRefferedHistoryRepository $custReceivePaymentDetRefferedHistoryRepo)
    {
        $this->custReceivePaymentDetRefferedHistoryRepository = $custReceivePaymentDetRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/custReceivePaymentDetRefferedHistories",
     *      summary="Get a listing of the CustReceivePaymentDetRefferedHistories.",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Get all CustReceivePaymentDetRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
        $this->custReceivePaymentDetRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->custReceivePaymentDetRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $custReceivePaymentDetRefferedHistories = $this->custReceivePaymentDetRefferedHistoryRepository->all();

        return $this->sendResponse($custReceivePaymentDetRefferedHistories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
    }

    /**
     * @param CreateCustReceivePaymentDetRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/custReceivePaymentDetRefferedHistories",
     *      summary="Store a newly created CustReceivePaymentDetRefferedHistory in storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Store CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustReceivePaymentDetRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustReceivePaymentDetRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $custReceivePaymentDetRefferedHistories = $this->custReceivePaymentDetRefferedHistoryRepository->create($input);

        return $this->sendResponse($custReceivePaymentDetRefferedHistories->toArray(), trans('custom.save', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Display the specified CustReceivePaymentDetRefferedHistory",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Get CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
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
        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
        }

        return $this->sendResponse($custReceivePaymentDetRefferedHistory->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
    }

    /**
     * @param int $id
     * @param UpdateCustReceivePaymentDetRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Update the specified CustReceivePaymentDetRefferedHistory in storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Update CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustReceivePaymentDetRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustReceivePaymentDetRefferedHistory")
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
     *                  ref="#/definitions/CustReceivePaymentDetRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustReceivePaymentDetRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
        }

        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($custReceivePaymentDetRefferedHistory->toArray(), trans('custom.update', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/custReceivePaymentDetRefferedHistories/{id}",
     *      summary="Remove the specified CustReceivePaymentDetRefferedHistory from storage",
     *      tags={"CustReceivePaymentDetRefferedHistory"},
     *      description="Delete CustReceivePaymentDetRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustReceivePaymentDetRefferedHistory",
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
        /** @var CustReceivePaymentDetRefferedHistory $custReceivePaymentDetRefferedHistory */
        $custReceivePaymentDetRefferedHistory = $this->custReceivePaymentDetRefferedHistoryRepository->findWithoutFail($id);

        if (empty($custReceivePaymentDetRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.cust_receive_payment_det_reffered_histories')]));
        }

        $custReceivePaymentDetRefferedHistory->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans(cust_receive_payment_det_reffered_histories)]));
    }

    public function getRVDetailAmendHistory(Request $request)
    {
        $input = $request->all();
        $directReceiptAutoID = $input['custReceivePaymentAutoID'];
        $timesReferred = $input['timesReferred'];

        $items = CustReceivePaymentDetRefferedHistory::where('custReceivePaymentAutoID', $directReceiptAutoID)
            ->where('timesReferred', $timesReferred)
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.purchase_order_details_reffered_history')]));
    }
}

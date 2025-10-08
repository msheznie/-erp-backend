<?php
/**
 * =============================================
 * -- File Name : PaymentBankTransferDetailRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name : Payment Bank Transfer Detail Referred Back
 * -- Author : Mohamed Fayas
 * -- Create date : 11 - December 2018
 * -- Description : This file contains the all CRUD for Payment Bank Transfer Detail Referred Back
 * -- REVISION HISTORY
 * -- Date: 11 - December 2018 By: Fayas Description: Added new functions named as
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentBankTransferDetailRefferedBackAPIRequest;
use App\Http\Requests\API\UpdatePaymentBankTransferDetailRefferedBackAPIRequest;
use App\Models\PaymentBankTransferDetailRefferedBack;
use App\Repositories\PaymentBankTransferDetailRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentBankTransferDetailRefferedBackController
 * @package App\Http\Controllers\API
 */

class PaymentBankTransferDetailRefferedBackAPIController extends AppBaseController
{
    /** @var  PaymentBankTransferDetailRefferedBackRepository */
    private $paymentBankTransferDetailRefferedBackRepository;

    public function __construct(PaymentBankTransferDetailRefferedBackRepository $paymentBankTransferDetailRefferedBackRepo)
    {
        $this->paymentBankTransferDetailRefferedBackRepository = $paymentBankTransferDetailRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransferDetailRefferedBacks",
     *      summary="Get a listing of the PaymentBankTransferDetailRefferedBacks.",
     *      tags={"PaymentBankTransferDetailRefferedBack"},
     *      description="Get all PaymentBankTransferDetailRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/PaymentBankTransferDetailRefferedBack")
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
        $this->paymentBankTransferDetailRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentBankTransferDetailRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentBankTransferDetailRefferedBacks = $this->paymentBankTransferDetailRefferedBackRepository->all();

        return $this->sendResponse($paymentBankTransferDetailRefferedBacks->toArray(), trans('custom.payment_bank_transfer_detail_reffered_backs_retrie'));
    }

    /**
     * @param CreatePaymentBankTransferDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentBankTransferDetailRefferedBacks",
     *      summary="Store a newly created PaymentBankTransferDetailRefferedBack in storage",
     *      tags={"PaymentBankTransferDetailRefferedBack"},
     *      description="Store PaymentBankTransferDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransferDetailRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransferDetailRefferedBack")
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
     *                  ref="#/definitions/PaymentBankTransferDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentBankTransferDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $paymentBankTransferDetailRefferedBacks = $this->paymentBankTransferDetailRefferedBackRepository->create($input);

        return $this->sendResponse($paymentBankTransferDetailRefferedBacks->toArray(), trans('custom.payment_bank_transfer_detail_reffered_back_saved_s'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransferDetailRefferedBacks/{id}",
     *      summary="Display the specified PaymentBankTransferDetailRefferedBack",
     *      tags={"PaymentBankTransferDetailRefferedBack"},
     *      description="Get PaymentBankTransferDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferDetailRefferedBack",
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
     *                  ref="#/definitions/PaymentBankTransferDetailRefferedBack"
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
        /** @var PaymentBankTransferDetailRefferedBack $paymentBankTransferDetailRefferedBack */
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            return $this->sendError(trans('custom.payment_bank_transfer_detail_reffered_back_not_fou'));
        }

        return $this->sendResponse($paymentBankTransferDetailRefferedBack->toArray(), trans('custom.payment_bank_transfer_detail_reffered_back_retriev'));
    }

    /**
     * @param int $id
     * @param UpdatePaymentBankTransferDetailRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentBankTransferDetailRefferedBacks/{id}",
     *      summary="Update the specified PaymentBankTransferDetailRefferedBack in storage",
     *      tags={"PaymentBankTransferDetailRefferedBack"},
     *      description="Update PaymentBankTransferDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferDetailRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransferDetailRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransferDetailRefferedBack")
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
     *                  ref="#/definitions/PaymentBankTransferDetailRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentBankTransferDetailRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentBankTransferDetailRefferedBack $paymentBankTransferDetailRefferedBack */
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            return $this->sendError(trans('custom.payment_bank_transfer_detail_reffered_back_not_fou'));
        }

        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->update($input, $id);

        return $this->sendResponse($paymentBankTransferDetailRefferedBack->toArray(), trans('custom.paymentbanktransferdetailrefferedback_updated_succ'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentBankTransferDetailRefferedBacks/{id}",
     *      summary="Remove the specified PaymentBankTransferDetailRefferedBack from storage",
     *      tags={"PaymentBankTransferDetailRefferedBack"},
     *      description="Delete PaymentBankTransferDetailRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransferDetailRefferedBack",
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
        /** @var PaymentBankTransferDetailRefferedBack $paymentBankTransferDetailRefferedBack */
        $paymentBankTransferDetailRefferedBack = $this->paymentBankTransferDetailRefferedBackRepository->findWithoutFail($id);

        if (empty($paymentBankTransferDetailRefferedBack)) {
            return $this->sendError(trans('custom.payment_bank_transfer_detail_reffered_back_not_fou'));
        }

        $paymentBankTransferDetailRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.payment_bank_transfer_detail_reffered_back_deleted'));
    }
}

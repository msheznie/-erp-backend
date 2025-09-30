<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentVoucherBankChargeDetailsAPIRequest;
use App\Http\Requests\API\UpdatePaymentVoucherBankChargeDetailsAPIRequest;
use App\Models\PaymentVoucherBankChargeDetails;
use App\Repositories\PaymentVoucherBankChargeDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentVoucherBankChargeDetailsController
 * @package App\Http\Controllers\API
 */

class PaymentVoucherBankChargeDetailsAPIController extends AppBaseController
{
    /** @var  PaymentVoucherBankChargeDetailsRepository */
    private $paymentVoucherBankChargeDetailsRepository;

    public function __construct(PaymentVoucherBankChargeDetailsRepository $paymentVoucherBankChargeDetailsRepo)
    {
        $this->paymentVoucherBankChargeDetailsRepository = $paymentVoucherBankChargeDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentVoucherBankChargeDetails",
     *      summary="getPaymentVoucherBankChargeDetailsList",
     *      tags={"PaymentVoucherBankChargeDetails"},
     *      description="Get all PaymentVoucherBankChargeDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/PaymentVoucherBankChargeDetails")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->paymentVoucherBankChargeDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentVoucherBankChargeDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->all();

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), trans('custom.payment_voucher_bank_charge_details_retrieved_succ'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/paymentVoucherBankChargeDetails",
     *      summary="createPaymentVoucherBankChargeDetails",
     *      tags={"PaymentVoucherBankChargeDetails"},
     *      description="Create PaymentVoucherBankChargeDetails",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PaymentVoucherBankChargeDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentVoucherBankChargeDetailsAPIRequest $request)
    {
        $input = $request->all();

        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->create($input);

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), trans('custom.payment_voucher_bank_charge_details_saved_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentVoucherBankChargeDetails/{id}",
     *      summary="getPaymentVoucherBankChargeDetailsItem",
     *      tags={"PaymentVoucherBankChargeDetails"},
     *      description="Get PaymentVoucherBankChargeDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentVoucherBankChargeDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PaymentVoucherBankChargeDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PaymentVoucherBankChargeDetails $paymentVoucherBankChargeDetails */
        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->findWithoutFail($id);

        if (empty($paymentVoucherBankChargeDetails)) {
            return $this->sendError(trans('custom.payment_voucher_bank_charge_details_not_found'));
        }

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), trans('custom.payment_voucher_bank_charge_details_retrieved_succ'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/paymentVoucherBankChargeDetails/{id}",
     *      summary="updatePaymentVoucherBankChargeDetails",
     *      tags={"PaymentVoucherBankChargeDetails"},
     *      description="Update PaymentVoucherBankChargeDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentVoucherBankChargeDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PaymentVoucherBankChargeDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentVoucherBankChargeDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentVoucherBankChargeDetails $paymentVoucherBankChargeDetails */
        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->findWithoutFail($id);

        if (empty($paymentVoucherBankChargeDetails)) {
            return $this->sendError(trans('custom.payment_voucher_bank_charge_details_not_found'));
        }

        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->update($input, $id);

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), trans('custom.paymentvoucherbankchargedetails_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/paymentVoucherBankChargeDetails/{id}",
     *      summary="deletePaymentVoucherBankChargeDetails",
     *      tags={"PaymentVoucherBankChargeDetails"},
     *      description="Delete PaymentVoucherBankChargeDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentVoucherBankChargeDetails",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PaymentVoucherBankChargeDetails $paymentVoucherBankChargeDetails */
        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->findWithoutFail($id);

        if (empty($paymentVoucherBankChargeDetails)) {
            return $this->sendError(trans('custom.payment_voucher_bank_charge_detail_not_found'));
        }

        if($paymentVoucherBankChargeDetails->master && $paymentVoucherBankChargeDetails->master->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_delete_detail_this_document_already_con'), 500);
        }

        $paymentVoucherBankChargeDetails->delete();

        return $this->sendResponse($id, trans('custom.payment_voucher_bank_charge_detail_deleted_success'));
    }
}

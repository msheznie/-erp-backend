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

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), 'Payment Voucher Bank Charge Details retrieved successfully');
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

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), 'Payment Voucher Bank Charge Details saved successfully');
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
            return $this->sendError('Payment Voucher Bank Charge Details not found');
        }

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), 'Payment Voucher Bank Charge Details retrieved successfully');
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
            return $this->sendError('Payment Voucher Bank Charge Details not found');
        }

        $paymentVoucherBankChargeDetails = $this->paymentVoucherBankChargeDetailsRepository->update($input, $id);

        return $this->sendResponse($paymentVoucherBankChargeDetails->toArray(), 'PaymentVoucherBankChargeDetails updated successfully');
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
            return $this->sendError('Payment Voucher Bank Charge Detail not found');
        }

        if($paymentVoucherBankChargeDetails->master && $paymentVoucherBankChargeDetails->master->confirmedYN){
            return $this->sendError('You cannot delete detail, this document already confirmed', 500);
        }

        $paymentVoucherBankChargeDetails->delete();

        return $this->sendResponse($id, 'Payment Voucher Bank Charge Detail deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentBankTransferAPIRequest;
use App\Http\Requests\API\UpdatePaymentBankTransferAPIRequest;
use App\Models\PaymentBankTransfer;
use App\Repositories\PaymentBankTransferRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentBankTransferController
 * @package App\Http\Controllers\API
 */

class PaymentBankTransferAPIController extends AppBaseController
{
    /** @var  PaymentBankTransferRepository */
    private $paymentBankTransferRepository;

    public function __construct(PaymentBankTransferRepository $paymentBankTransferRepo)
    {
        $this->paymentBankTransferRepository = $paymentBankTransferRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransfers",
     *      summary="Get a listing of the PaymentBankTransfers.",
     *      tags={"PaymentBankTransfer"},
     *      description="Get all PaymentBankTransfers",
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
     *                  @SWG\Items(ref="#/definitions/PaymentBankTransfer")
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
        $this->paymentBankTransferRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentBankTransferRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentBankTransfers = $this->paymentBankTransferRepository->all();

        return $this->sendResponse($paymentBankTransfers->toArray(), 'Payment Bank Transfers retrieved successfully');
    }

    /**
     * @param CreatePaymentBankTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paymentBankTransfers",
     *      summary="Store a newly created PaymentBankTransfer in storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Store PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransfer that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransfer")
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
     *                  ref="#/definitions/PaymentBankTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentBankTransferAPIRequest $request)
    {
        $input = $request->all();

        $paymentBankTransfers = $this->paymentBankTransferRepository->create($input);

        return $this->sendResponse($paymentBankTransfers->toArray(), 'Payment Bank Transfer saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Display the specified PaymentBankTransfer",
     *      tags={"PaymentBankTransfer"},
     *      description="Get PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
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
     *                  ref="#/definitions/PaymentBankTransfer"
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
        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        return $this->sendResponse($paymentBankTransfer->toArray(), 'Payment Bank Transfer retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaymentBankTransferAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Update the specified PaymentBankTransfer in storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Update PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaymentBankTransfer that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaymentBankTransfer")
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
     *                  ref="#/definitions/PaymentBankTransfer"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentBankTransferAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        $paymentBankTransfer = $this->paymentBankTransferRepository->update($input, $id);

        return $this->sendResponse($paymentBankTransfer->toArray(), 'PaymentBankTransfer updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paymentBankTransfers/{id}",
     *      summary="Remove the specified PaymentBankTransfer from storage",
     *      tags={"PaymentBankTransfer"},
     *      description="Delete PaymentBankTransfer",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaymentBankTransfer",
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
        /** @var PaymentBankTransfer $paymentBankTransfer */
        $paymentBankTransfer = $this->paymentBankTransferRepository->findWithoutFail($id);

        if (empty($paymentBankTransfer)) {
            return $this->sendError('Payment Bank Transfer not found');
        }

        $paymentBankTransfer->delete();

        return $this->sendResponse($id, 'Payment Bank Transfer deleted successfully');
    }
}

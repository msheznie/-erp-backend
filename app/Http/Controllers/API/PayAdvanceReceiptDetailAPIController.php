<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\AppBaseController;
use App\Http\Requests\API\CreatePayAdvanceReceiptDetailAPIRequest;
use App\Http\Requests\API\UpdatePayAdvanceReceiptDetailAPIRequest;
use App\Models\PayAdvanceReceiptDetail;
use App\Repositories\PayAdvanceReceiptDetailRepository;
use App\Services\PayAdvanceReceiptDetailsService;
use Illuminate\Http\Request;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Criteria\LimitOffsetCriteria;
use App\Models\PaySupplierInvoiceMaster;
use Response;

/**
 * Class PayAdvanceReceiptDetailController
 * @package App\Http\Controllers\API
 */
class PayAdvanceReceiptDetailAPIController extends AppBaseController
{
    /** @var PayAdvanceReceiptDetailRepository */
    private $payAdvanceReceiptDetailRepository;
    private PayAdvanceReceiptDetailsService $payAdvanceReceiptDetailService;

    public function __construct(
        PayAdvanceReceiptDetailRepository $payAdvanceReceiptDetailRepo,
        PayAdvanceReceiptDetailsService $payAdvanceReceiptDetailService
    ) {
        $this->payAdvanceReceiptDetailRepository = $payAdvanceReceiptDetailRepo;
        $this->payAdvanceReceiptDetailService = $payAdvanceReceiptDetailService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/payAdvanceReceiptDetails",
     *      summary="getPayAdvanceReceiptDetailList",
     *      tags={"PayAdvanceReceiptDetail"},
     *      description="Get all PayAdvanceReceiptDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/PayAdvanceReceiptDetail")
     *              ),
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->payAdvanceReceiptDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->payAdvanceReceiptDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $payAdvanceReceiptDetails = $this->payAdvanceReceiptDetailRepository->all();

        return $this->sendResponse($payAdvanceReceiptDetails->toArray(), 'Pay Advance Receipt Details retrieved successfully');
    }

    /**
     * @param CreatePayAdvanceReceiptDetailAPIRequest $request
     * @return Response
     *
     * @OA\Post(
     *      path="/payAdvanceReceiptDetails",
     *      summary="createPayAdvanceReceiptDetail",
     *      tags={"PayAdvanceReceiptDetail"},
     *      description="Create PayAdvanceReceiptDetail",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="data", ref="#/definitions/PayAdvanceReceiptDetail"),
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function store(CreatePayAdvanceReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        $payAdvanceReceiptDetail = $this->payAdvanceReceiptDetailRepository->create($input);

        return $this->sendResponse($payAdvanceReceiptDetail->toArray(), 'Pay Advance Receipt Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/payAdvanceReceiptDetails/{id}",
     *      summary="getPayAdvanceReceiptDetailItem",
     *      tags={"PayAdvanceReceiptDetail"},
     *      description="Get PayAdvanceReceiptDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayAdvanceReceiptDetail",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="data", ref="#/definitions/PayAdvanceReceiptDetail"),
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PayAdvanceReceiptDetail $payAdvanceReceiptDetail */
        $payAdvanceReceiptDetail = $this->payAdvanceReceiptDetailRepository->findWithoutFail($id);

        if (empty($payAdvanceReceiptDetail)) {
            return $this->sendError('Pay Advance Receipt Detail not found');
        }

        return $this->sendResponse($payAdvanceReceiptDetail->toArray(), 'Pay Advance Receipt Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePayAdvanceReceiptDetailAPIRequest $request
     * @return Response
     *
     * @OA\Put(
     *      path="/payAdvanceReceiptDetails/{id}",
     *      summary="updatePayAdvanceReceiptDetail",
     *      tags={"PayAdvanceReceiptDetail"},
     *      description="Update PayAdvanceReceiptDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayAdvanceReceiptDetail",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="data", ref="#/definitions/PayAdvanceReceiptDetail"),
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePayAdvanceReceiptDetailAPIRequest $request)
    {
        $input = $request->all();

        $result = $this->payAdvanceReceiptDetailService->update((int) $id, $input);

        if (!$result->isSuccess()) {
            return $this->sendError($result->getMessage(), $result->getStatusCode());
        }

        return $this->sendResponse($result->getData(), $result->getMessage());
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/payAdvanceReceiptDetails/{id}",
     *      summary="deletePayAdvanceReceiptDetail",
     *      tags={"PayAdvanceReceiptDetail"},
     *      description="Delete PayAdvanceReceiptDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayAdvanceReceiptDetail",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(property="success", type="boolean"),
     *              @OA\Property(property="data", type="string"),
     *              @OA\Property(property="message", type="string")
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PayAdvanceReceiptDetail $payAdvanceReceiptDetail */
        $payAdvanceReceiptDetail = $this->payAdvanceReceiptDetailRepository->findWithoutFail($id);

        if (empty($payAdvanceReceiptDetail)) {
            return $this->sendError(trans('custom.pay_advance_receipt_detail_not_found'));
        }

        $payAdvanceReceiptDetail->delete();

        return $this->sendResponse(null, trans('custom.pay_advance_receipt_detail_deleted_successfully'));
    }

    public function getAdvanceReceiptForPV(Request $request)
    {
        $input = $request->all();
        $result = $this->payAdvanceReceiptDetailService->getAdvanceReceiptForPV($input);
        if (!$result->isSuccess()) {
            return $this->sendError($result->getMessage(), $result->getStatusCode());
        }

        return $this->sendResponse($result->getData(), $result->getMessage());
    }

    public function getAdvanceReceiptPaymentDetails(Request $request)
    {
        $input = $request->all();
        $result = $this->payAdvanceReceiptDetailService->getAdvanceReceiptPaymentDetails($input);
        if (!$result->isSuccess()) {
            return $this->sendError($result->getMessage(), $result->getStatusCode());
        }

        return $this->sendResponse($result->getData(), $result->getMessage());
    }

    public function addAdvanceReceiptPaymentDetail(Request $request)
    {
        $input = $request->all();
        $result = $this->payAdvanceReceiptDetailService->addAdvanceReceiptPaymentDetail($input);
        if (!$result->isSuccess()) {
            return $this->sendError($result->getMessage(), $result->getStatusCode());
        }

        return $this->sendResponse($result->getData(), $result->getMessage());
    }

    public function deleteAllAdvanceReceiptPaymentDetail(Request $request)
    {
        $input = $request->all();
        $payMasterAutoId = $input['payMasterAutoId'];

        $payAdvanceReceiptDetails = PayAdvanceReceiptDetail::where('PayMasterAutoId', $payMasterAutoId)->delete();

        return $this->sendResponse(null, trans('custom.all_advance_receipt_payment_details_deleted_successfully'));
    }
}


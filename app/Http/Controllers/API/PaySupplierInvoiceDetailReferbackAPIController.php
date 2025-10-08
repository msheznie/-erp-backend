<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceDetailReferbackAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceDetailReferbackAPIRequest;
use App\Models\PaySupplierInvoiceDetailReferback;
use App\Repositories\PaySupplierInvoiceDetailReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceDetailReferbackController
 * @package App\Http\Controllers\API
 */
class PaySupplierInvoiceDetailReferbackAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailReferbackRepository */
    private $paySupplierInvoiceDetailReferbackRepository;

    public function __construct(PaySupplierInvoiceDetailReferbackRepository $paySupplierInvoiceDetailReferbackRepo)
    {
        $this->paySupplierInvoiceDetailReferbackRepository = $paySupplierInvoiceDetailReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetailReferbacks",
     *      summary="Get a listing of the PaySupplierInvoiceDetailReferbacks.",
     *      tags={"PaySupplierInvoiceDetailReferback"},
     *      description="Get all PaySupplierInvoiceDetailReferbacks",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceDetailReferback")
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
        $this->paySupplierInvoiceDetailReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceDetailReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceDetailReferbacks = $this->paySupplierInvoiceDetailReferbackRepository->all();

        return $this->sendResponse($paySupplierInvoiceDetailReferbacks->toArray(), trans('custom.pay_supplier_invoice_detail_referbacks_retrieved_s'));
    }

    /**
     * @param CreatePaySupplierInvoiceDetailReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceDetailReferbacks",
     *      summary="Store a newly created PaySupplierInvoiceDetailReferback in storage",
     *      tags={"PaySupplierInvoiceDetailReferback"},
     *      description="Store PaySupplierInvoiceDetailReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetailReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetailReferback")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetailReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceDetailReferbackAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetailReferbacks = $this->paySupplierInvoiceDetailReferbackRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceDetailReferbacks->toArray(), trans('custom.pay_supplier_invoice_detail_referback_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetailReferbacks/{id}",
     *      summary="Display the specified PaySupplierInvoiceDetailReferback",
     *      tags={"PaySupplierInvoiceDetailReferback"},
     *      description="Get PaySupplierInvoiceDetailReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetailReferback",
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
     *                  ref="#/definitions/PaySupplierInvoiceDetailReferback"
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
        /** @var PaySupplierInvoiceDetailReferback $paySupplierInvoiceDetailReferback */
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_detail_referback_not_found'));
        }

        return $this->sendResponse($paySupplierInvoiceDetailReferback->toArray(), trans('custom.pay_supplier_invoice_detail_referback_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceDetailReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceDetailReferbacks/{id}",
     *      summary="Update the specified PaySupplierInvoiceDetailReferback in storage",
     *      tags={"PaySupplierInvoiceDetailReferback"},
     *      description="Update PaySupplierInvoiceDetailReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetailReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetailReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetailReferback")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetailReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceDetailReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceDetailReferback $paySupplierInvoiceDetailReferback */
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_detail_referback_not_found'));
        }

        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->update($input, $id);

        return $this->sendResponse($paySupplierInvoiceDetailReferback->toArray(), trans('custom.paysupplierinvoicedetailreferback_updated_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceDetailReferbacks/{id}",
     *      summary="Remove the specified PaySupplierInvoiceDetailReferback from storage",
     *      tags={"PaySupplierInvoiceDetailReferback"},
     *      description="Delete PaySupplierInvoiceDetailReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetailReferback",
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
        /** @var PaySupplierInvoiceDetailReferback $paySupplierInvoiceDetailReferback */
        $paySupplierInvoiceDetailReferback = $this->paySupplierInvoiceDetailReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetailReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_detail_referback_not_found'));
        }

        $paySupplierInvoiceDetailReferback->delete();

        return $this->sendResponse($id, trans('custom.pay_supplier_invoice_detail_referback_deleted_succ'));
    }

    public function getPOPaymentHistoryDetails(Request $request)
    {
        $data = $this->paySupplierInvoiceDetailReferbackRepository->findWhere(['PayMasterAutoId' => $request->payMasterAutoId, 'timesReferred' => $request->timesReferred]);
        return $this->sendResponse($data, trans('custom.payment_details_retrieved_successfully'));
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceInvoicePaymentAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceInvoicePaymentAPIRequest;
use App\Models\POSSourceInvoicePayment;
use App\Repositories\POSSourceInvoicePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceInvoicePaymentController
 * @package App\Http\Controllers\API
 */

class POSSourceInvoicePaymentAPIController extends AppBaseController
{
    /** @var  POSSourceInvoicePaymentRepository */
    private $pOSSourceInvoicePaymentRepository;

    public function __construct(POSSourceInvoicePaymentRepository $pOSSourceInvoicePaymentRepo)
    {
        $this->pOSSourceInvoicePaymentRepository = $pOSSourceInvoicePaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceInvoicePayments",
     *      summary="Get a listing of the POSSourceInvoicePayments.",
     *      tags={"POSSourceInvoicePayment"},
     *      description="Get all POSSourceInvoicePayments",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceInvoicePayment")
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
        $this->pOSSourceInvoicePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceInvoicePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceInvoicePayments = $this->pOSSourceInvoicePaymentRepository->all();

        return $this->sendResponse($pOSSourceInvoicePayments->toArray(), trans('custom.p_o_s_source_invoice_payments_retrieved_successful'));
    }

    /**
     * @param CreatePOSSourceInvoicePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceInvoicePayments",
     *      summary="Store a newly created POSSourceInvoicePayment in storage",
     *      tags={"POSSourceInvoicePayment"},
     *      description="Store POSSourceInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceInvoicePayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceInvoicePayment")
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
     *                  ref="#/definitions/POSSourceInvoicePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceInvoicePaymentAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepository->create($input);

        return $this->sendResponse($pOSSourceInvoicePayment->toArray(), trans('custom.p_o_s_source_invoice_payment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceInvoicePayments/{id}",
     *      summary="Display the specified POSSourceInvoicePayment",
     *      tags={"POSSourceInvoicePayment"},
     *      description="Get POSSourceInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceInvoicePayment",
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
     *                  ref="#/definitions/POSSourceInvoicePayment"
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
        /** @var POSSourceInvoicePayment $pOSSourceInvoicePayment */
        $pOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_source_invoice_payment_not_found'));
        }

        return $this->sendResponse($pOSSourceInvoicePayment->toArray(), trans('custom.p_o_s_source_invoice_payment_retrieved_successfull'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceInvoicePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceInvoicePayments/{id}",
     *      summary="Update the specified POSSourceInvoicePayment in storage",
     *      tags={"POSSourceInvoicePayment"},
     *      description="Update POSSourceInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceInvoicePayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceInvoicePayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceInvoicePayment")
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
     *                  ref="#/definitions/POSSourceInvoicePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceInvoicePaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceInvoicePayment $pOSSourceInvoicePayment */
        $pOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_source_invoice_payment_not_found'));
        }

        $pOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepository->update($input, $id);

        return $this->sendResponse($pOSSourceInvoicePayment->toArray(), trans('custom.possourceinvoicepayment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceInvoicePayments/{id}",
     *      summary="Remove the specified POSSourceInvoicePayment from storage",
     *      tags={"POSSourceInvoicePayment"},
     *      description="Delete POSSourceInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceInvoicePayment",
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
        /** @var POSSourceInvoicePayment $pOSSourceInvoicePayment */
        $pOSSourceInvoicePayment = $this->pOSSourceInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_source_invoice_payment_not_found'));
        }

        $pOSSourceInvoicePayment->delete();

        return $this->sendSuccess('P O S Source Invoice Payment deleted successfully');
    }
}

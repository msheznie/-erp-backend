<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagInvoicePaymentAPIRequest;
use App\Http\Requests\API\UpdatePOSStagInvoicePaymentAPIRequest;
use App\Models\POSStagInvoicePayment;
use App\Repositories\POSStagInvoicePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagInvoicePaymentController
 * @package App\Http\Controllers\API
 */

class POSStagInvoicePaymentAPIController extends AppBaseController
{
    /** @var  POSStagInvoicePaymentRepository */
    private $pOSStagInvoicePaymentRepository;

    public function __construct(POSStagInvoicePaymentRepository $pOSStagInvoicePaymentRepo)
    {
        $this->pOSStagInvoicePaymentRepository = $pOSStagInvoicePaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagInvoicePayments",
     *      summary="Get a listing of the POSStagInvoicePayments.",
     *      tags={"POSStagInvoicePayment"},
     *      description="Get all POSStagInvoicePayments",
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
     *                  @SWG\Items(ref="#/definitions/POSStagInvoicePayment")
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
        $this->pOSStagInvoicePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagInvoicePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagInvoicePayments = $this->pOSStagInvoicePaymentRepository->all();

        return $this->sendResponse($pOSStagInvoicePayments->toArray(), trans('custom.p_o_s_stag_invoice_payments_retrieved_successfully'));
    }

    /**
     * @param CreatePOSStagInvoicePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagInvoicePayments",
     *      summary="Store a newly created POSStagInvoicePayment in storage",
     *      tags={"POSStagInvoicePayment"},
     *      description="Store POSStagInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagInvoicePayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagInvoicePayment")
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
     *                  ref="#/definitions/POSStagInvoicePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagInvoicePaymentAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepository->create($input);

        return $this->sendResponse($pOSStagInvoicePayment->toArray(), trans('custom.p_o_s_stag_invoice_payment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagInvoicePayments/{id}",
     *      summary="Display the specified POSStagInvoicePayment",
     *      tags={"POSStagInvoicePayment"},
     *      description="Get POSStagInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagInvoicePayment",
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
     *                  ref="#/definitions/POSStagInvoicePayment"
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
        /** @var POSStagInvoicePayment $pOSStagInvoicePayment */
        $pOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSStagInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_invoice_payment_not_found'));
        }

        return $this->sendResponse($pOSStagInvoicePayment->toArray(), trans('custom.p_o_s_stag_invoice_payment_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagInvoicePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagInvoicePayments/{id}",
     *      summary="Update the specified POSStagInvoicePayment in storage",
     *      tags={"POSStagInvoicePayment"},
     *      description="Update POSStagInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagInvoicePayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagInvoicePayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagInvoicePayment")
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
     *                  ref="#/definitions/POSStagInvoicePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagInvoicePaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagInvoicePayment $pOSStagInvoicePayment */
        $pOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSStagInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_invoice_payment_not_found'));
        }

        $pOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepository->update($input, $id);

        return $this->sendResponse($pOSStagInvoicePayment->toArray(), trans('custom.posstaginvoicepayment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagInvoicePayments/{id}",
     *      summary="Remove the specified POSStagInvoicePayment from storage",
     *      tags={"POSStagInvoicePayment"},
     *      description="Delete POSStagInvoicePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagInvoicePayment",
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
        /** @var POSStagInvoicePayment $pOSStagInvoicePayment */
        $pOSStagInvoicePayment = $this->pOSStagInvoicePaymentRepository->findWithoutFail($id);

        if (empty($pOSStagInvoicePayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_invoice_payment_not_found'));
        }

        $pOSStagInvoicePayment->delete();

        return $this->sendSuccess('P O S Stag Invoice Payment deleted successfully');
    }
}

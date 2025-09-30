<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSStagMenuSalesPaymentAPIRequest;
use App\Http\Requests\API\UpdatePOSStagMenuSalesPaymentAPIRequest;
use App\Models\POSStagMenuSalesPayment;
use App\Repositories\POSStagMenuSalesPaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSStagMenuSalesPaymentController
 * @package App\Http\Controllers\API
 */

class POSStagMenuSalesPaymentAPIController extends AppBaseController
{
    /** @var  POSStagMenuSalesPaymentRepository */
    private $pOSStagMenuSalesPaymentRepository;

    public function __construct(POSStagMenuSalesPaymentRepository $pOSStagMenuSalesPaymentRepo)
    {
        $this->pOSStagMenuSalesPaymentRepository = $pOSStagMenuSalesPaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesPayments",
     *      summary="Get a listing of the POSStagMenuSalesPayments.",
     *      tags={"POSStagMenuSalesPayment"},
     *      description="Get all POSStagMenuSalesPayments",
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
     *                  @SWG\Items(ref="#/definitions/POSStagMenuSalesPayment")
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
        $this->pOSStagMenuSalesPaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSStagMenuSalesPaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSStagMenuSalesPayments = $this->pOSStagMenuSalesPaymentRepository->all();

        return $this->sendResponse($pOSStagMenuSalesPayments->toArray(), trans('custom.p_o_s_stag_menu_sales_payments_retrieved_successfu'));
    }

    /**
     * @param CreatePOSStagMenuSalesPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSStagMenuSalesPayments",
     *      summary="Store a newly created POSStagMenuSalesPayment in storage",
     *      tags={"POSStagMenuSalesPayment"},
     *      description="Store POSStagMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesPayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesPayment")
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
     *                  ref="#/definitions/POSStagMenuSalesPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSStagMenuSalesPaymentAPIRequest $request)
    {
        $input = $request->all();

        $pOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepository->create($input);

        return $this->sendResponse($pOSStagMenuSalesPayment->toArray(), trans('custom.p_o_s_stag_menu_sales_payment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSStagMenuSalesPayments/{id}",
     *      summary="Display the specified POSStagMenuSalesPayment",
     *      tags={"POSStagMenuSalesPayment"},
     *      description="Get POSStagMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesPayment",
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
     *                  ref="#/definitions/POSStagMenuSalesPayment"
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
        /** @var POSStagMenuSalesPayment $pOSStagMenuSalesPayment */
        $pOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_payment_not_found'));
        }

        return $this->sendResponse($pOSStagMenuSalesPayment->toArray(), trans('custom.p_o_s_stag_menu_sales_payment_retrieved_successful'));
    }

    /**
     * @param int $id
     * @param UpdatePOSStagMenuSalesPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSStagMenuSalesPayments/{id}",
     *      summary="Update the specified POSStagMenuSalesPayment in storage",
     *      tags={"POSStagMenuSalesPayment"},
     *      description="Update POSStagMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesPayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSStagMenuSalesPayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSStagMenuSalesPayment")
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
     *                  ref="#/definitions/POSStagMenuSalesPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSStagMenuSalesPaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSStagMenuSalesPayment $pOSStagMenuSalesPayment */
        $pOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_payment_not_found'));
        }

        $pOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepository->update($input, $id);

        return $this->sendResponse($pOSStagMenuSalesPayment->toArray(), trans('custom.posstagmenusalespayment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSStagMenuSalesPayments/{id}",
     *      summary="Remove the specified POSStagMenuSalesPayment from storage",
     *      tags={"POSStagMenuSalesPayment"},
     *      description="Delete POSStagMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSStagMenuSalesPayment",
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
        /** @var POSStagMenuSalesPayment $pOSStagMenuSalesPayment */
        $pOSStagMenuSalesPayment = $this->pOSStagMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSStagMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_stag_menu_sales_payment_not_found'));
        }

        $pOSStagMenuSalesPayment->delete();

        return $this->sendSuccess('P O S Stag Menu Sales Payment deleted successfully');
    }
}

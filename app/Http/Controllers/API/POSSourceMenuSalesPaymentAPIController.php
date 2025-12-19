<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePOSSourceMenuSalesPaymentAPIRequest;
use App\Http\Requests\API\UpdatePOSSourceMenuSalesPaymentAPIRequest;
use App\Models\POSSourceMenuSalesPayment;
use App\Repositories\POSSourceMenuSalesPaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class POSSourceMenuSalesPaymentController
 * @package App\Http\Controllers\API
 */

class POSSourceMenuSalesPaymentAPIController extends AppBaseController
{
    /** @var  POSSourceMenuSalesPaymentRepository */
    private $pOSSourceMenuSalesPaymentRepository;

    public function __construct(POSSourceMenuSalesPaymentRepository $pOSSourceMenuSalesPaymentRepo)
    {
        $this->pOSSourceMenuSalesPaymentRepository = $pOSSourceMenuSalesPaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesPayments",
     *      summary="Get a listing of the POSSourceMenuSalesPayments.",
     *      tags={"POSSourceMenuSalesPayment"},
     *      description="Get all POSSourceMenuSalesPayments",
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
     *                  @SWG\Items(ref="#/definitions/POSSourceMenuSalesPayment")
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
        $this->pOSSourceMenuSalesPaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->pOSSourceMenuSalesPaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $pOSSourceMenuSalesPayments = $this->pOSSourceMenuSalesPaymentRepository->all();

        return $this->sendResponse($pOSSourceMenuSalesPayments->toArray(), trans('custom.p_o_s_source_menu_sales_payments_retrieved_success'));
    }

    /**
     * @param CreatePOSSourceMenuSalesPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/pOSSourceMenuSalesPayments",
     *      summary="Store a newly created POSSourceMenuSalesPayment in storage",
     *      tags={"POSSourceMenuSalesPayment"},
     *      description="Store POSSourceMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesPayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesPayment")
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
     *                  ref="#/definitions/POSSourceMenuSalesPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePOSSourceMenuSalesPaymentAPIRequest $request)
    {
        $input = $request->all();

        $pOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepository->create($input);

        return $this->sendResponse($pOSSourceMenuSalesPayment->toArray(), trans('custom.p_o_s_source_menu_sales_payment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/pOSSourceMenuSalesPayments/{id}",
     *      summary="Display the specified POSSourceMenuSalesPayment",
     *      tags={"POSSourceMenuSalesPayment"},
     *      description="Get POSSourceMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesPayment",
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
     *                  ref="#/definitions/POSSourceMenuSalesPayment"
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
        /** @var POSSourceMenuSalesPayment $pOSSourceMenuSalesPayment */
        $pOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_payment_not_found'));
        }

        return $this->sendResponse($pOSSourceMenuSalesPayment->toArray(), trans('custom.p_o_s_source_menu_sales_payment_retrieved_successf'));
    }

    /**
     * @param int $id
     * @param UpdatePOSSourceMenuSalesPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/pOSSourceMenuSalesPayments/{id}",
     *      summary="Update the specified POSSourceMenuSalesPayment in storage",
     *      tags={"POSSourceMenuSalesPayment"},
     *      description="Update POSSourceMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesPayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="POSSourceMenuSalesPayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/POSSourceMenuSalesPayment")
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
     *                  ref="#/definitions/POSSourceMenuSalesPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePOSSourceMenuSalesPaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var POSSourceMenuSalesPayment $pOSSourceMenuSalesPayment */
        $pOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_payment_not_found'));
        }

        $pOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepository->update($input, $id);

        return $this->sendResponse($pOSSourceMenuSalesPayment->toArray(), trans('custom.possourcemenusalespayment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/pOSSourceMenuSalesPayments/{id}",
     *      summary="Remove the specified POSSourceMenuSalesPayment from storage",
     *      tags={"POSSourceMenuSalesPayment"},
     *      description="Delete POSSourceMenuSalesPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of POSSourceMenuSalesPayment",
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
        /** @var POSSourceMenuSalesPayment $pOSSourceMenuSalesPayment */
        $pOSSourceMenuSalesPayment = $this->pOSSourceMenuSalesPaymentRepository->findWithoutFail($id);

        if (empty($pOSSourceMenuSalesPayment)) {
            return $this->sendError(trans('custom.p_o_s_source_menu_sales_payment_not_found'));
        }

        $pOSSourceMenuSalesPayment->delete();

        return $this->sendSuccess('P O S Source Menu Sales Payment deleted successfully');
    }
}

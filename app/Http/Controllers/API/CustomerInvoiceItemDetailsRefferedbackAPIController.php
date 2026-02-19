<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceItemDetailsRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceItemDetailsRefferedbackAPIRequest;
use App\Models\CustomerInvoiceItemDetailsRefferedback;
use App\Repositories\CustomerInvoiceItemDetailsRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceItemDetailsRefferedbackController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceItemDetailsRefferedbackAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceItemDetailsRefferedbackRepository */
    private $customerInvoiceItemDetailsRefferedbackRepository;

    public function __construct(CustomerInvoiceItemDetailsRefferedbackRepository $customerInvoiceItemDetailsRefferedbackRepo)
    {
        $this->customerInvoiceItemDetailsRefferedbackRepository = $customerInvoiceItemDetailsRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetailsRefferedbacks",
     *      summary="Get a listing of the CustomerInvoiceItemDetailsRefferedbacks.",
     *      tags={"CustomerInvoiceItemDetailsRefferedback"},
     *      description="Get all CustomerInvoiceItemDetailsRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceItemDetailsRefferedback")
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
        $this->customerInvoiceItemDetailsRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceItemDetailsRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceItemDetailsRefferedbacks = $this->customerInvoiceItemDetailsRefferedbackRepository->all();

        return $this->sendResponse($customerInvoiceItemDetailsRefferedbacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
    }

    /**
     * @param CreateCustomerInvoiceItemDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceItemDetailsRefferedbacks",
     *      summary="Store a newly created CustomerInvoiceItemDetailsRefferedback in storage",
     *      tags={"CustomerInvoiceItemDetailsRefferedback"},
     *      description="Store CustomerInvoiceItemDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetailsRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetailsRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceItemDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepository->create($input);

        return $this->sendResponse($customerInvoiceItemDetailsRefferedback->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceItemDetailsRefferedbacks/{id}",
     *      summary="Display the specified CustomerInvoiceItemDetailsRefferedback",
     *      tags={"CustomerInvoiceItemDetailsRefferedback"},
     *      description="Get CustomerInvoiceItemDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetailsRefferedback",
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
     *                  ref="#/definitions/CustomerInvoiceItemDetailsRefferedback"
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
        /** @var CustomerInvoiceItemDetailsRefferedback $customerInvoiceItemDetailsRefferedback */
        $customerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetailsRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
        }

        return $this->sendResponse($customerInvoiceItemDetailsRefferedback->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceItemDetailsRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceItemDetailsRefferedbacks/{id}",
     *      summary="Update the specified CustomerInvoiceItemDetailsRefferedback in storage",
     *      tags={"CustomerInvoiceItemDetailsRefferedback"},
     *      description="Update CustomerInvoiceItemDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetailsRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceItemDetailsRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceItemDetailsRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceItemDetailsRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceItemDetailsRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceItemDetailsRefferedback $customerInvoiceItemDetailsRefferedback */
        $customerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetailsRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
        }

        $customerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceItemDetailsRefferedback->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceItemDetailsRefferedbacks/{id}",
     *      summary="Remove the specified CustomerInvoiceItemDetailsRefferedback from storage",
     *      tags={"CustomerInvoiceItemDetailsRefferedback"},
     *      description="Delete CustomerInvoiceItemDetailsRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceItemDetailsRefferedback",
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
        /** @var CustomerInvoiceItemDetailsRefferedback $customerInvoiceItemDetailsRefferedback */
        $customerInvoiceItemDetailsRefferedback = $this->customerInvoiceItemDetailsRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceItemDetailsRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
        }

        $customerInvoiceItemDetailsRefferedback->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.customer_invoice_item_details_refferedbacks')]));
    }
}

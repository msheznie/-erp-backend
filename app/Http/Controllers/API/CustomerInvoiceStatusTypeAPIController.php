<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceStatusTypeAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceStatusTypeAPIRequest;
use App\Models\CustomerInvoiceStatusType;
use App\Repositories\CustomerInvoiceStatusTypeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceStatusTypeController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceStatusTypeAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceStatusTypeRepository */
    private $customerInvoiceStatusTypeRepository;

    public function __construct(CustomerInvoiceStatusTypeRepository $customerInvoiceStatusTypeRepo)
    {
        $this->customerInvoiceStatusTypeRepository = $customerInvoiceStatusTypeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceStatusTypes",
     *      summary="Get a listing of the CustomerInvoiceStatusTypes.",
     *      tags={"CustomerInvoiceStatusType"},
     *      description="Get all CustomerInvoiceStatusTypes",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceStatusType")
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
        $this->customerInvoiceStatusTypeRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceStatusTypeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceStatusTypes = $this->customerInvoiceStatusTypeRepository->all();

        return $this->sendResponse($customerInvoiceStatusTypes->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_status_types')]));
    }

    /**
     * @param CreateCustomerInvoiceStatusTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceStatusTypes",
     *      summary="Store a newly created CustomerInvoiceStatusType in storage",
     *      tags={"CustomerInvoiceStatusType"},
     *      description="Store CustomerInvoiceStatusType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceStatusType that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceStatusType")
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
     *                  ref="#/definitions/CustomerInvoiceStatusType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceStatusTypeAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceStatusType = $this->customerInvoiceStatusTypeRepository->create($input);

        return $this->sendResponse($customerInvoiceStatusType->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_status_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceStatusTypes/{id}",
     *      summary="Display the specified CustomerInvoiceStatusType",
     *      tags={"CustomerInvoiceStatusType"},
     *      description="Get CustomerInvoiceStatusType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceStatusType",
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
     *                  ref="#/definitions/CustomerInvoiceStatusType"
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
        /** @var CustomerInvoiceStatusType $customerInvoiceStatusType */
        $customerInvoiceStatusType = $this->customerInvoiceStatusTypeRepository->findWithoutFail($id);

        if (empty($customerInvoiceStatusType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_status_types')]));
        }

        return $this->sendResponse($customerInvoiceStatusType->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_status_types')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceStatusTypeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceStatusTypes/{id}",
     *      summary="Update the specified CustomerInvoiceStatusType in storage",
     *      tags={"CustomerInvoiceStatusType"},
     *      description="Update CustomerInvoiceStatusType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceStatusType",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceStatusType that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceStatusType")
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
     *                  ref="#/definitions/CustomerInvoiceStatusType"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceStatusTypeAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceStatusType $customerInvoiceStatusType */
        $customerInvoiceStatusType = $this->customerInvoiceStatusTypeRepository->findWithoutFail($id);

        if (empty($customerInvoiceStatusType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_status_types')]));
        }

        $customerInvoiceStatusType = $this->customerInvoiceStatusTypeRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceStatusType->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_status_types')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceStatusTypes/{id}",
     *      summary="Remove the specified CustomerInvoiceStatusType from storage",
     *      tags={"CustomerInvoiceStatusType"},
     *      description="Delete CustomerInvoiceStatusType",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceStatusType",
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
        /** @var CustomerInvoiceStatusType $customerInvoiceStatusType */
        $customerInvoiceStatusType = $this->customerInvoiceStatusTypeRepository->findWithoutFail($id);

        if (empty($customerInvoiceStatusType)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_status_types')]));
        }

        $customerInvoiceStatusType->delete();

        return $this->sendSuccess(trans('custom.delete', ['attribute' => trans('custom.customer_invoice_status_types')]));
    }
}

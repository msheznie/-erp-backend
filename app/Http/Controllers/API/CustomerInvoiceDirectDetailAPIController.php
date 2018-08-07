<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectDetailAPIRequest;
use App\Models\CustomerInvoiceDirectDetail;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceDirectDetailController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceDirectDetailAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectDetailRepository */
    private $customerInvoiceDirectDetailRepository;

    public function __construct(CustomerInvoiceDirectDetailRepository $customerInvoiceDirectDetailRepo)
    {
        $this->customerInvoiceDirectDetailRepository = $customerInvoiceDirectDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetails",
     *      summary="Get a listing of the CustomerInvoiceDirectDetails.",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Get all CustomerInvoiceDirectDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirectDetail")
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
        $this->customerInvoiceDirectDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirectDetails = $this->customerInvoiceDirectDetailRepository->all();

        return $this->sendResponse($customerInvoiceDirectDetails->toArray(), 'Customer Invoice Direct Details retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceDirectDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirectDetails",
     *      summary="Store a newly created CustomerInvoiceDirectDetail in storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Store CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetail")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectDetailAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectDetails = $this->customerInvoiceDirectDetailRepository->create($input);

        return $this->sendResponse($customerInvoiceDirectDetails->toArray(), 'Customer Invoice Direct Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Display the specified CustomerInvoiceDirectDetail",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Get CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
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
        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        return $this->sendResponse($customerInvoiceDirectDetail->toArray(), 'Customer Invoice Direct Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Update the specified CustomerInvoiceDirectDetail in storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Update CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetail")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirectDetail->toArray(), 'CustomerInvoiceDirectDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirectDetails/{id}",
     *      summary="Remove the specified CustomerInvoiceDirectDetail from storage",
     *      tags={"CustomerInvoiceDirectDetail"},
     *      description="Delete CustomerInvoiceDirectDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetail",
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
        /** @var CustomerInvoiceDirectDetail $customerInvoiceDirectDetail */
        $customerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetail)) {
            return $this->sendError('Customer Invoice Direct Detail not found');
        }

        $customerInvoiceDirectDetail->delete();

        return $this->sendResponse($id, 'Customer Invoice Direct Detail deleted successfully');
    }
}

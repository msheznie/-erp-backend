<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentDetailAPIRequest;
use App\Models\CustomerReceivePaymentDetail;
use App\Repositories\CustomerReceivePaymentDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerReceivePaymentDetailController
 * @package App\Http\Controllers\API
 */

class CustomerReceivePaymentDetailAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentDetailRepository */
    private $customerReceivePaymentDetailRepository;

    public function __construct(CustomerReceivePaymentDetailRepository $customerReceivePaymentDetailRepo)
    {
        $this->customerReceivePaymentDetailRepository = $customerReceivePaymentDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentDetails",
     *      summary="Get a listing of the CustomerReceivePaymentDetails.",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Get all CustomerReceivePaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePaymentDetail")
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
        $this->customerReceivePaymentDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->all();

        return $this->sendResponse($customerReceivePaymentDetails->toArray(), 'Customer Receive Payment Details retrieved successfully');
    }

    /**
     * @param CreateCustomerReceivePaymentDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePaymentDetails",
     *      summary="Store a newly created CustomerReceivePaymentDetail in storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Store CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentDetail")
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->create($input);

        return $this->sendResponse($customerReceivePaymentDetails->toArray(), 'Customer Receive Payment Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Display the specified CustomerReceivePaymentDetail",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Get CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
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
        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        return $this->sendResponse($customerReceivePaymentDetail->toArray(), 'Customer Receive Payment Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Update the specified CustomerReceivePaymentDetail in storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Update CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentDetail")
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
     *                  ref="#/definitions/CustomerReceivePaymentDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update($input, $id);

        return $this->sendResponse($customerReceivePaymentDetail->toArray(), 'CustomerReceivePaymentDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePaymentDetails/{id}",
     *      summary="Remove the specified CustomerReceivePaymentDetail from storage",
     *      tags={"CustomerReceivePaymentDetail"},
     *      description="Delete CustomerReceivePaymentDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentDetail",
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
        /** @var CustomerReceivePaymentDetail $customerReceivePaymentDetail */
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentDetail)) {
            return $this->sendError('Customer Receive Payment Detail not found');
        }

        $customerReceivePaymentDetail->delete();

        return $this->sendResponse($id, 'Customer Receive Payment Detail deleted successfully');
    }
}

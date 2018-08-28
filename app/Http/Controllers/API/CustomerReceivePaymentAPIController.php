<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentAPIRequest;
use App\Models\CustomerReceivePayment;
use App\Repositories\CustomerReceivePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerReceivePaymentController
 * @package App\Http\Controllers\API
 */

class CustomerReceivePaymentAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRepository */
    private $customerReceivePaymentRepository;

    public function __construct(CustomerReceivePaymentRepository $customerReceivePaymentRepo)
    {
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments",
     *      summary="Get a listing of the CustomerReceivePayments.",
     *      tags={"CustomerReceivePayment"},
     *      description="Get all CustomerReceivePayments",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePayment")
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
        $this->customerReceivePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePayments = $this->customerReceivePaymentRepository->all();

        return $this->sendResponse($customerReceivePayments->toArray(), 'Customer Receive Payments retrieved successfully');
    }

    /**
     * @param CreateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePayments",
     *      summary="Store a newly created CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Store CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentAPIRequest $request)
    {
        $input = $request->all();

        $customerReceivePayments = $this->customerReceivePaymentRepository->create($input);

        return $this->sendResponse($customerReceivePayments->toArray(), 'Customer Receive Payment saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePayments/{id}",
     *      summary="Display the specified CustomerReceivePayment",
     *      tags={"CustomerReceivePayment"},
     *      description="Get CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
     *                  ref="#/definitions/CustomerReceivePayment"
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
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        return $this->sendResponse($customerReceivePayment->toArray(), 'Customer Receive Payment retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePayments/{id}",
     *      summary="Update the specified CustomerReceivePayment in storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Update CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePayment")
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
     *                  ref="#/definitions/CustomerReceivePayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePayment = $this->customerReceivePaymentRepository->update($input, $id);

        return $this->sendResponse($customerReceivePayment->toArray(), 'CustomerReceivePayment updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePayments/{id}",
     *      summary="Remove the specified CustomerReceivePayment from storage",
     *      tags={"CustomerReceivePayment"},
     *      description="Delete CustomerReceivePayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePayment",
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
        /** @var CustomerReceivePayment $customerReceivePayment */
        $customerReceivePayment = $this->customerReceivePaymentRepository->findWithoutFail($id);

        if (empty($customerReceivePayment)) {
            return $this->sendError('Customer Receive Payment not found');
        }

        $customerReceivePayment->delete();

        return $this->sendResponse($id, 'Customer Receive Payment deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceLogisticAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceLogisticAPIRequest;
use App\Models\CustomerInvoiceLogistic;
use App\Repositories\CustomerInvoiceLogisticRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceLogisticController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceLogisticAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceLogisticRepository */
    private $customerInvoiceLogisticRepository;

    public function __construct(CustomerInvoiceLogisticRepository $customerInvoiceLogisticRepo)
    {
        $this->customerInvoiceLogisticRepository = $customerInvoiceLogisticRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceLogistics",
     *      summary="Get a listing of the CustomerInvoiceLogistics.",
     *      tags={"CustomerInvoiceLogistic"},
     *      description="Get all CustomerInvoiceLogistics",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceLogistic")
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
        $this->customerInvoiceLogisticRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceLogisticRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceLogistics = $this->customerInvoiceLogisticRepository->all();

        return $this->sendResponse($customerInvoiceLogistics->toArray(), trans('custom.customer_invoice_logistics_retrieved_successfully'));
    }

    /**
     * @param CreateCustomerInvoiceLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceLogistics",
     *      summary="Store a newly created CustomerInvoiceLogistic in storage",
     *      tags={"CustomerInvoiceLogistic"},
     *      description="Store CustomerInvoiceLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceLogistic that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceLogistic")
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
     *                  ref="#/definitions/CustomerInvoiceLogistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceLogisticAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->create($input);

        return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customer_invoice_logistic_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceLogistics/{id}",
     *      summary="Display the specified CustomerInvoiceLogistic",
     *      tags={"CustomerInvoiceLogistic"},
     *      description="Get CustomerInvoiceLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceLogistic",
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
     *                  ref="#/definitions/CustomerInvoiceLogistic"
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
        /** @var CustomerInvoiceLogistic $customerInvoiceLogistic */
        $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->findWithoutFail($id);

        if (empty($customerInvoiceLogistic)) {
            return $this->sendError(trans('custom.customer_invoice_logistic_not_found'));
        }

        return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customer_invoice_logistic_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceLogisticAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceLogistics/{id}",
     *      summary="Update the specified CustomerInvoiceLogistic in storage",
     *      tags={"CustomerInvoiceLogistic"},
     *      description="Update CustomerInvoiceLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceLogistic",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceLogistic that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceLogistic")
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
     *                  ref="#/definitions/CustomerInvoiceLogistic"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceLogisticAPIRequest $request)
    {
        $input = $request->all();

        $invoiceLogistic = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID',$input['custInvoiceDirectAutoID'])->first();

        if($invoiceLogistic){
                        /** @var CustomerInvoiceLogistic $customerInvoiceLogistic */
            $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->findWithoutFail($invoiceLogistic['id']);

            if (empty($customerInvoiceLogistic)) {
                return $this->sendError(trans('custom.customer_invoice_logistic_not_found'));
            }
            $input = $this->convertArrayToSelectedValue($input, array('port_of_discharge', 'port_of_loading'));

            $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->update($input, $invoiceLogistic['id']);

            return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customerinvoicelogistic_updated_successfully'));
            
        } else {
            $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->create($input);

            return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customer_invoice_logistic_saved_successfully'));        
        }


    }

    public function addNote(Request $request){
        $input = $request->all();
        $custInvoiceDirectAutoID = $request['custInvoiceDirectAutoID'];

        $invoiceLogistic = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->first();

            if($invoiceLogistic){
                /** @var CustomerInvoiceLogistic $customerInvoiceLogistic */
                $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->findWithoutFail($invoiceLogistic['id']);

                if (empty($customerInvoiceLogistic)) {
                    return $this->sendError(trans('custom.customer_invoice_logistic_not_found'));
                }

                $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->update($input, $invoiceLogistic['id']);

                return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customerinvoicelogistic_updated_successfully'));
                
            } else {
                $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->create($input);

                return $this->sendResponse($customerInvoiceLogistic->toArray(), trans('custom.customer_invoice_logistic_saved_successfully'));        
            }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceLogistics/{id}",
     *      summary="Remove the specified CustomerInvoiceLogistic from storage",
     *      tags={"CustomerInvoiceLogistic"},
     *      description="Delete CustomerInvoiceLogistic",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceLogistic",
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
        /** @var CustomerInvoiceLogistic $customerInvoiceLogistic */
        $customerInvoiceLogistic = $this->customerInvoiceLogisticRepository->findWithoutFail($id);

        if (empty($customerInvoiceLogistic)) {
            return $this->sendError(trans('custom.customer_invoice_logistic_not_found'));
        }

        $customerInvoiceLogistic->delete();

        return $this->sendSuccess('Customer Invoice Logistic deleted successfully');
    }

    public function getInvoiceLogistic(Request $request){

        $custInvoiceDirectAutoID = $request->custInvoiceDirectAutoID;

        $invoiceLogistic = CustomerInvoiceLogistic::where('custInvoiceDirectAutoID', $custInvoiceDirectAutoID)
            ->first();

        return $this->sendResponse($invoiceLogistic, trans('custom.invoice_logistic_details_retrieved_successfully'));

    }
}

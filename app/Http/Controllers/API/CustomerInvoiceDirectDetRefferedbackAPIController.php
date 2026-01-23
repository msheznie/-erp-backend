<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceDirectDetRefferedbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Invoice Direct Det Refferedback
 * -- Author : Mohamed Nazir
 * -- Create date : 28 - November 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 28-November 2018 By: Nazir Description: Added new function getCIDetailsForAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectDetRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectDetRefferedbackAPIRequest;
use App\Models\CustomerInvoiceDirectDetRefferedback;
use App\Models\CustomerInvoiceDirectRefferedback;
use App\Models\CustomerInvoiceItemDetailsRefferedback;
use App\Repositories\CustomerInvoiceDirectDetRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceDirectDetRefferedbackController
 * @package App\Http\Controllers\API
 */

class CustomerInvoiceDirectDetRefferedbackAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectDetRefferedbackRepository */
    private $customerInvoiceDirectDetRefferedbackRepository;

    public function __construct(CustomerInvoiceDirectDetRefferedbackRepository $customerInvoiceDirectDetRefferedbackRepo)
    {
        $this->customerInvoiceDirectDetRefferedbackRepository = $customerInvoiceDirectDetRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetRefferedbacks",
     *      summary="Get a listing of the CustomerInvoiceDirectDetRefferedbacks.",
     *      tags={"CustomerInvoiceDirectDetRefferedback"},
     *      description="Get all CustomerInvoiceDirectDetRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirectDetRefferedback")
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
        $this->customerInvoiceDirectDetRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectDetRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirectDetRefferedbacks = $this->customerInvoiceDirectDetRefferedbackRepository->all();

        return $this->sendResponse($customerInvoiceDirectDetRefferedbacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
    }

    /**
     * @param CreateCustomerInvoiceDirectDetRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirectDetRefferedbacks",
     *      summary="Store a newly created CustomerInvoiceDirectDetRefferedback in storage",
     *      tags={"CustomerInvoiceDirectDetRefferedback"},
     *      description="Store CustomerInvoiceDirectDetRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectDetRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectDetRefferedbacks = $this->customerInvoiceDirectDetRefferedbackRepository->create($input);

        return $this->sendResponse($customerInvoiceDirectDetRefferedbacks->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectDetRefferedbacks/{id}",
     *      summary="Display the specified CustomerInvoiceDirectDetRefferedback",
     *      tags={"CustomerInvoiceDirectDetRefferedback"},
     *      description="Get CustomerInvoiceDirectDetRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetRefferedback",
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetRefferedback"
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
        /** @var CustomerInvoiceDirectDetRefferedback $customerInvoiceDirectDetRefferedback */
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
        }

        return $this->sendResponse($customerInvoiceDirectDetRefferedback->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectDetRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirectDetRefferedbacks/{id}",
     *      summary="Update the specified CustomerInvoiceDirectDetRefferedback in storage",
     *      tags={"CustomerInvoiceDirectDetRefferedback"},
     *      description="Update CustomerInvoiceDirectDetRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectDetRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectDetRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceDirectDetRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectDetRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirectDetRefferedback $customerInvoiceDirectDetRefferedback */
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
        }

        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirectDetRefferedback->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirectDetRefferedbacks/{id}",
     *      summary="Remove the specified CustomerInvoiceDirectDetRefferedback from storage",
     *      tags={"CustomerInvoiceDirectDetRefferedback"},
     *      description="Delete CustomerInvoiceDirectDetRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectDetRefferedback",
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
        /** @var CustomerInvoiceDirectDetRefferedback $customerInvoiceDirectDetRefferedback */
        $customerInvoiceDirectDetRefferedback = $this->customerInvoiceDirectDetRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectDetRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
        }

        $customerInvoiceDirectDetRefferedback->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice_direct_det_refferedbacks')]));
    }

    public function getCIDetailsForAmendHistory(Request $request)
    {
        $input = $request->all();
        $custInvoiceDirectID = $input['custInvoiceDirectAutoID'];
        $timesReferred = $input['timesReferred'];

        $master = CustomerInvoiceDirectRefferedback::where('custInvoiceDirectAutoID',$custInvoiceDirectID)->where('timesReferred', $timesReferred)->first();
        if(!empty($master) && $master->isPerforma != 0 && $master->isPerforma != 1){
            $items = CustomerInvoiceItemDetailsRefferedback::where('custInvoiceDirectAutoID', $custInvoiceDirectID)
                ->where('timesReferred', $timesReferred)
                ->with(['uom_default', 'uom_issuing','delivery_order','sales_quotation'])
                ->get();
        }else{
            $items = CustomerInvoiceDirectDetRefferedback::where('custInvoiceDirectID', $custInvoiceDirectID)
                ->where('timesReferred', $timesReferred)
                ->with(['department', 'contract', 'unit'])
                ->get();
        }



        return $this->sendResponse($items->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_details_reffered_history')]));
    }
}

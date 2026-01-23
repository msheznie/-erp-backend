<?php
/**
 * =============================================
 * -- File Name : CustomerInvoiceDirectRefferedbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Invoice
 * -- Author : Mohamed Nazir
 * -- Create date : 27 - November 2018
 * -- Description : This file contains the all CRUD for Customer Invoice Direct Refferedback
 * -- REVISION HISTORY
 * -- Date: 30-November 2018 By: Nazir Description: Added new function getCIMasterAmendHistory()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectRefferedbackAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectRefferedbackAPIRequest;
use App\Models\CustomerInvoiceDirectRefferedback;
use App\Repositories\CustomerInvoiceDirectRefferedbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerInvoiceDirectRefferedbackController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectRefferedbackAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRefferedbackRepository */
    private $customerInvoiceDirectRefferedbackRepository;

    public function __construct(CustomerInvoiceDirectRefferedbackRepository $customerInvoiceDirectRefferedbackRepo)
    {
        $this->customerInvoiceDirectRefferedbackRepository = $customerInvoiceDirectRefferedbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectRefferedbacks",
     *      summary="Get a listing of the CustomerInvoiceDirectRefferedbacks.",
     *      tags={"CustomerInvoiceDirectRefferedback"},
     *      description="Get all CustomerInvoiceDirectRefferedbacks",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirectRefferedback")
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
        $this->customerInvoiceDirectRefferedbackRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectRefferedbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirectRefferedbacks = $this->customerInvoiceDirectRefferedbackRepository->all();

        return $this->sendResponse($customerInvoiceDirectRefferedbacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
    }

    /**
     * @param CreateCustomerInvoiceDirectRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirectRefferedbacks",
     *      summary="Store a newly created CustomerInvoiceDirectRefferedback in storage",
     *      tags={"CustomerInvoiceDirectRefferedback"},
     *      description="Store CustomerInvoiceDirectRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectRefferedback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceDirectRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirectRefferedbacks = $this->customerInvoiceDirectRefferedbackRepository->create($input);

        return $this->sendResponse($customerInvoiceDirectRefferedbacks->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirectRefferedbacks/{id}",
     *      summary="Display the specified CustomerInvoiceDirectRefferedback",
     *      tags={"CustomerInvoiceDirectRefferedback"},
     *      description="Get CustomerInvoiceDirectRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectRefferedback",
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
     *                  ref="#/definitions/CustomerInvoiceDirectRefferedback"
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
        /** @var CustomerInvoiceDirectRefferedback $customerInvoiceDirectRefferedback */
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->with(['createduser', 'confirmed_by', 'company', 'bankaccount', 'customer', 'currency', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        },'serviceline','warehouse','report_currency','local_currency'])->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
        }

        return $this->sendResponse($customerInvoiceDirectRefferedback->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectRefferedbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirectRefferedbacks/{id}",
     *      summary="Update the specified CustomerInvoiceDirectRefferedback in storage",
     *      tags={"CustomerInvoiceDirectRefferedback"},
     *      description="Update CustomerInvoiceDirectRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectRefferedback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirectRefferedback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirectRefferedback")
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
     *                  ref="#/definitions/CustomerInvoiceDirectRefferedback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectRefferedbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirectRefferedback $customerInvoiceDirectRefferedback */
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
        }

        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirectRefferedback->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirectRefferedbacks/{id}",
     *      summary="Remove the specified CustomerInvoiceDirectRefferedback from storage",
     *      tags={"CustomerInvoiceDirectRefferedback"},
     *      description="Delete CustomerInvoiceDirectRefferedback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirectRefferedback",
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
        /** @var CustomerInvoiceDirectRefferedback $customerInvoiceDirectRefferedback */
        $customerInvoiceDirectRefferedback = $this->customerInvoiceDirectRefferedbackRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirectRefferedback)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
        }

        $customerInvoiceDirectRefferedback->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_invoice_direct_refferedbacks')]));
    }

    public function getCIMasterAmendHistory(Request $request)
    {
        $input = $request->all();

        $customerInvoiceHistory = CustomerInvoiceDirectRefferedback::where('custInvoiceDirectAutoID', $input['custInvoiceDirectAutoID'])
            ->with(['createduser', 'confirmed_by', 'modified_by', 'customer', 'approved_by', 'cancelled_by', 'currency'])
            ->get();

        return $this->sendResponse($customerInvoiceHistory, trans('custom.retrieve', ['attribute' => trans('custom.invoice_detail')]));
    }
}

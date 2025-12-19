<?php
/**
 * =============================================
 * -- File Name : CustomerReceivePaymentRefferedHistoryAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Customer Receive Payment Reffered History
 * -- Author : Mohamed Nazir
 * -- Create date : 21 - November 2018
 * -- Description : This file contains the all CRUD for Customer Receive Payment Reffered History
 * -- REVISION HISTORY
 * -- Date: 21-November 2018 By: Nazir Description: Added new function getReceiptVoucherAmendHistory(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentRefferedHistoryAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentRefferedHistoryAPIRequest;
use App\Models\CustomerReceivePaymentRefferedHistory;
use App\Repositories\CustomerReceivePaymentRefferedHistoryRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CustomerReceivePaymentRefferedHistoryController
 * @package App\Http\Controllers\API
 */

class CustomerReceivePaymentRefferedHistoryAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentRefferedHistoryRepository */
    private $customerReceivePaymentRefferedHistoryRepository;

    public function __construct(CustomerReceivePaymentRefferedHistoryRepository $customerReceivePaymentRefferedHistoryRepo)
    {
        $this->customerReceivePaymentRefferedHistoryRepository = $customerReceivePaymentRefferedHistoryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentRefferedHistories",
     *      summary="Get a listing of the CustomerReceivePaymentRefferedHistories.",
     *      tags={"CustomerReceivePaymentRefferedHistory"},
     *      description="Get all CustomerReceivePaymentRefferedHistories",
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
     *                  @SWG\Items(ref="#/definitions/CustomerReceivePaymentRefferedHistory")
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
        $this->customerReceivePaymentRefferedHistoryRepository->pushCriteria(new RequestCriteria($request));
        $this->customerReceivePaymentRefferedHistoryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerReceivePaymentRefferedHistories = $this->customerReceivePaymentRefferedHistoryRepository->all();

        return $this->sendResponse($customerReceivePaymentRefferedHistories->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
    }

    /**
     * @param CreateCustomerReceivePaymentRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerReceivePaymentRefferedHistories",
     *      summary="Store a newly created CustomerReceivePaymentRefferedHistory in storage",
     *      tags={"CustomerReceivePaymentRefferedHistory"},
     *      description="Store CustomerReceivePaymentRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentRefferedHistory that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentRefferedHistory")
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
     *                  ref="#/definitions/CustomerReceivePaymentRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerReceivePaymentRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        $customerReceivePaymentRefferedHistories = $this->customerReceivePaymentRefferedHistoryRepository->create($input);

        return $this->sendResponse($customerReceivePaymentRefferedHistories->toArray(), trans('custom.save', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerReceivePaymentRefferedHistories/{id}",
     *      summary="Display the specified CustomerReceivePaymentRefferedHistory",
     *      tags={"CustomerReceivePaymentRefferedHistory"},
     *      description="Get CustomerReceivePaymentRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentRefferedHistory",
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
     *                  ref="#/definitions/CustomerReceivePaymentRefferedHistory"
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
        /** @var CustomerReceivePaymentRefferedHistory $customerReceivePaymentRefferedHistory */
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->with(['created_by', 'confirmed_by', 'company', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
        }

        return $this->sendResponse($customerReceivePaymentRefferedHistory->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
    }

    /**
     * @param int $id
     * @param UpdateCustomerReceivePaymentRefferedHistoryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerReceivePaymentRefferedHistories/{id}",
     *      summary="Update the specified CustomerReceivePaymentRefferedHistory in storage",
     *      tags={"CustomerReceivePaymentRefferedHistory"},
     *      description="Update CustomerReceivePaymentRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentRefferedHistory",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerReceivePaymentRefferedHistory that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerReceivePaymentRefferedHistory")
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
     *                  ref="#/definitions/CustomerReceivePaymentRefferedHistory"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerReceivePaymentRefferedHistoryAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerReceivePaymentRefferedHistory $customerReceivePaymentRefferedHistory */
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
        }

        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->update($input, $id);

        return $this->sendResponse($customerReceivePaymentRefferedHistory->toArray(), trans('custom.update', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerReceivePaymentRefferedHistories/{id}",
     *      summary="Remove the specified CustomerReceivePaymentRefferedHistory from storage",
     *      tags={"CustomerReceivePaymentRefferedHistory"},
     *      description="Delete CustomerReceivePaymentRefferedHistory",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerReceivePaymentRefferedHistory",
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
        /** @var CustomerReceivePaymentRefferedHistory $customerReceivePaymentRefferedHistory */
        $customerReceivePaymentRefferedHistory = $this->customerReceivePaymentRefferedHistoryRepository->findWithoutFail($id);

        if (empty($customerReceivePaymentRefferedHistory)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
        }

        $customerReceivePaymentRefferedHistory->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.customer_receive_payment_reffered_historie')]));
    }

    public function getReceiptVoucherAmendHistory(Request $request)
    {
        $input = $request->all();

        $customerReceivePaymentHistory = CustomerReceivePaymentRefferedHistory::where('custReceivePaymentAutoID', $input['custReceivePaymentAutoID'])
            ->with(['created_by','confirmed_by','modified_by','approved_by', 'currency', 'bankcurrency'])
            ->get();

        return $this->sendResponse($customerReceivePaymentHistory, trans('custom.retrieve', ['attribute' => trans('custom.invoice_details')]));
    }
}

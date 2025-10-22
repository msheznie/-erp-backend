<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateSalesOrderAdvPaymentAPIRequest;
use App\Http\Requests\API\UpdateSalesOrderAdvPaymentAPIRequest;
use App\Models\CompanyDocumentAttachment;
use App\Models\CurrencyMaster;
use App\Models\PoPaymentTermTypes;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesOrderAdvPayment;
use App\Models\SegmentMaster;
use App\Models\SoPaymentTerms;
use App\Repositories\SalesOrderAdvPaymentRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SalesOrderAdvPaymentController
 * @package App\Http\Controllers\API
 */

class SalesOrderAdvPaymentAPIController extends AppBaseController
{
    /** @var  SalesOrderAdvPaymentRepository */
    private $salesOrderAdvPaymentRepository;

    public function __construct(SalesOrderAdvPaymentRepository $salesOrderAdvPaymentRepo)
    {
        $this->salesOrderAdvPaymentRepository = $salesOrderAdvPaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesOrderAdvPayments",
     *      summary="Get a listing of the SalesOrderAdvPayments.",
     *      tags={"SalesOrderAdvPayment"},
     *      description="Get all SalesOrderAdvPayments",
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
     *                  @SWG\Items(ref="#/definitions/SalesOrderAdvPayment")
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
        $this->salesOrderAdvPaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->salesOrderAdvPaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $salesOrderAdvPayments = $this->salesOrderAdvPaymentRepository->all();

        return $this->sendResponse($salesOrderAdvPayments->toArray(), trans('custom.sales_order_adv_payments_retrieved_successfully'));
    }

    /**
     * @param CreateSalesOrderAdvPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/salesOrderAdvPayments",
     *      summary="Store a newly created SalesOrderAdvPayment in storage",
     *      tags={"SalesOrderAdvPayment"},
     *      description="Store SalesOrderAdvPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesOrderAdvPayment that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesOrderAdvPayment")
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
     *                  ref="#/definitions/SalesOrderAdvPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSalesOrderAdvPaymentAPIRequest $request)
    {

        $input = $request->all();
        $input = array_except($input, ['timestamp']);
        $input = $this->convertArrayToValue($input);
        $input['soID'] = isset($input['soID']) ? $input['soID'] : 0;
        $user = Helper::getEmployeeInfo();

        $salesOrder = QuotationMaster::find($input['soID']);

        if (empty($salesOrder)) {
            return $this->sendError(trans('custom.sales_order_not_found'));
        }

        if (!isset($input['comAmount']) || $input['comAmount'] == 0) {
            return $this->sendError('Amount should be greater than 0');
        }

        //check record all ready exist
        $poTermExist = SalesOrderAdvPayment::where('soTermID', $input['paymentTermID'])
            ->where('soID', $input['soID'])
            ->first();

        if (!empty($poTermExist)) {
            return $this->sendError('Advance Payment all ready requested');
        }

        $input['serviceLineSystemID'] = $salesOrder->serviceLineSystemID;

        $segment  = SegmentMaster::find($input['serviceLineSystemID']);

        if(!empty($segment)){
            $input['serviceLineID'] = $segment->ServiceLineCode;
        }

        $input['companySystemID'] = $salesOrder->companySystemID;
        $input['companyID'] = $salesOrder->companyID;
        $input['customerId'] = $salesOrder->customerSystemCode;
        $input['customerCode'] = $salesOrder->customerCode;
        $input['currencyID'] = $salesOrder->transactionCurrencyID;

        $input['soCode'] = $salesOrder->quotationCode;
        $input['soTermID'] = $input['paymentTermID'];
        $input['narration'] = $input['paymentTemDes'];

        $input['reqDate'] = date('Y-m-d H:i:s');
        $input['reqAmount'] = $input['comAmount'];
        $input['reqAmountTransCur_amount'] = $input['comAmount'];

        $companyCurrencyConversion = \Helper::currencyConversion($salesOrder->companySystemID, $salesOrder->transactionCurrencyID, $salesOrder->transactionCurrencyID, $input['comAmount']);

        $input['reqAmountInPOTransCur'] = $input['comAmount'];
        $input['reqAmountInPOLocalCur'] = Helper::roundValue($companyCurrencyConversion['localAmount']);
        $input['reqAmountInPORptCur'] = Helper::roundValue($companyCurrencyConversion['reportingAmount']);

        $vatAmount = 0;

        $totalAmount = QuotationDetails::selectRaw("(COALESCE(SUM(transactionAmount),0) + COALESCE(SUM(VATAmount * requestedQty),0) ) as totalTransactionAmount,
                                                     COALESCE(SUM(VATAmount * requestedQty),0) as totalVATAmount")
                                         ->where('quotationMasterID', $input['soID'])
                                         ->first();

        if(!empty($totalAmount) && $totalAmount->totalVATAmount != 0){
            $vatAmount = Helper::roundValue(($totalAmount->totalVATAmount/$totalAmount->totalTransactionAmount) * $input['comAmount']);
        }

        $vatCurrencyConversion = \Helper::currencyConversion($salesOrder->companySystemID, $salesOrder->transactionCurrencyID, $salesOrder->transactionCurrencyID, $vatAmount);

        $input['VATAmount'] = $vatAmount;
        $input['VATAmountLocal'] = Helper::roundValue($vatCurrencyConversion['localAmount']);
        $input['VATAmountRpt'] = Helper::roundValue($vatCurrencyConversion['reportingAmount']);




        $input['requestedByEmpID'] = $user['empID'];
        $input['requestedByEmpName'] = $user['empName'];

        $salesOrderAdvPayment = $this->salesOrderAdvPaymentRepository->create($input);

        if ($salesOrderAdvPayment) {
             SoPaymentTerms::where('paymentTermID', $input['paymentTermID'])
                ->update(['isRequested' => 1]);
        }

        return $this->sendResponse($salesOrderAdvPayment->toArray(), trans('custom.sales_order_adv_payment_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/salesOrderAdvPayments/{id}",
     *      summary="Display the specified SalesOrderAdvPayment",
     *      tags={"SalesOrderAdvPayment"},
     *      description="Get SalesOrderAdvPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesOrderAdvPayment",
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
     *                  ref="#/definitions/SalesOrderAdvPayment"
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
        /** @var SalesOrderAdvPayment $salesOrderAdvPayment */
        $salesOrderAdvPayment = $this->salesOrderAdvPaymentRepository->findWithoutFail($id);

        if (empty($salesOrderAdvPayment)) {
            return $this->sendError(trans('custom.sales_order_adv_payment_not_found'));
        }

        return $this->sendResponse($salesOrderAdvPayment->toArray(), trans('custom.sales_order_adv_payment_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSalesOrderAdvPaymentAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/salesOrderAdvPayments/{id}",
     *      summary="Update the specified SalesOrderAdvPayment in storage",
     *      tags={"SalesOrderAdvPayment"},
     *      description="Update SalesOrderAdvPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesOrderAdvPayment",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SalesOrderAdvPayment that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SalesOrderAdvPayment")
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
     *                  ref="#/definitions/SalesOrderAdvPayment"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSalesOrderAdvPaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var SalesOrderAdvPayment $salesOrderAdvPayment */
        $salesOrderAdvPayment = $this->salesOrderAdvPaymentRepository->findWithoutFail($id);

        if (empty($salesOrderAdvPayment)) {
            return $this->sendError(trans('custom.sales_order_adv_payment_not_found'));
        }

        if (isset($input['dueDate']) && $input['dueDate']) {
            $input['dueDate'] = new Carbon($input['dueDate']);
        }

        $salesOrderAdvPayment = $this->salesOrderAdvPaymentRepository->update($input, $id);

        return $this->sendResponse($salesOrderAdvPayment->toArray(), trans('custom.salesorderadvpayment_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/salesOrderAdvPayments/{id}",
     *      summary="Remove the specified SalesOrderAdvPayment from storage",
     *      tags={"SalesOrderAdvPayment"},
     *      description="Delete SalesOrderAdvPayment",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SalesOrderAdvPayment",
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
        /** @var SalesOrderAdvPayment $salesOrderAdvPayment */
        $salesOrderAdvPayment = $this->salesOrderAdvPaymentRepository->findWithoutFail($id);

        if (empty($salesOrderAdvPayment)) {
            return $this->sendError(trans('custom.sales_order_adv_payment_not_found'));
        }

        $salesOrderAdvPayment->delete();

        return $this->sendSuccess('Sales Order Adv Payment deleted successfully');
    }

    public function soPaymentTermsAdvanceDetailView(Request $request)
    {
        $input = $request->all();
        $input['paymentTermID'] = isset($input['paymentTermID']) ? $input['paymentTermID'] : 0;

        $advancePayment = SalesOrderAdvPayment::where('soTermID', $input['paymentTermID'])->first();;

        if (empty($advancePayment)) {
            return $this->sendError(trans('custom.payment_terms_not_found'));
        }

        $salesOrder = QuotationMaster::with(['segment'])->find($advancePayment->soID);

        if(empty($salesOrder)){
            return $this->sendError(trans('custom.sales_order_not_found'));
        }

        $currency = CurrencyMaster::where('currencyID', $salesOrder->transactionCurrencyID)->first();

        $detailPaymentType = PoPaymentTermTypes::where('paymentTermsCategoryID', $advancePayment->LCPaymentYN)->first();


        $output = array('somaster' => $salesOrder,
            'advancedetail' => $advancePayment,
            'currency' => $currency,
            'ptype' => $detailPaymentType
        );

        return $this->sendResponse($output, trans('custom.data_retrieved_successfully'));
    }

    public function getSoLogisticPrintDetail(Request $request)
    {
        $input = $request->all();
        $soAdvPaymentID = isset($input['soAdvPaymentID']) ? $input['soAdvPaymentID'] : 0;
        $typeID = isset($input['typeID']) ? $input['typeID'] : 0;

        if ($typeID == 1) {

            $soPaymentTerms = SalesOrderAdvPayment::find($soAdvPaymentID);

            if(empty($soPaymentTerms)){
                return $this->sendError(trans('custom.advance_payment_not_found'));
            }

            $soAdvPaymentID = $soPaymentTerms->soAdvPaymentID;
        }

        $items = SalesOrderAdvPayment::with(['company', 'currency', 'supplier_by'])->find($soAdvPaymentID);

        if(empty($soPaymentTerms)){
            return $this->sendError(trans('custom.advance_payment_not_found'));
        }


        $salesOrder = QuotationMaster::find($items->poID);

        if (empty($salesOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $referenceDoc = CompanyDocumentAttachment::where('companySystemID', $salesOrder->companySystemID)
                                                 ->where('documentSystemID', $salesOrder->documentSystemID)
                                                 ->first();

        $newRefDocNew = '';
        if(!empty($referenceDoc)){
            $newRefDoc = explode('D', $referenceDoc["docRefNumber"]);
            $newRefDocNew = $newRefDoc[0];
        }

        $printData = array(
            'sodata' => $items,
            'docRef' => $newRefDocNew
        );

        return $this->sendResponse($printData, trans('custom.data_retrieved_successfully'));
    }

}

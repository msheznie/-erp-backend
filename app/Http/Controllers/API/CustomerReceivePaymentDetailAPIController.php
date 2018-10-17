<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentDetailAPIRequest;
use App\Models\CustomerReceivePaymentDetail;

use App\Models\CustomerReceivePayment;
use App\Models\MatchDocumentMaster;
use App\Models\AccountsReceivableLedger;
use App\Models\GeneralLedger;
use App\Repositories\CustomerReceivePaymentDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
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

        $id = $input['id'];
        $value = $input['value'];
        $arAutoID = array_pluck($value, 'arAutoID');

        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $input['id'])->first();
        $detail = CustomerReceivePaymentDetail::select('bookingInvCode')->where('custReceivePaymentAutoID', $input['id'])->whereIn('arAutoID', $arAutoID)->get();

        if (count($detail) > 0) {
            $names = array_pluck($detail->toArray(), 'bookingInvCode');
            return $this->sendError('<b>Below listed invoices are already added to the current receipt.</b> <br>' . join(' <br> ', $names), 500);
        } else {


            /*ar autoID sumamount >
            already bookeed

            */

            $error['settled'] = [];
            $selectedArAutoID = [];
            if ($value) {
                $x = 0;
                foreach ($value as $item) {
                    $detail = CustomerReceivePaymentDetail::select(DB::raw("SUM(receiveAmountTrans) as receiveAmountTrans"))->where('arAutoID', $item['arAutoID'])->first();
                    if ($detail) {
                        if ($detail->receiveAmountTrans > $item['SumOfreceiveAmountTrans']) {
                            $error['settled'][] = $item['bookingInvDocCode'];
                        }

                    }

                    $siDetailExistGL = GeneralLedger::where('documentSystemID', $item['addedDocumentSystemID'])
                        ->where('companySystemID', $item['companySystemID'])
                        ->where('documentSystemCode', $item['bookingInvSystemCode'])
                        ->first();

                    if (empty($siDetailExistGL)) {
                        $error['ledger'][] = $item['bookingInvDocCode'];
                    }

                    $selectedArAutoID[] = $item['arAutoID'];

                    $inputData[$x]['custReceivePaymentAutoID'] = $id;
                    $inputData[$x]['arAutoID'] = $item['arAutoID'];
                    $inputData[$x]['companySystemID'] = $item['companySystemID'];
                    $inputData[$x]['companyID'] = $item['companyID'];
                    $inputData[$x]['addedDocumentSystemID'] = $item['addedDocumentSystemID'];
                    $inputData[$x]['addedDocumentID'] = $item['addedDocumentID'];
                    $inputData[$x]['bookingInvCodeSystem'] = $item['bookingInvSystemCode'];
                    $inputData[$x]['bookingInvCode'] = $item['bookingInvDocCode'];
                    $inputData[$x]['bookingDate'] = $item['bookingInvoiceDate'];
                    $inputData[$x]['custTransactionCurrencyID'] = $item['custTransCurrencyID'];
                    $inputData[$x]['custTransactionCurrencyER'] = $item['custTransER'];
                    $inputData[$x]['companyReportingCurrencyID'] = $item['comRptCurrencyID'];
                    $inputData[$x]['companyReportingER'] = $item['comRptER'];
                    $inputData[$x]['localCurrencyID'] = $item['localCurrencyID'];
                    $inputData[$x]['localCurrencyER'] = $item['localER'];
                    $inputData[$x]['bookingAmountTrans'] = $item['SumOfreceiveAmountTrans'];
                    $inputData[$x]['bookingAmountLocal'] = $item['SumOfreceiveAmountLocal'];
                    $inputData[$x]['bookingAmountRpt'] = $item['SumOfreceiveAmountRpt'];
                    $inputData[$x]['custReceiveCurrencyID'] = $item['custTransCurrencyID'];
                    $inputData[$x]['custReceiveCurrencyER'] = $item['custTransER'];
                    $inputData[$x]['custbalanceAmount'] = $item['SumOfcustbalanceAmount'];
                    $inputData[$x]['receiveAmountTrans'] = 0;
                    $inputData[$x]['receiveAmountLocal'] = 0;
                    $inputData[$x]['receiveAmountRpt'] = 0;
                    $x++;


                }


                if (!empty($error['settled'])) {
                    return $this->sendError('<b>Below listed invoices are already settled fully.</b> <br>' . join(' <br> ', $error['settled']), 500);
                }

                if(!empty($error['ledger'])){
                    return $this->sendError('<b>Below listed invoices are not updated in general ledger.</b> <br>' . join(' <br> ', $error['ledger']), 500);
                }

            }


        }


        $customerReceivePaymentDetails = CustomerReceivePaymentDetail::insert($inputData);

        AccountsReceivableLedger::whereIn('arAutoID', $selectedArAutoID)->update(array('selectedToPaymentInv' => -1));


        return $this->sendResponse($customerReceivePaymentDetails, 'Customer Receive Payment Detail saved successfully');
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
        AccountsReceivableLedger::where('arAutoID', $customerReceivePaymentDetail->arAutoID)->update(array('selectedToPaymentInv' => 0, 'fullyInvoiced' => 1));
        $customerReceivePaymentDetail->delete();

        return $this->sendResponse($id, 'Customer Receive Payment Detail deleted successfully');
    }

    public function saveReceiptVoucherUnAllocationsDetails(Request $request)
    {
        $input = $request->all();

        $custReceivePaymentAutoID = $input['custReceivePaymentAutoID'];


        $output = CustomerReceivePayment::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)->first();
        $detail = CustomerReceivePaymentDetail::where('custReceivePaymentAutoID', $custReceivePaymentAutoID)->where('bookingInvCode', 0)->first();
        if ($detail) {
            return $this->sendError('Unallocation detail is already exist');
        }
        $receiveAmountTrans = $input['receiveAmountTrans'];

        $data['custReceivePaymentAutoID'] = $custReceivePaymentAutoID;
        $data['companySystemID'] = $output->companySystemID;
        $data['companyID'] = $output->companyID;
        $data['matchingDocID'] = 0;
        $data['bookingInvCode'] = 0;
        $data['comments'] = $input['comments'];
        $data['custTransactionCurrencyID'] = $output->custTransactionCurrencyID;
        $data['custTransactionCurrencyER'] = $output->custTransactionCurrencyER;
        $data['companyReportingCurrencyID'] = $output->companyRptCurrencyID;
        $data['companyReportingER'] = $output->companyRptCurrencyER;
        $data['localCurrencyID'] = $output->localCurrencyID;
        $data['localCurrencyER'] = $output->localCurrencyER;
        $currency = \Helper::convertAmountToLocalRpt($output->documentSystemID, $output->custReceivePaymentAutoID, $receiveAmountTrans);
        $data['bookingAmountTrans'] = $receiveAmountTrans;
        $data['bookingAmountLocal'] = $currency['localAmount'];
        $data['bookingAmountRpt'] = $currency['reportingAmount'];

        $data['custReceiveCurrencyER'] = 0;
        $data['custbalanceAmount'] = 0;
        $data['receiveAmountTrans'] = $receiveAmountTrans;
        $data['receiveAmountLocal'] = $currency['localAmount'];
        $data['receiveAmountRpt'] = $currency['reportingAmount'];

        $customerReceivePaymentDetails = $this->customerReceivePaymentDetailRepository->create($data);

        return $this->sendResponse('', 'Unallocation amount added successfully');
    }

    public function updateCustomerReciept(Request $request)
    {
        $input = $request->all();


        $detail = CustomerReceivePaymentDetail::where('custRecivePayDetAutoID', $input['custRecivePayDetAutoID'])->first();
        if ($detail->comments != $input['comments']) {
            $post['comments'] = $input['comments'];
        }

        if ($input['receiveAmountTrans'] == "") {
            $input['receiveAmountTrans'] = 0;
        }

        /*if payment greater than blance amount */
        $totalPaidAmount = CustomerReceivePaymentDetail::select(DB::raw("SUM(receiveAmountTrans) as receiveAmountTrans"))->where('arAutoID', $detail['arAutoID'])->first();

        $totalinvoiceamount = $detail->bookingAmountTrans - ($totalPaidAmount->receiveAmountTrans + $input['receiveAmountTrans']);
        if ($totalinvoiceamount < 0) {
            return $this->sendError('You can not enter amount greater than invoice amount', 500);
        }
        /**/


        $post['receiveAmountTrans'] = $input['receiveAmountTrans'];
        $currency = \Helper::convertAmountToLocalRpt(21, $detail->custReceivePaymentAutoID, $input['receiveAmountTrans']);

        $input['receiveAmountTrans'] = $input['receiveAmountTrans'];
        $input['receiveAmountLocal'] = \Helper::roundValue($currency['localAmount']);
        $input['receiveAmountRpt'] = \Helper::roundValue($currency['reportingAmount']);

        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update($post, $input['custRecivePayDetAutoID']);

        $totalReceiveAmountTrans = CustomerReceivePaymentDetail::select(DB::raw("SUM(receiveAmountTrans) as receiveAmountTrans"))->where('arAutoID', $detail['arAutoID'])->first();

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0)*-1 AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvCodeSystem"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();


        $totReceiveAmount = $totalReceiveAmountTrans['receiveAmountTrans'] - $matchedAmount['SumOfmatchedAmount'];

        $custbalanceAmount = $detail->bookingAmountTrans - $totReceiveAmount;
        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update(array('custbalanceAmount' => $custbalanceAmount), $input['custRecivePayDetAutoID']);


        if ($totalinvoiceamount == 0) {
            $ledger['fullyInvoiced'] = 2;
        } else {
            $ledger['fullyInvoiced'] = 1;
        }
        AccountsReceivableLedger::where('arAutoID', $customerReceivePaymentDetail->arAutoID)->update($ledger);


        return $this->sendResponse('', 'Unallocation amount added successfully');
    }
}

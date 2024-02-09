<?php
/**
 * =============================================
 * -- File Name : CustomerReceivePaymentDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  CustomerReceivePaymentDetail
 * -- Author :
 * -- Create date :
 * -- Description : This file contains the all CRUD for Customer Receive Payment Detail table
 * -- REVISION HISTORY
 * -- Date: 22 October 2018 By: Nazir Description: Added new function getReceiptVoucherMatchDetails()
 * -- Date: 22 October 2018 By: Nazir Description: Added new function addReceiptVoucherMatchDetails()
 * -- Date: 23 October 2018 By: Nazir Description: Added new function updateReceiptVoucherMatchDetail()
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerReceivePaymentDetailAPIRequest;
use App\Http\Requests\API\UpdateCustomerReceivePaymentDetailAPIRequest;
use App\Models\CustomerReceivePaymentDetail;
use App\Repositories\UserRepository;
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
use Illuminate\Support\Facades\Auth;
use Response;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\CustomerInvoiceDirect;
use App\Models\TaxVatCategories;
use App\Models\CustomerInvoiceItemDetails;

/**
 * Class CustomerReceivePaymentDetailController
 * @package App\Http\Controllers\API
 */
class CustomerReceivePaymentDetailAPIController extends AppBaseController
{
    /** @var  CustomerReceivePaymentDetailRepository */
    private $customerReceivePaymentDetailRepository;
    private $userRepository;

    public function __construct(CustomerReceivePaymentDetailRepository $customerReceivePaymentDetailRepo, UserRepository $userRepo)
    {
        $this->customerReceivePaymentDetailRepository = $customerReceivePaymentDetailRepo;
        $this->userRepository = $userRepo;
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

        $itemExistArray = array();

        $master = CustomerReceivePayment::where('custReceivePaymentAutoID', $input['id'])->first();

        if(empty($master)){
            return $this->sendError('Receipt Voucher not found.');
        }

        if($master->confirmedYN){
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        $detail = CustomerReceivePaymentDetail::select('bookingInvCode')
            ->where('custReceivePaymentAutoID', $input['id'])
            ->whereIn('arAutoID', $arAutoID)
            ->get();

        if (count($detail) > 0) {
            $names = array_pluck($detail->toArray(), 'bookingInvCode');
            return $this->sendError('<b>Below listed invoices are already added to the current receipt.</b> <br>' . join(' <br> ', $names), 500);
        } else {

            $error['settled'] = [];
            $selectedArAutoID = [];
            if ($value) {
                $x = 0;
                foreach ($value as $item) {
                    $detail = CustomerReceivePaymentDetail::select(DB::raw("SUM(receiveAmountTrans) as receiveAmountTrans"))
                        ->where('arAutoID', $item['arAutoID'])
                        ->where('companySystemID', $item['companySystemID'])
                        ->first();
                    if ($detail) {
                        if (abs($detail->receiveAmountTrans) > abs($item['SumOfreceiveAmountTrans'])) {
                            $itemDrt = "Selected Invoice " . $item['bookingInvDocCode'] . " is all ready fully settled. Please check again";
                            $itemExistArray[] = [$itemDrt];

                        }

                    }

                    $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')
                        ->where('documentSystemID', $item['addedDocumentSystemID'])
                        ->where('companySystemID', $item['companySystemID'])
                        ->where('documentSystemCode', $item['bookingInvSystemCode'])
                        ->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')
                        ->first();

                    if ($glCheck) {
                        if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                            $itemDrt = "Selected Invoice " . $item['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                            $itemExistArray[] = [$itemDrt];
                        }
                    } else {
                        $itemDrt = "Selected Invoice " . $item['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                        $itemExistArray[] = [$itemDrt];
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
                    $inputData[$x]['custbalanceAmount'] = $item['balanceAmount'];
                    $inputData[$x]['receiveAmountTrans'] = 0;
                    $inputData[$x]['receiveAmountLocal'] = 0;
                    $inputData[$x]['receiveAmountRpt'] = 0;
                    $x++;

                }

            }

            if (!empty($itemExistArray)) {
                return $this->sendError($itemExistArray, 422);
            }

        }

        $customerReceivePaymentDetails = CustomerReceivePaymentDetail::insert($inputData);

        AccountsReceivableLedger::whereIn('arAutoID', $selectedArAutoID)
            ->update(array('selectedToPaymentInv' => -1));


        return $this->sendResponse($customerReceivePaymentDetails, 'Customer Receive Payment Detail added successfully');
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

        if($customerReceivePaymentDetail->matchingDocID == 0 && $customerReceivePaymentDetail->master && $customerReceivePaymentDetail->master->confirmedYN){
            return $this->sendError('You cannot delete detail, this document already confirmed', 500);
        }

        if($customerReceivePaymentDetail->matchingDocID != 0 && $customerReceivePaymentDetail->matching_master
            && $customerReceivePaymentDetail->matching_master->matchingConfirmedYN){
            return $this->sendError('You cannot delete detail, this document already confirmed', 500);
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


        if(empty($output)){
            return $this->sendError('Receipt Voucher not found.');
        }

        if($output->confirmedYN){
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        if (empty($input['receiveAmountTrans']) || $input['receiveAmountTrans'] == 0 || $input['receiveAmountTrans'] == '' || $input['receiveAmountTrans'] < 0) {
            return $this->sendError('Amount cannot be 0 or null');
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
        $serviceLineSystemID = null;
        if (isset($input['ar_data'])) {
            $serviceLineSystemID = $input['ar_data']['serviceLineSystemID'];
        }


        if (isset($input['ar_data'])) {
            unset($input['ar_data']);
        }

        $detail = CustomerReceivePaymentDetail::where('custRecivePayDetAutoID', $input['custRecivePayDetAutoID'])->first();

        if(empty($detail)){
            return $this->sendError('Receipt Voucher Detail not found.');
        }

        if($detail->master && $detail->master->confirmedYN){
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        if ($detail->comments != $input['comments']) {
            $post['comments'] = $input['comments'];
        }

        if ($input['receiveAmountTrans'] == "") {
            $input['receiveAmountTrans'] = 0;
        }

        // checking payment amount greater than balance amount
        $totalReceiveAmountPreCheck = CustomerReceivePaymentDetail::where('arAutoID', $input['arAutoID'])
            ->where('custRecivePayDetAutoID', '<>', $input['custRecivePayDetAutoID'])
            ->sum('receiveAmountTrans');

        $matchedAmountPreCheck = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
            ->where('companySystemID', $input["companySystemID"])
            ->where('PayMasterAutoId', $input["bookingInvCodeSystem"])
            ->where('documentSystemID', $input["addedDocumentSystemID"])
            ->where('serviceLineSystemID', $serviceLineSystemID)
            ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')->first();

        if(isset($input['tempType'])) {
            if ($input['tempType'] == 1) {
                $input["receiveAmountTrans"] = $input['custbalanceAmount'];
            }
        }

        if(!$matchedAmountPreCheck){
            $matchedAmountPreCheck['SumOfmatchedAmount'] = 0;
        }

        $totReceiveAmountDetail = $input['bookingAmountTrans'] - ($totalReceiveAmountPreCheck + $matchedAmountPreCheck['SumOfmatchedAmount']);


        if ($input['addedDocumentSystemID'] == 20) {
            if ($input["receiveAmountTrans"] > $totReceiveAmountDetail) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500);
            }
        } else if ($input['addedDocumentSystemID'] == 19) {
            if ($input["receiveAmountTrans"] < $totReceiveAmountDetail) {
                return $this->sendError('Payment amount cannot be greater than balance amount', 500);
            }
        }

        $currency = \Helper::convertAmountToLocalRpt(206, $input['arAutoID'], $input['receiveAmountTrans']);
        $input['receiveAmountLocal'] = \Helper::roundValue($currency['localAmount']);
        $input['receiveAmountRpt'] = \Helper::roundValue($currency['reportingAmount']);


        $customerReceivePaymentDetail = $this->customerReceivePaymentDetailRepository->update($input, $input['custRecivePayDetAutoID']);

        $detailUpdateBalance = CustomerReceivePaymentDetail::find($input['custRecivePayDetAutoID']);

        $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $input['arAutoID'])
            ->sum('receiveAmountTrans');

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
            ->where('companySystemID', $input["companySystemID"])
            ->where('PayMasterAutoId', $input["bookingInvCodeSystem"])
            ->where('documentSystemID', $input["addedDocumentSystemID"])
            ->where('serviceLineSystemID', $serviceLineSystemID)
            ->groupBy('PayMasterAutoId', 'documentSystemID', 'BPVsupplierID', 'supplierTransCurrencyID')->first();

        $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

        $custbalanceAmount = $detailUpdateBalance->bookingAmountTrans - $totReceiveAmount;

        $detailUpdateBalance->custbalanceAmount = $custbalanceAmount;
        $detailUpdateBalance->save();

        //updating Accounts receivable Ledger
        $arLedgerUpdate = AccountsReceivableLedger::find($input['arAutoID']);

        if ($input['addedDocumentSystemID'] == 20) {
            if ($totReceiveAmount == 0) {
                $arLedgerUpdate->fullyInvoiced = 0;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            } else if ($detailUpdateBalance->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount > $detailUpdateBalance->bookingAmountTrans) {
                $arLedgerUpdate->fullyInvoiced = 2;
                $arLedgerUpdate->selectedToPaymentInv = -1;
            } else if (($detailUpdateBalance->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                $arLedgerUpdate->fullyInvoiced = 1;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            }
        } else if ($input['addedDocumentSystemID'] == 19) {
            if ($totReceiveAmount == 0) {
                $arLedgerUpdate->fullyInvoiced = 0;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            } else if ($detailUpdateBalance->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount < $detailUpdateBalance->bookingAmountTrans) {
                $arLedgerUpdate->fullyInvoiced = 2;
                $arLedgerUpdate->selectedToPaymentInv = -1;
            } else if (($detailUpdateBalance->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                $arLedgerUpdate->fullyInvoiced = 1;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            }
        }

        $arLedgerUpdate->save();

        return $this->sendResponse('', 'Unallocation amount added successfully');
    }


    function getReceiptVoucherMatchDetails(Request $request)
    {
        $data = CustomerReceivePaymentDetail::with(['ar_data'])->where('matchingDocID', $request->matchDocumentMasterAutoID)
            ->get();
        return $this->sendResponse($data, 'Details saved successfully');
    }

    public function addReceiptVoucherMatchDetails(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $matchDocumentMasterAutoID = $input['matchDocumentMasterAutoID'];

        $matchDocumentMasterData = MatchDocumentMaster::find($matchDocumentMasterAutoID);

        if (empty($matchDocumentMasterData)) {
            return $this->sendError('Matching document not found');
        }

        if ($matchDocumentMasterData->matchingConfirmedYN) {
            return $this->sendError('You cannot add detail, this document already confirmed', 500);
        }

        $itemExistArray = array();

        //check supplier invoice all ready exist
        foreach ($input['detailTable'] as $itemExist) {

            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {
                $siDetailExistPS = CustomerReceivePaymentDetail::where('matchingDocID', $matchDocumentMasterAutoID)
                    ->where('companySystemID', $itemExist['companySystemID'])
                    ->where('bookingInvCodeSystem', $itemExist['bookingInvCodeSystem'])
                    ->first();

                if (!empty($siDetailExistPS)) {
                    $itemDrt = "Selected Invoice " . $itemExist['bookingInvDocCode'] . " is all ready added. Please check again";
                    $itemExistArray[] = [$itemDrt];
                }
            }
        }

        //check record total in General Ledger table
        foreach ($input['detailTable'] as $itemExist) {

            if (isset($itemExist['isChecked']) && $itemExist['isChecked']) {

                $glCheck = GeneralLedger::selectRaw('Sum(erp_generalledger.documentLocalAmount) AS SumOfdocumentLocalAmount, Sum(erp_generalledger.documentRptAmount) AS SumOfdocumentRptAmount,erp_generalledger.documentSystemID, erp_generalledger.documentSystemCode,documentCode,documentID')
                    ->where('documentSystemID', $itemExist['addedDocumentSystemID'])
                    ->where('companySystemID', $itemExist['companySystemID'])
                    ->where('documentSystemCode', $itemExist['bookingInvCodeSystem'])
                    ->groupBY('companySystemID', 'documentSystemID', 'documentSystemCode')
                    ->first();

                if ($glCheck) {
                    if (round($glCheck->SumOfdocumentLocalAmount, 0) != 0 || round($glCheck->SumOfdocumentRptAmount, 0) != 0) {
                        $itemDrt = "Selected Invoice " . $itemExist['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                        $itemExistArray[] = [$itemDrt];
                    }
                } else {
                    $itemDrt = "Selected Invoice " . $itemExist['bookingInvDocCode'] . " is not updated in general ledger. Please check again";
                    $itemExistArray[] = [$itemDrt];
                }
            }
        }

        if (!empty($itemExistArray)) {
            return $this->sendError($itemExistArray, 422);
        }
        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    
                    $tempArray = $new;
                    $tempArray["custReceivePaymentAutoID"] = $matchDocumentMasterData->PayMasterAutoId;
                    $tempArray["arAutoID"] = $new['arAutoID'];
                    $tempArray["companySystemID"] = $new['companySystemID'];
                    $tempArray["companyID"] = $new['companyID'];
                    $tempArray["matchingDocID"] = $matchDocumentMasterAutoID;
                    $tempArray["addedDocumentSystemID"] = $new['addedDocumentSystemID'];
                    $tempArray["addedDocumentID"] = $new['addedDocumentID'];
                    $tempArray["bookingInvCodeSystem"] = $new['bookingInvCodeSystem'];
                    $tempArray["bookingInvCode"] = $new['bookingInvDocCode'];
                    $tempArray["bookingDate"] = $new['bookingInvoiceDate'];
                    $tempArray["custTransactionCurrencyID"] = $new['custTransCurrencyID'];
                    $tempArray["custTransactionCurrencyER"] = $new['custTransER'];
                    $tempArray["companyReportingCurrencyID"] = $new['comRptCurrencyID'];
                    $tempArray["companyReportingER"] = $new['comRptER'];
                    $tempArray["localCurrencyID"] = $new['localCurrencyID'];
                    $tempArray["localCurrencyER"] = $new['localER'];
                    $tempArray["bookingAmountTrans"] = $new['custInvoiceAmount'];
                    $tempArray["bookingAmountLocal"] = $new['localAmount'];
                    $tempArray["bookingAmountRpt"] = $new['comRptAmount'];
                    $tempArray["custbalanceAmount"] = $new['balanceMemAmountNotRounded'];
                    $tempArray["receiveAmountTrans"] = 0;
                    $tempArray["receiveAmountLocal"] = 0;
                    $tempArray["receiveAmountRpt"] = 0;

                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);


                    $companySystemID = $new['companySystemID'];
                    $invoice = CustomerInvoiceDirect::where('custInvoiceDirectAutoID',$new['bookingInvCodeSystem'])->select('vatRegisteredYN','customerVATEligible','isPerforma')->first();

                    if($invoice->vatRegisteredYN)
                    {
                        if($invoice->isPerforma == 1 || $invoice->isPerforma == 0)
                        {
                            $details = CustomerInvoiceDirectDetail::where('custInvoiceDirectID',$new['bookingInvCodeSystem']); 
                        }
                        else
                        {
                            $details = CustomerInvoiceItemDetails::where('custInvoiceDirectAutoID',$new['bookingInvCodeSystem']); 
                            
                        }
                        
                         $allValuesAreTheSame = $this->areAllElementsSame($details->pluck('vatSubCategoryID'));

                          if($details->count() == 1 || $allValuesAreTheSame)
                          {
                            $det = $details->first();

                            $tempArray['vatSubCategoryID'] = $det->vatSubCategoryID;
                            $tempArray['vatMasterCategoryID'] = $det->vatMasterCategoryID;
                            $tempArray['VATPercentage'] = $det->VATPercentage;
                            $tempArray['isVatDisabled'] = true;
                            

                          }
                          else
                          {  

                                $defaultVAT = TaxVatCategories::whereHas('tax', function ($q) use ($companySystemID) {
                                    $q->where('companySystemID', $companySystemID)
                                        ->where('isActive', 1)
                                        ->where('taxCategory', 2);
                                })
                                ->whereHas('main', function ($q) {
                                    $q->where('isActive', 1);
                                })
                                ->where('isActive', 1)
                                ->where('isDefault', 1)
                                ->first();
    
                                    if ($defaultVAT) {
                                        $tempArray['vatSubCategoryID'] = $defaultVAT->taxVatSubCategoriesAutoID;
                                        $tempArray['vatMasterCategoryID'] = $defaultVAT->mainCategory;
                                        $tempArray['VATPercentage'] = $defaultVAT->percentage;
                                    } else {
                                        DB::rollBack();
                                        return $this->sendError("Default VAT not configured");
                                    }
                            

                          }
                    }

                    if ($tempArray) {
                        $receiptVoucherDetails = $this->customerReceivePaymentDetailRepository->create($tempArray);
                        $updatePayment = AccountsReceivableLedger::find($new['arAutoID'])
                            ->update(['selectedToPaymentInv' => -1]);
                    }
                }
            }

            

            DB::commit();
            return $this->sendResponse('', 'Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }

    }

    function areAllElementsSame($array) {
        $collection = collect($array);
        return $collection->every(function ($value) use ($collection) {
            return $value === $collection->first();
        });
    }
    public function updateReceiptVoucherMatchDetail(Request $request)
    {
        $input = $request->all();

        if (isset($input['ar_data'])) {
            unset($input['ar_data']);
        }
        $input = array_except($input, ['segment','updateKey','vatMasterCategoryAutoID','itemPrimaryCode','itemDescription','subCategoryArray','subCatgeoryType','exempt_vat_portion']);
        $input = $this->convertArrayToValue($input);


        $receiptVoucherDetails = $this->customerReceivePaymentDetailRepository->findWithoutFail($input['custRecivePayDetAutoID']);

        if (empty($receiptVoucherDetails)) {
            return $this->sendError('Receipt Voucher Detail not found');
        }

        $matchDocumentMasterData = MatchDocumentMaster::find($input['matchingDocID']);
        if (empty($matchDocumentMasterData)) {
            return $this->sendError('Matching document not found');
        }

        if ($matchDocumentMasterData->matchingConfirmedYN) {
            return $this->sendError('You cannot update detail, this document already confirmed', 500);
        }
        $documentCurrencyDecimalPlace = \Helper::getCurrencyDecimalPlace($matchDocumentMasterData->supplierTransCurrencyID);

        if ($input['receiveAmountTrans'] == "") {
            $input['receiveAmountTrans'] = 0;
        }

        if (round($input['receiveAmountTrans'], $documentCurrencyDecimalPlace) > round($matchDocumentMasterData->matchBalanceAmount, $documentCurrencyDecimalPlace)) {
            return $this->sendError('Matching amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch']);
        }

        // checking payment amount greater than balance amount
        $totalReceiveAmountPreCheck = CustomerReceivePaymentDetail::where('arAutoID', $input['arAutoID'])
            ->where('custRecivePayDetAutoID', '<>', $input['custRecivePayDetAutoID'])
            ->sum('receiveAmountTrans');

        $matchedAmountPreCheck = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) AS SumOfmatchedAmount')
            ->where('companySystemID', $input["companySystemID"])
            ->where('PayMasterAutoId', $input["bookingInvCodeSystem"])
            ->where('documentSystemID', $input["addedDocumentSystemID"])
            ->groupBy('PayMasterAutoId', 'documentSystemID')->first();

        $machAmount = 0;
        if ($matchedAmountPreCheck) {
            $machAmount = $matchedAmountPreCheck["SumOfmatchedAmount"];
        }

        if($input['temptype'] == 1){
            $input['receiveAmountTrans'] = $input['custbalanceAmount'];
        }

        $totReceiveAmountDetail = $input['bookingAmountTrans'] - ($totalReceiveAmountPreCheck + $machAmount);

        if ($input['addedDocumentSystemID'] == 20) {
            $compareValue = $input["receiveAmountTrans"] - $totReceiveAmountDetail;
            $epsilon = 0.00001;

            if ($compareValue > $epsilon) {
                return $this->sendError('Matching amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch']);
            }
        } else if ($input['addedDocumentSystemID'] == 19) {
            if ($input["receiveAmountTrans"] < $totReceiveAmountDetail) {
                return $this->sendError('Matching amount cannot be greater than balance amount', 500, ['type' => 'amountmismatch']);
            }
        }
        
        $conversionAmount = \Helper::convertAmountToLocalRpt(205, $input["custRecivePayDetAutoID"], ABS($input["receiveAmountTrans"]));
        //$input["paymentSupplierDefaultAmount"] = \Helper::roundValue($conversionAmount["defaultAmount"]);
        $input["receiveAmountLocal"] = \Helper::roundValue($conversionAmount["localAmount"]);
        $input["receiveAmountRpt"] = \Helper::roundValue($conversionAmount["reportingAmount"]);

        $receiptVoucherDetails = $this->customerReceivePaymentDetailRepository->update($input, $input['custRecivePayDetAutoID']);
        
        $detailUpdateBalance = CustomerReceivePaymentDetail::find($input['custRecivePayDetAutoID']);

        $totalReceiveAmountTrans = CustomerReceivePaymentDetail::where('arAutoID', $input['arAutoID'])
            ->sum('receiveAmountTrans');

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, IFNULL(Sum(erp_matchdocumentmaster.matchedAmount),0) * -1 AS SumOfmatchedAmount')
            ->where('companySystemID', $input["companySystemID"])
            ->where('PayMasterAutoId', $input["bookingInvCodeSystem"])
            ->where('documentSystemID', $input["addedDocumentSystemID"])
            ->groupBy('PayMasterAutoId', 'documentSystemID')->first();

        if(!$matchedAmount){
            $matchedAmount['SumOfmatchedAmount'] = 0;
        }
        
        $totReceiveAmount = $totalReceiveAmountTrans + $matchedAmount['SumOfmatchedAmount'];

        $custbalanceAmount = $detailUpdateBalance->bookingAmountTrans - $totReceiveAmount;

        $detailUpdateBalance->custbalanceAmount = \Helper::roundValue($custbalanceAmount);


        $vatAmount = round($input['receiveAmountTrans']*($input['VATPercentage']/(100+$input['VATPercentage'])),$documentCurrencyDecimalPlace);
        $detailUpdateBalance->VATAmount = $vatAmount;
        $detailUpdateBalance->VATAmountRpt = round($vatAmount / $matchDocumentMasterData->companyRptCurrencyER,$documentCurrencyDecimalPlace);
        $detailUpdateBalance->VATAmountLocal = round($vatAmount / $matchDocumentMasterData->localCurrencyER,$documentCurrencyDecimalPlace);
        
        $detailUpdateBalance->save();

        //updating Accounts receivable Ledger
        $arLedgerUpdate = AccountsReceivableLedger::find($input['arAutoID']);

        if ($input['addedDocumentSystemID'] == 20) {
            if ($totReceiveAmount == 0) {
                $arLedgerUpdate->fullyInvoiced = 0;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            } else if ($detailUpdateBalance->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount > $detailUpdateBalance->bookingAmountTrans) {
                $arLedgerUpdate->fullyInvoiced = 2;
                $arLedgerUpdate->selectedToPaymentInv = -1;
            } else if (($detailUpdateBalance->bookingAmountTrans > $totReceiveAmount) && ($totReceiveAmount > 0)) {
                $arLedgerUpdate->fullyInvoiced = 1;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            }
        } else if ($input['addedDocumentSystemID'] == 19) {
            if ($totReceiveAmount == 0) {
                $arLedgerUpdate->fullyInvoiced = 0;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            } else if ($detailUpdateBalance->bookingAmountTrans == $totReceiveAmount || $totReceiveAmount < $detailUpdateBalance->bookingAmountTrans) {
                $arLedgerUpdate->fullyInvoiced = 2;
                $arLedgerUpdate->selectedToPaymentInv = -1;
            } else if (($detailUpdateBalance->bookingAmountTrans < $totReceiveAmount) && ($totReceiveAmount < 0)) {
                $arLedgerUpdate->fullyInvoiced = 1;
                $arLedgerUpdate->selectedToPaymentInv = 0;
            }
        }
        $arLedgerUpdate->save();

        //updating match header table
        $detailAmountTotTran = CustomerReceivePaymentDetail::where('matchingDocID', $input['matchingDocID'])
            ->sum('receiveAmountTrans');

        $detailAmountTotLoc = CustomerReceivePaymentDetail::where('matchingDocID', $input['matchingDocID'])
            ->sum('receiveAmountLocal');

        $detailAmountTotRpt = CustomerReceivePaymentDetail::where('matchingDocID', $input['matchingDocID'])
            ->sum('receiveAmountRpt');


        $matchDocumentMasterData->matchingAmount = $detailAmountTotTran;
        $matchDocumentMasterData->matchedAmount = $detailAmountTotTran;
        $matchDocumentMasterData->matchLocalAmount = \Helper::roundValue($detailAmountTotLoc);
        $matchDocumentMasterData->matchRptAmount = \Helper::roundValue($detailAmountTotRpt);
        $matchDocumentMasterData->save();

        return $this->sendResponse($receiptVoucherDetails->toArray(), 'Detail updated successfully');
    }


}

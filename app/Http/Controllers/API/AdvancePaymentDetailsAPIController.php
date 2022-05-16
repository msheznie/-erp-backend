<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdvancePaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateAdvancePaymentDetailsAPIRequest;
use App\Models\AdvancePaymentDetails;
use App\Models\BankAssign;
use App\Models\BookInvSuppDet;
use App\Models\Company;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Repositories\AdvancePaymentDetailsRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
use App\Repositories\MatchDocumentMasterRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AdvancePaymentDetailsController
 * @package App\Http\Controllers\API
 */
class AdvancePaymentDetailsAPIController extends AppBaseController
{
    /** @var  AdvancePaymentDetailsRepository */
    private $advancePaymentDetailsRepository;
    private $paySupplierInvoiceMasterRepository;
    private $matchDocumentMasterRepository;
    private $userRepository;

    public function __construct(AdvancePaymentDetailsRepository $advancePaymentDetailsRepo, UserRepository $userRepo, PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo, MatchDocumentMasterRepository $matchDocumentMasterRepository)
    {
        $this->advancePaymentDetailsRepository = $advancePaymentDetailsRepo;
        $this->userRepository = $userRepo;
        $this->matchDocumentMasterRepository = $matchDocumentMasterRepository;
        $this->paySupplierInvoiceMasterRepository = $paySupplierInvoiceMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentDetails",
     *      summary="Get a listing of the AdvancePaymentDetails.",
     *      tags={"AdvancePaymentDetails"},
     *      description="Get all AdvancePaymentDetails",
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
     *                  @SWG\Items(ref="#/definitions/AdvancePaymentDetails")
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
        $this->advancePaymentDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->advancePaymentDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->all();

        return $this->sendResponse($advancePaymentDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.advance_payment_details')]));
    }

    /**
     * @param CreateAdvancePaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/advancePaymentDetails",
     *      summary="Store a newly created AdvancePaymentDetails in storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Store AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentDetails")
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
     *                  ref="#/definitions/AdvancePaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAdvancePaymentDetailsAPIRequest $request)
    {
        $input = $request->all();

        $advancePaymentDetails = $this->advancePaymentDetailsRepository->create($input);

        return $this->sendResponse($advancePaymentDetails->toArray(), trans('custom.save', ['attribute' => trans('custom.advance_payment_details')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Display the specified AdvancePaymentDetails",
     *      tags={"AdvancePaymentDetails"},
     *      description="Get AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
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
     *                  ref="#/definitions/AdvancePaymentDetails"
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
        /** @var AdvancePaymentDetails $advancePaymentDetails */
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->with('purchaseorder_by')->findWithoutFail($id);

        if (empty($advancePaymentDetails)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.advance_payment_details')]));
        }

        return $this->sendResponse($advancePaymentDetails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.advance_payment_details')]));
    }

    /**
     * @param int $id
     * @param UpdateAdvancePaymentDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Update the specified AdvancePaymentDetails in storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Update AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvancePaymentDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvancePaymentDetails")
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
     *                  ref="#/definitions/AdvancePaymentDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAdvancePaymentDetailsAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $input = array_except($input, ['purchaseorder_by']);
            /** @var AdvancePaymentDetails $advancePaymentDetails */
            $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);

            if (empty($advancePaymentDetails)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.advance_payment_details')]));
            }

            $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

            $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

            if (empty($bankMaster)) {
                return $this->sendError(trans('custom.is_not_active', ['attribute' => trans('custom.selected_bank')]), 500, ['type' => 'amountmismatch']);
            }

            $bankAccount = \App\Models\BankAccount::isActive()->find($payMaster->BPVAccount);

            if (empty($bankAccount)) {
                return $this->sendError(trans('custom.is_not_active', ['attribute' => trans('custom.selected_bank_account')]), 500, ['type' => 'amountmismatch']);
            }

            $advancePayment = PoAdvancePayment::find($input['poAdvPaymentID']);

            if (!$input["paymentAmount"]) {
                $input["paymentAmount"] = 0;
            }

            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL(Sum(erp_advancepaymentdetails.paymentAmount),0) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                ->where('purchaseOrderID', $advancePayment->poID)
                ->where('advancePaymentDetailAutoID', '<>', $id)
                ->first();

            $balanceAmount = $advancePayment->reqAmount - $advancePaymentDetailsSum->SumOfpaymentAmount;

            if ($input["paymentAmount"] > $balanceAmount) {
                return $this->sendError(trans('custom.payment_amount_cannot_be_greater_than_requested_amount'), 500, ['type' => 'amountmismatch']);
            }

            $conversion = \Helper::convertAmountToLocalRpt(201, $id, $input["paymentAmount"]);
            $input['supplierDefaultAmount'] = $conversion['defaultAmount'];
            $input['localAmount'] = $conversion['localAmount'];
            $input['comRptAmount'] = $conversion['reportingAmount'];
            $input['supplierTransAmount'] = $input["paymentAmount"];

            $advancePaymentDetails = $this->advancePaymentDetailsRepository->update($input, $id);

            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                ->where('purchaseOrderID', $advancePayment->poID)
                ->first();

            if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount || $advancePayment->reqAmount < $advancePaymentDetailsSum->SumOfpaymentAmount) {
                PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 2]);
            }

            if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                 PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 1]);
            }

            if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                 PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 0]);
            }

            DB::commit();

            return $this->sendResponse($advancePaymentDetails->toArray(), trans('custom.update', ['attribute' => trans('custom.advance_payment_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage(), 500);
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/advancePaymentDetails/{id}",
     *      summary="Remove the specified AdvancePaymentDetails from storage",
     *      tags={"AdvancePaymentDetails"},
     *      description="Delete AdvancePaymentDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvancePaymentDetails",
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
        DB::beginTransaction();
        try {
            /** @var AdvancePaymentDetails $advancePaymentDetails */
            $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWithoutFail($id);
            $advancePaymentDetails2 = $this->advancePaymentDetailsRepository->findWithoutFail($id);
            if (empty($advancePaymentDetails)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.advance_payment_details')]));
            }

            if($advancePaymentDetails->pay_invoice && $advancePaymentDetails->pay_invoice->confirmedYN){
                return $this->sendError(trans('custom.you_cannot_delete_advance_payment_detail_this_document_already_confirmed'),500);
            }


            $advancePaymentDetails->delete();

            $advancePayment = PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID);

            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                ->where('purchaseOrderID', $advancePayment->poID)
                ->first();

            if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
               PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 2, 'selectedToPayment' => 0]);
            }

            if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 1, 'selectedToPayment' => 0]);
            }

            if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                 PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 0, 'selectedToPayment' => 0]);
            }

            DB::commit();
            return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.advance_payment_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'));
        }
    }


    public function deleteAllADVPaymentDetail(Request $request)
    {
        $payMasterAutoId = $request->PayMasterAutoId;

        DB::beginTransaction();
        try {

            $payMaster = PaySupplierInvoiceMaster::find($payMasterAutoId);

            if (empty($payMaster)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment_voucher')]));
            }

            if($payMaster->confirmedYN){
                return $this->sendError(trans('custom.you_cannot_delete_advance_payment_detail_this_document_already_confirmed'),500);
            }

            /** @var AdvancePaymentDetails $advancePaymentDetails */
            $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWhere(['PayMasterAutoId' => $payMasterAutoId]);

            if (empty($advancePaymentDetails)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.pay_supplier_invoice_detail')]));
            }

            foreach ($advancePaymentDetails as $val) {

                $advancePaymentDetail = $this->advancePaymentDetailsRepository->find($val->advancePaymentDetailAutoID);
                $advancePaymentDetail->delete();

                $advancePayment = PoAdvancePayment::find($val->poAdvPaymentID);

                $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                    ->where('companySystemID', $advancePayment->companySystemID)
                    ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                    ->where('purchaseOrderID', $advancePayment->poID)
                    ->first();

                if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
                     PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 2, 'selectedToPayment' => 0]);
                }

                if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                     PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 1, 'selectedToPayment' => 0]);
                }

                if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                     PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 0, 'selectedToPayment' => 0]);
                }
            }

            $input['payAmountBank'] = 0;
            $input['payAmountSuppTrans'] = 0;
            $input['payAmountSuppDef'] = 0;
            $input['payAmountCompLocal'] = 0;
            $input['payAmountCompRpt'] = 0;
            $input['suppAmountDocTotal'] = 0;

            $this->paySupplierInvoiceMasterRepository->update($input, $payMasterAutoId);

            DB::commit();
            return $this->sendResponse($payMasterAutoId, trans('custom.delete', ['attribute' => trans('custom.pay_supplier_invoice_detail')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_occurred'));
        }
    }


    public function getADVPaymentDetails(Request $request)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->with('purchaseorder_by')->findWhere(['PayMasterAutoId' => $request->PayMasterAutoId]);
        return $this->sendResponse($advancePaymentDetails, trans('custom.save', ['attribute' => trans('custom.payment_details')]));
    }

    public function getMatchingADVPaymentDetails(Request $request)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->with('purchaseorder_by')->findWhere(['matchingDocID' => $request->matchingDocID]);
        return $this->sendResponse($advancePaymentDetails, trans('custom.save', ['attribute' => trans('custom.payment_details')]));
    }


    public function addADVPaymentDetail(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

        if (empty($payMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment_voucher')]));
        }

        if($payMaster->confirmedYN){
            return $this->sendError(trans('custom.you_cannot_add_advance_payment_detail_this_document_already_confirmed'),500);
        }

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {

                    $finalError = array(
                        'po_amount_not_matching' => array(),
                        'adv_payment_already_exist' => array(),
                    );

                    $error_count = 0;

                    $totalPOAmount = $new['poTotalSupplierTransactionCurrency'];
                    $advancePaymentAmount = 0;
                    $supplierInvoAmount = 0;

                    $advancePayment = AdvancePaymentDetails::selectRaw('SUM(paymentAmount) as paymentAmount')->whereHas('advancepaymentmaster', function ($query) use ($payMaster) {
                        $query->where('isAdvancePaymentYN', 0)->where('supplierID', $payMaster->BPVsupplierID);
                    })->where('purchaseOrderID', $new["purchaseOrderID"])->first();

                    if ($advancePayment) {
                        $advancePaymentAmount = $advancePayment->paymentAmount;
                    }

                    $bookInvDet = BookInvSuppDet::selectRaw('SUM(supplierInvoAmount) as supplierInvoAmount')->whereHas('suppinvmaster', function ($query) use ($payMaster) {
                        $query->whereHas('paysuppdetail')->where('approved', -1)->where('supplierID', $payMaster->BPVsupplierID);
                    })->where('companySystemID', $payMaster->companySystemID)->where('purchaseOrderID', $new["purchaseOrderID"])->first();

                    if ($bookInvDet) {
                        $supplierInvoAmount = $bookInvDet->supplierInvoAmount;
                    }

                    $balanceAmount = $totalPOAmount - ($advancePaymentAmount + $supplierInvoAmount);

                    if ($balanceAmount < 0) {
                        array_push($finalError['po_amount_not_matching'], 'PO' . ' | ' . $new['purchaseOrderCode']);
                        $error_count++;
                    }

                    $alreadyExistChk = AdvancePaymentDetails::where('PayMasterAutoId', $input["PayMasterAutoId"])->where('poAdvPaymentID', $new['poAdvPaymentID'])->first();
                    if ($alreadyExistChk) {
                        array_push($finalError['adv_payment_already_exist'], 'PO' . ' | ' . $new['purchaseOrderCode']);
                        $error_count++;
                    }

                    $confirm_error = array('type' => 'po_amount_not_matching', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError(trans('custom.selected_order_has_been_already_paid_more_than_the_order_amount_please_check_the_payment_status_for_this_order'), 500, $confirm_error);
                    }

                    $tempArray = $new;
                    $tempArray["PayMasterAutoId"] = $input["PayMasterAutoId"];
                    $tempArray["paymentAmount"] = $new["BalanceAmount"];
                    $tempArray["supplierTransAmount"] = $tempArray["paymentAmount"];
                    $tempArray["supplierTransCurrencyID"] = $new["currencyID"];
                    $tempArray["supplierTransER"] = 1;
                    $tempArray["supplierDefaultCurrencyID"] = $new["currencyID"];
                    $tempArray["supplierDefaultCurrencyER"] = 1;

                    $companyCurrencyConversion = \Helper::currencyConversion($new['companySystemID'], $new['currencyID'], $new['currencyID'], 0);

                    $company = Company::where('companySystemID', $new['companySystemID'])->first();

                    $tempArray["localCurrencyID"] = $company->localCurrencyID;
                    $tempArray["localER"] = $companyCurrencyConversion['trasToLocER'];

                    $tempArray["comRptCurrencyID"] = $company->reportingCurrency;
                    $tempArray["comRptER"] = $companyCurrencyConversion['trasToRptER'];

                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);
                    unset($tempArray['currencyID']);
                    unset($tempArray['supplierID']);
                    unset($tempArray['reqAmount']);
                    unset($tempArray['BalanceAmount']);
                    unset($tempArray['poTotalSupplierTransactionCurrency']);

                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->advancePaymentDetailsRepository->create($tempArray);
                        $conversion = \Helper::convertAmountToLocalRpt(201, $paySupplierInvoiceDetails->advancePaymentDetailAutoID, $new["BalanceAmount"]);
                        AdvancePaymentDetails::where('advancePaymentDetailAutoID', $paySupplierInvoiceDetails->advancePaymentDetailAutoID)->update(['supplierDefaultAmount' => $conversion['defaultAmount'], 'localAmount' => $conversion['localAmount'], 'comRptAmount' => $conversion['reportingAmount']]);

                        $advancePayment = PoAdvancePayment::find($new['poAdvPaymentID']);

                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                        if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
                             PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 2, 'selectedToPayment' => -1]);
                        }

                        if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                             PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 1, 'selectedToPayment' => -1]);
                        }

                        if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                             PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 0, 'selectedToPayment' => -1]);
                        }

                    }
                }
            }

            DB::commit();
            return $this->sendResponse('', trans('custom.save', ['attribute' => trans('custom.payment_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    public function addADVPaymentDetailForDirectPay(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);
        $documentMaster = $this->matchDocumentMasterRepository->findWithoutFail($request["matchDocumentMasterAutoID"]);

        $payMaster = PaySupplierInvoiceMaster::find($documentMaster->PayMasterAutoId);

        if (empty($payMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment_voucher')]));
        }

//        if($payMaster->confirmedYN){
//            return $this->sendError(trans('custom.you_cannot_add_advance_payment_detail_this_document_already_confirmed'),500);
//        }

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {

                    $finalError = array(
                        'po_amount_not_matching' => array(),
                        'adv_payment_already_exist' => array(),
                    );

                    $error_count = 0;

                    $totalPOAmount = $new['poTotalSupplierTransactionCurrency'];
                    $advancePaymentAmount = 0;
                    $supplierInvoAmount = 0;

                    $advancePayment = AdvancePaymentDetails::selectRaw('SUM(paymentAmount) as paymentAmount')->whereHas('advancepaymentmaster', function ($query) use ($payMaster) {
                        $query->where('isAdvancePaymentYN', 0)->where('supplierID', $payMaster->BPVsupplierID);
                    })->where('purchaseOrderID', $new["purchaseOrderID"])->first();

                    if ($advancePayment) {
                        $advancePaymentAmount = $advancePayment->paymentAmount;
                    }

                    $bookInvDet = BookInvSuppDet::selectRaw('SUM(supplierInvoAmount) as supplierInvoAmount')->whereHas('suppinvmaster', function ($query) use ($payMaster) {
                        $query->whereHas('paysuppdetail')->where('approved', -1)->where('supplierID', $payMaster->BPVsupplierID);
                    })->where('companySystemID', $payMaster->companySystemID)->where('purchaseOrderID', $new["purchaseOrderID"])->first();

                    if ($bookInvDet) {
                        $supplierInvoAmount = $bookInvDet->supplierInvoAmount;
                    }

                    $balanceAmount = $totalPOAmount - ($advancePaymentAmount + $supplierInvoAmount);

                    if ($balanceAmount < 0) {
                        array_push($finalError['po_amount_not_matching'], 'PO' . ' | ' . $new['purchaseOrderCode']);
                        $error_count++;
                    }

                    $alreadyExistChk = AdvancePaymentDetails::where('PayMasterAutoId', $documentMaster->PayMasterAutoId)->where('poAdvPaymentID', $new['poAdvPaymentID'])->first();
                    if ($alreadyExistChk) {
                        array_push($finalError['adv_payment_already_exist'], 'PO' . ' | ' . $new['purchaseOrderCode']);
                        $error_count++;
                    }

                    $confirm_error = array('type' => 'po_amount_not_matching', 'data' => $finalError);
                    if ($error_count > 0) {
                        return $this->sendError(trans('custom.selected_order_has_been_already_paid_more_than_the_order_amount_please_check_the_payment_status_for_this_order'), 500, $confirm_error);
                    }

                    $tempArray = $new;
                    $tempArray["PayMasterAutoId"] = $documentMaster->PayMasterAutoId;
                    $tempArray["matchingDocID"] = $documentMaster->matchDocumentMasterAutoID;
                    $tempArray["paymentAmount"] = $new["BalanceAmount"];
                    $tempArray["supplierTransAmount"] = $tempArray["paymentAmount"];
                    $tempArray["supplierTransCurrencyID"] = $new["currencyID"];
                    $tempArray["supplierTransER"] = 1;
                    $tempArray["supplierDefaultCurrencyID"] = $new["currencyID"];
                    $tempArray["supplierDefaultCurrencyER"] = 1;

                    $companyCurrencyConversion = \Helper::currencyConversion($new['companySystemID'], $new['currencyID'], $new['currencyID'], 0);

                    $company = Company::where('companySystemID', $new['companySystemID'])->first();

                    $tempArray["localCurrencyID"] = $company->localCurrencyID;
                    $tempArray["localER"] = $companyCurrencyConversion['trasToLocER'];

                    $tempArray["comRptCurrencyID"] = $company->reportingCurrency;
                    $tempArray["comRptER"] = $companyCurrencyConversion['trasToRptER'];

                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);
                    unset($tempArray['currencyID']);
                    unset($tempArray['supplierID']);
                    unset($tempArray['reqAmount']);
                    unset($tempArray['BalanceAmount']);
                    unset($tempArray['poTotalSupplierTransactionCurrency']);

                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->advancePaymentDetailsRepository->create($tempArray);
                        $conversion = \Helper::convertAmountToLocalRpt(201, $paySupplierInvoiceDetails->advancePaymentDetailAutoID, $new["BalanceAmount"]);
                        AdvancePaymentDetails::where('advancePaymentDetailAutoID', $paySupplierInvoiceDetails->advancePaymentDetailAutoID)->update(['supplierDefaultAmount' => $conversion['defaultAmount'], 'localAmount' => $conversion['localAmount'], 'comRptAmount' => $conversion['reportingAmount']]);

                        $advancePayment = PoAdvancePayment::find($new['poAdvPaymentID']);

                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                        if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
                            PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 2, 'selectedToPayment' => -1]);
                        }

                        if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                            PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 1, 'selectedToPayment' => -1]);
                        }

                        if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                            PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 0, 'selectedToPayment' => -1]);
                        }

                    }
                }
            }

            DB::commit();
            return $this->sendResponse('', trans('custom.save', ['attribute' => trans('custom.payment_details')]));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}

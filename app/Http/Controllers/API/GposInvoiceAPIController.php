<?php
/**
 * =============================================
 * -- File Name : GposInvoiceAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  General pos invoice
 * -- Author : Mohamed Fayas
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for  General pos invoice
 * -- REVISION HISTORY
 * -- Date: 24 - January 2019 By: Fayas Description: Added new function getInvoicesByShift()
 * -- Date: 28 - January 2019 By: Fayas Description: Added new function getInvoiceDetails(),printInvoice()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGposInvoiceAPIRequest;
use App\Http\Requests\API\UpdateGposInvoiceAPIRequest;
use App\Models\Company;
use App\Models\CompanyFinancePeriod;
use App\Models\ErpItemLedger;
use App\Models\GposInvoice;
use App\Models\GposInvoiceDetail;
use App\Models\GposInvoicePayments;
use App\Models\GposPaymentGlConfigDetail;
use App\Models\ItemAssigned;
use App\Models\ShiftDetails;
use App\Models\WarehouseMaster;
use App\Repositories\GposInvoiceDetailRepository;
use App\Repositories\GposInvoicePaymentsRepository;
use App\Repositories\GposInvoiceRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class GposInvoiceController
 * @package App\Http\Controllers\API
 */
class GposInvoiceAPIController extends AppBaseController
{
    /** @var  GposInvoiceRepository */
    private $gposInvoiceRepository;
    private $gposInvoiceDetailRepository;
    private $gposInvoicePaymentsRepository;

    public function __construct(GposInvoiceRepository $gposInvoiceRepo, GposInvoiceDetailRepository $gposInvoiceDetailRepo, GposInvoicePaymentsRepository $gposInvoicePaymentsRepo)
    {
        $this->gposInvoiceRepository = $gposInvoiceRepo;
        $this->gposInvoiceDetailRepository = $gposInvoiceDetailRepo;
        $this->gposInvoicePaymentsRepository = $gposInvoicePaymentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoices",
     *      summary="Get a listing of the GposInvoices.",
     *      tags={"GposInvoice"},
     *      description="Get all GposInvoices",
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
     *                  @SWG\Items(ref="#/definitions/GposInvoice")
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
        $this->gposInvoiceRepository->pushCriteria(new RequestCriteria($request));
        $this->gposInvoiceRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gposInvoices = $this->gposInvoiceRepository->all();

        return $this->sendResponse($gposInvoices->toArray(), trans('custom.gpos_invoices_retrieved_successfully'));
    }

    /**
     * @param CreateGposInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/gposInvoices",
     *      summary="Store a newly created GposInvoice in storage",
     *      tags={"GposInvoice"},
     *      description="Store GposInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoice that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoice")
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
     *                  ref="#/definitions/GposInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateGposInvoiceAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'invoiceItems' => 'required',
            'invoiceMaster' => 'required',
            'payments' => 'required',
            'companySystemID' => 'required',
            'shiftID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $invoiceMaster = $input['invoiceMaster'];
        $invoiceDetails = $input['invoiceItems'];
        $invoicePayments = $input['payments'];

        $validatorInvoiceMaster = \Validator::make($invoiceMaster, [
            'subTotal' => 'required',
            'netTotal' => 'required',
            'discountPercentage' => 'required',
            'discountAmount' => 'required',
            'paidAmount' => 'required',
            'balanceAmount' => 'required'
        ]);

        if ($validatorInvoiceMaster->fails()) {
            return $this->sendError($validatorInvoiceMaster->messages(), 422);
        }

        $invoiceId = isset($invoiceMaster['invoiceID']) ? $invoiceMaster['invoiceID'] : 0;

        if($invoiceId > 0){
            $posInvoices = $this->gposInvoiceRepository->find($invoiceId);

            if(empty($posInvoices)){
                return $this->sendError(trans('custom.invoice_not_found'), 500);
            }

            if($posInvoices->isCancelled == 1){
                return $this->sendError(trans('custom.invoice_already_canceled'), 500);
            }

            if($posInvoices->isVoid == 1){
                return $this->sendError('Invoice voided', 500);
            }

            if($posInvoices->isHold == 0){
                return $this->sendError(trans('custom.cannot_modify_this_invoice'), 500);
            }
        }

        DB::beginTransaction();
        try {
            $invoiceMasterData = array();
            $company = Company::find($input['companySystemID']);
            if (empty($company)) {
                return $this->sendError(trans('custom.company_not_found'), 500);
            }

            $employee = \Helper::getEmployeeInfo();

            $invoiceMasterData['segmentID'] = '';
            $invoiceMasterData['segmentCode'] = '';
            $invoiceMasterData['companySystemID'] = $input['companySystemID'];
            $invoiceMasterData['companyID'] = \Helper::getCompanyById($input['companySystemID']);
            $invoiceMasterData['documentSystemID'] = 67;
            $invoiceMasterData['documentID'] = 'GPOS';

            $shift = ShiftDetails::where('shiftID', $input['shiftID'])
                ->where('companyID', $input['companySystemID'])
                ->where('empID', $employee->employeeSystemID)
                ->first();

            if (empty($shift)) {
                return $this->sendError(trans('custom.shift_not_found'), 500);
            }

            if ($shift->isClosed == 1) {
                return $this->sendError(trans('custom.this_shift_already_closed_you_cannot_create_the_in'), 500);
            }

            $financialPeriod = CompanyFinancePeriod::where('companySystemID', $input['companySystemID'])
                ->where('isActive', -1)
                ->where('isCurrent', -1)
                ->where('departmentSystemID', 10)
                ->first();

            if (empty($financialPeriod)) {
                return $this->sendError(trans('custom.this_is_no_active_financial_period_you_cannot_crea'), 500);
            }

            $invoiceMasterData['financialYearID'] = $financialPeriod->companyFinanceYearID;
            $invoiceMasterData['financialPeriodID'] = $financialPeriod->companyFinancePeriodID;
            $invoiceMasterData['FYBegin'] = '';
            $invoiceMasterData['FYEnd'] = '';

            $invoiceMasterData['FYPeriodDateFrom'] = $financialPeriod->dateFrom;
            $invoiceMasterData['FYPeriodDateTo'] = $financialPeriod->dateTo;

            $invoiceMasterData['customerID'] = '';
            $invoiceMasterData['customerCode'] = '';
            $invoiceMasterData['counterID'] = $shift->counterID;
            $invoiceMasterData['shiftID'] = $shift->shiftID;
            $invoiceMasterData['invoiceDate'] = now();
            $invoiceMasterData['subTotal'] = $invoiceMaster['subTotal'];
            $invoiceMasterData['discountPercentage'] = $invoiceMaster['discountPercentage'];
            $invoiceMasterData['discountAmount'] = $invoiceMaster['discountAmount'];
            $invoiceMasterData['netTotal'] = $invoiceMaster['netTotal'];
            $invoiceMasterData['paidAmount'] = $invoiceMaster['paidAmount'];
            $invoiceMasterData['balanceAmount'] = $invoiceMaster['balanceAmount'];
            $invoiceMasterData['cashAmount'] = '';
            $invoiceMasterData['cardAmount'] = '';
            $invoiceMasterData['creditNoteID'] = '';
            $invoiceMasterData['creditNoteAmount'] = '';
            $invoiceMasterData['giftCardID'] = '';
            $invoiceMasterData['giftCardAmount'] = '';

            if (isset($invoiceMaster['isHold'])) {
                $invoiceMasterData['isHold'] = $invoiceMaster['isHold'];
            }

            if (isset($invoiceMaster['isCancelled'])) {
                $invoiceMasterData['isCancelled'] = $invoiceMaster['isCancelled'];
            }

            if (isset($invoiceMaster['reCalledYN'])) {
                $invoiceMasterData['reCalledYN'] = $invoiceMaster['reCalledYN'];
            }

            $outlet = WarehouseMaster::where('wareHouseSystemCode', $shift->wareHouseID)
                ->where('companySystemID', $input['companySystemID'])
                ->first();

            if (empty($outlet)) {
                return $this->sendError(trans('custom.outlet_not_found'), 500);
            }

            if ($outlet->isActive == 0) {
                return $this->sendError(trans('custom.outlet_not_active_you_cannot_create_the_invoice'), 500);
            }

            $invoiceMasterData['wareHouseAutoID'] = $outlet->wareHouseSystemCode;
            $invoiceMasterData['wareHouseCode'] = $outlet->wareHouseCode;
            //$invoiceMasterData['wareHouseLocation'] = $outlet->wareHouseSystemCode;
            $invoiceMasterData['wareHouseDescription'] = $outlet->wareHouseDescription;
            $invoiceMasterData['transactionCurrencyID'] = $shift->transactionCurrencyID;
            $invoiceMasterData['transactionCurrency'] = $shift->transactionCurrency;
            $invoiceMasterData['transactionExchangeRate'] = $shift->transactionExchangeRate;
            $invoiceMasterData['transactionCurrencyDecimalPlaces'] = $shift->transactionCurrencyDecimalPlaces;
            $invoiceMasterData['companyLocalCurrencyID'] = $shift->transactionCurrencyID;
            $invoiceMasterData['companyLocalCurrency'] = $shift->transactionCurrencyID;
            $invoiceMasterData['companyLocalExchangeRate'] = $shift->transactionCurrencyID;
            $invoiceMasterData['companyLocalCurrencyDecimalPlaces'] = $shift->companyLocalCurrencyDecimalPlaces;
            $invoiceMasterData['companyReportingCurrencyID'] = $shift->companyReportingCurrencyID;
            $invoiceMasterData['companyReportingCurrency'] = $shift->companyReportingCurrency;
            $invoiceMasterData['companyReportingExchangeRate'] = $shift->companyReportingExchangeRate;
            $invoiceMasterData['companyReportingCurrencyDecimalPlaces'] = $shift->companyReportingCurrencyDecimalPlaces;
            $invoiceMasterData['customerCurrencyID'] = '';
            $invoiceMasterData['customerCurrency'] = '';
            $invoiceMasterData['customerCurrencyExchangeRate'] = '';
            $invoiceMasterData['customerCurrencyDecimalPlaces'] = '';
            $invoiceMasterData['customerReceivableAutoID'] = '';
            $invoiceMasterData['customerReceivableSystemGLCode'] = '';
            $invoiceMasterData['customerReceivableGLAccount'] = '';
            $invoiceMasterData['customerReceivableDescription'] = '';
            $invoiceMasterData['customerReceivableType'] = '';
            $invoiceMasterData['timestamp'] = now();

            if ($invoiceId == 0) {
                $invoiceMasterData['createdPCID'] = gethostname();
                $invoiceMasterData['createdUserID'] = $employee->empID;
                $invoiceMasterData['createdUserSystemID'] = $employee->employeeSystemID;
                $invoiceMasterData['createdUserName'] = $employee->empName;

                $lastSerial = GposInvoice::where('companySystemID', $input['companySystemID'])
                    ->where('wareHouseAutoID', $outlet->wareHouseSystemCode)
                    ->orderBy('invoiceSequenceNo', 'desc')
                    ->first();

                $lastSerialNumber = 1;
                if ($lastSerial) {
                    $lastSerialNumber = intval($lastSerial->invoiceSequenceNo) + 1;
                }

                $invoiceMasterData['invoiceSequenceNo'] = $lastSerialNumber;
                $invoiceMasterData['invoiceCode'] = ($invoiceMasterData['companyID'] . '\\' . $invoiceMasterData['wareHouseCode'] . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));

            } else {
                $invoiceMasterData['modifiedPCID'] = gethostname();
                $invoiceMasterData['modifiedUserID'] = $employee->empID;
                $invoiceMasterData['modifiedUserSystemID'] = $employee->employeeSystemID;
                $invoiceMasterData['modifiedUserName'] = $employee->empName;
            }
            if (count($invoiceDetails) == 0) {
                return $this->sendError('There is no item to proceed . ', 500);
            }

            if ($invoiceId > 0) {
                $posInvoices = $this->gposInvoiceRepository->update($invoiceMasterData, $invoiceId);
            } else {
                $posInvoices = $this->gposInvoiceRepository->create($invoiceMasterData);
            }

            $itemError = array();

            if ($posInvoices) {

                GposInvoiceDetail::where('invoiceID', $invoiceId)->delete();

                foreach ($invoiceDetails as $item) {
                    $temItem = array();
                    $temItem['invoiceID'] = $posInvoices->invoiceID;
                    $temItem['companySystemID'] = $posInvoices->companySystemID;
                    $temItem['companyID'] = $posInvoices->companyID;

                    $itemAssign = ItemAssigned::where('companySystemID', $posInvoices->companySystemID)
                        ->where('itemCodeSystem', $item['itemCodeSystem'])
                        ->where('isAssigned', -1)
                        ->where('isActive', 1)
                        ->where('isPOSItem', 1)
                        ->first();

                    if (!empty($itemAssign)) {

                        $temItem['itemAutoID'] = $itemAssign->itemCodeSystem;
                        $temItem['itemSystemCode'] = $itemAssign->itemPrimaryCode;
                        $temItem['itemDescription'] = $itemAssign->itemDescription;
                        //$temItem['itemCategory'] = $posInvoices->invoiceID;
                        $temItem['itemFinanceCategory'] = $itemAssign->financeCategoryMaster;
                        $temItem['itemFinanceCategorySub'] = $itemAssign->financeCategorySub;
                        $temItem['defaultUOM'] = $itemAssign->itemUnitOfMeasure;
                        $temItem['unitOfMeasure'] = $itemAssign->itemUnitOfMeasure;

                        /*$temItem['conversionRateUOM'] = $posInvoices->invoiceID;
                        $temItem['expenseGLAutoID'] = $posInvoices->invoiceID; PL GL
                        $temItem['expenseGLCode'] = $posInvoices->invoiceID;
                        $temItem['expenseSystemGLCode'] = $posInvoices->invoiceID;
                        $temItem['expenseGLDescription'] = $posInvoices->invoiceID;
                        $temItem['expenseGLType'] = $posInvoices->invoiceID;
                        $temItem['revenueGLAutoID'] = $posInvoices->invoiceID;
                        $temItem['revenueGLCode'] = $posInvoices->invoiceID;
                        $temItem['revenueSystemGLCode'] = $posInvoices->invoiceID;
                        $temItem['revenueGLDescription'] = $posInvoices->invoiceID;
                        $temItem['revenueGLType'] = $posInvoices->invoiceID;
                        $temItem['assetGLAutoID'] = $posInvoices->invoiceID;  BS GL
                        $temItem['assetGLCode'] = $posInvoices->invoiceID;
                        $temItem['revenueSystemGLCode'] = $posInvoices->invoiceID;
                        $temItem['assetSystemGLCode'] = $posInvoices->invoiceID;
                        $temItem['assetGLDescription'] = $posInvoices->invoiceID;
                        $temItem['assetGLType'] = $posInvoices->invoiceID;
                        $temItem['assetSystemGLCode'] = $posInvoices->invoiceID;*/

                        $temItem['qty'] = $item['qty'];
                        $temItem['price'] = $item['sellingCost'];
                        $temItem['totalAmount'] = $item['subTotal'];
                        $temItem['discountPercentage'] = $item['discountPercentage'];
                        $temItem['discountAmount'] = $item['discountAmount'];
                        // $temItem['wacAmount'] = $posInvoices->invoiceID;
                        $temItem['netAmount'] = $item['netTotal'];


                        $temItem['transactionCurrencyID'] = $posInvoices->transactionCurrencyID;
                        $temItem['transactionCurrency'] = $posInvoices->transactionCurrency;
                        $temItem['transactionAmountBeforeDiscount'] = $item['subTotal'];
                        $temItem['transactionAmount'] = $item['netTotal'];
                        $temItem['transactionCurrencyDecimalPlaces'] = $posInvoices->transactionCurrencyDecimalPlaces;
                        $temItem['transactionExchangeRate'] = $posInvoices->transactionExchangeRate;

                        $temItem['companyLocalCurrencyID'] = $posInvoices->companyLocalCurrencyID;
                        $temItem['companyLocalCurrency'] = $posInvoices->companyLocalCurrency;
                        $temItem['companyLocalAmount'] = $item['netTotal'];
                        $temItem['companyLocalExchangeRate'] = $posInvoices->companyLocalExchangeRate;
                        $temItem['companyLocalCurrencyDecimalPlaces'] = $posInvoices->companyLocalCurrencyDecimalPlaces;
                        $temItem['companyReportingCurrencyID'] = $posInvoices->companyReportingCurrencyID;
                        $temItem['companyReportingCurrency'] = $posInvoices->companyReportingCurrency;

                        $currencyConvert = \Helper::convertAmountToLocalRpt(208, $posInvoices->invoiceID, $item['netTotal']);
                        $temItem['companyReportingAmount'] = round($currencyConvert['reportingAmount'], $posInvoices->companyLocalCurrencyDecimalPlaces);

                        $temItem['companyReportingCurrencyDecimalPlaces'] = $posInvoices->companyReportingCurrencyDecimalPlaces;
                        $temItem['companyReportingExchangeRate'] = $posInvoices->companyReportingExchangeRate;

                        $temItem['createdPCID'] = gethostname();
                        $temItem['createdUserID'] = $employee->empID;
                        $temItem['createdUserSystemID'] = $employee->employeeSystemID;
                        $temItem['createdUserName'] = $employee->empName;
                        $temItem['timestamp'] = now();
                        $this->gposInvoiceDetailRepository->create($temItem);
                    }
                }
                $total = 0;
                $cardTotal = 0;
                $cashTotal = 0;

                GposInvoicePayments::where('invoiceID', $invoiceId)->delete();
                foreach ($invoicePayments as $payment) {
                    if ($payment['amount'] > 0) {

                        $paymentConfig = GposPaymentGlConfigDetail::where('ID', $payment['ID'])
                            ->where('companyID', $posInvoices->companySystemID)
                            ->with(['type'])
                            ->first();

                        if (!empty($paymentConfig)) {

                            $total = $total + intval($payment['amount']);
                            if ($payment['paymentConfigMasterID'] != 1) {
                                $cardTotal = $cardTotal + intval($payment['amount']);
                            }

                            if($payment['paymentConfigMasterID'] == 1){
                                $cashTotal = intval($payment['amount']);
                            }

                            $tempPayment = array();
                            $tempPayment['invoiceID'] = $posInvoices->invoiceID;
                            $tempPayment['paymentConfigMasterID'] = $paymentConfig->paymentConfigMasterID;
                            $tempPayment['paymentConfigDetailID'] = $paymentConfig->ID;
                            if ($paymentConfig['type']) {
                                $tempPayment['glAccountType'] = $paymentConfig['type']['glAccountType'];
                            }

                            $tempPayment['GLCode'] = $paymentConfig->GLCode;
                            $tempPayment['amount'] = $payment['amount'];

                            if (isset($payment['reference'])) {
                                $tempPayment['reference'] = $payment['reference'];
                            }
                            //$temItem['customerAutoID'] = $posInvoices->transactionCurrencyID;
                            $tempPayment['createdPCID'] = gethostname();
                            $tempPayment['createdUserID'] = $employee->empID;
                            $tempPayment['createdUserSystemID'] = $employee->employeeSystemID;
                            $tempPayment['createdUserName'] = $employee->empName;
                            $tempPayment['timestamp'] = now();
                            $this->gposInvoicePaymentsRepository->create($tempPayment);
                        }
                    }
                }

                  $posInvoices = $this->gposInvoiceRepository->update(['cashAmount' => $cashTotal,'cardAmount' => $cardTotal], $posInvoices->invoiceID);
                if (!$posInvoices->isHold && !$posInvoices->isCancelled) {
                    if ($cardTotal > $posInvoices->netTotal) {
                        return $this->sendError('You can not pay more than the net total using cards!', 500);
                    }

                    if ($posInvoices->paidAmount < $posInvoices->netTotal) {
                        return $this->sendError('Under payment of ' . $posInvoices->balanceAmount . ' ' . $posInvoices->transactionCurrency . ' . Please enter the exact bill amount and submit again', 500);
                    }
                }
            }
            DB::commit();
            return $this->sendResponse($posInvoices, trans('custom.invoice_saved_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return ['success' => false, 'message' => $e . 'Error'];
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/gposInvoices/{id}",
     *      summary="Display the specified GposInvoice",
     *      tags={"GposInvoice"},
     *      description="Get GposInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoice",
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
     *                  ref="#/definitions/GposInvoice"
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
        /** @var GposInvoice $gposInvoice */
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            return $this->sendError(trans('custom.gpos_invoice_not_found'));
        }

        return $this->sendResponse($gposInvoice->toArray(), trans('custom.gpos_invoice_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateGposInvoiceAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/gposInvoices/{id}",
     *      summary="Update the specified GposInvoice in storage",
     *      tags={"GposInvoice"},
     *      description="Update GposInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoice",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="GposInvoice that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/GposInvoice")
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
     *                  ref="#/definitions/GposInvoice"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateGposInvoiceAPIRequest $request)
    {
        $input = $request->all();

        /** @var GposInvoice $gposInvoice */
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            return $this->sendError(trans('custom.invoice_not_found'));
        }

        if(isset($input['isVoid']) && $input['isVoid'] == 1){

            if($gposInvoice->isVoid == 1){
                return $this->sendError(trans('custom.invoice_already_voided'));
            }
            $employee = \Helper::getEmployeeInfo();
            $input['voidBy'] = $employee->employeeSystemID;
            $input['voidDatetime'] = now();
        }

        $gposInvoice = $this->gposInvoiceRepository->update(array_only($input, ['isVoid','voidBy','voidDatetime']), $id);

        return $this->sendResponse($gposInvoice->toArray(), trans('custom.invoice_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/gposInvoices/{id}",
     *      summary="Remove the specified GposInvoice from storage",
     *      tags={"GposInvoice"},
     *      description="Delete GposInvoice",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of GposInvoice",
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
        /** @var GposInvoice $gposInvoice */
        $gposInvoice = $this->gposInvoiceRepository->findWithoutFail($id);

        if (empty($gposInvoice)) {
            return $this->sendError(trans('custom.gpos_invoice_not_found'));
        }

        $gposInvoice->delete();

        return $this->sendResponse($id, trans('custom.gpos_invoice_deleted_successfully'));
    }

    public function getInvoicesByShift(Request $request)
    {
        $input = $request->all();
        $input['shiftID'] = isset($input['shiftID']) ? $input['shiftID'] : 0;
        $companyId = isset($input['companyId']) ? $input['companyId'] : 0;
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $input['warehouseSystemCode'] = 0;
        $shift = ShiftDetails::where('companyID',$companyId)->where('shiftID',$input['shiftID'])
                             ->first();

        if(!empty($shift)){
            $input['warehouseSystemCode'] = $shift->wareHouseID;
        }


        $invoices = GposInvoice::where('companySystemID', $companyId)
            ->where('shiftID', $input['shiftID'])
            ->when(request('isVoid') == 1 || request('isVoid') == 0, function ($q) use ($input) {
                $q->where('isVoid', $input['isVoid']);
            })
            ->when(request('isHold') == 1 || request('isHold') == 0, function ($q) use ($input) {
                $q->where('isHold', $input['isHold']);
            })
            ->when(request('isCancelled') == 1 || request('isCancelled') == 0, function ($q) use ($input) {
                $q->where('isCancelled', $input['isCancelled']);
            })
            ->with(['details' => function ($q) use ($input) {
                $q->with(['unit', 'item_ledger' => function ($q) use ($input) {
                    $q->where('warehouseSystemCode', $input['warehouseSystemCode'])
                        ->groupBy('itemSystemCode')
                        ->selectRaw('sum(inOutQty) AS stock,itemSystemCode');
                }]);
            },'transaction_currency','created_by']);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invoices = $invoices->where(function ($query) use ($search) {
                $query->where('invoiceCode', 'LIKE', "%{$search}%");
                // ->orWhere('itemDescription', 'LIKE', "%{$search}%")
                // ->orWhere('secondaryItemCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($invoices)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('invoiceID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->addColumn('Actions', 'Actions', "Actions")
            ->make(true);
    }

    public function getCurrentStock($row)
    {

        foreach ($row['details'] as $item) {
            $item->current_stock = ErpItemLedger::where('itemSystemCode', $item['itemAutoID'])
                ->where('companySystemID', $row['companySystemID'])
                ->where('wareHouseSystemCode', $row['wareHouseAutoID'])
                ->groupBy('itemSystemCode')
                ->sum('inOutQty');
        }
        return $row->toArray();
    }

    public function getInvoiceDetails(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        /** @var GposInvoice $gposInvoice */
        $gposInvoice = $this->gposInvoiceRepository->getAudit($id);

        if (empty($gposInvoice)) {
            return $this->sendError(trans('custom.invoice_not_found'));
        }

        if($gposInvoice->transaction_currency){
            $gposInvoice->decimalPlaces = $gposInvoice->transaction_currency->DecimalPlaces;
            $gposInvoice->currencyCode = $gposInvoice->transaction_currency->CurrencyCode;
        }

        return $this->sendResponse($gposInvoice->toArray(), trans('custom.gpos_invoice_retrieved_successfully'));

    }

    public function printInvoice(Request $request)
    {
        $input = $request->all();
        $id = isset($input['id']) ? $input['id'] : 0;
        /** @var GposInvoice $gposInvoice */
        $gposInvoice = $this->gposInvoiceRepository->getAudit($id);

        if (empty($gposInvoice)) {
            return $this->sendError(trans('custom.invoice_not_found'));
        }

        if($gposInvoice->transaction_currency){
            $gposInvoice->decimalPlaces = $gposInvoice->transaction_currency->DecimalPlaces;
            $gposInvoice->currencyCode = $gposInvoice->transaction_currency->CurrencyCode;
        }


        $array = array('entity' => $gposInvoice);
        $time = strtotime("now");
        $fileName = 'invoice_' . $id . '_' . $time . '.pdf';
        $viewName  = 'print.pos_invoice.default';
        $html = view($viewName, $array)->render();

        return $this->sendResponse($html, trans('custom.invoice_print_successfully'));

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
    }
}

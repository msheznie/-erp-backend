<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateAdvanceReceiptDetailsAPIRequest;
use App\Http\Requests\API\UpdateAdvanceReceiptDetailsAPIRequest;
use App\Models\AdvanceReceiptDetails;
use App\Models\BankAccount;
use App\Models\BankAssign;
use App\Models\Company;
use App\Models\CustomerReceivePayment;
use App\Models\SalesOrderAdvPayment;
use App\Repositories\AdvanceReceiptDetailsRepository;
use App\Repositories\CustomerReceivePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AdvanceReceiptDetailsController
 * @package App\Http\Controllers\API
 */
class AdvanceReceiptDetailsAPIController extends AppBaseController
{
    /** @var  AdvanceReceiptDetailsRepository */
    private $advanceReceiptDetailsRepository;
    private $customerReceivePaymentRepository;

    public function __construct(AdvanceReceiptDetailsRepository $advanceReceiptDetailsRepo,
                                CustomerReceivePaymentRepository $customerReceivePaymentRepo)
    {
        $this->advanceReceiptDetailsRepository = $advanceReceiptDetailsRepo;
        $this->customerReceivePaymentRepository = $customerReceivePaymentRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/advanceReceiptDetails",
     *      summary="Get a listing of the AdvanceReceiptDetails.",
     *      tags={"AdvanceReceiptDetails"},
     *      description="Get all AdvanceReceiptDetails",
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
     *                  @SWG\Items(ref="#/definitions/AdvanceReceiptDetails")
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
        $this->advanceReceiptDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->advanceReceiptDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->all();

        return $this->sendResponse($advanceReceiptDetails->toArray(), 'Advance Receipt Details retrieved successfully');
    }

    /**
     * @param CreateAdvanceReceiptDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/advanceReceiptDetails",
     *      summary="Store a newly created AdvanceReceiptDetails in storage",
     *      tags={"AdvanceReceiptDetails"},
     *      description="Store AdvanceReceiptDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvanceReceiptDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvanceReceiptDetails")
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
     *                  ref="#/definitions/AdvanceReceiptDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAdvanceReceiptDetailsAPIRequest $request)
    {

        $input = $request->all();
        $input["custReceivePaymentAutoID"] = isset($input["custReceivePaymentAutoID"]) ? $input["custReceivePaymentAutoID"] : 0;

        $advanceReceipt = CustomerReceivePayment::find($input["custReceivePaymentAutoID"]);

        if (empty($advanceReceipt)) {
            return $this->sendError('Advance receipt voucher not found');
        }

        if ($advanceReceipt->confirmedYN) {
            return $this->sendError('You cannot add Advance Payment Detail, this document already confirmed', 500);
        }

        DB::beginTransaction();
        try {
            if (isset($input['detailTable'])) {
                $finalError = array(
                    'so_amount_not_matching' => array(),
                    'adv_payment_already_exist' => array(),
                );
                $error_count = 0;
                foreach ($input['detailTable'] as $new) {
                    if (isset($new['isChecked']) && $new['isChecked']) {

                        $totalSOAmount = isset($new['totalTransactionAmount']) ? $new['totalTransactionAmount'] : 0;
                        $advancePaymentAmount = 0;
                        $customerInvoiceAmount = 0;

                        $advanceReceiptDetail = AdvanceReceiptDetails::selectRaw('SUM(paymentAmount) as paymentAmount')
                            ->whereHas('advance_payment_master', function ($query) use ($advanceReceipt) {
                                $query->where('isAdvancePaymentYN', 0)
                                    ->where('customerId', $advanceReceipt->customerID);
                            })
                            ->where('salesOrderID', $new["salesOrderID"])
                            ->first();

                        if (empty($advanceReceiptDetail)) {
                            return $this->sendError('Advance payment detail not found');
                        }

                        $advancePaymentAmount = $advanceReceiptDetail->paymentAmount;

                        /*$bookInvDet = BookInvSuppDet::selectRaw('SUM(supplierInvoAmount) as supplierInvoAmount')
                            ->whereHas('suppinvmaster', function ($query) use ($advanceReceipt) {
                                $query->whereHas('paysuppdetail')
                                    ->where('approved', -1)
                                    ->where('supplierID', $advanceReceipt->BPVsupplierID);
                            })
                            ->where('companySystemID', $advanceReceipt->companySystemID)
                            ->where('salesOrderID', $new["salesOrderID"])
                            ->first();

                        if ($bookInvDet) {
                            $customerInvoiceAmount = $bookInvDet->supplierInvoAmount;
                        }*/

                        $balanceAmount = $totalSOAmount - ($advancePaymentAmount + $customerInvoiceAmount);

                        if ($balanceAmount < 0) {
                            array_push($finalError['so_amount_not_matching'], 'SO' . ' | ' . $new['salesOrderCode']);
                            $error_count++;
                        }

                        $alreadyExistChk = AdvanceReceiptDetails::where('custReceivePaymentAutoID', $input["custReceivePaymentAutoID"])
                                                                ->where('soAdvPaymentID', $new['soAdvPaymentID'])
                                                                ->first();
                        if (!empty($alreadyExistChk)) {
                            array_push($finalError['adv_payment_already_exist'], 'SO' . ' | ' . $new['salesOrderCode']);
                            $error_count++;
                        }

                        $confirm_error = array('type' => 'so_amount_not_matching', 'data' => $finalError);
                        if ($error_count > 0) {
                            return $this->sendError("Selected order has been already paid more than the order amount. Please check the payment status for this order.", 500, $confirm_error);
                        }

                        $tempArray = $new;
                        $tempArray["custReceivePaymentAutoID"] = $input["custReceivePaymentAutoID"];
                        $tempArray["paymentAmount"] = Helper::roundValue($new["BalanceAmount"]);
                        $tempArray["supplierTransAmount"] = $tempArray["paymentAmount"];
                        $tempArray["customerTransCurrencyID"] = $new["currencyID"];
                        $tempArray["customerTransER"] = 1;
                        $tempArray["customerDefaultCurrencyID"] = $new["currencyID"];
                        $tempArray["customerDefaultCurrencyER"] = 1;

                        $advancePayment = SalesOrderAdvPayment::find($new['soAdvPaymentID']);

                        if (empty($advancePayment)) {
                            return $this->sendError('Advance payment not found');
                        }

                        // vat calculation
                        $vatAmount = 0;

                        if($advancePayment->reqAmount != 0 ){
                            $new["VATAmount"]  = ($advancePayment->VATAmount / $advancePayment->reqAmount) * $tempArray["paymentAmount"];
                        }

                        $conversionVAT = \Helper::currencyConversion($new['companySystemID'], $new['currencyID'], $new['currencyID'], $new["VATAmount"]);
                        $tempArray['VATAmountLocal'] = Helper::roundValue($conversionVAT['localAmount']);
                        $tempArray['VATAmountRpt'] = Helper::roundValue($conversionVAT['reportingAmount']);
                        $tempArray["VATAmount"] = Helper::roundValue($new["VATAmount"]);


                        $companyCurrencyConversion = \Helper::currencyConversion($new['companySystemID'], $new['currencyID'], $new['currencyID'], 0);

                        $company = Company::where('companySystemID', $new['companySystemID'])->first();

                        if(empty($company)){
                            return $this->sendError("Company not found",500);
                        }

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
                        unset($tempArray['totalTransactionAmount']);

                        if ($tempArray) {
                            $advanceReceiptDetail = $this->advanceReceiptDetailsRepository->create($tempArray);
                            $conversion = \Helper::convertAmountToLocalRpt(206, $advanceReceiptDetail->advanceReceiptDetailAutoID, $new["BalanceAmount"]);

                            AdvanceReceiptDetails::where('advanceReceiptDetailAutoID', $advanceReceiptDetail->advanceReceiptDetailAutoID)
                                ->update(['supplierDefaultAmount' => $conversion['defaultAmount'],
                                          'localAmount' => $conversion['localAmount'],
                                          'comRptAmount' => $conversion['reportingAmount']]);

                            $this->updateSalesOrderAdvPayment($new['soAdvPaymentID']);
                        }
                    }
                }
            } else {
                return $this->sendError('Details  not found', 500);
            }
            DB::commit();
            return $this->sendResponse('', 'Advance Receipt Details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/advanceReceiptDetails/{id}",
     *      summary="Display the specified AdvanceReceiptDetails",
     *      tags={"AdvanceReceiptDetails"},
     *      description="Get AdvanceReceiptDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvanceReceiptDetails",
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
     *                  ref="#/definitions/AdvanceReceiptDetails"
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
        /** @var AdvanceReceiptDetails $advanceReceiptDetails */
        $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->findWithoutFail($id);

        if (empty($advanceReceiptDetails)) {
            return $this->sendError('Advance Receipt Details not found');
        }

        return $this->sendResponse($advanceReceiptDetails->toArray(), 'Advance Receipt Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateAdvanceReceiptDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/advanceReceiptDetails/{id}",
     *      summary="Update the specified AdvanceReceiptDetails in storage",
     *      tags={"AdvanceReceiptDetails"},
     *      description="Update AdvanceReceiptDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvanceReceiptDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AdvanceReceiptDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AdvanceReceiptDetails")
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
     *                  ref="#/definitions/AdvanceReceiptDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAdvanceReceiptDetailsAPIRequest $request)
    {
        $input = $request->all();

        DB::beginTransaction();
        try {
            $input = array_except($input, ['sales_order']);
            $input['custReceivePaymentAutoID'] = isset($input['custReceivePaymentAutoID']) ? $input['custReceivePaymentAutoID'] : 0;
            $input['soAdvPaymentID'] = isset($input['soAdvPaymentID']) ? $input['soAdvPaymentID'] : 0;
            $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->findWithoutFail($id);

            if (empty($advanceReceiptDetails)) {
                return $this->sendError('Advance Receipt Details not found');
            }

            $receiptMaster = CustomerReceivePayment::find($input["custReceivePaymentAutoID"]);

            if(empty($receiptMaster)){
                return $this->sendError('Advance Receipt not found');
            }

            $bankMaster = BankAssign::ofCompany($receiptMaster->companySystemID)
                                   ->isActive()
                                   ->where('bankmasterAutoID', $receiptMaster->bankID)
                                   ->first();

            if (empty($bankMaster)) {
                return $this->sendError('Selected Bank is not active', 500, ['type' => 'amountmismatch']);
            }

            $bankAccount = BankAccount::isActive()->find($receiptMaster->bankAccount);

            if (empty($bankAccount)) {
                return $this->sendError('Selected Bank Account is not active', 500, ['type' => 'amountmismatch']);
            }

            $advancePayment = SalesOrderAdvPayment::find($input['soAdvPaymentID']);

            if (empty($advancePayment)) {
                return $this->sendError('Sales order payment not found', 500);
            }

            if (!$input["paymentAmount"]) {
                $input["paymentAmount"] = 0;
            }

            $advanceReceiptDetailsSum = AdvanceReceiptDetails::selectRaw('IFNULL(Sum(paymentAmount),0) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('soAdvPaymentID', $advancePayment->soAdvPaymentID)
                ->where('salesOrderID', $advancePayment->soID)
                ->where('advanceReceiptDetailAutoID', '<>', $id)
                ->first();

            if(!empty($advanceReceiptDetailsSum)){
                $balanceAmount = $advancePayment->reqAmount - $advanceReceiptDetailsSum->SumOfpaymentAmount;
            }else{
                $balanceAmount = $advancePayment->reqAmount;
            }



            if ($input["paymentAmount"] > $balanceAmount) {
                return $this->sendError('Payment amount cannot be greater than requested amount', 500, ['type' => 'amountmismatch']);
            }

            $conversion = \Helper::currencyConversion($receiptMaster->companySystemID, $receiptMaster->custTransactionCurrencyID, $receiptMaster->custTransactionCurrencyID, $input["paymentAmount"]);
            $input['supplierDefaultAmount'] = $input["paymentAmount"];
            $input['localAmount'] = $conversion['localAmount'];
            $input['comRptAmount'] = $conversion['reportingAmount'];
            $input['supplierTransAmount'] = $input["paymentAmount"];

            // vat calculation
            $vatAmount = 0;

            if($advancePayment->reqAmount != 0 ){
                $vatAmount  = ($advancePayment->VATAmount / $advancePayment->reqAmount) * $input["paymentAmount"];
            }

            $conversionVAT = \Helper::currencyConversion($receiptMaster->companySystemID, $receiptMaster->custTransactionCurrencyID, $receiptMaster->custTransactionCurrencyID, $vatAmount);
            $input['VATAmountLocal'] = $conversionVAT['localAmount'];
            $input['VATAmountRpt'] = $conversionVAT['reportingAmount'];
            $input['VATAmount'] = $vatAmount;


            $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->update($input, $id);

            $this->updateSalesOrderAdvPayment($input['soAdvPaymentID']);

            DB::commit();

            return $this->sendResponse($advanceReceiptDetails->toArray(), 'AdvancePaymentDetails updated successfully');
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
     *      path="/advanceReceiptDetails/{id}",
     *      summary="Remove the specified AdvanceReceiptDetails from storage",
     *      tags={"AdvanceReceiptDetails"},
     *      description="Delete AdvanceReceiptDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AdvanceReceiptDetails",
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
            $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->findWithoutFail($id);
            $advanceReceiptDetails2 = $this->advanceReceiptDetailsRepository->findWithoutFail($id);
            if (empty($advanceReceiptDetails)) {
                return $this->sendError('Advance Receipt Details not found');
            }

            if($advanceReceiptDetails->pay_invoice && $advanceReceiptDetails->pay_invoice->confirmedYN){
                return $this->sendError('You cannot delete Advance Receipt Detail, this document already confirmed',500);
            }


            $advanceReceiptDetails->delete();

            $this->updateSalesOrderAdvPayment($advanceReceiptDetails2->soAdvPaymentID);

            DB::commit();
            return $this->sendResponse($id, 'Advance Receipt Details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }

    public function getADVPReceiptDetails(Request $request)
    {
        $request->custReceivePaymentAutoID = isset($request->custReceivePaymentAutoID) ? $request->custReceivePaymentAutoID : 0;
        $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->with('sales_order')
            ->findWhere(['custReceivePaymentAutoID' => $request->custReceivePaymentAutoID]);
        return $this->sendResponse($advanceReceiptDetails, 'Receipt details saved successfully');
    }

    public function deleteAllADVReceiptDetail(Request $request)
    {
        $id = isset($request->custReceivePaymentAutoID) ? $request->custReceivePaymentAutoID : 0;

        DB::beginTransaction();
        try {

            $receiptMaster = CustomerReceivePayment::find($id);

            if (empty($receiptMaster)) {
                return $this->sendError('Receipt voucher not found');
            }

            if($receiptMaster->confirmedYN){
                return $this->sendError('You cannot delete Advance Receipt Detail, this document already confirmed',500);
            }

            $advanceReceiptDetails = $this->advanceReceiptDetailsRepository->findWhere(['custReceivePaymentAutoID' => $id]);

            if (empty($advanceReceiptDetails)) {
                return $this->sendError('Receipt Detail not found');
            }

            foreach ($advanceReceiptDetails as $val) {

                $advanceReceiptDetail = $this->advanceReceiptDetailsRepository->find($val->advanceReceiptDetailAutoID);
                $advanceReceiptDetail->delete();

               $this->updateSalesOrderAdvPayment($val->soAdvPaymentID);
            }

            $input['payAmountBank'] = 0;
            $input['payAmountSuppTrans'] = 0;
            $input['payAmountSuppDef'] = 0;
            $input['payAmountCompLocal'] = 0;
            $input['payAmountCompRpt'] = 0;
            $input['suppAmountDocTotal'] = 0;

            $this->customerReceivePaymentRepository->update($input, $id);

            DB::commit();
            return $this->sendResponse($id, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
            return $this->sendError('Error Occurred');
        }
    }

    private function updateSalesOrderAdvPayment($soAdvPaymentID)
    {

        $advanceReceipt= SalesOrderAdvPayment::find($soAdvPaymentID);

        if(!empty($advanceReceipt)) {

            $advanceReceiptDetailsSum = AdvanceReceiptDetails::selectRaw('IFNULL( Sum(paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advanceReceipt->companySystemID)
                ->where('soAdvPaymentID', $advanceReceipt->soAdvPaymentID)
                ->where('salesOrderID', $advanceReceipt->soID)
                ->first();

            if (!empty($advanceReceiptDetailsSum)) {

                if ($advanceReceipt->reqAmount == $advanceReceiptDetailsSum->SumOfpaymentAmount) {
                    SalesOrderAdvPayment::find($soAdvPaymentID)
                        ->update(['fullyPaid' => 2, 'selectedToPayment' => 0]);
                }

                if (($advanceReceipt->reqAmount > $advanceReceiptDetailsSum->SumOfpaymentAmount) && ($advanceReceiptDetailsSum->SumOfpaymentAmount > 0)) {
                    SalesOrderAdvPayment::find($soAdvPaymentID)
                        ->update(['fullyPaid' => 1, 'selectedToPayment' => 0]);
                }

                if ($advanceReceiptDetailsSum->SumOfpaymentAmount == 0) {
                    SalesOrderAdvPayment::find($soAdvPaymentID)
                        ->update(['fullyPaid' => 0, 'selectedToPayment' => 0]);
                }
            }
        }

    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAdvancePaymentDetailsAPIRequest;
use App\Http\Requests\API\UpdateAdvancePaymentDetailsAPIRequest;
use App\Models\AdvancePaymentDetails;
use App\Models\BankAssign;
use App\Models\BookInvSuppDet;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Repositories\AdvancePaymentDetailsRepository;
use App\Repositories\PaySupplierInvoiceMasterRepository;
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
    private $userRepository;

    public function __construct(AdvancePaymentDetailsRepository $advancePaymentDetailsRepo, UserRepository $userRepo, PaySupplierInvoiceMasterRepository $paySupplierInvoiceMasterRepo)
    {
        $this->advancePaymentDetailsRepository = $advancePaymentDetailsRepo;
        $this->userRepository = $userRepo;
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

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
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

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details saved successfully');
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
            return $this->sendError('Advance Payment Details not found');
        }

        return $this->sendResponse($advancePaymentDetails->toArray(), 'Advance Payment Details retrieved successfully');
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
                return $this->sendError('Advance Payment Details not found');
            }

            $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

            $bankMaster = BankAssign::ofCompany($payMaster->companySystemID)->isActive()->where('bankmasterAutoID', $payMaster->BPVbank)->first();

            if (empty($bankMaster)) {
                return $this->sendError('Selected Bank is not active', 500, ['type' => 'amountmismatch']);
            }

            $bankAccount = \App\Models\BankAccount::isActive()->find($payMaster->BPVAccount);

            if (empty($bankAccount)) {
                return $this->sendError('Selected Bank Account is not active', 500, ['type' => 'amountmismatch']);
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
                return $this->sendError('Payment amount cannot be greater than requested amount', 500, ['type' => 'amountmismatch']);
            }

            $conversion = \Helper::convertAmountToLocalRpt(201, $id, $input["paymentAmount"]);
            $input['supplierDefaultAmount'] = $conversion['defaultAmount'];
            $input['localAmount'] = $conversion['localAmount'];
            $input['comRptAmount'] = $conversion['reportingAmount'];

            $advancePaymentDetails = $this->advancePaymentDetailsRepository->update($input, $id);

            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                ->where('purchaseOrderID', $advancePayment->poID)
                ->first();

            if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount || $advancePayment->reqAmount < $advancePaymentDetailsSum->SumOfpaymentAmount) {
                $updatePayment = PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 2]);
            }

            if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                $updatePayment = PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 1]);
            }

            if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                $updatePayment = PoAdvancePayment::find($input['poAdvPaymentID'])
                    ->update(['fullyPaid' => 0]);
            }

            DB::commit();

            return $this->sendResponse($advancePaymentDetails->toArray(), 'AdvancePaymentDetails updated successfully');
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
                return $this->sendError('Advance Payment Details not found');
            }

            $advancePaymentDetails->delete();

            $advancePayment = PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID);

            $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                ->where('companySystemID', $advancePayment->companySystemID)
                ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                ->where('purchaseOrderID', $advancePayment->poID)
                ->first();

            if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
                $updatePayment = PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 2, 'selectedToPayment' => 0]);
            }

            if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                $updatePayment = PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 1, 'selectedToPayment' => 0]);
            }

            if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                $updatePayment = PoAdvancePayment::find($advancePaymentDetails2->poAdvPaymentID)
                    ->update(['fullyPaid' => 0, 'selectedToPayment' => 0]);
            }

            $totalAmount = AdvancePaymentDetails::selectRaw("SUM(paymentAmount) as paymentAmount,SUM(localAmount) as localAmount, SUM(comRptAmount) as comRptAmount, SUM(supplierDefaultAmount) as supplierDefaultAmount, SUM(supplierTransAmount) as supplierTransAmount")->where('PayMasterAutoId', $advancePaymentDetails2->PayMasterAutoId)->first();

            $bankAmount = \Helper::convertAmountToLocalRpt(203, $id, $totalAmount->supplierTransAmount);

            $input['payAmountBank'] = \Helper::roundValue($bankAmount["defaultAmount"]);
            $input['payAmountSuppTrans'] = \Helper::roundValue($totalAmount->supplierTransAmount);
            $input['payAmountSuppDef'] = \Helper::roundValue($totalAmount->supplierDefaultAmount);
            $input['payAmountCompLocal'] = \Helper::roundValue($totalAmount->localAmount);
            $input['payAmountCompRpt'] = \Helper::roundValue($totalAmount->comRptAmount);
            $input['suppAmountDocTotal'] = \Helper::roundValue($totalAmount->supplierTransAmount);

            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $advancePaymentDetails2->PayMasterAutoId);

            DB::commit();
            return $this->sendResponse($id, 'Advance Payment Details deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }


    public function deleteAllADVPaymentDetail(Request $request)
    {
        $payMasterAutoId = $request->PayMasterAutoId;

        DB::beginTransaction();
        try {
            /** @var AdvancePaymentDetails $advancePaymentDetails */
            $advancePaymentDetails = $this->advancePaymentDetailsRepository->findWhere(['PayMasterAutoId' => $payMasterAutoId]);

            if (empty($advancePaymentDetails)) {
                return $this->sendError('Pay Supplier Invoice Detail not found');
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
                    $updatePayment = PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 2, 'selectedToPayment' => 0]);
                }

                if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                    $updatePayment = PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 1, 'selectedToPayment' => 0]);
                }

                if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                    $updatePayment = PoAdvancePayment::find($val->poAdvPaymentID)
                        ->update(['fullyPaid' => 0, 'selectedToPayment' => 0]);
                }
            }

            $input['payAmountBank'] = 0;
            $input['payAmountSuppTrans'] = 0;
            $input['payAmountSuppDef'] = 0;
            $input['payAmountCompLocal'] = 0;
            $input['payAmountCompRpt'] = 0;
            $input['suppAmountDocTotal'] = 0;

            $paySupplierInvoiceMaster = $this->paySupplierInvoiceMasterRepository->update($input, $payMasterAutoId);

            DB::commit();
            return $this->sendResponse($payMasterAutoId, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }


    public function getADVPaymentDetails(Request $request)
    {
        $advancePaymentDetails = $this->advancePaymentDetailsRepository->with('purchaseorder_by')->findWhere(['PayMasterAutoId' => $request->PayMasterAutoId]);
        return $this->sendResponse($advancePaymentDetails, 'Payment details saved successfully');
    }

    public function addADVPaymentDetail(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

        DB::beginTransaction();
        try {
            /*$finalError = array(
                'po_amount_not_matching' => array(),
                'adv_payment_already_exist' => array(),
            );
            $error_count = 0;

            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
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

                }
            }

            $confirm_error = array('type' => 'po_amount_not_matching', 'data' => $finalError);
            if ($error_count > 0) {
                return $this->sendError("Selected order has been already paid more than the order amount. Please check the payment status for this order.", 500, $confirm_error);
            }*/

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
                        return $this->sendError("Selected order has been already paid more than the order amount. Please check the payment status for this order.", 500, $confirm_error);
                    }

                    $tempArray = $new;
                    $tempArray["PayMasterAutoId"] = $input["PayMasterAutoId"];
                    $tempArray["paymentAmount"] = $new["BalanceAmount"];
                    $tempArray["supplierTransAmount"] = $tempArray["paymentAmount"];

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
                        $advancePaymentUpdate = AdvancePaymentDetails::where('advancePaymentDetailAutoID', $paySupplierInvoiceDetails->advancePaymentDetailAutoID)->update(['supplierDefaultAmount' => $conversion['defaultAmount'], 'localAmount' => $conversion['localAmount'], 'comRptAmount' => $conversion['reportingAmount']]);

                        $advancePayment = PoAdvancePayment::find($new['poAdvPaymentID']);

                        $advancePaymentDetailsSum = AdvancePaymentDetails::selectRaw('IFNULL( Sum( erp_advancepaymentdetails.paymentAmount ), 0 ) AS SumOfpaymentAmount ')
                            ->where('companySystemID', $advancePayment->companySystemID)
                            ->where('poAdvPaymentID', $advancePayment->poAdvPaymentID)
                            ->where('purchaseOrderID', $advancePayment->poID)
                            ->first();

                        if ($advancePayment->reqAmount == $advancePaymentDetailsSum->SumOfpaymentAmount) {
                            $updatePayment = PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 2, 'selectedToPayment' => -1]);
                        }

                        if (($advancePayment->reqAmount > $advancePaymentDetailsSum->SumOfpaymentAmount) && ($advancePaymentDetailsSum->SumOfpaymentAmount > 0)) {
                            $updatePayment = PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 1, 'selectedToPayment' => -1]);
                        }

                        if ($advancePaymentDetailsSum->SumOfpaymentAmount == 0) {
                            $updatePayment = PoAdvancePayment::find($new['poAdvPaymentID'])
                                ->update(['fullyPaid' => 0, 'selectedToPayment' => -1]);
                        }

                    }
                }
            }

            DB::commit();
            return $this->sendResponse('', 'Payment details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage());
        }
    }

}

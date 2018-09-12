<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceDetailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceDetail
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file contains the all CRUD for Pay Pay Supplier Invoice Detail
 * -- REVISION HISTORY
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceDetailAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceDetailAPIRequest;
use App\Models\AccountsPayableLedger;
use App\Models\MatchDocumentMaster;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\PaySupplierInvoiceDetailRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceDetailController
 * @package App\Http\Controllers\API
 */
class PaySupplierInvoiceDetailAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceDetailRepository */
    private $paySupplierInvoiceDetailRepository;
    private $userRepository;

    public function __construct(PaySupplierInvoiceDetailRepository $paySupplierInvoiceDetailRepo, UserRepository $userRepo)
    {
        $this->paySupplierInvoiceDetailRepository = $paySupplierInvoiceDetailRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Get a listing of the PaySupplierInvoiceDetails.",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get all PaySupplierInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceDetail")
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
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->all();

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Details retrieved successfully');
    }

    /**
     * @param CreatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceDetails",
     *      summary="Store a newly created PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Store PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceDetails->toArray(), 'Pay Supplier Invoice Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Display the specified PaySupplierInvoiceDetail",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Get PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
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
        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'Pay Supplier Invoice Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Update the specified PaySupplierInvoiceDetail in storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Update PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceDetail")
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
     *                  ref="#/definitions/PaySupplierInvoiceDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceDetail)) {
            return $this->sendError('Pay Supplier Invoice Detail not found');
        }

        $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->where('apAutoID', $input["apAutoID"])->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

        $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $input["bookingInvSystemCode"])->where('documentSystemID', $input["addedDocumentSystemID"])->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

        $machAmount = 0;
        if ($matchedAmount) {
            $machAmount = $matchedAmount["SumOfmatchedAmount"];
        }
        $input["paymentBalancedAmount"] = \Helper::roundValue($input["supplierInvoiceAmount"] - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1)));

        $conversionAmount = \Helper::convertAmountToLocalRpt(4, $input["payDetailAutoID"], $input["supplierPaymentAmount"]);
        $input["paymentSupplierDefaultAmount"] = \Helper::roundValue($conversionAmount["defaultAmount"]);
        $input["paymentLocalAmount"] = $conversionAmount["localAmount"];
        $input["paymentComRptAmount"] = $conversionAmount["reportingAmount"];

        $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->update($input, $id);

        //$master = PaySupplierInvoiceMaster::with('transactioncurrency')->find($paySupplierInvoiceDetail->PayMasterAutoId);

        if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $paySupplierInvoiceDetail->paymentBalancedAmount) {
            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                ->update(['fullyInvoice' => 0]);
        }

        if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $paySupplierInvoiceDetail->paymentBalancedAmount) && ($paySupplierInvoiceDetail->paymentBalancedAmount > 0)) {
            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                ->update(['fullyInvoice' => 1]);
        }

        if ($paySupplierInvoiceDetail->paymentBalancedAmount <= 0) {
            $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                ->update(['fullyInvoice' => 2]);
        }

        return $this->sendResponse($paySupplierInvoiceDetail->toArray(), 'PaySupplierInvoiceDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceDetails/{id}",
     *      summary="Remove the specified PaySupplierInvoiceDetail from storage",
     *      tags={"PaySupplierInvoiceDetail"},
     *      description="Delete PaySupplierInvoiceDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceDetail",
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
            /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
            $paySupplierInvoiceDetailDelete = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);
            $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWithoutFail($id);
            if (empty($paySupplierInvoiceDetail)) {
                return $this->sendError('Pay Supplier Invoice Detail not found');
            }

            $paySupplierInvoiceDetailDelete->delete();

            $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->where('apAutoID', $paySupplierInvoiceDetail->apAutoID)->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

            $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $paySupplierInvoiceDetail->bookingInvSystemCode)->where('documentSystemID', $paySupplierInvoiceDetail->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

            $machAmount = 0;
            if ($matchedAmount) {
                $machAmount = $matchedAmount["SumOfmatchedAmount"];
            }

           $paymentBalancedAmount = \Helper::roundValue($paySupplierInvoiceDetail->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1)));

            if ($paySupplierInvoiceDetail->supplierInvoiceAmount == $paymentBalancedAmount) {
                $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                    ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
            }

            if (($paySupplierInvoiceDetail->supplierInvoiceAmount > $paymentBalancedAmount) && ($paymentBalancedAmount > 0)) {
                $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                    ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
            }

            if ($paymentBalancedAmount <= 0) {
                $updatePayment = AccountsPayableLedger::find($paySupplierInvoiceDetail->apAutoID)
                    ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
            }

            DB::commit();
            return $this->sendResponse($id, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }


    public function deleteAllPOPaymentDetail(Request $request)
    {
        $payMasterAutoId = $request->PayMasterAutoId;

        DB::beginTransaction();
        try {
            /** @var PaySupplierInvoiceDetail $paySupplierInvoiceDetail */
            $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->findWhere(['PayMasterAutoId' => $payMasterAutoId]);

            if (empty($paySupplierInvoiceDetail)) {
                return $this->sendError('Pay Supplier Invoice Detail not found');
            }

            foreach ($paySupplierInvoiceDetail as $val) {

                $paySupplierInvoiceDetail = $this->paySupplierInvoiceDetailRepository->find($val->payDetailAutoID);
                $paySupplierInvoiceDetail->delete();

                $supplierPaidAmountSum = PaySupplierInvoiceDetail::selectRaw('erp_paysupplierinvoicedetail.apAutoID, erp_paysupplierinvoicedetail.supplierInvoiceAmount, Sum(erp_paysupplierinvoicedetail.supplierPaymentAmount) AS SumOfsupplierPaymentAmount')->where('apAutoID', $val->apAutoID)->groupBy('erp_paysupplierinvoicedetail.apAutoID')->first();

                $matchedAmount = MatchDocumentMaster::selectRaw('erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.documentID, Sum(erp_matchdocumentmaster.matchedAmount) AS SumOfmatchedAmount')->where('PayMasterAutoId', $val->bookingInvSystemCode)->where('documentSystemID', $val->addedDocumentSystemID)->groupBy('erp_matchdocumentmaster.PayMasterAutoId', 'erp_matchdocumentmaster.documentSystemID')->first();

                $machAmount = 0;
                if ($matchedAmount) {
                    $machAmount = $matchedAmount["SumOfmatchedAmount"];
                }

                $paymentBalancedAmount = \Helper::roundValue($val->supplierInvoiceAmount - ($supplierPaidAmountSum["SumOfsupplierPaymentAmount"] + ($machAmount * -1)));

                if ($val->supplierInvoiceAmount == $paymentBalancedAmount) {
                    $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                        ->update(['fullyInvoice' => 0, 'selectedToPaymentInv' => 0]);
                }

                if (($val->supplierInvoiceAmount > $paymentBalancedAmount) && ($paymentBalancedAmount > 0)) {
                    $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                        ->update(['fullyInvoice' => 1, 'selectedToPaymentInv' => 0]);
                }

                if ($paymentBalancedAmount <= 0) {
                    $updatePayment = AccountsPayableLedger::find($val->apAutoID)
                        ->update(['fullyInvoice' => 2, 'selectedToPaymentInv' => 0]);
                }
            }

            DB::commit();
            return $this->sendResponse($payMasterAutoId, 'Pay Supplier Invoice Detail deleted successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }
    }


    public function addPOPaymentDetail(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $payMaster = PaySupplierInvoiceMaster::find($input["PayMasterAutoId"]);

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $new) {
                if ($new['isChecked']) {
                    $tempArray = $new;
                    $tempArray["supplierPaymentCurrencyID"] = $payMaster["BPVbankCurrency"];
                    $tempArray["supplierPaymentER"] = $payMaster["BPVbankCurrencyER"];
                    $tempArray["paymentSupplierDefaultAmount"] = 0;
                    $tempArray["paymentLocalAmount"] = 0;
                    $tempArray["paymentComRptAmount"] = 0;
                    $tempArray["supplierPaymentAmount"] = 0;
                    $tempArray["PayMasterAutoId"] = $input["PayMasterAutoId"];

                    $tempArray['createdPcID'] = gethostname();
                    $tempArray['createdUserID'] = $user->employee['empID'];
                    $tempArray['createdUserSystemID'] = $user->employee['employeeSystemID'];

                    unset($tempArray['isChecked']);
                    unset($tempArray['DecimalPlaces']);
                    unset($tempArray['CurrencyCode']);

                    if ($tempArray) {
                        $paySupplierInvoiceDetails = $this->paySupplierInvoiceDetailRepository->create($tempArray);
                        $updatePayment = AccountsPayableLedger::find($new['apAutoID'])
                            ->update(['selectedToPaymentInv' => -1]);
                    }
                }
            }
            DB::commit();
            return $this->sendResponse('', 'Payment details saved successfully');
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError('Error Occurred');
        }

    }

    function getPOPaymentDetails(Request $request)
    {
        $data = PaySupplierInvoiceDetail::where('PayMasterAutoId', $request->PayMasterAutoId)->get();
        return $this->sendResponse($data, 'Payment details saved successfully');
    }

}

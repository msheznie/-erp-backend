<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePayCreditNoteDetailAPIRequest;
use App\Http\Requests\API\UpdatePayCreditNoteDetailAPIRequest;
use App\Models\PayCreditNoteDetail;
use App\Repositories\PayCreditNoteDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\CreditNote;
use App\Models\PaySupplierInvoiceMaster;
use App\helper\Helper;
use App\Models\CreditNoteReceipt;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PayCreditNoteDetailController
 * @package App\Http\Controllers\API
 */

class PayCreditNoteDetailAPIController extends AppBaseController
{
    /** @var  PayCreditNoteDetailRepository */
    private $payCreditNoteDetailRepository;

    public function __construct(PayCreditNoteDetailRepository $payCreditNoteDetailRepo)
    {
        $this->payCreditNoteDetailRepository = $payCreditNoteDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/payCreditNoteDetails",
     *      summary="getPayCreditNoteDetailList",
     *      tags={"PayCreditNoteDetail"},
     *      description="Get all PayCreditNoteDetails",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/PayCreditNoteDetail")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->payCreditNoteDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->payCreditNoteDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $payCreditNoteDetails = $this->payCreditNoteDetailRepository->all();

        return $this->sendResponse($payCreditNoteDetails->toArray(), 'Pay Credit Note Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/payCreditNoteDetails",
     *      summary="createPayCreditNoteDetail",
     *      tags={"PayCreditNoteDetail"},
     *      description="Create PayCreditNoteDetail",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PayCreditNoteDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePayCreditNoteDetailAPIRequest $request)
    {
        $input = $request->all();

        $payCreditNoteDetail = $this->payCreditNoteDetailRepository->create($input);

        return $this->sendResponse($payCreditNoteDetail->toArray(), 'Pay Credit Note Detail saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/payCreditNoteDetails/{id}",
     *      summary="getPayCreditNoteDetailItem",
     *      tags={"PayCreditNoteDetail"},
     *      description="Get PayCreditNoteDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayCreditNoteDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PayCreditNoteDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PayCreditNoteDetail $payCreditNoteDetail */
        $payCreditNoteDetail = $this->payCreditNoteDetailRepository->findWithoutFail($id);

        if (empty($payCreditNoteDetail)) {
            return $this->sendError('Pay Credit Note Detail not found');
        }

        return $this->sendResponse($payCreditNoteDetail->toArray(), 'Pay Credit Note Detail retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/payCreditNoteDetails/{id}",
     *      summary="updatePayCreditNoteDetail",
     *      tags={"PayCreditNoteDetail"},
     *      description="Update PayCreditNoteDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayCreditNoteDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/PayCreditNoteDetail"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePayCreditNoteDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var PayCreditNoteDetail $payCreditNoteDetail */
        $payCreditNoteDetail = $this->payCreditNoteDetailRepository->findWithoutFail($id);

        if (empty($payCreditNoteDetail)) {
            return $this->sendError('Pay Credit Note Detail not found');
        }

        $data = [
            'creditNotePaymentAmount' => $input['creditNotePaymentAmount']
        ];

        $payCreditNoteDetail = $this->payCreditNoteDetailRepository->update($data, $id);

        return $this->sendResponse($payCreditNoteDetail->toArray(), 'PayCreditNoteDetail updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/payCreditNoteDetails/{id}",
     *      summary="deletePayCreditNoteDetail",
     *      tags={"PayCreditNoteDetail"},
     *      description="Delete PayCreditNoteDetail",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PayCreditNoteDetail",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PayCreditNoteDetail $payCreditNoteDetail */
        $payCreditNoteDetail = $this->payCreditNoteDetailRepository->findWithoutFail($id);

        if (empty($payCreditNoteDetail)) {
            return $this->sendError(trans('custom.pay_credit_note_detail_not_found'));
        }

        $payCreditNoteDetail->delete();

        return $this->sendResponse(null,trans('custom.pay_credit_note_detail_deleted_successfully'));
    }

    public function getCreditNotePaymentDetails(Request $request)
    {
        $input = $request->all();

        $payMasterAutoId = $input['payMasterAutoId'];
        $paymentVocher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoId)->first();
        if(empty($paymentVocher)) {
            return $this->sendError(trans('custom.payment_voucher_not_found'));
        }

        $decimalPlaces = Helper::getCurrencyDecimalPlace($paymentVocher->supplierTransCurrencyID);
        $companySystemID = $paymentVocher->companySystemID;
        
        $creditNotePaymentDetails = PayCreditNoteDetail::with(['creditnote'])
            ->select('erp_paycreditnotedetails.*')
            ->selectRaw($decimalPlaces . ' as DecimalPlaces')
            ->selectRaw('IFNULL((SELECT SUM(refundAmount) FROM erp_creditnote_receipts 
                         WHERE erp_creditnote_receipts.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID 
                         AND erp_creditnote_receipts.companySystemID = ' . $companySystemID . '), 0) as creditNoteAmount')
            ->selectRaw('IFNULL((SELECT SUM(creditNotePaymentAmount) FROM erp_paycreditnotedetails pcd 
                         WHERE pcd.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID 
                         AND pcd.companySystemID = ' . $companySystemID . '), 0) as totalPaidAmount')
            ->selectRaw('(IFNULL((SELECT SUM(refundAmount) FROM erp_creditnote_receipts 
                         WHERE erp_creditnote_receipts.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID 
                         AND erp_creditnote_receipts.companySystemID = ' . $companySystemID . '), 0) - 
                         IFNULL((SELECT SUM(creditNotePaymentAmount) FROM erp_paycreditnotedetails pcd 
                         WHERE pcd.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID 
                         AND pcd.companySystemID = ' . $companySystemID . '), 0)) as paymentBalancedAmount')
            ->where('erp_paycreditnotedetails.PayMasterAutoId', $payMasterAutoId)
            ->where('erp_paycreditnotedetails.companySystemID', $companySystemID)
            ->get();

        $creditNoteIds = $creditNotePaymentDetails->pluck('creditNoteAutoID')->toArray();

        $creditNoteReceipts = CreditNoteReceipt::with(['customerReceivePayment.currency', 'creditNote'])->whereIn('creditNoteAutoID', $creditNoteIds)->where('companySystemID', $companySystemID)->get();

        $data = [
            'creditNotePaymentDetails' => $creditNotePaymentDetails->toArray(),
            'creditNoteReceipts' => $creditNoteReceipts->toArray()
        ];
            
        return $this->sendResponse($data, 'Credit Note Payment Details retrieved successfully');
    }

    public function getCreditNoteForPV(Request $request)
    {
        $input = $request->all();
        $payMasterAutoId = $input['payMasterAutoId'];

        $paymentVoucher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoId)->first();
        if(empty($paymentVoucher)) {
            return $this->sendError(trans('custom.payment_voucher_not_found'));
        }

        $decimalPlaces = Helper::getCurrencyDecimalPlace($paymentVoucher->supplierTransCurrencyID);
        $companySystemID = $paymentVoucher->companySystemID;

        $creditNotes = CreditNote::with('currency')
            ->select(
                'erp_creditnote.creditNoteAutoID', 
                'erp_creditnote.creditNoteCode', 
                'erp_creditnote.creditNoteDate', 
                'erp_creditnote.customerCurrencyID'
            )
            ->selectRaw($decimalPlaces . ' as DecimalPlaces')
            ->selectRaw('IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0) as creditNoteAmount')
            ->selectRaw('IFNULL(SUM(erp_paycreditnotedetails.creditNotePaymentAmount), 0) as totalPaidAmount')
            ->selectRaw('(IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0) - IFNULL(SUM(erp_paycreditnotedetails.creditNotePaymentAmount), 0)) as paymentBalancedAmount')
            ->selectRaw('GROUP_CONCAT(DISTINCT erp_customerreceivepayment.custPaymentReceiveCode SEPARATOR "|") as receiptVoucherCode')
            ->selectRaw('(SELECT COUNT(*) > 0 FROM erp_paycreditnotedetails pcd 
                         INNER JOIN erp_paysupplierinvoicemaster pvm ON pcd.PayMasterAutoId = pvm.PayMasterAutoId 
                         WHERE pcd.creditNoteAutoID = erp_creditnote.creditNoteAutoID 
                         AND pcd.companySystemID = ' . $companySystemID . ' 
                         AND pvm.approved != -1) as isLinkedToNonApprovedPV')
            ->selectRaw('(SELECT COUNT(*) > 0 FROM erp_matchdocumentmaster mdm 
                         WHERE mdm.PayMasterAutoId = erp_creditnote.creditNoteAutoID 
                         AND mdm.companySystemID = ' . $companySystemID . ' 
                         AND mdm.documentSystemID = 19 
                         AND mdm.matchingConfirmedYN = 0) as isLinkedToDraftReceiptMatching')
            ->leftJoin('erp_creditnote_receipts', function($join) use ($companySystemID) {
                $join->on('erp_creditnote_receipts.creditNoteAutoID', '=', 'erp_creditnote.creditNoteAutoID')
                     ->where('erp_creditnote_receipts.companySystemID', $companySystemID);
            })
            ->leftJoin('erp_customerreceivepayment', function($join) use ($companySystemID) {
                $join->on('erp_creditnote_receipts.custReceivePaymentAutoID', '=', 'erp_customerreceivepayment.custReceivePaymentAutoID')
                     ->where('erp_customerreceivepayment.companySystemID', $companySystemID);
            })
            ->leftJoin('erp_paycreditnotedetails', function($join) use ($companySystemID) {
                $join->on('erp_creditnote.creditNoteAutoID', '=', 'erp_paycreditnotedetails.creditNoteAutoID')
                     ->where('erp_paycreditnotedetails.companySystemID', $companySystemID);
            })
            ->leftJoin('erp_paysupplierinvoicemaster', function($join) use ($companySystemID) {
                $join->on('erp_paycreditnotedetails.PayMasterAutoId', '=', 'erp_paysupplierinvoicemaster.PayMasterAutoId')
                     ->where('erp_paysupplierinvoicemaster.companySystemID', $companySystemID);
            })
            ->where('erp_creditnote.approved', -1)
            ->where('erp_creditnote.type', 3)
            ->where('erp_creditnote.matchInvoice', '!=', 2)
            ->where('erp_creditnote.companySystemID', $companySystemID)
            ->where('erp_creditnote.customerID', $paymentVoucher->BPVcustomerID)
            ->where('erp_creditnote.customerCurrencyID', $paymentVoucher->supplierTransCurrencyID)
            ->groupBy('erp_creditnote.creditNoteAutoID')
            ->havingRaw('(IFNULL(SUM(erp_creditnote_receipts.refundAmount), 0) - IFNULL(SUM(erp_paycreditnotedetails.creditNotePaymentAmount), 0)) > 0')
            ->orderBy('erp_creditnote.creditNoteAutoID', 'desc')
            ->get();

        return $this->sendResponse($creditNotes->toArray(), 'Credit Note Payment Details retrieved successfully');
    }

    public function addCreditNotePaymentDetail(Request $request)
    {
        $input = $request->all();

        $payMaster = PaySupplierInvoiceMaster::where('PayMasterAutoId', $input["payMasterAutoId"])->first();

        if (empty($payMaster)) {
            return $this->sendError(trans('custom.payment_voucher_not_found'));
        }

        $selectedCreditNoteIds = [];
        foreach ($input['detailTable'] as $item) {
            if (isset($item['isChecked']) && $item['isChecked']) {
                $selectedCreditNoteIds[] = $item['creditNoteAutoID'];
            }
        }

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $item) {
                if (isset($item['isChecked']) && $item['isChecked']) {
                    $payCreditNoteDetail = $this->payCreditNoteDetailRepository->create([
                        'PayMasterAutoId' => $input["payMasterAutoId"],
                        'creditNoteAutoID' => $item['creditNoteAutoID'],
                        'companySystemID' => $payMaster->companySystemID,
                        'creditNotePaymentAmount' => $item['creditNotePaymentAmount'] ?? 0
                    ]);
                }
            }

            DB::commit();
            return $this->sendResponse('', trans('custom.credit_note_payment_details_saved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(trans('custom.error_saving_credit_note_payment_details') . ': ' . $e->getMessage(), 500);
        }
    }

    public function deleteAllCreditNotePaymentDetail(Request $request)
    {
        $input = $request->all();
        $payMasterAutoId = $input['payMasterAutoId'];

        $payCreditNoteDetails = PayCreditNoteDetail::where('PayMasterAutoId', $payMasterAutoId)->delete();

        return $this->sendResponse(null, trans('custom.all_credit_note_payment_details_deleted_successfully'));
    }
}

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
use App\Criteria\LimitOffsetCriteria;
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

        $transAmount = Helper::convertAmountToLocalRpt(203, $payCreditNoteDetail["PayMasterAutoId"], $input['creditNotePaymentAmount'] ?? 0);

        $data = [
            'creditNotePaymentAmount' => $input['creditNotePaymentAmount'],
            'creditNotePaymentAmountLocal' => $transAmount['localAmount'] ?? 0,
            'creditNotePaymentAmountRpt' => $transAmount['reportingAmount'] ?? 0
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
        
        $companySystemIDInt = (int) $companySystemID;

        $creditNoteAmountSubquery = "
            IFNULL((SELECT SUM(refundAmount)
                FROM erp_creditnote_receipts
                WHERE erp_creditnote_receipts.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID
                  AND erp_creditnote_receipts.companySystemID = ?), 0)
        ";

        $paymentVoucherAmountSubquery = "
            IFNULL((SELECT SUM(creditNotePaymentAmount)
                FROM erp_paycreditnotedetails pcd
                WHERE pcd.creditNoteAutoID = erp_paycreditnotedetails.creditNoteAutoID
                  AND pcd.companySystemID = ?), 0)
        ";

        $receiptMatchingAmountSubquery = "
            IFNULL((SELECT SUM(mdm.matchingAmount)
                FROM erp_matchdocumentmaster mdm
                WHERE mdm.PayMasterAutoId = erp_paycreditnotedetails.creditNoteAutoID
                  AND mdm.documentSystemID = 19
                  AND mdm.matchingConfirmedYN = 1
                  AND mdm.companySystemID = ?), 0)
        ";

        $receiptVoucherAmountSubquery = "
            IFNULL((SELECT SUM(ecrd.receiveAmountTrans)
                FROM erp_custreceivepaymentdet ecrd
                INNER JOIN erp_customerreceivepayment ecrp
                    ON ecrd.custReceivePaymentAutoID = ecrp.custReceivePaymentAutoID
                    AND ecrp.approved = -1
                WHERE ecrd.addedDocumentSystemID = 19
                  AND ecrd.bookingInvCodeSystem = erp_paycreditnotedetails.creditNoteAutoID
                  AND ecrd.matchingDocID = 0
                  AND ecrd.companySystemID = ?), 0)
        ";

        $totalPaidAmount = "({$paymentVoucherAmountSubquery} + {$receiptMatchingAmountSubquery} - {$receiptVoucherAmountSubquery})";

        $creditNotePaymentDetails = PayCreditNoteDetail::with(['creditnote'])
            ->select('erp_paycreditnotedetails.*')
            ->selectRaw('? as DecimalPlaces', [$decimalPlaces])
            ->selectRaw("{$creditNoteAmountSubquery} as creditNoteAmount", [$companySystemIDInt])
            ->selectRaw("{$totalPaidAmount} as totalPaidAmount", array_fill(0, 3, $companySystemIDInt))
            ->selectRaw("({$creditNoteAmountSubquery} - {$totalPaidAmount}) as paymentBalancedAmount", array_fill(0, 4, $companySystemIDInt))
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
                'erp_creditnote.customerCurrencyID',
                'erp_creditnote.creditAmountTrans'
            )
            ->selectRaw($decimalPlaces . ' as DecimalPlaces')
            ->selectRaw('IFNULL(erp_creditnote.creditAmountTrans, 0) as creditNoteAmount')
            ->selectRaw('0 as isChecked')
            ->selectRaw('(IFNULL(SUM(erp_paycreditnotedetails.creditNotePaymentAmount), 0) + IFNULL(match_sum.totalMatchedAmount, 0) - IFNULL(rv_sum.totalReceiptVoucherAmount, 0)) as totalPaidAmount')
            ->selectRaw('(IFNULL(erp_creditnote.creditAmountTrans, 0) - (IFNULL(SUM(erp_paycreditnotedetails.creditNotePaymentAmount), 0) + IFNULL(match_sum.totalMatchedAmount, 0) - IFNULL(rv_sum.totalReceiptVoucherAmount, 0))) as paymentBalancedAmount')
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
            ->leftJoin(DB::raw('(SELECT 
                erp_matchdocumentmaster.PayMasterAutoId,
                erp_matchdocumentmaster.companySystemID,
                SUM(erp_matchdocumentmaster.matchingAmount) AS totalMatchedAmount
                FROM erp_matchdocumentmaster
                WHERE erp_matchdocumentmaster.documentSystemID = 19
                AND erp_matchdocumentmaster.matchingConfirmedYN = 1
                AND erp_matchdocumentmaster.companySystemID = ' . (int) $companySystemID . '
                GROUP BY erp_matchdocumentmaster.PayMasterAutoId, erp_matchdocumentmaster.companySystemID) as match_sum'), function($join) use ($companySystemID) {
                $join->on('match_sum.PayMasterAutoId', '=', 'erp_creditnote.creditNoteAutoID')
                     ->where('match_sum.companySystemID', '=', $companySystemID);
            })
            ->leftJoin(DB::raw('(SELECT 
                erp_custreceivepaymentdet.bookingInvCodeSystem as creditNoteAutoID,
                erp_custreceivepaymentdet.companySystemID,
                SUM(erp_custreceivepaymentdet.receiveAmountTrans) AS totalReceiptVoucherAmount
                FROM erp_custreceivepaymentdet
                INNER JOIN erp_customerreceivepayment ON erp_custreceivepaymentdet.custReceivePaymentAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                    AND erp_customerreceivepayment.approved = -1
                WHERE erp_custreceivepaymentdet.addedDocumentSystemID = 19
                AND erp_custreceivepaymentdet.bookingInvCodeSystem > 0
                AND erp_custreceivepaymentdet.matchingDocID = 0
                AND erp_custreceivepaymentdet.companySystemID = ' . (int) $companySystemID . '
                GROUP BY erp_custreceivepaymentdet.bookingInvCodeSystem, erp_custreceivepaymentdet.companySystemID) as rv_sum'), function($join) use ($companySystemID) {
                $join->on('rv_sum.creditNoteAutoID', '=', 'erp_creditnote.creditNoteAutoID')
                     ->where('rv_sum.companySystemID', '=', $companySystemID);
            })
            ->where('erp_creditnote.approved', -1)
            ->where('erp_creditnote.type', 3)
            ->where('erp_creditnote.matchInvoice', '!=', 2)
            ->where('erp_creditnote.companySystemID', $companySystemID)
            ->where('erp_creditnote.customerID', $paymentVoucher->BPVcustomerID)
            ->where('erp_creditnote.customerCurrencyID', $paymentVoucher->supplierTransCurrencyID)
            ->groupBy('erp_creditnote.creditNoteAutoID')
            ->havingRaw('paymentBalancedAmount > 0')
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

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $item) {
                if (isset($item['isChecked']) && $item['isChecked']) {
                    $transAmount = Helper::convertAmountToLocalRpt(203, $input["payMasterAutoId"], $item['creditNotePaymentAmount'] ?? 0);
                    $this->payCreditNoteDetailRepository->create([
                        'PayMasterAutoId' => $input["payMasterAutoId"],
                        'creditNoteAutoID' => $item['creditNoteAutoID'],
                        'companySystemID' => $payMaster->companySystemID,
                        'creditNotePaymentAmount' => $item['creditNotePaymentAmount'] ?? 0,
                        'creditNotePaymentAmountLocal' => $transAmount['localAmount'] ?? 0,
                        'creditNotePaymentAmountRpt' => $transAmount['reportingAmount'] ?? 0
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

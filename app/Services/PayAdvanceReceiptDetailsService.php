<?php

namespace App\Services;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\CustomerReceivePayment;
use App\Models\PayAdvanceReceiptDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\PayAdvanceReceiptDetailRepository;
use App\Utils\ServiceResponse;
use Illuminate\Support\Facades\DB;

class PayAdvanceReceiptDetailsService extends AppBaseController
{
    public function __construct(
        private PayAdvanceReceiptDetailRepository $payAdvanceReceiptDetailRepository
    ) {
    }

    public function update(int $id, $input): ServiceResponse
    {

        $payAdvanceReceiptDetail = $this->payAdvanceReceiptDetailRepository->findWithoutFail($id);

        if (empty($payAdvanceReceiptDetail)) {
            return ServiceResponse::failure(trans('custom.pay_advance_receipt_detail_not_found'));
        }

        $transAmount = Helper::convertAmountToLocalRpt(203, $payAdvanceReceiptDetail['PayMasterAutoId'], $input['advanceReceiptAmount'] ?? 0);

        $data = [
            'advanceReceiptAmount' => $input['advanceReceiptAmount'],
            'advanceReceiptAmountLocal' => $transAmount['localAmount'] ?? 0,
            'advanceReceiptAmountRpt' => $transAmount['reportingAmount'] ?? 0,
        ];

        $payAdvanceReceiptDetail = $this->payAdvanceReceiptDetailRepository->update($data, $id);

        return ServiceResponse::success($payAdvanceReceiptDetail->toArray(), 'PayAdvanceReceiptDetail updated successfully');
    }

    public function getAdvanceReceiptForPV($input): ServiceResponse
    {
        $payMasterAutoId = $input['payMasterAutoId'];

        $paymentVoucher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoId)->first();
        if (empty($paymentVoucher)) {
            return ServiceResponse::failure(trans('custom.payment_voucher_not_found'));
        }

        $decimalPlaces = Helper::getCurrencyDecimalPlace($paymentVoucher->supplierTransCurrencyID);
        $companySystemID = $paymentVoucher->companySystemID;

        $paymentVoucherAmountSubquery = "
            IFNULL((SELECT SUM(ABS(pard.advanceReceiptAmount))
                FROM erp_pay_advance_receipt_details pard
                WHERE pard.advanceReceiptAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                  AND pard.companySystemID = ?), 0)
        ";

        $receiptMatchingAmountSubquery = "
            IFNULL((SELECT SUM(ABS(mdm.matchingAmount))
                FROM erp_matchdocumentmaster mdm
                WHERE mdm.PayMasterAutoId = erp_customerreceivepayment.custReceivePaymentAutoID
                  AND mdm.documentSystemID = 21
                  AND mdm.matchingConfirmedYN = 1
                  AND mdm.companySystemID = ?), 0)
        ";

        $receiptVoucherAmountSubquery = "
            IFNULL((SELECT SUM(ABS(ecrd.receiveAmountTrans))
                FROM erp_custreceivepaymentdet ecrd
                INNER JOIN erp_customerreceivepayment ecrp
                    ON ecrd.custReceivePaymentAutoID = ecrp.custReceivePaymentAutoID
                    AND ecrp.approved = -1
                WHERE ecrd.addedDocumentSystemID = 21
                  AND ecrd.bookingInvCodeSystem = erp_customerreceivepayment.custReceivePaymentAutoID
                  AND ecrd.matchingDocID = 0
                  AND ecrd.companySystemID = ?), 0)
        ";

        $totalUsedAmount = "({$paymentVoucherAmountSubquery} + {$receiptMatchingAmountSubquery} - {$receiptVoucherAmountSubquery})";

        $advanceTransAbs = 'ABS(IFNULL(erp_customerreceivepayment.receivedAmount, 0))';

        $advanceReceipts = CustomerReceivePayment::with('currency')
            ->select(
                'erp_customerreceivepayment.custReceivePaymentAutoID',
                'erp_customerreceivepayment.custPaymentReceiveCode',
                'erp_customerreceivepayment.custPaymentReceiveDate',
                'erp_customerreceivepayment.custTransactionCurrencyID',
                'erp_customerreceivepayment.receivedAmount'
            )
            ->selectRaw('? as DecimalPlaces', [$decimalPlaces])
            ->selectRaw('erp_customerreceivepayment.custReceivePaymentAutoID as advanceReceiptAutoID')
            ->selectRaw('erp_customerreceivepayment.custPaymentReceiveCode as advanceVoucherCode')
            ->selectRaw('erp_customerreceivepayment.custPaymentReceiveDate as advanceVoucherDate')
            ->selectRaw("{$advanceTransAbs} as advanceReceiptAmount")
            ->selectRaw('0 as isChecked')
            ->selectRaw("{$totalUsedAmount} as totalPaidAmount", array_fill(0, 3, $companySystemID))
            ->selectRaw("({$advanceTransAbs} - {$totalUsedAmount}) as paymentBalancedAmount", array_fill(0, 3, $companySystemID))
            ->selectRaw('(SELECT COUNT(*) > 0 FROM erp_pay_advance_receipt_details pard
                         INNER JOIN erp_paysupplierinvoicemaster pvm ON pard.PayMasterAutoId = pvm.PayMasterAutoId
                         WHERE pard.advanceReceiptAutoID = erp_customerreceivepayment.custReceivePaymentAutoID
                         AND pard.companySystemID = ?
                         AND pvm.approved != -1) as isLinkedToNonApprovedPV', [$companySystemID])
            ->selectRaw('(SELECT COUNT(*) > 0 FROM erp_matchdocumentmaster mdm
                         WHERE mdm.PayMasterAutoId = erp_customerreceivepayment.custReceivePaymentAutoID
                         AND mdm.companySystemID = ?
                         AND mdm.documentSystemID = 21
                         AND mdm.matchingConfirmedYN = 0) as isLinkedToDraftReceiptMatching', [$companySystemID])
            ->where('erp_customerreceivepayment.approved', -1)
            ->where('erp_customerreceivepayment.documentType', 15)
            ->where('erp_customerreceivepayment.matchInvoice', '!=', 2)
            ->where('erp_customerreceivepayment.companySystemID', $companySystemID)
            ->where('erp_customerreceivepayment.customerID', $paymentVoucher->BPVcustomerID)
            ->where('erp_customerreceivepayment.custTransactionCurrencyID', $paymentVoucher->supplierTransCurrencyID)
            ->whereRaw("({$advanceTransAbs} - {$totalUsedAmount}) > 0", array_fill(0, 3, $companySystemID))
            ->orderBy('erp_customerreceivepayment.custReceivePaymentAutoID', 'desc')
            ->get();

        return ServiceResponse::success($advanceReceipts->toArray(), 'Advance Receipt vouchers retrieved successfully');
    }

    public function getAdvanceReceiptPaymentDetails($input): ServiceResponse
    {
        $payMasterAutoId = $input['payMasterAutoId'];
        $paymentVocher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoId)->first();
        if (empty($paymentVocher)) {
            return ServiceResponse::failure(trans('custom.payment_voucher_not_found'));
        }

        $decimalPlaces = Helper::getCurrencyDecimalPlace($paymentVocher->supplierTransCurrencyID);
        $companySystemID = $paymentVocher->companySystemID;

        $advanceVoucherTotalSubquery = "
            IFNULL((SELECT ABS(receivedAmount)
                FROM erp_customerreceivepayment ecrp
                WHERE ecrp.custReceivePaymentAutoID = erp_pay_advance_receipt_details.advanceReceiptAutoID
                  AND ecrp.companySystemID = ?), 0)
        ";

        $paymentVoucherAmountSubquery = "
            IFNULL((SELECT SUM(pard.advanceReceiptAmount)
                FROM erp_pay_advance_receipt_details pard
                WHERE pard.advanceReceiptAutoID = erp_pay_advance_receipt_details.advanceReceiptAutoID
                  AND pard.companySystemID = ?), 0)
        ";

        $receiptMatchingAmountSubquery = "
            IFNULL((SELECT SUM(ABS(mdm.matchingAmount))
                FROM erp_matchdocumentmaster mdm
                WHERE mdm.PayMasterAutoId = erp_pay_advance_receipt_details.advanceReceiptAutoID
                  AND mdm.documentSystemID = 21
                  AND mdm.matchingConfirmedYN = 1
                  AND mdm.companySystemID = ?), 0)
        ";

        $totalPaidAmountSql = "({$paymentVoucherAmountSubquery} + {$receiptMatchingAmountSubquery})";

        $advanceReceiptPaymentDetails = PayAdvanceReceiptDetail::with(['advanceReceipt.currency'])
            ->select('erp_pay_advance_receipt_details.*')
            ->selectRaw('? as DecimalPlaces', [$decimalPlaces])
            ->selectRaw("{$advanceVoucherTotalSubquery} as advanceAmount", [$companySystemID])
            ->selectRaw("{$totalPaidAmountSql} as totalPaidAmount", array_fill(0, 2, $companySystemID))
            ->selectRaw("({$advanceVoucherTotalSubquery} - {$totalPaidAmountSql}) as paymentBalancedAmount", array_fill(0, 3, $companySystemID))
            ->where('erp_pay_advance_receipt_details.PayMasterAutoId', $payMasterAutoId)
            ->where('erp_pay_advance_receipt_details.companySystemID', $companySystemID)
            ->get();

        return ServiceResponse::success($advanceReceiptPaymentDetails, 'Advance Receipt Payment Details retrieved successfully');
    }

    public function addAdvanceReceiptPaymentDetail($input): ServiceResponse
    {
        $payMaster = PaySupplierInvoiceMaster::where('PayMasterAutoId', $input['payMasterAutoId'])->first();

        if (empty($payMaster)) {
            return ServiceResponse::failure(trans('custom.payment_voucher_not_found'));
        }

        DB::beginTransaction();
        try {
            foreach ($input['detailTable'] as $item) {
                if (isset($item['isChecked']) && $item['isChecked']) {
                    $transAmount = Helper::convertAmountToLocalRpt(203, $input['payMasterAutoId'], $item['advanceReceiptPaymentAmount'] ?? 0);
                    $this->payAdvanceReceiptDetailRepository->create([
                        'PayMasterAutoId' => $input['payMasterAutoId'],
                        'advanceReceiptAutoID' => $item['advanceReceiptAutoID'],
                        'companySystemID' => $payMaster->companySystemID,
                        'advanceReceiptAmount' => $item['advanceReceiptPaymentAmount'] ?? 0,
                        'advanceReceiptAmountLocal' => $transAmount['localAmount'] ?? 0,
                        'advanceReceiptAmountRpt' => $transAmount['reportingAmount'] ?? 0,
                    ]);
                }
            }

            DB::commit();

            return ServiceResponse::success('', trans('custom.advance_receipt_payment_details_saved_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();

            return ServiceResponse::failure(trans('custom.error_saving_advance_receipt_payment_details') . ': ' . $e->getMessage());
        }
    }
}


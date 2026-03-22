<?php

namespace App\Services;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\CreditNoteReceipt;
use App\Models\PayCreditNoteDetail;
use App\Models\PaySupplierInvoiceMaster;
use App\Utils\ServiceResponse;
use Illuminate\Database\Eloquent\Collection;

class PayCreditNoteDetailsService extends AppBaseController
{
    public function getCreditNotePaymentDetails(array $input): ServiceResponse
    {
        $payMasterAutoId = $input['payMasterAutoId'];
        $paymentVocher = PaySupplierInvoiceMaster::where('PayMasterAutoId', $payMasterAutoId)->first();
        if (empty($paymentVocher)) {
            return ServiceResponse::failure(trans('custom.payment_voucher_not_found'));
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

        $creditNoteReceipts = CreditNoteReceipt::with(['customerReceivePayment.currency', 'creditNote'])
            ->whereIn('creditNoteAutoID', $creditNoteIds)
            ->where('companySystemID', $companySystemID)
            ->get();

        return ServiceResponse::success([
            // Keep Eloquent models/collections so Blade can access relations
            // like `$ddet->creditnote` without converting them to plain arrays.
            'creditNotePaymentDetails' => $creditNotePaymentDetails,
            'creditNoteReceipts' => $creditNoteReceipts,
        ], 'Credit Note Payment Details retrieved successfully');
    }
}


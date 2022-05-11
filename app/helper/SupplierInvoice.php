<?php


namespace App\helper;


use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\Taxdetail;

class SupplierInvoice
{
    public static function updateMaster($id = 0)
    {

        // update master table
        $bookInvSuppMaster = BookInvSuppMaster::with(['supplier'])->find($id);

        if(!empty($bookInvSuppMaster) && ($bookInvSuppMaster->documentType == 1 || $bookInvSuppMaster->documentType == 4)) {

            $total = DirectInvoiceDetails::where('directInvoiceAutoID', $bookInvSuppMaster->bookingSuppMasInvAutoID)
                ->selectRaw("SUM(DIAmount) as DIAmount,
                             SUM(localAmount) as localAmount,
                             SUM(comRptAmount) as comRptAmount,
                             SUM(netAmount) as netAmount,
                             SUM(netAmountLocal) as netAmountLocal,
                             SUM(netAmountRpt) as netAmountRpt,
                             SUM(VATAmount) as VATAmount,
                             SUM(VATAmountLocal) as VATAmountLocal,
                             SUM(VATAmountRpt) as VATAmountRpt")
                ->groupBy('directInvoiceAutoID')
                ->first();

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', 11)
                ->delete();
            if(!empty($total)) {
                $bookInvSuppMaster['bookingAmountTrans'] = ($bookInvSuppMaster->rcmActivated) ? \Helper::roundValue($total->DIAmount) : \Helper::roundValue($total->DIAmount) + \Helper::roundValue($total->VATAmount);
                $bookInvSuppMaster['bookingAmountLocal'] = ($bookInvSuppMaster->rcmActivated) ? \Helper::roundValue($total->localAmount) : \Helper::roundValue($total->localAmount) + \Helper::roundValue($total->VATAmountLocal);
                $bookInvSuppMaster['bookingAmountRpt'] = ($bookInvSuppMaster->rcmActivated) ? \Helper::roundValue($total->comRptAmount) : \Helper::roundValue($total->comRptAmount) + \Helper::roundValue($total->VATAmountRpt);
                $bookInvSuppMaster['netAmount'] = \Helper::roundValue($total->netAmount);
                $bookInvSuppMaster['netAmountLocal'] = \Helper::roundValue($total->netAmountLocal);
                $bookInvSuppMaster['netAmountRpt'] = \Helper::roundValue($total->netAmountRpt);
                $bookInvSuppMaster['VATAmount'] = \Helper::roundValue($total->VATAmount);
                $bookInvSuppMaster['VATAmountLocal'] = \Helper::roundValue($total->VATAmountLocal);
                $bookInvSuppMaster['VATAmountRpt'] = \Helper::roundValue($total->VATAmountRpt);
                $bookInvSuppMaster->save();

                // insert to tax details
                $newVat['companyID'] = $bookInvSuppMaster->companyID;
                $newVat['companySystemID'] = $bookInvSuppMaster->companySystemID;
                $newVat['documentID'] = $bookInvSuppMaster->documentID;
                $newVat['documentSystemID'] = $bookInvSuppMaster->documentSystemID;
                $newVat['documentSystemCode'] = $id;
                $newVat['documentCode'] = $bookInvSuppMaster->bookingInvCode;
                $newVat['taxPercent'] = $bookInvSuppMaster->VATPercentage;
                $newVat['payeeSystemCode'] = $bookInvSuppMaster->supplierID;
                $newVat['payeeCode'] = isset($bookInvSuppMaster->supplier->primarySupplierCode) ? $bookInvSuppMaster->supplier->primarySupplierCode : '';
                $newVat['payeeName'] = isset($bookInvSuppMaster->supplier->supplierName) ? $bookInvSuppMaster->supplier->supplierName : '';
                $newVat['currency'] = $bookInvSuppMaster->supplierTransactionCurrencyID;
                $newVat['currencyER'] = $bookInvSuppMaster->supplierTransactionCurrencyER;
                $newVat['amount'] = $bookInvSuppMaster['VATAmount'];
                $newVat['payeeDefaultCurrencyID'] = $bookInvSuppMaster->supplierTransactionCurrencyID;
                $newVat['payeeDefaultCurrencyER'] = $bookInvSuppMaster->supplierTransactionCurrencyER;
                $newVat['payeeDefaultAmount'] = $bookInvSuppMaster['VATAmount'];
                $newVat['localCurrencyID'] = $bookInvSuppMaster->localCurrencyID;
                $newVat['localCurrencyER'] = $bookInvSuppMaster->localCurrencyER;
                $newVat['localAmount'] = $bookInvSuppMaster['VATAmountLocal'];
                $newVat['rptCurrencyID'] = $bookInvSuppMaster->companyReportingCurrencyID;
                $newVat['rptCurrencyER'] = $bookInvSuppMaster->companyReportingER;
                $newVat['rptAmount'] = $bookInvSuppMaster['VATAmountRpt'];
                Taxdetail::create($newVat);
            }
        }
    }
}

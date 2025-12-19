<?php


namespace App\helper;


use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\DirectPaymentDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\Taxdetail;

class PaySupplier
{
    public static function updateMaster($id = 0)
    {

        // update master table
        $paySuppMaster = PaySupplierInvoiceMaster::with(['supplier'])->find($id);

        if(!empty($paySuppMaster) && ($paySuppMaster->invoiceType == 3)) {

            $total = DirectPaymentDetails::where('directPaymentAutoID', $paySuppMaster->PayMasterAutoId)
                ->selectRaw("SUM(netAmount) as netAmount,
                             SUM(netAmountLocal) as netAmountLocal,
                             SUM(netAmountRpt) as netAmountRpt,
                             SUM(VATAmount) as VATAmount,
                             SUM(VATAmountLocal) as VATAmountLocal,
                             SUM(VATAmountRpt) as VATAmountRpt")
                ->groupBy('directPaymentAutoID')
                ->first();

            Taxdetail::where('documentSystemCode', $id)
                ->where('documentSystemID', 4)
                ->delete();
            if(!empty($total)) {

                $convertAmount = \Helper::convertAmountToLocalRpt(203, $id, $total->VATAmount);

                $paySuppMaster['netAmount'] = \Helper::roundValue($total->netAmount);
                $paySuppMaster['netAmountLocal'] = \Helper::roundValue($total->netAmountLocal);
                $paySuppMaster['netAmountRpt'] = \Helper::roundValue($total->netAmountRpt);
                $paySuppMaster['VATAmount'] = \Helper::roundValue($total->VATAmount);
                $paySuppMaster['VATAmountBank'] = \Helper::roundValue($convertAmount['defaultAmount']);
                $paySuppMaster['VATAmountLocal'] = \Helper::roundValue($total->VATAmountLocal);
                $paySuppMaster['VATAmountRpt'] = \Helper::roundValue($total->VATAmountRpt);
                $paySuppMaster->save();

                // insert to tax details
                $newVat['companyID'] = $paySuppMaster->companyID;
                $newVat['companySystemID'] = $paySuppMaster->companySystemID;
                $newVat['documentID'] = $paySuppMaster->documentID;
                $newVat['documentSystemID'] = $paySuppMaster->documentSystemID;
                $newVat['documentSystemCode'] = $id;
                $newVat['documentCode'] = $paySuppMaster->BPVcode;
                $newVat['taxPercent'] = $paySuppMaster->VATPercentage;
                $newVat['payeeSystemCode'] = $paySuppMaster->BPVsupplierID;
                $newVat['payeeCode'] = isset($paySuppMaster->supplier->primarySupplierCode) ? $paySuppMaster->supplier->primarySupplierCode : '';
                $newVat['payeeName'] = isset($paySuppMaster->supplier->supplierName) ? $paySuppMaster->supplier->supplierName : '';
                $newVat['currency'] = $paySuppMaster->supplierTransCurrencyID;
                $newVat['currencyER'] = $paySuppMaster->supplierTransCurrencyER;
                $newVat['amount'] = $paySuppMaster['VATAmount'];
                $newVat['payeeDefaultCurrencyID'] = $paySuppMaster->supplierTransCurrencyID;
                $newVat['payeeDefaultCurrencyER'] = $paySuppMaster->supplierTransCurrencyER;
                $newVat['payeeDefaultAmount'] = $paySuppMaster['VATAmount'];
                $newVat['localCurrencyID'] = $paySuppMaster->localCurrencyID;
                $newVat['localCurrencyER'] = $paySuppMaster->localCurrencyER;
                $newVat['localAmount'] = $paySuppMaster['VATAmountLocal'];
                $newVat['rptCurrencyID'] = $paySuppMaster->companyRptCurrencyID;
                $newVat['rptCurrencyER'] = $paySuppMaster->companyRptCurrencyER;
                $newVat['rptAmount'] = $paySuppMaster['VATAmountRpt'];
                Taxdetail::create($newVat);
            } else {
                $paySuppMaster['netAmount'] = 0;
                $paySuppMaster['netAmountLocal'] = 0;
                $paySuppMaster['netAmountRpt'] = 0;
                $paySuppMaster['VATAmount'] = 0;
                $paySuppMaster['VATAmountBank'] = 0;
                $paySuppMaster['VATAmountLocal'] = 0;
                $paySuppMaster['VATAmountRpt'] = 0;
                $paySuppMaster->save();
            }
        }
    }
}

<?php


namespace App\helper;


use App\Models\Company;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\SupplierAssigned;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use Illuminate\Support\Facades\DB;

class TaxService
{

    public static function checkPOVATEligible($supplierVATEligible = 0,$vatRegisteredYN = 0) {
        //$vatRegisteredYN == 1 &
        if($supplierVATEligible == 1 ){
            return true;
        }
        return false;
    }

    public static function checkGRVVATEligible($companySystemID = 0,$supplierID = 0){

        $valEligible = false;
        $company = Company::where('companySystemID', $companySystemID)->first();
        $supplierAssignedDetail = SupplierAssigned::where('supplierCodeSytem', $supplierID)
            ->where('companySystemID', $companySystemID)
            ->first();

        if (!empty($company) && !empty($supplierAssignedDetail) && $company->vatRegisteredYN == 1 && $supplierAssignedDetail->vatEligible == 1) {
            $valEligible = true;
        }
        return $valEligible;
    }

    public  static function getInputVATTransferGLAccount($companySystemID = 0){

        return Tax::where('companySystemID',$companySystemID)
                //->where('isActive',1)
                ->where('taxCategory',2)
                ->whereNotNull('inputVatTransferGLAccountAutoID')
                ->first();
    }

    public  static function getInputVATGLAccount($companySystemID = 0){

        return Tax::where('companySystemID',$companySystemID)
            //->where('isActive',1)
            ->where('taxCategory',2)
            ->whereNotNull('inputVatGLAccountAutoID')
            ->first();
    }

    public  static function getOutputVATGLAccount($companySystemID = 0){

        return Tax::where('companySystemID',$companySystemID)
            //->where('isActive',1)
            ->where('taxCategory',2)
            ->whereNotNull('outputVatGLAccountAutoID')
            ->first();
    }

    public static function getVATDetailsByItem($companySystemID = 0 ,$itemCode = 0,$supplierID=0) {

        $data = array('applicableOn' => 2,'percentage' => 0);
        $taxDetails = TaxVatCategories::whereHas('tax',function($q) use($companySystemID){
                $q->where('companySystemID',$companySystemID)
                    ->where('isActive',1)
                    ->where('taxCategory',2);
            })
            ->whereHas('main',function ($q){
                $q->where('isActive',1);
            })
            ->whereHas('items',function ($q) use($itemCode){
                $q->where('itemCodeSystem',$itemCode);
            })
            ->where('isActive',1)
            ->first();

        if(!empty($taxDetails)){
            $data['applicableOn'] = $taxDetails->applicableOn; // 1 - Gross , 2 - Net
            $data['percentage']   = $taxDetails->percentage;
        }else{
            $supplier = SupplierAssigned::where('companySystemID',$companySystemID)
                ->where('supplierCodeSytem',$supplierID)
                ->first();

            if(!empty($supplier)){
                $data['percentage']   = $supplier->vatPercentage;
            }
        }

        return $data;
    }

    public static function updatePOVAT($id) {

        $purchaseOrder = ProcumentOrder::find($id);

        if(empty($purchaseOrder)){
            return false;
        }

        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum,COALESCE(SUM(VATAmount * noQty),0) as totalVAT'),'budgetYear')
            ->where('purchaseOrderMasterID', $id)
            ->first();
        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        //if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
        $poVATPercentage = 0;
        if($poMasterSum && $purchaseOrder->isVatEligible){
            // $calculateVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);
            $calculateVatAmount = $poMasterSum['totalVAT'];
            if($poMasterSum['masterTotalSum'] > 0){
                $poVATPercentage = ($poMasterSum['totalVAT']/($poMasterSum['masterTotalSum'] - $poMasterSum['totalVAT'])) * 100;
            }

            $currencyConversionVatAmount = Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateVatAmount);

            ProcumentOrder::find($id)
                ->update([
                    'VATPercentage' => round($poVATPercentage,2),
                    'VATAmount' =>  Helper::roundValue($calculateVatAmount),
                    'VATAmountLocal' => Helper::roundValue($currencyConversionVatAmount['localAmount']),
                    'VATAmountRpt' => Helper::roundValue($currencyConversionVatAmount['reportingAmount'])
                ]);
        }else{
            ProcumentOrder::find($id)
                ->update([
                    'VATPercentage' => 0,
                    'VATAmount' => 0,
                    'VATAmountLocal' => 0,
                    'VATAmountRpt' => 0
                ]);
        }
        ProcumentOrder::find($id)->update(['budgetYear' => $poMasterSum['budgetYear']]);
        return true;
    }
}

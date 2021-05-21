<?php


namespace App\helper;


use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\GRVDetails;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\BookInvSuppDet;
use App\Models\SupplierAssigned;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use Illuminate\Support\Facades\DB;

class TaxService
{

    public static function checkPOVATEligible($supplierVATEligible = 0,$vatRegisteredYN = 0,$documentSystemID = 0) {
        //$vatRegisteredYN == 1 &
        if(($supplierVATEligible == 1  || $vatRegisteredYN == 1 )&& $documentSystemID != 67){ // 67 Quotation
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

        if ((!empty($company) && $company->vatRegisteredYN == 1) || (!empty($supplierAssignedDetail) && $supplierAssignedDetail->vatEligible == 1)) {
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

    public  static function getOutputVATTransferGLAccount($companySystemID = 0){

        return Tax::where('companySystemID',$companySystemID)
            //->where('isActive',1)
            ->where('taxCategory',2)
            ->whereNotNull('outputVatTransferGLAccountAutoID')
            ->first();
    }

    public static function checkCompanyVATEligible($companySystemID = 0) {

        $vatConfig = Tax::where('companySystemID',$companySystemID)
                            ->where('isActive',1)
                            ->where('taxCategory',2)
                            ->count();

        if($vatConfig == 1 ){
            return true;
        }
        return false;
    }

    public static function getVATDetailsByItem($companySystemID = 0 ,$itemCode = 0,$partyID=0 , $isSupplier = 1) {

        $data = array('applicableOn' => 2,'percentage' => 0, 'vatSubCategoryID' => null, 'vatMasterCategoryID' => null);
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
            $data['vatSubCategoryID']   = $taxDetails->taxVatSubCategoriesAutoID;
            $data['vatMasterCategoryID']   = $taxDetails->mainCategory;
        }else{
            $defaultVAT = TaxVatCategories::where('isDefault', 1)
                                          ->first();

            if ($defaultVAT) {
                $data['vatSubCategoryID']   = $defaultVAT->taxVatSubCategoriesAutoID;
                $data['vatMasterCategoryID']   = $defaultVAT->mainCategory;
                $data['percentage']   = $defaultVAT->percentage;
            } else {
                 if($isSupplier){
                    $supplier = SupplierAssigned::where('companySystemID',$companySystemID)
                        ->where('supplierCodeSytem',$partyID)
                        ->first();

                    if(!empty($supplier)){
                        $data['percentage']   = $supplier->vatPercentage;
                    }
                }else{
                    $customer = CustomerAssigned::where('companySystemID',$companySystemID)
                        ->where('customerCodeSystem',$partyID)
                        ->first();

                    if(!empty($customer)){
                        $data['percentage']   = $customer->vatPercentage;
                    }
                }
            }

        }

        return $data;
    }

    public static function getDefaultVAT($companySystemID = 0, $partyID = 0, $isSupplier = 1) {

        $data = array('percentage' => 0, 'vatSubCategoryID' => null, 'vatMasterCategoryID' => null);
        $taxDetails = TaxVatCategories::whereHas('tax',function($q) use($companySystemID){
                                        $q->where('companySystemID',$companySystemID)
                                            ->where('isActive',1)
                                            ->where('taxCategory',2);
                                    })
                                    ->where('isDefault', 1)
                                    ->first();

        if(!empty($taxDetails)){
            $data['percentage']   = $taxDetails->percentage;
            $data['vatSubCategoryID']   = $taxDetails->taxVatSubCategoriesAutoID;
            $data['vatMasterCategoryID']   = $taxDetails->mainCategory;
        }else{
            if ($isSupplier) {
                $supplier = SupplierAssigned::where('companySystemID',$companySystemID)
                    ->where('supplierCodeSytem',$partyID)
                    ->first();

                if(!empty($supplier)){
                    $data['percentage']   = $supplier->vatPercentage;
                }
            } else {
                $customer = CustomerAssigned::where('companySystemID',$companySystemID)
                    ->where('customerCodeSystem',$partyID)
                    ->first();

                if(!empty($customer)){
                    $data['percentage']   = $customer->vatPercentage;
                }
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
        if($poMasterSum && ($purchaseOrder->isVatEligible || $purchaseOrder->rcmActivated)){
            // $calculateVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);
            $calculateVatAmount = $poMasterSum['totalVAT'];
            if($poMasterSum['masterTotalSum'] > 0){
                $poVATPercentage = ($poMasterSum['totalVAT']/($poMasterSum['masterTotalSum'])) * 100;
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

        if (!is_null($poMasterSum['budgetYear'])) {
            ProcumentOrder::find($id)->update(['budgetYear' => $poMasterSum['budgetYear']]);
        }
        return true;
    }

    public static function getRCMAvailable($companySystemID,$supplierID) {

        if(!Helper::isLocalSupplier($supplierID, $companySystemID)){
            $company = Company::find($companySystemID);

            if(!empty($company) && $company->vatRegisteredYN == 1){
                return true;
            }
        }

        return false;
    }

    public static function getRCMAvailability($isLocalSupplier,$vatRegisteredYN) {

        if(!$isLocalSupplier && $vatRegisteredYN == 1){
            return true;
        }

        return false;
    }

    public static function isGRVRCMActivation($id = 0){

        return GRVDetails::where('grvAutoID',$id)
                           ->whereHas('po_master',function ($q){
                               $q->where('rcmActivated',1);
                           })
                          ->exists();
    }

    public static function isSupplierInvoiceRcmActivated($id = 0){

        return BookInvSuppDet::where('bookingSuppMasInvAutoID',$id)
                           ->whereHas('pomaster',function ($q){
                               $q->where('rcmActivated',1);
                           })
                          ->exists();
    }
}

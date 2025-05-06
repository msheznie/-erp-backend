<?php


namespace App\helper;


use App\Models\AssetDisposalDetail;
use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Models\BookInvSuppMaster;
use App\Models\Taxdetail;
use App\Models\GRVMaster;
use App\Models\SupplierInvoiceDirectItem;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\PurchaseReturn;
use App\Models\BookInvSuppDet;
use App\Models\SupplierAssigned;
use App\Models\PaySupplierInvoiceDetail;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Scalar\MagicConst\Dir;

class TaxService
{


    public static function checkPOVATEligible($supplierVATEligible = 0, $vatRegisteredYN = 0, $documentSystemID = 0)
    {
        //$vatRegisteredYN == 1 &
        if (($supplierVATEligible == 1 || $vatRegisteredYN == 1) && $documentSystemID != 67) { // 67 Quotation
            return true;
        }
        return false;
    }

    public static function checkGRVVATEligible($companySystemID = 0, $supplierID = 0)
    {

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

    public static function getInputVATTransferGLAccount($companySystemID = 0)
    {

        return Tax::where('companySystemID', $companySystemID)
            //->where('isActive',1)
            ->where('taxCategory', 2)
            ->whereNotNull('inputVatTransferGLAccountAutoID')
            ->first();
    }

    public static function getInputVATGLAccount($companySystemID = 0)
    {

        return Tax::where('companySystemID', $companySystemID)
            //->where('isActive',1)
            ->where('taxCategory', 2)
            ->whereNotNull('inputVatGLAccountAutoID')
            ->first();
    }

    public static function getOutputVATGLAccount($companySystemID = 0)
    {

        return Tax::where('companySystemID', $companySystemID)
            //->where('isActive',1)
            ->where('taxCategory', 2)
            ->whereNotNull('outputVatGLAccountAutoID')
            ->first();
    }

    public static function getOutputVATTransferGLAccount($companySystemID = 0)
    {

        return Tax::where('companySystemID', $companySystemID)
            //->where('isActive',1)
            ->where('taxCategory', 2)
            ->whereNotNull('outputVatTransferGLAccountAutoID')
            ->first();
    }

    public static function checkCompanyVATEligible($companySystemID = 0)
    {

        $vatConfig = Tax::where('companySystemID', $companySystemID)
            ->where('isActive', 1)
            ->where('taxCategory', 2)
            ->count();

        if ($vatConfig == 1) {
            return true;
        }
        return false;
    }

    public static function getVATDetailsByItem($companySystemID = 0, $itemCode = 0, $partyID = 0, $isSupplier = 1)
    {

        $data = array('applicableOn' => 2, 'percentage' => 0, 'vatSubCategoryID' => null, 'vatMasterCategoryID' => null);
        $taxDetails = TaxVatCategories::whereHas('tax', function ($q) use ($companySystemID) {
            $q->where('companySystemID', $companySystemID)
                ->where('isActive', 1)
                ->where('taxCategory', 2);
            })
            ->whereHas('main', function ($q) {
                $q->where('isActive', 1);
            })
            ->whereHas('items', function ($q) use ($itemCode) {
                $q->where('itemCodeSystem', $itemCode);
            })
            ->where('isActive', 1)
            ->first();

        if (!empty($taxDetails)) {
            $data['applicableOn'] = $taxDetails->applicableOn; // 1 - Gross , 2 - Net
            $data['percentage'] = $taxDetails->percentage;
            $data['vatSubCategoryID'] = $taxDetails->taxVatSubCategoriesAutoID;
            $data['vatMasterCategoryID'] = $taxDetails->mainCategory;
            $data['subCatgeoryType'] = $taxDetails->subCatgeoryType;
        } else {
            $defaultVAT = TaxVatCategories::whereHas('tax', function ($q) use ($companySystemID) {
                    $q->where('companySystemID', $companySystemID)
                        ->where('isActive', 1)
                        ->where('taxCategory', 2);
                })
                ->whereHas('main', function ($q) {
                    $q->where('isActive', 1);
                })
                ->where('isActive', 1)
                ->where('isDefault', 1)
                ->first();

            if ($defaultVAT) {
                $data['applicableOn'] = $defaultVAT->applicableOn;
                $data['vatSubCategoryID'] = $defaultVAT->taxVatSubCategoriesAutoID;
                $data['vatMasterCategoryID'] = $defaultVAT->mainCategory;
                $data['subCatgeoryType'] = $defaultVAT->subCatgeoryType;
                $data['percentage'] = $defaultVAT->percentage;
            } else {
                if ($isSupplier) {
                    $supplier = SupplierAssigned::where('companySystemID', $companySystemID)
                        ->where('supplierCodeSytem', $partyID)
                        ->first();

                    if (!empty($supplier)) {
                        $data['percentage'] = $supplier->vatPercentage;
                    }
                } else {
                    $customer = CustomerAssigned::where('companySystemID', $companySystemID)
                        ->where('customerCodeSystem', $partyID)
                        ->first();

                    if (!empty($customer)) {
                        $data['percentage'] = $customer->vatPercentage;
                    }
                }
            }
        }

        return $data;
    }

    public static function validateVatCategoriesInDocumentDetails($documentSystemID, $companySystemID, $documentDetailID, $updateData, $supplierID = 0, $documentType = 0)
    {
        $vatMasterCategoryID = null;
        $vatSubCategoryID = null;
        switch ($documentSystemID) {
            case 2:
            case 5:
            case 52:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                        $vatDetails = self::getVATDetailsByItem($companySystemID, $updateData['itemCode']);

                        if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                            return ['status' => false, 'message' => "Please assign a vat category to this item (or) setup a default vat category"];
                        }

                        $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                        $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                    }
                }      
                PurchaseOrderDetails::where('purchaseOrderDetailsID', $documentDetailID)->update(['vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID]);

                break;
            case 3:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                        $vatDetails = self::getVATDetailsByItem($companySystemID, $updateData['itemCode']);
                        if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                            return ['status' => false, 'message' => "Please assign a vat category to this item (or) setup a default vat category"];
                        }

                        $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                        $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                    }
                }      
                GRVDetails::where('grvDetailsID', $documentDetailID)->update(['vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID]);

                break;
            case 68:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                        $vatDetails = self::getVATDetailsByItem($companySystemID, $updateData['itemAutoID']);

                        if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                            return ['status' => false, 'message' => "Please assign a vat category to this item (or) setup a default vat category"];
                        }

                        $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                        $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                    }
                }      
                break;
             case 71:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                        $vatDetails = self::getVATDetailsByItem($companySystemID, $updateData['itemCodeSystem']);

                        if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                            return ['status' => false, 'message' => "Please assign a vat category to this item (or) setup a default vat category"];
                        }

                        $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                        $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                    }
                }      
                break;
            case 11:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($documentType == 3 || $documentType == 1 || $documentType == 4) {
                        if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                            $vatDetails = ($documentType == 3) ?  self::getVATDetailsByItem($companySystemID, $updateData['itemCode']) : self::getDefaultVAT($companySystemID, $supplierID);

                            if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                                return ['status' => false, 'message' => "Please assign a vat category to this line (or) setup a default vat category"];
                            }

                            $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                            $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                        }
                    }
                }

                if ($documentType == 3) {
                      SupplierInvoiceDirectItem::where('id', $documentDetailID)->update(['vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID]);
                }      
                break;
            case 20:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($documentType == 2 || $documentType == 0) {
                        if ($updateData['VATAmount'] > 0 || $updateData['VATPercentage'] > 0) {
                            $vatDetails = ($documentType == 2) ?  self::getVATDetailsByItem($companySystemID, $updateData['itemCodeSystem']) : self::getDefaultVAT($companySystemID, $supplierID);

                            if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                                return ['status' => false, 'message' => "Please assign a vat category to this line (or) setup a default vat category"];
                            }

                            $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                            $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                        }
                    }
                }
                break;
            case 41:
                if (!is_null($updateData['vatSubCategoryID']) && $updateData['vatSubCategoryID'] > 0) {
                    $vatMasterCategoryID = $updateData['vatMasterCategoryID'];
                    $vatSubCategoryID = $updateData['vatSubCategoryID'];
                } else {
                    if ($updateData['vatAmount'] > 0 || $updateData['vatPercentage'] > 0) {
                        $vatDetails = self::getVATDetailsByItem($companySystemID, $updateData['itemCode']);

                        if (is_null($vatDetails['vatMasterCategoryID']) || is_null($vatDetails['vatSubCategoryID'])) {
                            return ['status' => false, 'message' => "Please assign a vat category to this item (or) setup a default vat category"];
                        }

                        $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
                        $vatSubCategoryID = $vatDetails['vatSubCategoryID'];

                    }
                }
                AssetDisposalDetail::where('assetDisposalDetailAutoID', $documentDetailID)->update(['vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID]);

                break;
                // if ($documentType == 2) {
                //       SupplierInvoiceDirectItem::where('id', $documentDetailID)->update(['vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID]);
                // }      
                break;
            default:
                // code...
                break;
        }

        return ['status' => true, 'vatMasterCategoryID' => $vatMasterCategoryID, 'vatSubCategoryID' => $vatSubCategoryID];

    }

    public static function getDefaultVAT($companySystemID = 0, $partyID = 0, $isSupplier = 1)
    {

        $data = array('percentage' => 0, 'vatSubCategoryID' => null, 'vatMasterCategoryID' => null);
        $taxDetails = TaxVatCategories::whereHas('tax', function ($q) use ($companySystemID) {
            $q->where('companySystemID', $companySystemID)
                ->where('isActive', 1)
                ->where('taxCategory', 2);
             })
            ->whereHas('main', function ($q) {
                $q->where('isActive', 1);
            })
            ->where('isActive', 1)
            ->where('isDefault', 1)
            ->first();

        if (!empty($taxDetails)) {
            $data['percentage'] = $taxDetails->percentage;
            $data['vatSubCategoryID'] = $taxDetails->taxVatSubCategoriesAutoID;
            $data['vatMasterCategoryID'] = $taxDetails->mainCategory;
        }
        /*else {
            if ($isSupplier) {
                $supplier = SupplierAssigned::where('companySystemID', $companySystemID)
                    ->where('supplierCodeSytem', $partyID)
                    ->first();

                if (!empty($supplier)) {
                    $data['percentage'] = $supplier->vatPercentage;
                }
            } else {
                $customer = CustomerAssigned::where('companySystemID', $companySystemID)
                    ->where('customerCodeSystem', $partyID)
                    ->first();

                if (!empty($customer)) {
                    $data['percentage'] = $customer->vatPercentage;
                }
            }
        }*/

        return $data;
    }

    public static function updatePOVAT($id)
    {

        $purchaseOrder = ProcumentOrder::find($id);

        if (empty($purchaseOrder)) {
            return false;
        }

        $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum,COALESCE(SUM(VATAmount * noQty),0) as totalVAT'), 'budgetYear')
            ->where('purchaseOrderMasterID', $id)
            ->first();
        //if($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1 && $purchaseOrder->vatRegisteredYN == 0){
        //if ($purchaseOrder->VATPercentage > 0 && $purchaseOrder->supplierVATEligible == 1) {
        $poVATPercentage = 0;
        if ($poMasterSum && ($purchaseOrder->isVatEligible || $purchaseOrder->rcmActivated)) {
            // $calculateVatAmount = ($poMasterSum['masterTotalSum'] - $purchaseOrder->poDiscountAmount) * ($purchaseOrder->VATPercentage / 100);
            $calculateVatAmount = $poMasterSum['totalVAT'];
            if ($poMasterSum['masterTotalSum'] > 0) {
                $poVATPercentage = ($poMasterSum['totalVAT'] / ($poMasterSum['masterTotalSum'])) * 100;
            }

            $currencyConversionVatAmount = Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $calculateVatAmount);

            ProcumentOrder::find($id)
                ->update([
                    'VATPercentage' => round($poVATPercentage, 2),
                    'VATAmount' => Helper::roundValue($calculateVatAmount),
                    'VATAmountLocal' => Helper::roundValue($currencyConversionVatAmount['localAmount']),
                    'VATAmountRpt' => Helper::roundValue($currencyConversionVatAmount['reportingAmount'])
                ]);
        } else {
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

    public static function getRCMAvailable($companySystemID, $supplierID)
    {

        if (!Helper::isLocalSupplier($supplierID, $companySystemID)) {
            $company = Company::find($companySystemID);

            if (!empty($company) && $company->vatRegisteredYN == 1) {
                return true;
            }
        }

        return false;
    }

    public static function getRCMAvailability($isLocalSupplier, $vatRegisteredYN)
    {

        if (!$isLocalSupplier && $vatRegisteredYN == 1) {
            return true;
        }

        return false;
    }

    public static function isGRVRCMActivation($id = 0)
    {

        return GRVDetails::where('grvAutoID', $id)
            ->whereHas('po_master', function ($q) {
                $q->where('rcmActivated', 1);
            })
            ->exists();
    }

    public static function isPRNRCMActivation($purhaseReturnAutoID = 0)
    {

        $purchaseReturnDetail = PurchaseReturnDetails::where('purhaseReturnAutoID', $purhaseReturnAutoID)->first();

        $rcm = false;

        if ($purchaseReturnDetail) {
            $rcm = self::isGRVRCMActivation($purchaseReturnDetail->grvAutoID);
        }

        return $rcm;
    }

    public static function isSupplierInvoiceRcmActivated($id = 0)
    {

        return BookInvSuppDet::where('bookingSuppMasInvAutoID', $id)
            ->whereHas('pomaster', function ($q) {
                $q->where('rcmActivated', 1);
            })
            ->exists();
    }

    public static function poLogisticVATDistributionForGRV($grvId = 0, $isPoWise = 1,$supplierID = 0 )
    {
        $output = array(
            'vatOnPOTotalAmountTrans' => 0,
            'vatOnPOTotalAmountLocal' => 0,
            'vatOnPOTotalAmountRpt' => 0
        );

        $unbilledGRVVATAddVatOnPO = PoAdvancePayment::selectRaw("erp_purchaseorderadvpayment.grvAutoID, 
                                                                erp_purchaseorderadvpayment.poID,
                                                                erp_purchaseorderadvpayment.VATPercentage,
                                                                erp_purchaseorderadvpayment.reqAmount,
                                                                erp_purchaseorderadvpayment.VATAmount,
                                                                erp_purchaseorderadvpayment.VATAmountLocal,
                                                                erp_purchaseorderadvpayment.reqAmountInPOLocalCur,
                                                                erp_purchaseorderadvpayment.VATAmountRpt,
                                                                erp_purchaseorderadvpayment.reqAmountInPORptCur,
                                                                erp_purchaseorderadvpayment.addVatOnPO")
            ->leftJoin('erp_grvmaster', 'erp_purchaseorderadvpayment.grvAutoID', '=', 'erp_grvmaster.grvAutoID')
            ->leftJoin('erp_purchaseordermaster', 'erp_purchaseorderadvpayment.poID', '=', 'erp_purchaseordermaster.purchaseOrderID')
            ->where('erp_purchaseorderadvpayment.grvAutoID', $grvId);

            if($supplierID){
               $unbilledGRVVATAddVatOnPO = $unbilledGRVVATAddVatOnPO->where('erp_purchaseorderadvpayment.supplierID',$supplierID)
                                                                    ->get();
            }else{
                $unbilledGRVVATAddVatOnPO = $unbilledGRVVATAddVatOnPO->get();
            }

        Log::info('poLogisticVATDistributionForGRV function count:' . count($unbilledGRVVATAddVatOnPO));

        foreach ($unbilledGRVVATAddVatOnPO as $advPayment) {

            $data = self::poLogisticForLineWise($advPayment);
            // PO Total VAT Amount trans currency
            $output['vatOnPOTotalAmountTrans'] = $output['vatOnPOTotalAmountTrans']  + $data['vatOnPOTotalAmountTrans'] ;

            // PO Total VAT Amount Local currency
            $output['vatOnPOTotalAmountLocal'] =  $output['vatOnPOTotalAmountLocal'] + $data['vatOnPOTotalAmountLocal'] ;

            // PO Total VAT Amount Rpt currency
            $output['vatOnPOTotalAmountRpt']   =   $output['vatOnPOTotalAmountRpt'] + $data['vatOnPOTotalAmountRpt'] ;

        }

        return $output;
    }

    public static function logisticVATAmountCalculationForGRV($VATAmount = 0 ,$reqAmount = 0,$VATPercentage = 0,$percentage = 0){
        $vatOnPOTotalAmount =  $VATAmount - ($reqAmount * ($VATPercentage/100)) ;
        return $vatOnPOTotalAmount * $percentage + ($VATAmount - $vatOnPOTotalAmount);
    }

    public static function  poLogisticForLineWise($advPayment){
        $output = array(
            'vatOnPOTotalAmountTrans' => 0,
            'vatOnPOTotalAmountLocal' => 0,
            'vatOnPOTotalAmountRpt' => 0
        );

        if($advPayment->addVatOnPO){
                $totalPoAmount = PurchaseOrderDetails::selectRaw("SUM(GRVcostPerUnitSupTransCur * noQty) as totalPOAmount")
                    ->where('purchaseOrderMasterID', $advPayment->poID)
                    ->first();

                $totalGRVAmountByPO = GRVDetails::selectRaw("SUM(GRVcostPerUnitSupTransCur * noQty) as totalGRVAmount")
                    ->where('grvAutoID', $advPayment->grvAutoID)
                    ->where('purchaseOrderMastertID', $advPayment->poID)
                    ->first();

                $percentage = 0;
                if (!empty($totalGRVAmountByPO) && !empty($totalPoAmount) && $totalPoAmount->totalPOAmount != 0) {
                    $percentage = ($totalGRVAmountByPO->totalGRVAmount / $totalPoAmount->totalPOAmount);
                }

            // PO Total VAT Amount trans currency
            $output['vatOnPOTotalAmountTrans'] =  self::logisticVATAmountCalculationForGRV($advPayment->VATAmount, $advPayment->reqAmount, $advPayment->VATPercentage, $percentage);

            // PO Total VAT Amount Local currency
            $output['vatOnPOTotalAmountLocal'] =  self::logisticVATAmountCalculationForGRV($advPayment->VATAmountLocal, $advPayment->reqAmountInPOLocalCur, $advPayment->VATPercentage, $percentage);

            // PO Total VAT Amount Rpt currency
            $output['vatOnPOTotalAmountRpt']   =  self::logisticVATAmountCalculationForGRV($advPayment->VATAmountRpt, $advPayment->reqAmountInPORptCur, $advPayment->VATPercentage, $percentage);

        }else{
            $output['vatOnPOTotalAmountTrans'] = $advPayment->VATAmount;
            $output['vatOnPOTotalAmountLocal'] = $advPayment->VATAmountLocal;
            $output['vatOnPOTotalAmountRpt']   = $advPayment->VATAmountRpt;
        }

        return $output;
    }

    public static function processGrvExpense($grvAutoID){

        $grvVATCategories = GRVDetails::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, financeGLcodebBSSystemID,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount * noQty 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal * noQty  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt * noQty  
            ELSE 0
        END) as VATAmountRpt,
        exempt_vat_portion,
        erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('erp_grvdetails.grvAutoID', $grvAutoID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->groupBy('erp_grvdetails.grvAutoID')
            ->first();

        return $grvVATCategories;
    }

    public static function processGrvExpenseDetail($grvDetailsID){

        $grvVATCategories = GRVDetails::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, financeGLcodebBSSystemID,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt  
            ELSE 0
        END) as VATAmountRpt,
        exempt_vat_portion,
        erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('erp_grvdetails.grvDetailsID', $grvDetailsID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->first();

        return $grvVATCategories;
    }

    public static function processGrvVAT($grvAutoID)
    {
        $checkGrvVATCategories = GRVDetails::selectRaw('vatSubCategoryID, erp_tax_vat_sub_categories.subCatgeoryType as vatSubCategoryType')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                           ->where('grvAutoID', $grvAutoID)
                                                           ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
                                                           ->get();

        $exemptVAT = false;
        $vatSubCategoryTypes = collect($checkGrvVATCategories)->pluck('vatSubCategoryType')->toArray();

        if (in_array(3, $vatSubCategoryTypes)) {
            $exemptVAT = true;
        } 

        $exemptVATPortainate = GRVDetails::whereNotNull('exempt_vat_portion')
                                       ->where('exempt_vat_portion', '>', 0)
                                       ->where('grvAutoID', $grvAutoID)
                                       ->get();

        $exemptVATPortainateFlag = (count($exemptVATPortainate) > 0) ? true : false;
        
        $bsVATData = [];
        $plVATData = [];
        $exemptVATportionBs = [];
        $exemptVATportionPL = [];
        $vatData = [
            'masterVATTrans' => 0,
            'masterVATRpt' => 0,
            'masterVATLocal' => 0,
            'bsVAT' => $bsVATData,
            'exemptVATTrans' => 0,
            'exemptVATRpt' => 0,
            'exemptVATLocal' => 0,
            'exemptVATportionBs' => $exemptVATportionBs,
            'exemptVATportionPL' => $exemptVATportionPL,
            'plVAT' => $plVATData
        ];

        if (!$exemptVAT && !$exemptVATPortainateFlag) {
            $masterData = GRVMaster::with(['details' => function ($query) {
                                        $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER");
                                    }])->find($grvAutoID);

            if (isset($masterData->details[0])){
                $vatData['masterVATTrans'] = $masterData->details[0]->transVATAmount;
                $vatData['masterVATLocal'] = $masterData->details[0]->localVATAmount;
                $vatData['masterVATRpt'] = $masterData->details[0]->rptVATAmount;
            }

        } else {
            $masterData = GRVMaster::with(['details' => function ($query) {
                                    $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER")
                                         ->whereHas('vat_sub_category', function($query) {
                                            $query->where('subCatgeoryType', '!=', 3);
                                         })
                                         ->where('exempt_vat_portion', 0);
                                }])
                                ->whereHas('details', function($query) {
                                    $query->whereHas('vat_sub_category', function($query) {
                                        $query->where('subCatgeoryType', '!=', 3);
                                     })
                                    ->where('exempt_vat_portion', 0);
                                })->find($grvAutoID);

            $vatData['masterVATTrans'] = ($masterData) ? $masterData->details[0]->transVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->details[0]->localVATAmount : 0;
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->details[0]->rptVATAmount : 0;

            //get portainateAccounts
           $exemptPotianteData = GRVDetails::selectRaw("(VATAmount*noQty) as transVATAmount,(VATAmountLocal*noQty) as localVATAmount ,(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID, vatSubCategoryID, financeGLcodebBSSystemID, financeGLcodePLSystemID, exempt_vat_portion, grvDetailsID, includePLForGRVYN")
                                  ->where('grvAutoID', $grvAutoID)
                                  ->whereHas('vat_sub_category', function($query) {
                                    $query->where('subCatgeoryType', '!=',3);
                                  })
                                  ->where('exempt_vat_portion', '>', 0)
                                  ->get();

            
            foreach ($exemptPotianteData as $key => $value) {
                $exemptVATTransAmount = $value->transVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATTrans'] += ($value->transVATAmount - $exemptVATTransAmount);
                $vatData['exemptVATTrans'] += $exemptVATTransAmount;

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);
                $vatData['exemptVATLocal'] += $exemptVATLocalAmount;

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);
                $vatData['exemptVATRpt'] += $exemptVATRptAmount;

                if ($value->financeGLcodebBSSystemID > 0 && !is_null($value->financeGLcodebBSSystemID)) {
                    $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }

                if ($value->financeGLcodePLSystemID > 0 && !is_null($value->financeGLcodePLSystemID) && $value->includePLForGRVYN == -1) {
                    $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }
            }

            // $vatData['exemptVATportionBs'] = $exemptVATportionBs;
            // $vatData['exemptVATportionPL'] = $exemptVATportionPL;


            //get balansheet account
            $bsVAT = GRVDetails::selectRaw("SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID, vatSubCategoryID, financeGLcodebBSSystemID")
                              ->where('grvAutoID', $grvAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->whereNotNull('financeGLcodebBSSystemID')
                              ->where('financeGLcodebBSSystemID', '>', 0)
                              ->groupBy('financeGLcodebBSSystemID')
                              ->get();

             foreach ($bsVAT as $key => $value) {
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $vatData['exemptVATTrans'] += $value['transVATAmount'];
                $vatData['exemptVATLocal'] += $value['localVATAmount'];
                $vatData['exemptVATRpt'] += $value['rptVATAmount'];

                $bsVATData[$value['financeGLcodebBSSystemID']] = $temp;
            }

            $plVAT = GRVDetails::selectRaw("SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID, vatSubCategoryID, financeGLcodePLSystemID")
                              ->where('grvAutoID', $grvAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->whereNotNull('financeGLcodePLSystemID')
                              ->where('financeGLcodePLSystemID', '>', 0)
                              ->where('includePLForGRVYN', -1)
                              ->groupBy('financeGLcodePLSystemID')
                              ->get();

            foreach ($plVAT as $key => $value) {
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $vatData['exemptVATTrans'] += $value['transVATAmount'];
                $vatData['exemptVATLocal'] += $value['localVATAmount'];
                $vatData['exemptVATRpt'] += $value['rptVATAmount'];

                $plVATData[$value['financeGLcodePLSystemID']] = $temp;
            }

             // $vatData['plVAT'] = $plVATData;
             // $vatData['bsVAT'] = $bsVATData;
        }


        return $vatData;
    }


    public static function processPRVAT($purhaseReturnAutoID)
    {
        $checkPRVATCategories = PurchaseReturnDetails::selectRaw('vatSubCategoryID, erp_tax_vat_sub_categories.subCatgeoryType as vatSubCategoryType')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->join('erp_tax_vat_sub_categories', 'erp_purchasereturndetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                           ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                                           ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
                                                           ->get();

        $exemptVAT = false;
        $vatSubCategoryTypes = collect($checkPRVATCategories)->pluck('vatSubCategoryType')->toArray();

        if (in_array(3, $vatSubCategoryTypes)) {
            $exemptVAT = true;
        } 

        $exemptVATPortainate = PurchaseReturnDetails::whereNotNull('exempt_vat_portion')
                                       ->where('exempt_vat_portion', '>', 0)
                                       ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                       ->get();

        $exemptVATPortainateFlag = (count($exemptVATPortainate) > 0) ? true : false;
        
        $bsVATData = [];
        $plVATData = [];
        $exemptVATportionBs = [];
        $exemptVATportionPL = [];
        $vatData = [
            'masterVATTrans' => 0,
            'masterVATRpt' => 0,
            'masterVATLocal' => 0,
            'exemptVATTrans' => 0,
            'exemptVATRpt' => 0,
            'exemptVATLocal' => 0,
            'bsVAT' => $bsVATData,
            'exemptVATportionBs' => $exemptVATportionBs,
            'exemptVATportionPL' => $exemptVATportionPL,
            'plVAT' => $plVATData
        ];

        if (!$exemptVAT && !$exemptVATPortainateFlag) {
            $masterData = PurchaseReturn::with(['details' => function ($query) {
                            $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER");
                        }])->find($purhaseReturnAutoID);

            $vatData['masterVATTrans'] = $masterData->details[0]->transVATAmount;
            $vatData['masterVATLocal'] = $masterData->details[0]->localVATAmount;
            $vatData['masterVATRpt'] = $masterData->details[0]->rptVATAmount;

        } else {
            $masterData = PurchaseReturn::with(['details' => function ($query) {
                                    $query->selectRaw("SUM(noQty * GRVcostPerUnitLocalCur) as localAmount, SUM(noQty * GRVcostPerUnitComRptCur) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,purhaseReturnAutoID, SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount, supplierTransactionCurrencyID, supplierTransactionER, localCurrencyID, localCurrencyER, companyReportingCurrencyID, companyReportingER")
                                         ->whereHas('vat_sub_category', function($query) {
                                            $query->where('subCatgeoryType', '!=', 3);
                                         })
                                         ->where('exempt_vat_portion', 0);
                                }])
                                ->whereHas('details', function($query) {
                                    $query->whereHas('vat_sub_category', function($query) {
                                        $query->where('subCatgeoryType', '!=', 3);
                                     })
                                    ->where('exempt_vat_portion', 0);
                                })->find($purhaseReturnAutoID);

            $vatData['masterVATTrans'] = ($masterData) ? $masterData->details[0]->transVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->details[0]->localVATAmount : 0;
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->details[0]->rptVATAmount : 0;

            //get portainateAccounts
           $exemptPotianteData = PurchaseReturnDetails::selectRaw("(VATAmount*noQty) as transVATAmount,(VATAmountLocal*noQty) as localVATAmount ,(VATAmountRpt*noQty) as rptVATAmount ,purhaseReturnAutoID, vatSubCategoryID, financeGLcodebBSSystemID, exempt_vat_portion, purhasereturnDetailID")
                                  ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                                  ->whereHas('vat_sub_category', function($query) {
                                    $query->where('subCatgeoryType', '!=',3);
                                  })
                                  ->where('exempt_vat_portion', '>', 0)
                                  ->get();

            
            foreach ($exemptPotianteData as $key => $value) {
                $exemptVATTransAmount = $value->transVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATTrans'] += ($value->transVATAmount - $exemptVATTransAmount);
                $vatData['exemptVATTrans'] += $exemptVATTransAmount;

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);
                $vatData['exemptVATLocal'] += $exemptVATLocalAmount;

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);
                $vatData['exemptVATRpt'] += $exemptVATRptAmount;

                if ($value->financeGLcodebBSSystemID > 0 && !is_null($value->financeGLcodebBSSystemID)) {
                    $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }
            }

            // $vatData['exemptVATportionBs'] = $exemptVATportionBs;

            //get balansheet account
            $bsVAT = PurchaseReturnDetails::selectRaw("SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,purhaseReturnAutoID, vatSubCategoryID, financeGLcodebBSSystemID")
                              ->where('purhaseReturnAutoID', $purhaseReturnAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->whereNotNull('financeGLcodebBSSystemID')
                              ->where('financeGLcodebBSSystemID', '>', 0)
                              ->groupBy('financeGLcodebBSSystemID')
                              ->get();

             foreach ($bsVAT as $key => $value) {
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $vatData['exemptVATTrans'] += $value['transVATAmount'];
                $vatData['exemptVATLocal'] += $value['localVATAmount'];
                $vatData['exemptVATRpt'] += $value['rptVATAmount'];

                $bsVATData[$value['financeGLcodebBSSystemID']] = $temp;
            }

             // $vatData['bsVAT'] = $bsVATData;
        }


        return $vatData;
    }


    public static function processPoBasedSupllierInvoiceVAT($bookingSuppMasInvAutoID)
    {
        $detailVAT = SupplierInvoiceItemDetail::selectRaw('SUM(VATAmount) as totalVAT, SUM(VATAmountLocal) as totalVATLocal, SUM(VATAmountRpt) as totalVATRpt, vatSubCategoryID')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->when(self::isSupplierInvoiceRcmActivated($bookingSuppMasInvAutoID), function($query) {
                                                               $query->whereHas('vat_sub_category', function($query) {
                                                                    $query->where('subCatgeoryType', '!=', 3);
                                                               })
                                                               ->where('exempt_vat_portion', 0);
                                                           })
                                                           ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                                           ->where('supplierInvoAmount', '>', 0)
                                                           ->groupBy('bookingSuppMasInvAutoID')
                                                           ->first();

        $vatData = [
            'totalVAT' => 0,
            'totalVATLocal' => 0,
            'totalVATRpt' => 0,
            'exemptVAT' => 0,
            'exemptVATRpt' => 0,
            'exemptVATLocal' => 0,
        ];

        if ($detailVAT) {
             $vatData = [
                'totalVAT' => $detailVAT->totalVAT,
                'totalVATLocal' => $detailVAT->totalVATLocal,
                'totalVATRpt' => $detailVAT->totalVATRpt,
                'exemptVAT' => 0,
                'exemptVATRpt' => 0,
                'exemptVATLocal' => 0
            ];
        }

        //get portainateAccounts
       $exemptPotianteData = SupplierInvoiceItemDetail::selectRaw("VATAmount,VATAmountLocal ,VATAmountRpt ,bookingSuppMasInvAutoID, vatSubCategoryID, exempt_vat_portion")
                              ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', '!=',3);
                              })
                              ->where('exempt_vat_portion', '>', 0)
                              ->get();

        
        foreach ($exemptPotianteData as $key => $value) {
            $exemptVATTransAmount = $value->VATAmount * ($value->exempt_vat_portion/100);

            $exemptVATLocalAmount = $value->VATAmountLocal * ($value->exempt_vat_portion/100);

            $exemptVATRptAmount = $value->VATAmountRpt * ($value->exempt_vat_portion/100);

            if (self::isSupplierInvoiceRcmActivated($bookingSuppMasInvAutoID)) {
                $vatData['totalVAT'] += ($value->VATAmount - $exemptVATTransAmount);
                $vatData['totalVATLocal'] += ($value->VATAmountLocal - $exemptVATLocalAmount);
                $vatData['totalVATRpt'] += ($value->VATAmountRpt - $exemptVATRptAmount);
            }

            $vatData['exemptVAT'] += $exemptVATTransAmount;
            $vatData['exemptVATLocal'] += $exemptVATLocalAmount;
            $vatData['exemptVATRpt'] += $exemptVATRptAmount;
        }

        $detailExemptVAT = SupplierInvoiceItemDetail::selectRaw('SUM(VATAmount) as totalVAT, SUM(VATAmountLocal) as totalVATLocal, SUM(VATAmountRpt) as totalVATRpt, vatSubCategoryID')
                                                   ->whereNotNull('vatSubCategoryID')
                                                   ->where('vatSubCategoryID', '>', 0)
                                                   ->whereHas('vat_sub_category', function($query) {
                                                        $query->where('subCatgeoryType', 3);
                                                   })
                                                   ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                                   ->groupBy('bookingSuppMasInvAutoID')
                                                   ->first();

        if ($detailExemptVAT) {
            $vatData['exemptVAT'] += $detailExemptVAT->totalVAT;
            $vatData['exemptVATLocal'] += $detailExemptVAT->totalVATLocal;
            $vatData['exemptVATRpt'] += $detailExemptVAT->totalVATRpt;
        }


        return $vatData;
    }

    public static function processDirectSupplierInvoiceVAT($directInvoiceAutoID, $documentSystemID)
    {
        $checkVATCategories = DirectInvoiceDetails::selectRaw('vatSubCategoryID, erp_tax_vat_sub_categories.subCatgeoryType as vatSubCategoryType')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                           ->where('directInvoiceAutoID', $directInvoiceAutoID)
                                                           ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
                                                           ->get();

        $exemptVAT = false;
        $vatSubCategoryTypes = collect($checkVATCategories)->pluck('vatSubCategoryType')->toArray();

        if (in_array(3, $vatSubCategoryTypes)) {
            $exemptVAT = true;
        } 

        $exemptVATPortainate = DirectInvoiceDetails::whereNotNull('exempt_vat_portion')
                                       ->where('exempt_vat_portion', '>', 0)
                                       ->where('directInvoiceAutoID', $directInvoiceAutoID)
                                       ->get();

        $exemptVATPortainateFlag = (count($exemptVATPortainate) > 0) ? true : false;
        
        $bsVATData = [];
        $plVATData = [];
        $exemptVATportionBs = [];
        $exemptVATportionPL = [];
        $vatData = [
            'masterVATTrans' => 0,
            'masterVATRpt' => 0,
            'masterVATLocal' => 0,
            'bsVAT' => $bsVATData,
            'exemptVATportionBs' => $exemptVATportionBs
        ];

        if (!$exemptVAT && !$exemptVATPortainateFlag) {
            $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                                        ->WHERE('documentSystemCode', $directInvoiceAutoID)
                                        ->WHERE('documentSystemID', $documentSystemID)
                                        ->groupBy('documentSystemCode')
                                        ->first();

            if ($tax) {
                $vatData['masterVATLocal'] = $tax->localAmount;
                $vatData['masterVATRpt'] = $tax->rptAmount;
                $vatData['masterVATTrans'] = $tax->transAmount;
            }
        } else {
            $masterData = DirectInvoiceDetails::selectRaw('SUM(VATAmount) as totalVATAmount, SUM(VATAmountLocal) as totalVATAmountLocal, SUM(VATAmountRpt) as totalVATAmountRpt, vatSubCategoryID')
                                            ->whereHas('vat_sub_category', function($query) {
                                                $query->where('subCatgeoryType', '!=', 3);
                                             })
                                            ->where('exempt_vat_portion', 0)
                                            ->where('directInvoiceAutoID', $directInvoiceAutoID)
                                            ->groupBy('directInvoiceAutoID')
                                            ->first();

            $vatData['masterVATTrans'] = ($masterData) ? $masterData->totalVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->totalVATAmountLocal : 0;
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->totalVATAmountRpt : 0;

            //get portainateAccounts
           $exemptPotianteData = DirectInvoiceDetails::selectRaw("(VATAmount) as transVATAmount,(VATAmountLocal) as localVATAmount ,(VATAmountRpt) as rptVATAmount ,directInvoiceAutoID, vatSubCategoryID, chartOfAccountSystemID, serviceLineSystemID, comments, exempt_vat_portion, directInvoiceDetailsID")
                                  ->where('directInvoiceAutoID', $directInvoiceAutoID)
                                  ->whereHas('vat_sub_category', function($query) {
                                    $query->where('subCatgeoryType', '!=',3);
                                  })
                                  ->where('exempt_vat_portion', '>', 0)
                                  ->get();

            
            foreach ($exemptPotianteData as $key => $value) {
                $exemptVATTransAmount = $value->transVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATTrans'] += ($value->transVATAmount - $exemptVATTransAmount);

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);

                if ($value->chartOfAccountSystemID > 0 && !is_null($value->chartOfAccountSystemID)) {
                    $serviceLineSystemID = (!is_null($value->serviceLineSystemID)) ? $value->serviceLineSystemID : "";
                    $comment = (!is_null($value->comments)) ? $value->comments : "";

                    $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATTransAmount'] = ((isset($exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATTransAmount'])) ? $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATLocalAmount'] = ((isset($exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATLocalAmount'])) ? $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATRptAmount'] = ((isset($exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATRptAmount'])) ? $exemptVATportionBs[$value->chartOfAccountSystemID.$serviceLineSystemID.$comment]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }
            }

            $vatData['exemptVATportionBs'] = $exemptVATportionBs;


            //get balansheet account
            $bsVAT = DirectInvoiceDetails::selectRaw("SUM(VATAmount) as transVATAmount,SUM(VATAmountLocal) as localVATAmount ,SUM(VATAmountRpt) as rptVATAmount ,directInvoiceAutoID, vatSubCategoryID, chartOfAccountSystemID, serviceLineSystemID, comments")
                              ->where('directInvoiceAutoID', $directInvoiceAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->groupBy('chartOfAccountSystemID', 'serviceLineSystemID', 'comments')
                              ->get();

             foreach ($bsVAT as $key => $value) {
                $serviceLineSystemID = (!is_null($value->serviceLineSystemID)) ? $value->serviceLineSystemID : "";
                $comment = (!is_null($value->comments)) ? $value->comments : "";
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $bsVATData[$value['chartOfAccountSystemID'].$serviceLineSystemID.$comment] = $temp;
            }
            
            $vatData['bsVAT'] = $bsVATData;
        }


        return $vatData;
    }

    public static function processGRVVATForUnbilled($grvData)
    {
        $grvAutoID = is_array($grvData) ? $grvData['grvAutoID'] : $grvData->grvAutoID;
        $purchaseOrderID = is_array($grvData) ? $grvData['purchaseOrderID'] : $grvData->purchaseOrderID;

        $grvDetails = GRVDetails::where('grvAutoID', $grvAutoID)
            ->where('purchaseOrderMastertID', $purchaseOrderID)
            ->with(['vat_sub_category'])
            ->get();


        $totalTransAmount = 0;
        $totalRptAmount = 0;
        $totalLocalAmount = 0;
        $totalTransVATAmount = 0;
        $totalRptVATAmount = 0;
        $totalLocalVATAmount = 0;
        $exemptVATTrans = 0;
        $exemptVATLocal = 0;
        $exemptVATRpt = 0;

        foreach ($grvDetails as $key => $value) {
            if (isset($value->vat_sub_category->subCatgeoryType) && $value->vat_sub_category->subCatgeoryType == 1 && $value->exempt_vat_portion > 0) {
                
                $normalVAT = $value->VATAmount - ($value->VATAmount * ($value->exempt_vat_portion /100));
                $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($normalVAT * $value->noQty));
                $totalTransVATAmount += ($normalVAT * $value->noQty);

                $normalVATRpt = $value->VATAmountRpt - ($value->VATAmountRpt * ($value->exempt_vat_portion /100));
                $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($normalVATRpt * $value->noQty));
                $totalRptVATAmount += ($normalVATRpt * $value->noQty);

                $normalVATLocal = $value->VATAmountLocal - ($value->VATAmountLocal * ($value->exempt_vat_portion /100));
                $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($normalVATLocal * $value->noQty));
                $totalLocalVATAmount += ($normalVATLocal * $value->noQty);

                $exemptVATTrans += (($value->VATAmount - $normalVAT) * $value->noQty);
                $exemptVATRpt += (($value->VATAmountRpt - $normalVATRpt) * $value->noQty);
                $exemptVATLocal += (($value->VATAmountLocal - $normalVATLocal) * $value->noQty);

            } else if (isset($value->vat_sub_category->subCatgeoryType) && $value->vat_sub_category->subCatgeoryType == 3) {
                $totalTransAmount += ($value->GRVcostPerUnitSupTransCur * $value->noQty);
                $totalRptAmount += ($value->GRVcostPerUnitComRptCur * $value->noQty);
                $totalLocalAmount += ($value->GRVcostPerUnitLocalCur * $value->noQty);

                $exemptVATTrans += ($value->VATAmount * $value->noQty);
                $exemptVATRpt += ($value->VATAmountRpt * $value->noQty);
                $exemptVATLocal += ($value->VATAmountLocal * $value->noQty);
            } else {
                $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($value->VATAmount * $value->noQty));
                $totalTransVATAmount += ($value->VATAmount * $value->noQty);

                $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($value->VATAmountRpt * $value->noQty));
                $totalRptVATAmount += ($value->VATAmountRpt * $value->noQty);

                $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($value->VATAmountLocal * $value->noQty));
                $totalLocalVATAmount += ($value->VATAmountLocal * $value->noQty);
            }
        }

        return ['totalTransAmount' => $totalTransAmount, 'totalTransVATAmount' => $totalTransVATAmount, 'totalRptAmount' => $totalRptAmount, 'totalLocalAmount' => $totalLocalAmount, 'totalRptVATAmount' => $totalRptVATAmount, 'totalLocalVATAmount' => $totalLocalVATAmount, 'exemptVATTrans' => $exemptVATTrans, 'exemptVATRpt' => $exemptVATRpt, 'exemptVATLocal' => $exemptVATLocal];
    }

    public static function processGRVDetailVATForUnbilled($grvDetailsID)
    {
        $value = GRVDetails::with(['vat_sub_category'])->find($grvDetailsID);

        $totalTransAmount = 0;
        $totalRptAmount = 0;
        $totalLocalAmount = 0;
        $totalTransVATAmount = 0;
        $totalRptVATAmount = 0;
        $totalLocalVATAmount = 0;
        $exemptVATTrans = 0;
        $exemptVATLocal = 0;
        $exemptVATRpt = 0;

        $rcmActivated = self::isGRVRCMActivation($value->grvAutoID);

        if (isset($value->vat_sub_category->subCatgeoryType) && $value->vat_sub_category->subCatgeoryType == 1 && $value->exempt_vat_portion > 0) {
            
            $normalVAT = $value->VATAmount - ($value->VATAmount * ($value->exempt_vat_portion /100));
            $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($normalVAT * $value->noQty));
            $totalTransVATAmount += ($normalVAT * $value->noQty);

            $normalVATRpt = $value->VATAmountRpt - ($value->VATAmountRpt * ($value->exempt_vat_portion /100));
            $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($normalVATRpt * $value->noQty));
            $totalRptVATAmount += ($normalVATRpt * $value->noQty);

            $normalVATLocal = $value->VATAmountLocal - ($value->VATAmountLocal * ($value->exempt_vat_portion /100));
            $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($normalVATLocal * $value->noQty));
            $totalLocalVATAmount += ($normalVATLocal * $value->noQty);

            $exemptVATTrans += (($value->VATAmount - $normalVAT) * $value->noQty);
            $exemptVATRpt += (($value->VATAmountRpt - $normalVATRpt) * $value->noQty);
            $exemptVATLocal += (($value->VATAmountLocal - $normalVATLocal) * $value->noQty);

        } else if (isset($value->vat_sub_category->subCatgeoryType) && $value->vat_sub_category->subCatgeoryType == 3) {
            $totalTransAmount += ($value->GRVcostPerUnitSupTransCur * $value->noQty);
            $totalRptAmount += ($value->GRVcostPerUnitComRptCur * $value->noQty);
            $totalLocalAmount += ($value->GRVcostPerUnitLocalCur * $value->noQty);

            $exemptVATTrans += ($value->VATAmount * $value->noQty);
            $exemptVATRpt += ($value->VATAmountRpt * $value->noQty);
            $exemptVATLocal += ($value->VATAmountLocal * $value->noQty);
        } else {
            $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($value->VATAmount * $value->noQty));
            $totalTransVATAmount += ($value->VATAmount * $value->noQty);

            $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($value->VATAmountRpt * $value->noQty));
            $totalRptVATAmount += ($value->VATAmountRpt * $value->noQty);

            $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($value->VATAmountLocal * $value->noQty));
            $totalLocalVATAmount += ($value->VATAmountLocal * $value->noQty);
        }

        if ($rcmActivated) {
            $totalTransVATAmount += $exemptVATTrans;
            $totalRptVATAmount += $exemptVATRpt;
            $totalLocalVATAmount += $exemptVATLocal;
        }


        return ['totalTransAmount' => $totalTransAmount, 'totalTransVATAmount' => $totalTransVATAmount, 'totalRptAmount' => $totalRptAmount, 'totalLocalAmount' => $totalLocalAmount, 'totalRptVATAmount' => $totalRptVATAmount, 'totalLocalVATAmount' => $totalLocalVATAmount, 'exemptVATTrans' => $exemptVATTrans, 'exemptVATRpt' => $exemptVATRpt, 'exemptVATLocal' => $exemptVATLocal];
    }


    public static function processPRNVATForUnbilled($grvAutoID, $purhaseReturnAutoID)
    {
        $grvDetails = GRVDetails::selectRaw('erp_tax_vat_sub_categories.subCatgeoryType, erp_grvdetails.exempt_vat_portion, erp_grvdetails.GRVcostPerUnitSupTransCur, erp_grvdetails.VATAmount, erp_grvdetails.VATAmountRpt, erp_grvdetails.GRVcostPerUnitComRptCur, erp_grvdetails.VATAmountLocal, erp_grvdetails.GRVcostPerUnitLocalCur, erp_purchasereturndetails.noQty')
                                ->where('erp_grvdetails.grvAutoID', $grvAutoID)
                                ->join('erp_purchasereturndetails', 'erp_grvdetails.grvDetailsID', '=', 'erp_purchasereturndetails.grvDetailsID')
                                ->join('erp_tax_vat_sub_categories', 'erp_grvdetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                ->where('erp_purchasereturndetails.purhaseReturnAutoID', $purhaseReturnAutoID)
                                ->get();

        $totalTransAmount = 0;
        $totalRptAmount = 0;
        $totalLocalAmount = 0;
        $totalTransVATAmount = 0;
        $totalRptVATAmount = 0;
        $totalLocalVATAmount = 0;
        $exemptVATTrans = 0;
        $exemptVATLocal = 0;
        $exemptVATRpt = 0;

        foreach ($grvDetails as $key => $value) {
            if (isset($value->subCatgeoryType) && $value->subCatgeoryType == 1 && $value->exempt_vat_portion > 0) {
                
                $normalVAT = $value->VATAmount - ($value->VATAmount * ($value->exempt_vat_portion /100));
                $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($normalVAT * $value->noQty));
                $totalTransVATAmount += ($normalVAT * $value->noQty);

                $normalVATRpt = $value->VATAmountRpt - ($value->VATAmountRpt * ($value->exempt_vat_portion /100));
                $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($normalVATRpt * $value->noQty));
                $totalRptVATAmount += ($normalVATRpt * $value->noQty);

                $normalVATLocal = $value->VATAmountLocal - ($value->VATAmountLocal * ($value->exempt_vat_portion /100));
                $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($normalVATLocal * $value->noQty));
                $totalLocalVATAmount += ($normalVATLocal * $value->noQty);


                $exemptVATTrans += (($value->VATAmount - $normalVAT) * $value->noQty);
                $exemptVATRpt += (($value->VATAmountRpt - $normalVATRpt) * $value->noQty);
                $exemptVATLocal += (($value->VATAmountLocal - $normalVATLocal) * $value->noQty);

            } else if (isset($value->subCatgeoryType) && $value->subCatgeoryType == 3) {
                $totalTransAmount += ($value->GRVcostPerUnitSupTransCur * $value->noQty);
                $totalRptAmount += ($value->GRVcostPerUnitComRptCur * $value->noQty);
                $totalLocalAmount += ($value->GRVcostPerUnitLocalCur * $value->noQty);

                 $exemptVATTrans += ($value->VATAmount * $value->noQty);
                $exemptVATRpt += ($value->VATAmountRpt * $value->noQty);
                $exemptVATLocal += ($value->VATAmountLocal * $value->noQty);
            } else {
                $totalTransAmount += (($value->GRVcostPerUnitSupTransCur * $value->noQty) + ($value->VATAmount * $value->noQty));
                $totalTransVATAmount += ($value->VATAmount * $value->noQty);

                $totalRptAmount += (($value->GRVcostPerUnitComRptCur * $value->noQty) + ($value->VATAmountRpt * $value->noQty));
                $totalRptVATAmount += ($value->VATAmountRpt * $value->noQty);

                $totalLocalAmount += (($value->GRVcostPerUnitLocalCur * $value->noQty) + ($value->VATAmountLocal * $value->noQty));
                $totalLocalVATAmount += ($value->VATAmountLocal * $value->noQty);
            }
        }

        return ['totalTransAmount' => $totalTransAmount, 'totalTransVATAmount' => $totalTransVATAmount, 'totalRptAmount' => $totalRptAmount, 'totalLocalAmount' => $totalLocalAmount, 'totalRptVATAmount' => $totalRptVATAmount, 'totalLocalVATAmount' => $totalLocalVATAmount, 'exemptVATTrans' => $exemptVATTrans, 'exemptVATRpt' => $exemptVATRpt, 'exemptVATLocal' => $exemptVATLocal];
    }


    public static function processPRNLogisticDetails($purhaseReturnAutoID)
    {
        $purchaseReturnDetails = PurchaseReturnDetails::with(['grv_detail_master'])->where('purhaseReturnAutoID', $purhaseReturnAutoID)->get();


        $resultData = [
            'logisticTransAmount' => 0,
            'logisticLocalAmount' => 0,
            'logisticRptAmount' => 0,
            'logisticTransVATAmount' => 0,
            'logisticLocalVATAmount' => 0,
            'logisticRptVATAmount' => 0
        ];

        $grvAutoID = isset($purchaseReturnDetails[0]['grvAutoID']) ? $purchaseReturnDetails[0]['grvAutoID'] : 0;
        $grvTotalLogisticAmount = PoAdvancePayment::select(DB::raw('COALESCE(SUM(VATAmount),0) as VATAmountTotal, COALESCE(SUM(VATAmountLocal),0) as VATAmountLocalTotal, COALESCE(SUM(VATAmountRpt),0) as VATAmountRptTotal, COALESCE(SUM(reqAmountInPOTransCur),0) as transactionTotalSum, COALESCE(SUM(reqAmountInPORptCur),0) as reportingTotalSum, COALESCE(SUM(reqAmountInPOLocalCur),0) as localTotalSum'))
                ->where('grvAutoID', $grvAutoID)
                ->first();

        if (isset($grvTotalLogisticAmount['transactionTotalSum']) && $grvTotalLogisticAmount['transactionTotalSum'] > 0) {
            foreach ($purchaseReturnDetails as $key => $value) {
                $resultData['logisticTransAmount'] += (isset($value->grv_detail_master->logisticsCharges_TransCur) ? ($value->grv_detail_master->logisticsCharges_TransCur * $value->noQty) : 0);
                $resultData['logisticLocalAmount'] += (isset($value->grv_detail_master->logisticsCharges_LocalCur) ? ($value->grv_detail_master->logisticsCharges_LocalCur * $value->noQty) : 0);
                $resultData['logisticRptAmount'] += (isset($value->grv_detail_master->logisticsChargest_RptCur) ? ($value->grv_detail_master->logisticsChargest_RptCur * $value->noQty) : 0);


                $resultData['logisticTransVATAmount'] += (((floatval($value->grv_detail_master->logisticsCharges_TransCur) / $grvTotalLogisticAmount['transactionTotalSum']) * $grvTotalLogisticAmount['VATAmountTotal']) * $value->noQty);
                $resultData['logisticLocalVATAmount'] += (((floatval($value->grv_detail_master->logisticsCharges_LocalCur) / $grvTotalLogisticAmount['localTotalSum']) * $grvTotalLogisticAmount['VATAmountLocalTotal']) * $value->noQty);
                $resultData['logisticRptVATAmount'] += (((floatval($value->grv_detail_master->logisticsChargest_RptCur) / $grvTotalLogisticAmount['reportingTotalSum']) * $grvTotalLogisticAmount['VATAmountRptTotal']) * $value->noQty);
            }
        }


        return $resultData;
    }

    public static function processSIExpenseVatItemInvoice($autoID){

        $siVATCategoryDetails = SupplierInvoiceDirectItem::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, financeGLcodebBSSystemID, SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount * noQty 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal * noQty  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt * noQty  
            ELSE 0
        END) as VATAmountRpt, exempt_vat_portion, erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('bookingSuppMasInvAutoID', $autoID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->groupBy('supplier_invoice_items.bookingSuppMasInvAutoID')
            ->first();

        return $siVATCategoryDetails;
    }

    public static function processSIExpenseVatItemInvoiceDetail($autoID, $financeGLcodebBSSystemID){

        $siVATCategoryDetails = SupplierInvoiceDirectItem::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, financeGLcodebBSSystemID, SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount * noQty 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal * noQty  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt * noQty  
            ELSE 0
        END) as VATAmountRpt, exempt_vat_portion, erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('bookingSuppMasInvAutoID', $autoID)
            ->where('financeGLcodebBSSystemID', $financeGLcodebBSSystemID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->groupBy('supplier_invoice_items.bookingSuppMasInvAutoID')
            ->first();

        return $siVATCategoryDetails;
    }

    public static function processSIExpenseVatItemInvoiceDetailForPL($autoID, $financeGLcodePLSystemID){

        $siVATCategoryDetails = SupplierInvoiceDirectItem::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, financeGLcodePLSystemID, SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount * noQty 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal * noQty  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * noQty * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt * noQty  
            ELSE 0
        END) as VATAmountRpt, exempt_vat_portion, erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('bookingSuppMasInvAutoID', $autoID)
            ->where('financeGLcodePLSystemID', $financeGLcodePLSystemID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->groupBy('supplier_invoice_items.bookingSuppMasInvAutoID')
            ->first();

        return $siVATCategoryDetails;
    }
    public static function processSIExemptVatDirectInvoice($directInvoiceAutoID, $groupByServiceLine = false){

        $siVATCategoryDetails = DirectInvoiceDetails::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmount * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmount 
            ELSE 0
        END) as VATAmount,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountLocal * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountLocal  
            ELSE 0
        END) as VATAmountLocal,
        SUM(CASE 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 1 THEN VATAmountRpt * exempt_vat_portion / 100 
            WHEN erp_tax_vat_sub_categories.subCatgeoryType = 3 THEN VATAmountRpt  
            ELSE 0
        END) as VATAmountRpt, exempt_vat_portion, erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType, erp_directinvoicedetails.serviceLineSystemID, erp_directinvoicedetails.serviceLineCode')
            ->whereNotNull('vatSubCategoryID')
            ->where('vatSubCategoryID', '>', 0)
            ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('erp_directinvoicedetails.directInvoiceAutoID', $directInvoiceAutoID)
            ->whereIn('erp_tax_vat_sub_categories.subCatgeoryType', [1,3])
            ->when($groupByServiceLine, function($query) {
                $query->groupBy('erp_directinvoicedetails.directInvoiceAutoID', 'erp_directinvoicedetails.serviceLineSystemID');
            })
            ->when(!$groupByServiceLine, function($query) {
                $query->groupBy('erp_directinvoicedetails.directInvoiceAutoID');
            });

        return ($groupByServiceLine) ? $siVATCategoryDetails->get() : $siVATCategoryDetails->first();
    }

    public static function checkSIExpenseVatDirectInvoice($directInvoiceAutoID, $chartOfAccountSystemID, $serviceLineSystemID){

        $siVATCategoryDetails = DirectInvoiceDetails::selectRaw('erp_tax_vat_sub_categories.expenseGL as expenseGL, erp_tax_vat_sub_categories.recordType as recordType, SUM(VATAmount) as VATAmount, SUM(VATAmountLocal) as VATAmountLocal, SUM(VATAmountRpt) as VATAmountRpt, exempt_vat_portion, erp_tax_vat_sub_categories.subCatgeoryType as subCatgeoryType')
            ->whereNotNull('vatSubCategoryID')
            ->where('vatSubCategoryID', '>', 0)
            ->join('erp_tax_vat_sub_categories', 'erp_directinvoicedetails.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
            ->where('directInvoiceAutoID', $directInvoiceAutoID)
            ->where('chartOfAccountSystemID', $chartOfAccountSystemID)
            ->where('serviceLineSystemID', $serviceLineSystemID)
            ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
            ->first();

        return $siVATCategoryDetails;
    }
    public static function processSupplierInvoiceItemsVAT($bookingSuppMasInvAutoID)
    {
        $checkVATCategories = SupplierInvoiceDirectItem::selectRaw('vatSubCategoryID, erp_tax_vat_sub_categories.subCatgeoryType as vatSubCategoryType')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->join('erp_tax_vat_sub_categories', 'supplier_invoice_items.vatSubCategoryID', '=', 'erp_tax_vat_sub_categories.taxVatSubCategoriesAutoID')
                                                           ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                                           ->groupBy('erp_tax_vat_sub_categories.subCatgeoryType')
                                                           ->get();

        $exemptVAT = false;
        $vatSubCategoryTypes = collect($checkVATCategories)->pluck('vatSubCategoryType')->toArray();

        if (in_array(3, $vatSubCategoryTypes)) {
            $exemptVAT = true;
        } 

        $exemptVATPortainate = SupplierInvoiceDirectItem::whereNotNull('exempt_vat_portion')
                                       ->where('exempt_vat_portion', '>', 0)
                                       ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                       ->get();

        $exemptVATPortainateFlag = (count($exemptVATPortainate) > 0) ? true : false;
        
        $bsVATData = [];
        $plVATData = [];
        $exemptVATportionBs = [];
        $exemptVATportionPL = [];
        $vatData = [
            'masterVATTrans' => 0,
            'masterVATRpt' => 0,
            'masterVATLocal' => 0,
            'bsVAT' => $bsVATData,
            'exemptVATTrans' => 0,
            'exemptVATRpt' => 0,
            'exemptVATLocal' => 0,
            'exemptVATportionBs' => $exemptVATportionBs,
            'exemptVATportionPL' => $exemptVATportionPL,
            'plVAT' => $plVATData
        ];

        if (!$exemptVAT && !$exemptVATPortainateFlag) {
            $masterData = BookInvSuppMaster::with(['item_details' => function ($query) {
                                        $query->selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,bookingSuppMasInvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,supplier_invoice_items.companyReportingCurrencyID,supplier_invoice_items.companyReportingER,supplier_invoice_items.localCurrencyID,supplier_invoice_items.localCurrencyER");
                                    }])->find($bookingSuppMasInvAutoID);

            $vatData['masterVATTrans'] = $masterData->item_details[0]->transVATAmount;
            $vatData['masterVATLocal'] = $masterData->item_details[0]->localVATAmount;
            $vatData['masterVATRpt'] = $masterData->item_details[0]->rptVATAmount;

        } else {
            $masterData = BookInvSuppMaster::with(['item_details' => function ($query) {
                                    $query->selectRaw("SUM(costPerUnitLocalCur*noQty) as localAmount, SUM(costPerUnitComRptCur*noQty) as rptAmount,SUM(costPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,bookingSuppMasInvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,supplier_invoice_items.companyReportingCurrencyID,supplier_invoice_items.companyReportingER,supplier_invoice_items.localCurrencyID,supplier_invoice_items.localCurrencyER")
                                         ->whereHas('vat_sub_category', function($query) {
                                            $query->where('subCatgeoryType', '!=', 3);
                                         })
                                         ->where('exempt_vat_portion', 0);
                                }])
                                ->whereHas('item_details', function($query) {
                                    $query->whereHas('vat_sub_category', function($query) {
                                        $query->where('subCatgeoryType', '!=', 3);
                                     })
                                    ->where('exempt_vat_portion', 0);
                                })->find($bookingSuppMasInvAutoID);

            $vatData['masterVATTrans'] = ($masterData) ? $masterData->item_details[0]->transVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->item_details[0]->localVATAmount : 0;
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->item_details[0]->rptVATAmount : 0;

            //get portainateAccounts
           $exemptPotianteData = SupplierInvoiceDirectItem::selectRaw("(VATAmount*noQty) as transVATAmount,(VATAmountLocal*noQty) as localVATAmount ,(VATAmountRpt*noQty) as rptVATAmount ,bookingSuppMasInvAutoID, vatSubCategoryID, financeGLcodebBSSystemID, financeGLcodePLSystemID, exempt_vat_portion, id, includePLForGRVYN")
                                  ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                  ->whereHas('vat_sub_category', function($query) {
                                    $query->where('subCatgeoryType', '!=',3);
                                  })
                                  ->where('exempt_vat_portion', '>', 0)
                                  ->get();

            
            foreach ($exemptPotianteData as $key => $value) {
                $exemptVATTransAmount = $value->transVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATTrans'] += ($value->transVATAmount - $exemptVATTransAmount);
                $vatData['exemptVATTrans'] += $exemptVATTransAmount;

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);
                $vatData['exemptVATLocal'] += $exemptVATLocalAmount;

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);
                $vatData['exemptVATRpt'] += $exemptVATRptAmount;

                if ($value->financeGLcodebBSSystemID > 0 && !is_null($value->financeGLcodebBSSystemID)) {
                    $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }

                if ($value->financeGLcodePLSystemID > 0 && !is_null($value->financeGLcodePLSystemID) && $value->includePLForGRVYN == -1) {
                    $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'])) ? $exemptVATportionPL[$value->financeGLcodePLSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }
            }


            //get balansheet account
            $bsVAT = SupplierInvoiceDirectItem::selectRaw("SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,bookingSuppMasInvAutoID, vatSubCategoryID, financeGLcodebBSSystemID")
                              ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->whereNotNull('financeGLcodebBSSystemID')
                              ->where('financeGLcodebBSSystemID', '>', 0)
                              ->groupBy('financeGLcodebBSSystemID')
                              ->get();

             foreach ($bsVAT as $key => $value) {
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $vatData['exemptVATTrans'] += $value['transVATAmount'];
                $vatData['exemptVATLocal'] += $value['localVATAmount'];
                $vatData['exemptVATRpt'] += $value['rptVATAmount'];

                $bsVATData[$value['financeGLcodebBSSystemID']] = $temp;
            }

            $plVAT = SupplierInvoiceDirectItem::selectRaw("SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,bookingSuppMasInvAutoID, vatSubCategoryID, financeGLcodePLSystemID")
                              ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                              ->whereHas('vat_sub_category', function($query) {
                                $query->where('subCatgeoryType', 3);
                              })
                              ->whereNotNull('financeGLcodePLSystemID')
                              ->where('financeGLcodePLSystemID', '>', 0)
                              ->where('includePLForGRVYN', -1)
                              ->groupBy('financeGLcodePLSystemID')
                              ->get();

            foreach ($plVAT as $key => $value) {
                $temp = [];

                $temp['transVATAmount'] = $value['transVATAmount'];
                $temp['localVATAmount'] = $value['localVATAmount'];
                $temp['rptVATAmount'] = $value['rptVATAmount'];

                $vatData['exemptVATTrans'] += $value['transVATAmount'];
                $vatData['exemptVATLocal'] += $value['localVATAmount'];
                $vatData['exemptVATRpt'] += $value['rptVATAmount'];

                $plVATData[$value['financeGLcodePLSystemID']] = $temp;
            }

        }


        return $vatData;
    }

    public static function calculateRetentionVatAmount($PayMasterAutoId)
    {
        $retentionData = PaySupplierInvoiceDetail::WHERE('PayMasterAutoId', $PayMasterAutoId)
                                                 ->WHERE('matchingDocID', 0)
                                                 ->WHERE('isRetention', 1)
                                                 ->get();

        $totalTransVATAmount = 0;
        foreach ($retentionData as $key => $value) {

            if ($retentionData) {
                $vatAmount = $value->retentionVatAmount;
            
                $totalTransVATAmount += $vatAmount;
            }
        }

        return $totalTransVATAmount;
    }

     public static function calculateRCMRetentionVatAmount($PayMasterAutoId)
    {
        $retentionData = PaySupplierInvoiceDetail::WHERE('PayMasterAutoId', $PayMasterAutoId)
                                                 ->WHERE('matchingDocID', 0)
                                                 ->WHERE('isRetention', 1)
                                                 ->get();

        $totalTransVATAmount = 0;
        foreach ($retentionData as $key => $value) {
            $bookInvSuppMaster = BookInvSuppMaster::find($value->bookingInvSystemCode);

            if ($bookInvSuppMaster) {
                if ($bookInvSuppMaster->documentType == 1 && $bookInvSuppMaster->rcmActivated == 1) {
                    $tax = Taxdetail::selectRaw("SUM(localAmount) as localAmount, SUM(rptAmount) as rptAmount,SUM(amount) as transAmount,localCurrencyID,rptCurrencyID as reportingCurrencyID,currency as supplierTransactionCurrencyID,currencyER as supplierTransactionER,rptCurrencyER as companyReportingER,localCurrencyER,payeeSystemCode")
                                            ->WHERE('documentSystemCode', $value->bookingInvSystemCode)
                                            ->WHERE('documentSystemID', 11)
                                            ->groupBy('documentSystemCode')
                                            ->first();

                    if ($tax) {
                        $vatAmount = (($tax->transAmount * ($bookInvSuppMaster->retentionPercentage / 100)) / $bookInvSuppMaster->retentionAmount) * $value->supplierPaymentAmount;

                        $totalTransVATAmount += $vatAmount;
                    }

                } else if (($bookInvSuppMaster->documentType == 0 || $bookInvSuppMaster->documentType == 2) && self::isSupplierInvoiceRcmActivated($value->bookingInvSystemCode)) {
                    $vatDetails = TaxService::processPoBasedSupllierInvoiceVAT($value->bookingInvSystemCode);

                    $vatAmount = ((($vatDetails['totalVAT'] + $vatDetails['exemptVAT']) * ($bookInvSuppMaster->retentionPercentage / 100)) / $bookInvSuppMaster->retentionAmount) * $value->supplierPaymentAmount;

                    $totalTransVATAmount += $vatAmount;
                }
            }

        }

        return $totalTransVATAmount;
    }

    public static function processMatchingVAT($matchDocumentMasterAutoID)
    {
        $supplierInvoices = PaySupplierInvoiceDetail::where('matchingDocID', $matchDocumentMasterAutoID)
                                                                ->whereHas('supplier_invoice')
                                                                ->with(['supplier_invoice'])
                                                                ->get();

        $supplierInvoiceVAT = 0;
        $supplierInvoiceVATLocal = 0;
        $supplierInvoiceVATRpt = 0;

        foreach ($supplierInvoices as $key => $value) {
            if ($value->supplier_invoice->documentType == 0) {
                $vatDetails = self::processPoBasedSupllierInvoiceVAT($value->bookingInvSystemCode);
                $totalVATAmount = isset($vatDetails['totalVAT']) ? $vatDetails['totalVAT'] : 0;
                $totalExemptVAT = isset($vatDetails['exemptVAT']) ? $vatDetails['exemptVAT'] : 0;
                $totalVATAmountLocal = isset($vatDetails['totalVATLocal']) ? $vatDetails['totalVATLocal'] : 0;
                $totalVATAmountRpt = isset($vatDetails['totalVATRpt']) ? $vatDetails['totalVATRpt'] : 0;
            
                $supplierInvoiceVAT += (($totalVATAmount / $value->supplierInvoiceAmount) * $value->supplierPaymentAmount);
                $supplierInvoiceVATLocal += (($totalVATAmountLocal / $value->localAmount) * $value->paymentLocalAmount);
                $supplierInvoiceVATRpt += (($totalVATAmountRpt / $value->comRptAmount) * $value->paymentComRptAmount);

            } else if ($value->supplier_invoice->documentType == 1) {
                $supplierInvoiceVAT += (($value->supplier_invoice->VATAmount / $value->supplierInvoiceAmount) * $value->supplierPaymentAmount);
                $supplierInvoiceVATLocal += (($value->supplier_invoice->VATAmountLocal / $value->localAmount) * $value->paymentLocalAmount);
                $supplierInvoiceVATRpt += (($value->supplier_invoice->VATAmountRpt / $value->comRptAmount) * $value->paymentComRptAmount);
            }                                    
        }

        return ['supplierInvoiceVAT' => $supplierInvoiceVAT, 'supplierInvoiceVATLocal' => $supplierInvoiceVATLocal, 'supplierInvoiceVATRpt' => $supplierInvoiceVATRpt];
    }
}

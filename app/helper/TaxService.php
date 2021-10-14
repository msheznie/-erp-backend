<?php


namespace App\helper;


use App\Models\Company;
use App\Models\CustomerAssigned;
use App\Models\GRVDetails;
use App\Models\PoAdvancePayment;
use App\Models\ProcumentOrder;
use App\Models\Taxdetail;
use App\Models\GRVMaster;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DirectInvoiceDetails;
use App\Models\SupplierInvoiceItemDetail;
use App\Models\PurchaseReturn;
use App\Models\BookInvSuppDet;
use App\Models\SupplierAssigned;
use App\Models\Tax;
use App\Models\TaxVatCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
                $data['vatSubCategoryID'] = $defaultVAT->taxVatSubCategoriesAutoID;
                $data['vatMasterCategoryID'] = $defaultVAT->mainCategory;
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
            'exemptVATportionBs' => $exemptVATportionBs,
            'exemptVATportionPL' => $exemptVATportionPL,
            'plVAT' => $plVATData
        ];

        if (!$exemptVAT && !$exemptVATPortainateFlag) {
            $masterData = GRVMaster::with(['details' => function ($query) {
                                        $query->selectRaw("SUM(GRVcostPerUnitLocalCur*noQty) as localAmount, SUM(GRVcostPerUnitComRptCur*noQty) as rptAmount,SUM(GRVcostPerUnitSupTransCur*noQty) as transAmount,SUM(VATAmount*noQty) as transVATAmount,SUM(VATAmountLocal*noQty) as localVATAmount ,SUM(VATAmountRpt*noQty) as rptVATAmount ,grvAutoID,supplierItemCurrencyID as supplierTransactionCurrencyID,foreignToLocalER as supplierTransactionER,erp_grvdetails.companyReportingCurrencyID,erp_grvdetails.companyReportingER,erp_grvdetails.localCurrencyID,erp_grvdetails.localCurrencyER");
                                    }])->find($grvAutoID);

            $vatData['masterVATTrans'] = $masterData->details[0]->transVATAmount;
            $vatData['masterVATRpt'] = $masterData->details[0]->localVATAmount;
            $vatData['masterVATLocal'] = $masterData->details[0]->rptVATAmount;

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
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->details[0]->localVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->details[0]->rptVATAmount : 0;

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

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);

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

            $vatData['exemptVATportionBs'] = $exemptVATportionBs;
            $vatData['exemptVATportionPL'] = $exemptVATportionPL;


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

                $plVATData[$value['financeGLcodePLSystemID']] = $temp;
            }

             $vatData['plVAT'] = $plVATData;
             $vatData['bsVAT'] = $bsVATData;
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
            $vatData['masterVATRpt'] = $masterData->details[0]->localVATAmount;
            $vatData['masterVATLocal'] = $masterData->details[0]->rptVATAmount;

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
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->details[0]->localVATAmount : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->details[0]->rptVATAmount : 0;

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

                $exemptVATLocalAmount = $value->localVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATLocal'] += ($value->localVATAmount - $exemptVATLocalAmount);

                $exemptVATRptAmount = $value->rptVATAmount * ($value->exempt_vat_portion/100);
                $vatData['masterVATRpt'] += ($value->rptVATAmount - $exemptVATRptAmount);

                if ($value->financeGLcodebBSSystemID > 0 && !is_null($value->financeGLcodebBSSystemID)) {
                    $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATTransAmount'] : 0) + $exemptVATTransAmount;

                     $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATLocalAmount'] : 0) + $exemptVATLocalAmount;

                      $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] = ((isset($exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'])) ? $exemptVATportionBs[$value->financeGLcodebBSSystemID]['exemptVATRptAmount'] : 0) + $exemptVATRptAmount;
                }
            }

            $vatData['exemptVATportionBs'] = $exemptVATportionBs;

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

                $bsVATData[$value['financeGLcodebBSSystemID']] = $temp;
            }

             $vatData['bsVAT'] = $bsVATData;
        }


        return $vatData;
    }


    public static function processPoBasedSupllierInvoiceVAT($bookingSuppMasInvAutoID)
    {
        $detailVAT = SupplierInvoiceItemDetail::selectRaw('SUM(VATAmount) as totalVAT, SUM(VATAmountLocal) as totalVATLocal, SUM(VATAmountRpt) as totalVATRpt, vatSubCategoryID')
                                                           ->whereNotNull('vatSubCategoryID')
                                                           ->where('vatSubCategoryID', '>', 0)
                                                           ->whereHas('vat_sub_category', function($query) {
                                                                $query->where('subCatgeoryType', '!=', 3);
                                                           })
                                                           ->where('bookingSuppMasInvAutoID', $bookingSuppMasInvAutoID)
                                                           ->where('exempt_vat_portion', 0)
                                                           ->groupBy('bookingSuppMasInvAutoID')
                                                           ->first();

        $vatData = [
            'totalVAT' => 0,
            'totalVATLocal' => 0,
            'totalVATRpt' => 0
        ];

        if ($detailVAT) {
             $vatData = [
                'totalVAT' => $detailVAT->totalVAT,
                'totalVATLocal' => $detailVAT->totalVATLocal,
                'totalVATRpt' => $detailVAT->totalVATRpt
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
            $vatData['totalVAT'] += ($value->VATAmount - $exemptVATTransAmount);

            $exemptVATLocalAmount = $value->VATAmountLocal * ($value->exempt_vat_portion/100);
            $vatData['totalVATLocal'] += ($value->VATAmountLocal - $exemptVATLocalAmount);

            $exemptVATRptAmount = $value->VATAmountRpt * ($value->exempt_vat_portion/100);
            $vatData['totalVATRpt'] += ($value->VATAmountRpt - $exemptVATRptAmount);
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
            $vatData['masterVATRpt'] = ($masterData) ? $masterData->totalVATAmountLocal : 0;
            $vatData['masterVATLocal'] = ($masterData) ? $masterData->totalVATAmountRpt : 0;

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
}

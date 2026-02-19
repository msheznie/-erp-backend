<?php

namespace App\Services\ProcurementOrder;

use App\Models\ProcumentOrder;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\ProcumentOrderDetail;
use App\Models\CompanyPolicyMaster;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\SupplierAssigned;
use App\Models\User;
use App\Models\AssetFinanceCategory;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use Response;
use Illuminate\Support\Facades\Auth;
use App\helper\TaxService;

class ProcurementOrderService
{
    public static function validatePoItem($itemCode, $companySystemID, $purchaseOrderID)
    {

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        if (empty($purchaseOrder)) {
            return ['status' => false, 'message' => 'Purchase Order not found'];
        }

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();

        if (empty($item)) {
            return ['status' => false, 'message' => 'Item not found'];
        }

        $itemExist = PurchaseOrderDetails::where('itemCode', $itemCode)
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->first();

        if ($item->financeCategoryMaster == 1) {
           if (!empty($itemExist)) {
                return ['status' => false, 'message' => 'Added item already exist'];
           }
        }


        $companyPolicyMaster = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
            ->where('companySystemID', $companySystemID)
            ->first();

        if ($companyPolicyMaster) {
            if (($companyPolicyMaster->isYesNO == 0) && ($purchaseOrder->financeCategory == 1)) {

                $checkWhether = ProcumentOrder::where('purchaseOrderID', '!=', $purchaseOrder->purchaseOrderID)
                    ->where('companySystemID', $companySystemID)
                    ->where('serviceLineSystemID', $purchaseOrder->serviceLineSystemID)
                    ->select(['erp_purchaseordermaster.purchaseOrderID', 'erp_purchaseordermaster.companySystemID',
                        'erp_purchaseordermaster.serviceLine', 'erp_purchaseordermaster.purchaseOrderCode', 'erp_purchaseordermaster.poConfirmedYN', 'erp_purchaseordermaster.approved', 'erp_purchaseordermaster.poCancelledYN'])
                    ->groupBy('erp_purchaseordermaster.purchaseOrderID', 'erp_purchaseordermaster.companySystemID', 'erp_purchaseordermaster.serviceLine', 'erp_purchaseordermaster.purchaseOrderCode', 'erp_purchaseordermaster.poConfirmedYN', 'erp_purchaseordermaster.approved', 'erp_purchaseordermaster.poCancelledYN'
                    );

                $anyPendingApproval = $checkWhether->whereHas('detail', function ($query) use ($companySystemID, $purchaseOrder, $item) {
                    $query->where('itemPrimaryCode', $item->itemPrimaryCode);
                })
                    ->where('approved', 0)
                    ->where('poCancelledYN', 0)
                    ->first();

                if (!empty($anyPendingApproval)) {
                    return ['status' => false, 'message' => "There is a purchase order (" . $anyPendingApproval->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again."];
                }

            }
        }

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
                ->where('companySystemID', $purchaseOrder->companySystemID)
                ->first();
        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;
            if ($policy == 0) {
                if ($purchaseOrder->financeCategory == null || $purchaseOrder->financeCategory == 0) {
                    return ['status' => false, 'message' => 'Category is not found'];
                }

                //checking if item category is same or not
                $pRDetailExistSameItem = ProcumentOrderDetail::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
                    ->where('purchaseOrderMasterID', $purchaseOrderID)
                    ->first();

                if ($pRDetailExistSameItem) {
                    if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
                        return ['status' => false, 'message' => 'You cannot add different category item'];
                    }
                }
            }
        }

        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if (empty($financeItemCategorySubAssigned)) {
            return ['status' => false, 'message' => 'Finance category not assigned for the selected item'];
        }

    
        if ($item->financeCategoryMaster == 3) {
            $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
            if (!$assetCategory) {
                return ['status' => false, 'message' => 'Asset category not assigned for the selected item.'];
            }
        } 

        return ['status' => true];
    }


    public static function savePoItem($itemCode, $companySystemID, $purchaseOrderID, $empID, $employeeSystemID)
    {
        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)
            ->first();

        $itemData = [];
        $itemData['noQty'] = 0;
        $itemData['unitCost'] = 0;
        $itemData['clientReferenceNumber'] = null;
        $itemData['comment'] = null;
        $itemData['companySystemID'] = $companySystemID;
        $itemData['discountAmount'] = 0;
        $itemData['discountPercentage'] = 0;
        $itemData['netAmount'] = 0;
        $itemData['netAmount'] = 0;
        $itemData['itemCategoryID'] = null;
        $itemData['poTypeID'] = $purchaseOrder ? $purchaseOrder->poTypeID : 0;
        $itemData['supplierPartNumber'] = null;

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();


       
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        $itemData['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
        $itemData['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
        if ($item->financeCategoryMaster == 3) {
            $assetCategory = AssetFinanceCategory::find($item->faFinanceCatID);
            $itemData['financeGLcodePLSystemID'] = $assetCategory->COSTGLCODESystemID;
            $itemData['financeGLcodePL'] = $assetCategory->COSTGLCODE;
        } else {
            $itemData['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
            $itemData['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
        }
        $itemData['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
        $itemData['budgetYear'] = $purchaseOrder->budgetYear;

        $currencyConversion = Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $item->wacValueLocal);

        $itemData['unitCost'] =  Helper::roundValue($currencyConversion['documentAmount']);

        $itemData['localCurrencyID'] = $purchaseOrder->localCurrencyID;
        $itemData['localCurrencyER'] = $purchaseOrder->localCurrencyER;

        $itemData['supplierItemCurrencyID'] = $purchaseOrder->supplierTransactionCurrencyID;
        $itemData['foreignToLocalER'] = $purchaseOrder->supplierTransactionER;

        $itemData['companyReportingCurrencyID'] = $purchaseOrder->companyReportingCurrencyID;
        $itemData['companyReportingER'] = $purchaseOrder->companyReportingER;

        $itemData['supplierDefaultCurrencyID'] = $purchaseOrder->supplierDefaultCurrencyID;
        $itemData['supplierDefaultER'] = $purchaseOrder->supplierDefaultER;
        $itemData['VATAmount'] = 0;
        if ($purchaseOrder->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem($purchaseOrder->companySystemID, $itemCode, $purchaseOrder->supplierID);
            $itemData['VATPercentage'] = $vatDetails['percentage'];
            $itemData['VATApplicableOn'] = $vatDetails['applicableOn'];
            $itemData['vatMasterCategoryID'] = $vatDetails['vatMasterCategoryID'];
            $itemData['vatSubCategoryID'] = $vatDetails['vatSubCategoryID'];
            $itemData['VATAmount'] = 0;
            if ($itemData['unitCost'] > 0) {
                $itemData['VATAmount'] = (($itemData['unitCost'] / 100) * $vatDetails['percentage']);
            }
            $prDetail_arr['netAmount'] = ($itemData['unitCost'] + $itemData['VATAmount']) * $itemData['noQty'];
            $currencyConversionVAT = Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $itemData['VATAmount']);

            $itemData['VATAmount'] = 0;
            $itemData['VATAmountLocal'] = 0;
            $itemData['VATAmountRpt'] = 0;

        }

        $grvCost = $itemData['unitCost'];

        if ($grvCost > 0) {
            $currencyConversion = Helper::currencyConversion($companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $grvCost);

            $itemData['GRVcostPerUnitLocalCur'] = Helper::roundValue($currencyConversion['localAmount']);
            $itemData['GRVcostPerUnitSupTransCur'] = $grvCost;
            $itemData['GRVcostPerUnitComRptCur'] = Helper::roundValue($currencyConversion['reportingAmount']);

            $itemData['purchaseRetcostPerUnitLocalCur'] = Helper::roundValue($currencyConversion['localAmount']);
            $itemData['purchaseRetcostPerUnitTranCur'] = $itemData['unitCost'];
            $itemData['purchaseRetcostPerUnitRptCur'] = Helper::roundValue($currencyConversion['reportingAmount']);
        }

        // adding supplier Default CurrencyID base currency conversion
        if ($grvCost > 0) {
            $currencyConversionDefault = Helper::currencyConversion($companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierDefaultCurrencyID, $grvCost);

            $itemData['GRVcostPerUnitSupDefaultCur'] = Helper::roundValue($currencyConversionDefault['documentAmount']);
            $itemData['purchaseRetcostPerUniSupDefaultCur'] = Helper::roundValue($currencyConversionDefault['documentAmount']);
        }

        $itemData['purchaseOrderMasterID'] = $purchaseOrderID;
        $itemData['itemCode'] = $item->itemCodeSystem;
        $itemData['itemPrimaryCode'] = $item->itemPrimaryCode;
        $itemData['supplierPartNumber'] = $item->secondaryItemCode;
        $itemData['itemDescription'] = $item->itemDescription;
        $itemData['unitOfMeasure'] = $item->itemUnitOfMeasure;
        $itemData['itemFinanceCategoryID'] = $item->financeCategoryMaster;
        $itemData['itemFinanceCategorySubID'] = $item->financeCategorySub;

        $itemData['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $itemData['serviceLineCode'] = $purchaseOrder->serviceLine;
        $itemData['companySystemID'] = $item->companySystemID;
        $itemData['companyID'] =  Helper::getCompanyById($item->companySystemID);

        $itemData['createdPcID'] = gethostname();
        $itemData['createdUserID'] = $empID;
        $itemData['createdUserSystemID'] = $employeeSystemID;

        $markupArray = self::setMarkupPercentage($itemData['unitCost'], $purchaseOrder);
        $itemData['markupPercentage'] = $markupArray['markupPercentage'];
        $itemData['markupTransactionAmount'] = $markupArray['markupTransactionAmount'];
        $itemData['markupLocalAmount'] = $markupArray['markupLocalAmount'];
        $itemData['markupReportingAmount'] = $markupArray['markupReportingAmount'];

        $poData = PurchaseOrderDetails::create($itemData);
    }

    public static function setMarkupPercentage($unitCost, $poData, $markupPercentage = 0, $markupTransAmount = 0, $by = '')
    {

        $output['markupPercentage'] = 0;
        $output['markupTransactionAmount'] = 0;
        $output['markupLocalAmount'] = 0;
        $output['markupReportingAmount'] = 0;

        if (isset($poData->supplierID) && $poData->supplierID) {

            $supplier = SupplierAssigned::where('supplierCodeSytem', $poData->supplierID)
                ->where('companySystemID', $poData->companySystemID)
                ->where('isActive', 1)
                ->where('isAssigned', -1)
                ->first();

            if ($supplier->companySystemID && $supplier->isMarkupPercentage) {
                $hasEEOSSPolicy = CompanyPolicyMaster::where('companySystemID', $supplier->companySystemID)
                    ->where('companyPolicyCategoryID', 41)
                    ->where('isYesNO', 1)
                    ->exists();

                if ($hasEEOSSPolicy) {

                    if ($by == 'amount') {
                        $output['markupTransactionAmount'] = $markupTransAmount;
                        if ($unitCost > 0 && $markupTransAmount > 0) {
                            $output['markupPercentage'] = $markupTransAmount * 100 / $unitCost;
                        }
                    } else {
                        $percentage = ($markupPercentage != 0) ? $markupPercentage : $supplier->markupPercentage;
                        if ($percentage != 0) {
                            $output['markupPercentage'] = $percentage;
                            if ($unitCost > 0) {
                                $output['markupTransactionAmount'] = $percentage * $unitCost / 100;
                            }
                        }
                    }

                    if ($output['markupTransactionAmount'] > 0) {
                        if ($poData->supplierTransactionCurrencyID != $poData->localCurrencyID) {
                            $currencyConversion = Helper::currencyConversion($poData->companySystemID, $poData->supplierTransactionCurrencyID, $poData->localCurrencyID, $output['markupTransactionAmount']);
                            if (!empty($currencyConversion)) {
                                $output['markupLocalAmount'] = $currencyConversion['documentAmount'];
                            }
                        } else {
                            $output['markupLocalAmount'] = $output['markupTransactionAmount'];
                        }

                        if ($poData->supplierTransactionCurrencyID != $poData->companyReportingCurrencyID) {
                            $currencyConversion = Helper::currencyConversion($poData->companySystemID, $poData->supplierTransactionCurrencyID, $poData->companyReportingCurrencyID, $output['markupTransactionAmount']);
                            if (!empty($currencyConversion)) {
                                $output['markupReportingAmount'] = $currencyConversion['documentAmount'];
                            }
                        } else {
                            $output['markupReportingAmount'] = $output['markupTransactionAmount'];
                        }

                        /*round to 7 decimals*/
                        $output['markupTransactionAmount'] = Helper::roundValue($output['markupTransactionAmount']);
                        $output['markupLocalAmount'] = Helper::roundValue($output['markupLocalAmount']);
                        $output['markupReportingAmount'] = Helper::roundValue($output['markupReportingAmount']);

                    }


                }

            }

        }

        return $output;
    }
}
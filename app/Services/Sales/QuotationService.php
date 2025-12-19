<?php

namespace App\Services\Sales;

use App\helper\TaxService;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\ItemAssigned;
use App\Models\QuotationMaster;
use App\Models\QuotationDetails;
use App\Models\ItemMaster;
use App\Models\CurrencyMaster;
use App\Models\Unit;
use App\Models\Company;
use App\Models\Employee;

class QuotationService
{
    public static function validateQuotationItem($itemCode, $companySystemID, $quotationId)
    {
        $quotationMaster = QuotationMaster::find($quotationId);

        $item = ItemAssigned::where('itemCodeSystem', $itemCode)
            ->where('companySystemID', $companySystemID)
            ->first();

        if(empty($quotationMaster)){
            return ['status' =>  false, 'message' => trans('custom.quotation_not_found')];
        }

        if(empty($item)){
            return ['status' =>  false, 'message' => trans('custom.item_not_found_or_not_assigned')];
        }

        // Check if item is already added to quotation
        $alreadyAdded = QuotationMaster::where('quotationMasterID', $quotationId)
            ->whereHas('detail', function ($query) use ($itemCode) {
                $query->where('itemAutoID', $itemCode);
            })
            ->exists();

        if ($alreadyAdded) {
            return ['status' =>  false, 'message' => trans('custom.selected_item_already_added')];
        }

        // Check for fixed assets (category 3) - not allowed in quotations
        if($item->financeCategoryMaster == 3){
            return ['status' =>  false, 'message' => trans('custom.fixed_assets_cannot_add_to_quotations')];
        }

        // Ensure only sales items are added
        $itemMaster = ItemMaster::find($itemCode);
        
        if (!$itemMaster || !$itemMaster->item_category_type) {
            return ['status' => false, 'message' => trans('custom.item_not_found_or_no_category_type')];
        }

        $salesItems = \App\Models\ItemCategoryTypeMaster::salesItems();
        $hasSalesCategory = false;
        
        foreach ($itemMaster->item_category_type as $categoryType) {
            if (in_array($categoryType['categoryTypeID'], $salesItems)) {
                $hasSalesCategory = true;
                break;
            }
        }

        if (!$hasSalesCategory) {
            return ['status' => false, 'message' => trans('custom.only_sales_items_can_add_to_quotations')];
        }

        // Validate finance category assignment
        $financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $companySystemID)
            ->where('mainItemCategoryID', $item->financeCategoryMaster)
            ->where('itemCategorySubID', $item->financeCategorySub)
            ->first();

        if(empty($financeItemCategorySubAssigned))
        {
            return ['status'=> false , 'message' => trans('custom.finance_item_category_sub_not_found')];
        }

        return ['status' => true, 'message' => trans('custom.quotation_success')];
    }

    public static function saveQuotationItem($itemCodeSystem, $companySystemID, $quotationId, $empID, $employeeSystemID)
    {
        $item = ItemAssigned::where('itemCodeSystem', $itemCodeSystem)
            ->where('companySystemID', $companySystemID)
            ->first();

        $quotationMaster = QuotationMaster::find($quotationId);
        
        // Get employee info from parameters instead of Helper
        $employee = Employee::find($employeeSystemID);

        // Get unit data
        $unitMasterData = Unit::find($item->itemUnitOfMeasure);
        $unitOfMeasure = $unitMasterData ? $unitMasterData->UnitShortCode : null;

        // Get company data
        $company = Company::where('companySystemID', $companySystemID)->first();

        // Calculate currency conversions
        $wacValueLocal = $item->wacValueLocal ?? 0;
        $unittransactionAmount = 0;
        if ($quotationMaster->documentSystemID == 68) {
            $unittransactionAmount = round(\Helper::currencyConversion(
                $quotationMaster->companySystemID, 
                $quotationMaster->companyLocalCurrencyID, 
                $quotationMaster->transactionCurrencyID, 
                $wacValueLocal
            )['documentAmount'], $quotationMaster->transactionCurrencyDecimalPlaces ?? 2);
        }

        // Get VAT details if applicable
        $vatPercentage = 0;
        $vatAmount = 0;
        $vatAmountLocal = 0;
        $vatAmountRpt = 0;
        $vatApplicableOn = null;
        $vatMasterCategoryID = null;
        $vatSubCategoryID = null;

        if ($quotationMaster->isVatEligible) {
            $vatDetails = TaxService::getVATDetailsByItem(
                $quotationMaster->companySystemID, 
                $itemCodeSystem, 
                $quotationMaster->customerSystemCode, 
                0
            );
            $vatPercentage = $vatDetails['percentage'];
            $vatApplicableOn = $vatDetails['applicableOn'];
            $vatMasterCategoryID = $vatDetails['vatMasterCategoryID'];
            $vatSubCategoryID = $vatDetails['vatSubCategoryID'];
            
            if ($unittransactionAmount > 0) {
                $vatAmount = (($unittransactionAmount / 100) * $vatPercentage);
            }
            
            $currencyConversionVAT = \Helper::currencyConversion(
                $quotationMaster->companySystemID, 
                $quotationMaster->transactionCurrencyID, 
                $quotationMaster->transactionCurrencyID, 
                $vatAmount
            );
            $vatAmountLocal = \Helper::roundValue($currencyConversionVAT['localAmount']);
            $vatAmountRpt = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
        }

        $itemData = [
            'quotationMasterID' => $quotationId,
            'itemAutoID' => $item->itemCodeSystem,
            'itemSystemCode' => $item->itemPrimaryCode,
            'itemDescription' => $item->itemDescription,
            'itemCategory' => $item->financeCategoryMaster,
            'itemReferenceNo' => $item->secondaryItemCode,
            'itemFinanceCategoryID' => $item->financeCategoryMaster,
            'itemFinanceCategorySubID' => $item->financeCategorySub,
            'unitOfMeasureID' => $item->itemUnitOfMeasure,
            'unitOfMeasure' => $unitOfMeasure,
            'requestedQty' => 0,
            'unittransactionAmount' => $unittransactionAmount,
            'discountPercentage' => 0,
            'discountAmount' => 0,
            'transactionAmount' => 0,
            'companyLocalAmount' => 0,
            'companyReportingAmount' => 0,
            'customerAmount' => 0,
            'wacValueLocal' => $wacValueLocal,
            'wacValueReporting' => $item->wacValueReporting ?? 0,
            'VATPercentage' => $vatPercentage,
            'VATAmount' => $vatAmount,
            'VATAmountLocal' => $vatAmountLocal,
            'VATAmountRpt' => $vatAmountRpt,
            'VATApplicableOn' => $vatApplicableOn,
            'vatMasterCategoryID' => $vatMasterCategoryID,
            'vatSubCategoryID' => $vatSubCategoryID,
            'companySystemID' => $companySystemID,
            'companyID' => $company ? $company->CompanyID : null,
            'serviceLineSystemID' => $quotationMaster->serviceLineSystemID,
            'serviceLineCode' => $quotationMaster->serviceLine,
            'createdPCID' => gethostname(),
            'createdUserID' => $empID,
            'createdUserSystemID' => $employeeSystemID,
            'createdUserName' => $employee ? $employee->empName : null,
            'documentSystemID' => $quotationMaster->documentSystemID
        ];

        return QuotationDetails::create($itemData);
    }
} 
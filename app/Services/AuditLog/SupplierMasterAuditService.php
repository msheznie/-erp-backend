<?php

namespace App\Services\AuditLog;

use App\Models\ChartOfAccount;
use App\Models\CountryMaster;
use App\Models\CustomerMaster;
use App\Models\SupplierCategory;
use App\Models\SupplierCategoryICVMaster;
use App\Models\SupplierCategoryICVSub;
use App\Models\SupplierCritical;
use App\Models\SupplierGroup;
use App\Models\SupplierImportance;
use App\Models\suppliernature;
use App\Models\SupplierType;
use Carbon\Carbon;

class SupplierMasterAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "U"){

            if($auditData['previosValue']['supplier_group_id'] != $auditData['newValue']['supplier_group_id']) {                
                $newSupplierGroup = SupplierGroup::where('id', $auditData['newValue']['supplier_group_id'])->first();
                $previousSupplierGroup = SupplierGroup::where('id', $auditData['previosValue']['supplier_group_id'])->first();

                $modifiedData[] = ['amended_field' => "supplier_group", 'previous_value' => ($previousSupplierGroup) ? $previousSupplierGroup->group: '', 'new_value' => ($newSupplierGroup) ? $newSupplierGroup->group : ''];
            }
            if($auditData['previosValue']['supplier_category_id'] != $auditData['newValue']['supplier_category_id']) {
                $newSupplierCategory = SupplierCategory::where('id', $auditData['newValue']['supplier_category_id'])->first();
                $previousSupplierCategory = SupplierCategory::where('id', $auditData['previosValue']['supplier_category_id'])->first();

                $modifiedData[] = ['amended_field' => "supplier_category", 'previous_value' => ($previousSupplierCategory) ? $previousSupplierCategory->category: '', 'new_value' => ($newSupplierCategory) ? $newSupplierCategory->category : ''];
            }
            if($auditData['previosValue']['supplierName'] != $auditData['newValue']['supplierName']) {
                $modifiedData[] = ['amended_field' => "name", 'previous_value' => ($auditData['previosValue']['supplierName']) ? $auditData['previosValue']['supplierName'] : '', 'new_value' => ($auditData['newValue']['supplierName']) ? $auditData['newValue']['supplierName'] : ''];
            }
            if($auditData['previosValue']['address'] != $auditData['newValue']['address']) {
                $modifiedData[] = ['amended_field' => "address", 'previous_value' => ($auditData['previosValue']['address']) ? $auditData['previosValue']['address'] : '', 'new_value' => ($auditData['newValue']['address']) ? $auditData['newValue']['address'] : ''];
            }
            if($auditData['previosValue']['supplierCountryID'] != $auditData['newValue']['supplierCountryID']) {
                $newCountry = CountryMaster::where('countryID',$auditData['newValue']['supplierCountryID'])->first();
                $previousCountry = CountryMaster::where('countryID',$auditData['previosValue']['supplierCountryID'])->first();
                $modifiedData[] = ['amended_field' => "country", 'previous_value' => ($previousCountry) ? $previousCountry->countryName : '', 'new_value' => ($newCountry) ? $newCountry->countryName : ''];
            }
            if($auditData['previosValue']['telephone'] != $auditData['newValue']['telephone']) {
                $modifiedData[] = ['amended_field' => "telephone", 'previous_value' => ($auditData['previosValue']['telephone']) ? $auditData['previosValue']['telephone'] : '', 'new_value' => ($auditData['newValue']['telephone']) ? $auditData['newValue']['telephone'] : ''];
            }
            if($auditData['previosValue']['fax'] != $auditData['newValue']['fax']) {
                $modifiedData[] = ['amended_field' => "fax", 'previous_value' => ($auditData['previosValue']['fax']) ? $auditData['previosValue']['fax'] : '', 'new_value' => ($auditData['newValue']['fax']) ? $auditData['newValue']['fax'] : ''];
            }
            if($auditData['previosValue']['supEmail'] != $auditData['newValue']['supEmail']) {
                $modifiedData[] = ['amended_field' => "email", 'previous_value' => ($auditData['previosValue']['supEmail']) ? $auditData['previosValue']['supEmail'] : '', 'new_value' => ($auditData['newValue']['supEmail']) ? $auditData['newValue']['supEmail'] : ''];
            }
            if($auditData['previosValue']['webAddress'] != $auditData['newValue']['webAddress']) {
                $modifiedData[] = ['amended_field' => "web_address", 'previous_value' => ($auditData['previosValue']['webAddress']) ? $auditData['previosValue']['webAddress'] : '', 'new_value' => ($auditData['newValue']['webAddress']) ? $auditData['newValue']['webAddress'] : ''];
            }
            if($auditData['previosValue']['registrationNumber'] != $auditData['newValue']['registrationNumber']) {
                $modifiedData[] = ['amended_field' => "registration_number", 'previous_value' => ($auditData['previosValue']['registrationNumber']) ? $auditData['previosValue']['registrationNumber'] : '', 'new_value' => ($auditData['newValue']['registrationNumber']) ? $auditData['newValue']['registrationNumber'] : ''];
            }
            if($auditData['previosValue']['registrationExprity'] != $auditData['newValue']['registrationExprity']) {
                $newregistrationExprity = $auditData['newValue']['registrationExprity'];
                $carbonDate = Carbon::parse($newregistrationExprity);
                $newregistrationExprity = $carbonDate->toDateString();

                $previousregistrationExprity = $auditData['previosValue']['registrationExprity'];
                $carbonDate = Carbon::parse($previousregistrationExprity);
                $previousregistrationExprity = $carbonDate->toDateString();
                
                $modifiedData[] = ['amended_field' => "registration_expiry", 'previous_value' => ($auditData['previosValue']['registrationExprity']) ? $previousregistrationExprity: '', 'new_value' => ($auditData['newValue']['registrationExprity']) ? $newregistrationExprity : ''];
            }
            if($auditData['previosValue']['nameOnPaymentCheque'] != $auditData['newValue']['nameOnPaymentCheque']) {
                $modifiedData[] = ['amended_field' => "name_on_the_cheque", 'previous_value' => ($auditData['previosValue']['nameOnPaymentCheque']) ? $auditData['previosValue']['nameOnPaymentCheque'] : '', 'new_value' => ($auditData['newValue']['nameOnPaymentCheque']) ? $auditData['newValue']['nameOnPaymentCheque'] : ''];
            }
            if($auditData['previosValue']['linkCustomerYN'] != $auditData['newValue']['linkCustomerYN']) {
                $modifiedData[] = ['amended_field' => "link_customer_yes/no", 'previous_value' => ($auditData['previosValue']['linkCustomerYN']==1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['linkCustomerYN'] == 1) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['linkCustomerID'] != $auditData['newValue']['linkCustomerID']) {
                $newCustomer = CustomerMaster::where('customerCodeSystem',$auditData['newValue']['linkCustomerID'])->first();
                $previousCustomer = CustomerMaster::where('customerCodeSystem',$auditData['previosValue']['linkCustomerID'])->first();
                $modifiedData[] = ['amended_field' => "customer", 'previous_value' => ($previousCustomer) ? $previousCustomer->CustomerName : '', 'new_value' => ($newCustomer) ? $newCustomer->CustomerName : ''];
            }
            if($auditData['previosValue']['liabilityAccountSysemID'] != $auditData['newValue']['liabilityAccountSysemID']) {            
                $newLiabilityAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['liabilityAccountSysemID'])->first();
                $previousLiabilityAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['liabilityAccountSysemID'])->first();
                $modifiedData[] = ['amended_field' => "liability_account", 'previous_value' => ($previousLiabilityAccount) ? $previousLiabilityAccount->AccountCode.'-'.$previousLiabilityAccount->AccountDescription : '', 'new_value' => ($newLiabilityAccount) ? $newLiabilityAccount->AccountCode.'-'.$newLiabilityAccount->AccountDescription : ''];
            }
            if($auditData['previosValue']['UnbilledGRVAccountSystemID'] != $auditData['newValue']['UnbilledGRVAccountSystemID']) {
                $newUnbilledAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['UnbilledGRVAccountSystemID'])->first();
                $previousUnbilledAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['UnbilledGRVAccountSystemID'])->first();
                $modifiedData[] = ['amended_field' => "unbilled_account", 'previous_value' => ($previousUnbilledAccount) ? $previousUnbilledAccount->AccountCode.'-'.$previousUnbilledAccount->AccountDescription : '', 'new_value' => ($newUnbilledAccount) ? $newUnbilledAccount->AccountCode.'-'.$newUnbilledAccount->AccountDescription : ''];
            }
            if($auditData['previosValue']['advanceAccountSystemID'] != $auditData['newValue']['advanceAccountSystemID']) {
                $newAdvanceAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['advanceAccountSystemID'])->first();
                $previousAdvanceAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['advanceAccountSystemID'])->first();
                $modifiedData[] = ['amended_field' => "advance_account", 'previous_value' => ($previousAdvanceAccount) ? $previousAdvanceAccount->AccountCode.'-'.$previousAdvanceAccount->AccountDescription : '', 'new_value' => ($newAdvanceAccount) ? $newAdvanceAccount->AccountCode.'-'.$newAdvanceAccount->AccountDescription : ''];
            }
            if($auditData['previosValue']['vatEligible'] != $auditData['newValue']['vatEligible']) {
                $modifiedData[] = ['amended_field' => "vat_eligible", 'previous_value' => ($auditData['previosValue']['vatEligible']==1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['vatEligible'] == 1) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['vatNumber'] != $auditData['newValue']['vatNumber']) {
                $modifiedData[] = ['amended_field' => "vat_number", 'previous_value' => ($auditData['previosValue']['vatNumber']) ? $auditData['previosValue']['vatNumber'] : '', 'new_value' => ($auditData['newValue']['vatNumber']) ? $auditData['newValue']['vatNumber'] : ''];
            }
            if($auditData['previosValue']['vatPercentage'] != $auditData['newValue']['vatPercentage']) {
                $modifiedData[] = ['amended_field' => "vat_percentage", 'previous_value' => ($auditData['previosValue']['vatPercentage']) ? $auditData['previosValue']['vatPercentage'] : '', 'new_value' => ($auditData['newValue']['vatPercentage']) ? $auditData['newValue']['vatPercentage'] : ''];
            }
            if($auditData['previosValue']['retentionPercentage'] != $auditData['newValue']['retentionPercentage']) {
                $modifiedData[] = ['amended_field' => "retention_percentage", 'previous_value' => ($auditData['previosValue']['retentionPercentage']) ? $auditData['previosValue']['retentionPercentage'] : '', 'new_value' => ($auditData['newValue']['retentionPercentage']) ? $auditData['newValue']['retentionPercentage'] : ''];
            }
            if($auditData['previosValue']['creditLimit'] != $auditData['newValue']['creditLimit']) {
                $modifiedData[] = ['amended_field' => "credit_limit", 'previous_value' => ($auditData['previosValue']['creditLimit']) ? $auditData['previosValue']['creditLimit'] : '', 'new_value' => ($auditData['newValue']['creditLimit']) ? $auditData['newValue']['creditLimit'] : ''];
            }
            if($auditData['previosValue']['creditPeriod'] != $auditData['newValue']['creditPeriod']) {
                $modifiedData[] = ['amended_field' => "credit_period_days", 'previous_value' => ($auditData['previosValue']['creditPeriod']) ? $auditData['previosValue']['creditPeriod'] : '', 'new_value' => ($auditData['newValue']['creditPeriod']) ? $auditData['newValue']['creditPeriod'] : ''];
            }
            if($auditData['previosValue']['jsrsNo'] != $auditData['newValue']['jsrsNo']) {
                $modifiedData[] = ['amended_field' => "jsrs_number", 'previous_value' => ($auditData['previosValue']['jsrsNo']) ? $auditData['previosValue']['jsrsNo'] : '', 'new_value' => ($auditData['newValue']['jsrsNo']) ? $auditData['newValue']['jsrsNo'] : ''];
            }
            if($auditData['previosValue']['jsrsExpiry'] != $auditData['newValue']['jsrsExpiry']) {
                $newjsrsExpiry = $auditData['newValue']['jsrsExpiry'];
                $carbonDate = Carbon::parse($newjsrsExpiry);
                $newjsrsExpiry = $carbonDate->toDateString();

                $previousjsrsExpiry = $auditData['previosValue']['jsrsExpiry'];
                $carbonDate = Carbon::parse($previousjsrsExpiry);
                $previousjsrsExpiry = $carbonDate->toDateString();

                $modifiedData[] = ['amended_field' => "jsrs_expiry", 'previous_value' => ($auditData['previosValue']['jsrsExpiry']) ? $previousjsrsExpiry : '', 'new_value' => ($auditData['newValue']['jsrsExpiry']) ? $newjsrsExpiry : ''];
            }
            if($auditData['previosValue']['supCategoryICVMasterID'] != $auditData['newValue']['supCategoryICVMasterID']) {
                $newICVCategory = SupplierCategoryICVMaster::where('supCategoryICVMasterID',$auditData['newValue']['supCategoryICVMasterID'])->first();
                $previousICVCategory = SupplierCategoryICVMaster::where('supCategoryICVMasterID',$auditData['previosValue']['supCategoryICVMasterID'])->first();
                $modifiedData[] = ['amended_field' => "icv_category", 'previous_value' => ($previousICVCategory) ? $previousICVCategory->categoryDescription: '', 'new_value' => ($newICVCategory) ? $newICVCategory->categoryDescription : ''];
            }
            if($auditData['previosValue']['supCategorySubICVID'] != $auditData['newValue']['supCategorySubICVID']) {
                $newSubICVCategory = SupplierCategoryICVSub::where('supCategorySubICVID',$auditData['newValue']['supCategorySubICVID'])->first();
                $previousSubICVCategory = SupplierCategoryICVSub::where('supCategorySubICVID',$auditData['previosValue']['supCategorySubICVID'])->first();
                $modifiedData[] = ['amended_field' => "icv_sub_category", 'previous_value' => ($previousSubICVCategory) ? $previousSubICVCategory->categoryDescription: '', 'new_value' => ($newSubICVCategory) ? $newSubICVCategory->categoryDescription : ''];
            }
            if($auditData['previosValue']['isLCCYN'] != $auditData['newValue']['isLCCYN']) {
                if($auditData['previosValue']['isLCCYN'] == 1){
                    $previosValue = 'Yes';
                } elseif ($auditData['previosValue']['isLCCYN'] == 0){
                    $previosValue = 'No';
                } elseif($auditData['previosValue']['isLCCYN'] == -1){
                    $previosValue = '';
                }

                if($auditData['newValue']['isLCCYN'] == 1){
                    $newValue = 'Yes';
                } elseif ($auditData['newValue']['isLCCYN'] == 0){
                    $newValue = 'No';
                } elseif($auditData['newValue']['isLCCYN'] == -1){
                    $newValue = '';
                }
                $modifiedData[] = ['amended_field' => "LCC", 'previous_value' => $previosValue, 'new_value' => $newValue];
            }
            if($auditData['previosValue']['isSMEYN'] != $auditData['newValue']['isSMEYN']) {
                if($auditData['previosValue']['isSMEYN'] == 1){
                    $previosValue = 'Yes';
                } elseif ($auditData['previosValue']['isSMEYN'] == 0){
                    $previosValue = 'No';
                } elseif($auditData['previosValue']['isSMEYN'] == -1){
                    $previosValue = '';
                }

                if($auditData['newValue']['isSMEYN'] == 1){
                    $newValue = 'Yes';
                } elseif ($auditData['newValue']['isSMEYN'] == 0){
                    $newValue = 'No';
                } elseif($auditData['newValue']['isSMEYN'] == -1){
                    $newValue = '';
                }
                $modifiedData[] = ['amended_field' => "SME", 'previous_value' => $previosValue, 'new_value' => $newValue];
            }
            if($auditData['previosValue']['supplierImportanceID'] != $auditData['newValue']['supplierImportanceID']) {
                $newImportance = SupplierImportance::where('supplierImportanceID',$auditData['newValue']['supplierImportanceID'])->first();
                $previousImportance = SupplierImportance::where('supplierImportanceID',$auditData['previosValue']['supplierImportanceID'])->first();
                $modifiedData[] = ['amended_field' => "importance", 'previous_value' => ($previousImportance) ? $previousImportance->importanceDescription: '', 'new_value' => ($newImportance) ? $newImportance->importanceDescription : ''];
            }
            if($auditData['previosValue']['supplierNatureID'] != $auditData['newValue']['supplierNatureID']) {                
                $newNature = suppliernature::where('supplierNatureID',$auditData['newValue']['supplierNatureID'])->first();
                $previousNature = suppliernature::where('supplierNatureID',$auditData['previosValue']['supplierNatureID'])->first();
                $modifiedData[] = ['amended_field' => "nature", 'previous_value' => ($previousNature) ? $previousNature->natureDescription: '', 'new_value' => ($newNature) ? $newNature->natureDescription : ''];
            }
            if($auditData['previosValue']['supplierTypeID'] != $auditData['newValue']['supplierTypeID']) {
                $newType = SupplierType::where('supplierTypeID',$auditData['newValue']['supplierTypeID'])->first();
                $previousType = SupplierType::where('supplierTypeID',$auditData['previosValue']['supplierTypeID'])->first();
                $modifiedData[] = ['amended_field' => "type", 'previous_value' => ($previousType) ? $previousType->typeDescription: '', 'new_value' => ($newType) ? $newType->typeDescription : ''];
            }
            if($auditData['previosValue']['isActive'] != $auditData['newValue']['isActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isActive']==1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isActive'] == 1) ? 'yes' : 'no'];
            }
            if($auditData['previosValue']['isBlocked'] != $auditData['newValue']['isBlocked']) {
                $modifiedData[] = ['amended_field' => "blocked", 'previous_value' => ($auditData['previosValue']['isBlocked']==1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isBlocked'] == 1) ? 'yes' : 'no'];
            }

            if($auditData['previosValue']['isCriticalYN'] != $auditData['newValue']['isCriticalYN']) {
                $newCriticalYN = SupplierCritical::where('suppliercriticalID',$auditData['newValue']['isCriticalYN'])->first();
                $previousCriticalYN = SupplierCritical::where('suppliercriticalID',$auditData['previosValue']['isCriticalYN'])->first();
                $modifiedData[] = ['amended_field' => "is_critical", 'previous_value' => ($previousCriticalYN) ? $previousCriticalYN->description: '', 'new_value' => ($newCriticalYN) ? $newCriticalYN->description : ''];
            }

        }

        return $modifiedData;
    }
}

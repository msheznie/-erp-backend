<?php

namespace App\Services\AuditLog;

use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\CustomerMasterCategory;

class CustomerMasterAuditService
{
    public static function process($auditData)
    {
        $modifiedData = [];
        if ($auditData['crudType'] == "U"){
            if($auditData['previosValue']['customerShortCode'] != $auditData['newValue']['customerShortCode']) {
                $modifiedData[] = ['amended_field' => "secondary_code", 'previous_value' => ($auditData['previosValue']['customerShortCode']) ? $auditData['previosValue']['customerShortCode'] : '', 'new_value' => ($auditData['newValue']['customerShortCode']) ? $auditData['newValue']['customerShortCode'] : ''];
            }
            if($auditData['previosValue']['CustomerName'] != $auditData['newValue']['CustomerName']) {
                $modifiedData[] = ['amended_field' => "customer_name", 'previous_value' => ($auditData['previosValue']['CustomerName']) ? $auditData['previosValue']['CustomerName'] : '', 'new_value' => ($auditData['newValue']['CustomerName']) ? $auditData['newValue']['CustomerName'] : ''];
            }
            if($auditData['previosValue']['ReportTitle'] != $auditData['newValue']['ReportTitle']) {
                $modifiedData[] = ['amended_field' => "report_title", 'previous_value' => ($auditData['previosValue']['ReportTitle']) ? $auditData['previosValue']['ReportTitle'] : '', 'new_value' => ($auditData['newValue']['ReportTitle']) ? $auditData['newValue']['ReportTitle'] : ''];
            }
            if($auditData['previosValue']['customerAddress1'] != $auditData['newValue']['customerAddress1']) {
                $modifiedData[] = ['amended_field' => "address_one", 'previous_value' => ($auditData['previosValue']['customerAddress1']) ? $auditData['previosValue']['customerAddress1'] : '', 'new_value' => ($auditData['newValue']['customerAddress1']) ? $auditData['newValue']['customerAddress1'] : ''];
            }

            if($auditData['previosValue']['customerAddress2'] != $auditData['newValue']['customerAddress2']) {
                $modifiedData[] = ['amended_field' => "address_two", 'previous_value' => ($auditData['previosValue']['customerAddress2']) ? $auditData['previosValue']['customerAddress2'] : '', 'new_value' => ($auditData['newValue']['customerAddress2']) ? $auditData['newValue']['customerAddress2'] : ''];
            }

            if($auditData['previosValue']['customerCategoryID'] != $auditData['newValue']['customerCategoryID']) {
                $newCategory = CustomerMasterCategory::where('categoryID',$auditData['newValue']['customerCategoryID'])->first();
                $previousCategory = CustomerMasterCategory::where('categoryID',$auditData['previosValue']['customerCategoryID'])->first();
                $modifiedData[] = ['amended_field' => "category", 'previous_value' => ($previousCategory) ? $previousCategory->categoryDescription : '', 'new_value' => ($newCategory) ? $newCategory->categoryDescription : ''];
            }

            if($auditData['previosValue']['custGLAccountSystemID'] != $auditData['newValue']['custGLAccountSystemID']) {
                $newReceivableAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['custGLAccountSystemID'])->first();
                $previousReceivableAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['custGLAccountSystemID'])->first();

                $modifiedData[] = ['amended_field' => "receivable_account", 'previous_value' => ($previousReceivableAccount) ? $previousReceivableAccount->AccountCode.'-'.$previousReceivableAccount->AccountDescription : '', 'new_value' => ($newReceivableAccount) ? $newReceivableAccount->AccountCode.'-'.$newReceivableAccount->AccountDescription : ''];
            }

            if($auditData['previosValue']['custUnbilledAccountSystemID'] != $auditData['newValue']['custUnbilledAccountSystemID']) {
                $newUnbilledAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['custUnbilledAccountSystemID'])->first();
                $previousUnbilledAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['custUnbilledAccountSystemID'])->first();

                $modifiedData[] = ['amended_field' => "unbilled_account", 'previous_value' => ($previousUnbilledAccount) ? $previousUnbilledAccount->AccountCode.'-'.$previousUnbilledAccount->AccountDescription : '', 'new_value' => ($newUnbilledAccount) ? $newUnbilledAccount->AccountCode.'-'.$newUnbilledAccount->AccountDescription : ''];
            }

            if($auditData['previosValue']['custAdvanceAccountSystemID'] != $auditData['newValue']['custAdvanceAccountSystemID']) {
                $newAdvanceAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['newValue']['custAdvanceAccountSystemID'])->first();
                $previousAdvanceAccount = ChartOfAccount::where('chartOfAccountSystemID', $auditData['previosValue']['custAdvanceAccountSystemID'])->first();

                $modifiedData[] = ['amended_field' => "advance_account", 'previous_value' => ($previousAdvanceAccount) ? $previousAdvanceAccount->AccountCode.'-'.$previousAdvanceAccount->AccountDescription : '', 'new_value' => ($newAdvanceAccount) ? $newUnbilledAccount->AccountCode.'-'.$newAdvanceAccount->AccountDescription : ''];
            }

            if($auditData['previosValue']['customerCountry'] != $auditData['newValue']['customerCountry']) {
                $newCountry = CountryMaster::where('countryID',$auditData['newValue']['customerCountry'])->first();
                $previousCountry = CountryMaster::where('countryID',$auditData['previosValue']['customerCountry'])->first();

                $modifiedData[] = ['amended_field' => "country", 'previous_value' => ($previousCountry) ? $previousCountry->countryName : '', 'new_value' => ($newCountry) ? $newCountry->countryName : ''];
            }

            if($auditData['previosValue']['customerCity'] != $auditData['newValue']['customerCity']) {
                $modifiedData[] = ['amended_field' => "city", 'previous_value' => ($auditData['previosValue']['customerCity']) ? $auditData['previosValue']['customerCity'] : '', 'new_value' => ($auditData['newValue']['customerCity']) ? $auditData['newValue']['customerCity'] : ''];
            }

            if($auditData['previosValue']['creditLimit'] != $auditData['newValue']['creditLimit']) {
                $modifiedData[] = ['amended_field' => "credit_limit", 'previous_value' => ($auditData['previosValue']['creditLimit']) ? $auditData['previosValue']['creditLimit'] : '', 'new_value' => ($auditData['newValue']['creditLimit']) ? $auditData['newValue']['creditLimit'] : ''];
            }

            if($auditData['previosValue']['creditDays'] != $auditData['newValue']['creditDays']) {
                $modifiedData[] = ['amended_field' => "credit_period", 'previous_value' => ($auditData['previosValue']['creditDays']) ? $auditData['previosValue']['creditDays'] : '', 'new_value' => ($auditData['newValue']['creditDays']) ? $auditData['newValue']['creditDays'] : ''];
            }

            if($auditData['previosValue']['customer_registration_no'] != $auditData['newValue']['customer_registration_no']) {
                $modifiedData[] = ['amended_field' => "registration_no", 'previous_value' => ($auditData['previosValue']['customer_registration_no']) ? $auditData['previosValue']['customer_registration_no'] : '', 'new_value' => ($auditData['newValue']['customer_registration_no']) ? $auditData['newValue']['customer_registration_no'] : ''];
            }

            if($auditData['previosValue']['customer_registration_expiry_date'] != $auditData['newValue']['customer_registration_expiry_date']) {
                $modifiedData[] = ['amended_field' => "registration_expiry_date", 'previous_value' => ($auditData['previosValue']['customer_registration_expiry_date']) ? $auditData['previosValue']['customer_registration_expiry_date'] : '', 'new_value' => ($auditData['newValue']['customer_registration_expiry_date']) ? $auditData['newValue']['customer_registration_expiry_date'] : ''];
            }

            if($auditData['previosValue']['isCustomerActive'] != $auditData['newValue']['isCustomerActive']) {
                $modifiedData[] = ['amended_field' => "is_active", 'previous_value' => ($auditData['previosValue']['isCustomerActive']==1) ? 'yes' : 'no', 'new_value' => ($auditData['newValue']['isCustomerActive'] == 1) ? 'yes' : 'no'];
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

            if($auditData['previosValue']['consignee_name'] != $auditData['newValue']['consignee_name']) {
                $modifiedData[] = ['amended_field' => "consignee_name", 'previous_value' => ($auditData['previosValue']['consignee_name']) ? $auditData['previosValue']['consignee_name'] : '', 'new_value' => ($auditData['newValue']['consignee_name']) ? $auditData['newValue']['consignee_name'] : ''];
            }

            if($auditData['previosValue']['consignee_contact_no'] != $auditData['newValue']['consignee_contact_no']) {
                $modifiedData[] = ['amended_field' => "consignee_contact_no", 'previous_value' => ($auditData['previosValue']['consignee_contact_no']) ? $auditData['previosValue']['consignee_contact_no'] : '', 'new_value' => ($auditData['newValue']['consignee_contact_no']) ? $auditData['newValue']['consignee_contact_no'] : ''];
            }

            if($auditData['previosValue']['consignee_address'] != $auditData['newValue']['consignee_address']) {
                $modifiedData[] = ['amended_field' => "consignee_address", 'previous_value' => ($auditData['previosValue']['consignee_address']) ? $auditData['previosValue']['consignee_address'] : '', 'new_value' => ($auditData['newValue']['consignee_address']) ? $auditData['newValue']['consignee_address'] : ''];
            }

            if($auditData['previosValue']['payment_terms'] != $auditData['newValue']['payment_terms']) {
                $modifiedData[] = ['amended_field' => "payment_terms", 'previous_value' => ($auditData['previosValue']['payment_terms']) ? $auditData['previosValue']['payment_terms'] : '', 'new_value' => ($auditData['newValue']['payment_terms']) ? $auditData['newValue']['payment_terms'] : ''];
            }
        }

        return $modifiedData;
    }
}

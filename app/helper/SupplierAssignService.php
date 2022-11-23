<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CompanyPolicyMaster;
use App\Models\SupplierAssigned;
use App\Models\SupplierMaster;
use App\Models\Company;

class SupplierAssignService
{
	public static function assignSupplier($supplierCodeSystem, $companySystemID)
	{


		 $supplierMaster = SupplierMaster::selectRaw('supplierCodeSystem as supplierCodeSytem,primaryCompanySystemID as companySystemID,primaryCompanyID as companyID,uniqueTextcode,
		 									primarySupplierCode,secondarySupplierCode,supplierName,liabilityAccountSysemID,liabilityAccount,UnbilledGRVAccountSystemID,UnbilledGRVAccount,advanceAccountSystemID,AdvanceAccount,address,countryID,supplierCountryID,telephone,fax,supEmail,webAddress,currency,nameOnPaymentCheque,creditLimit,creditPeriod,supCategoryMasterID,supCategorySubID,registrationNumber,registrationExprity,supplierImportanceID,supplierNatureID,supplierTypeID,WHTApplicable,vatEligible,vatNumber,vatPercentage,supCategoryICVMasterID,supCategorySubICVID,isLCCYN,-1 as isAssigned,markupPercentage,isMarkupPercentage,NOW() as timeStamp,jsrsNo,jsrsExpiry')->find($supplierCodeSystem);

		 $checkSupplierAssignPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 51)
					                                    ->where('companySystemID', $supplierMaster->companySystemID)
					                                    ->first();


         $supData = array_except($supplierMaster->toArray(),'isSUPDAmendAccess');
		 if ($checkSupplierAssignPolicy && $checkSupplierAssignPolicy->isYesNO == 1) {
			$allCompanies = Company::where('isGroup', 0)
								    ->where('isActive', 1)
            					    ->get();

            foreach ($allCompanies as $key => $value) {
            	$supData['companyID']= $value->CompanyID;
            	$supData['companySystemID']= $value->companySystemID;

            	$supplierAssign = SupplierAssigned::insert($supData);
            }

		 } else {
            $supplierAssign = SupplierAssigned::insert($supData);
		 }
		
		 return ['status' => true];
	}
}
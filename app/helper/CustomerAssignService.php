<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\CompanyPolicyMaster;
use App\Models\CustomerAssigned;
use App\Models\Company;
use App\Models\CustomerMaster;

class CustomerAssignService
{
	public static function assignCustomer($customerCodeSystem, $companySystemID)
	{

		 $customerData = CustomerMaster::find($customerCodeSystem)
		 								  ->toArray();

		 $checkCustomerAssignPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 52)
					                                    ->where('companySystemID', $customerData['primaryCompanySystemID'])
					                                    ->first();

		 if ($checkCustomerAssignPolicy && $checkCustomerAssignPolicy->isYesNO == 1) {


			$allCompanies = Company::where('isGroup', 0)
								    ->where('isActive', 1)
            					    ->get();

            $customerData['isAssigned'] = -1;
            foreach ($allCompanies as $key => $value) {
            	$customerData['companyID']= $value->CompanyID;
            	$customerData['companySystemID']= $value->companySystemID;

            	$supplierAssign = CustomerAssigned::create($customerData);
            }

		 } else {
		 	$customerData['isAssigned'] = -1;
        	$customerData['companyID']= $customerData['primaryCompanyID'];
        	$customerData['companySystemID']= $customerData['primaryCompanySystemID'];

        	$supplierAssign = CustomerAssigned::create($customerData);
		 }
		
		 return ['status' => true];
	}
}
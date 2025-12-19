<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\RegisteredSupplier;
use App\Models\RegisteredSupplierCurrency;
use App\Models\DocumentMaster;
use App\Models\SupplierMaster;
use App\Models\SupplierCurrency;
use App\Models\RegisteredSupplierContactDetail;
use App\Models\RegisteredBankMemoSupplier;
use App\Models\BankMemoSupplier;
use App\Models\SupplierContactDetails;
use App\Models\BankMemoTypes;
use App\Models\RegisteredSupplierAttachment;
use App\Models\DocumentAttachments;
use App\Models\Company;
use App\Models\RegisterSupplierBusinessCategoryAssign;
use App\Models\SupplierBusinessCategoryAssign;
use App\Models\RegisterSupplierSubcategoryAssign;
use App\Models\SupplierSubCategoryAssign;


class SupplierRegister
{
	public static function registerSupplier($input)
	{
		$registeredData = RegisteredSupplier::find($input['id']);  

		if (!$registeredData) {
			return ['status' => false, 'message' => "Supplier registered data not found"];
		}

		$employee = \Helper::getEmployeeInfo();

		$document = DocumentMaster::where('documentID', 'SUPM')->first();

		$supplierMasterData = [
			'uniqueTextcode' => 'S',
	        'supplierName' => $registeredData->supplierName,
	        'address' => $registeredData->address,
	        'countryID' => $registeredData->supplierCountryID,
	        'supplierCountryID' => $registeredData->supplierCountryID,
	        'telephone' => $registeredData->telephone,
	        'fax' => $registeredData->fax,
	        'supEmail' => $registeredData->supEmail,
	        'webAddress' => $registeredData->webAddress,
	        'currency' => $registeredData->currency,
	        'nameOnPaymentCheque' => $registeredData->nameOnPaymentCheque,
	        'registrationNumber' => $registeredData->registrationNumber,
	        'registrationExprity' => $registeredData->registrationExprity,
	        'createdPcID' => gethostname(),
	        'createdUserID' => $employee->empID,
	        'isActive' => 1,
	        'documentSystemID' => $document->documentSystemID,
	        'documentID' => $document->documentID,
	        'createdUserSystemID' => $employee->employeeSystemID,
		];

		$supplierMasters = SupplierMaster::create($supplierMasterData);

        $updateSupplierMasters = SupplierMaster::where('supplierCodeSystem', $supplierMasters->supplierCodeSystem)->first();
        $updateSupplierMasters->primarySupplierCode = 'S0' . strval($supplierMasters->supplierCodeSystem);
        $updateSupplierMasters->save();

        $registeredData->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
        $registeredData->save();

        $registeredSupplierCurrencyData = RegisteredSupplierCurrency::where('registeredSupplierID', $input['id'])
        														 	->get();


        foreach ($registeredSupplierCurrencyData as $key => $value) {
        	$suppCurrencyData = [
        		'supplierCodeSystem' => $supplierMasters->supplierCodeSystem,
		        'currencyID' => $value->currencyID,
		        'isAssigned' => $value->isAssigned,
		        'isDefault' => $value->isDefault,
        	];

        	$supplierCurrencyResult = SupplierCurrency::create($suppCurrencyData);

        	$companyDefaultBankMemos = BankMemoTypes::orderBy('sortOrder', 'asc')->get();

	        foreach ($companyDefaultBankMemos as $val) {
	            $temBankMemo = new BankMemoSupplier();
	            $temBankMemo->memoHeader = $val['bankMemoHeader'];
	            $temBankMemo->bankMemoTypeID = $val['bankMemoTypeID'];

	            $registeredBankMemo = RegisteredBankMemoSupplier::where('registeredSupplierID', $input['id'])
	            												->where('bankMemoTypeID', $val['bankMemoTypeID'])
	            												->where('supplierCurrencyID', $value['id'])
	            												->first();

	            $temBankMemo->memoDetail = ($registeredBankMemo) ? $registeredBankMemo->memoDetail : '';
	            $temBankMemo->supplierCodeSystem = $supplierMasters->supplierCodeSystem;
	            $temBankMemo->supplierCurrencyID = $supplierCurrencyResult->supplierCurrencyID;
	            $temBankMemo->updatedByUserID = $employee->empID;
	            $temBankMemo->updatedByUserName = $employee->empName;
	            $temBankMemo->save();
	        }
        }


        $registeredSupplierContactDetails = RegisteredSupplierContactDetail::where('registeredSupplierID', $input['id'])
        														 		   ->get();


        foreach ($registeredSupplierContactDetails as $key => $value) {
        	$contactData = [
        		'supplierID' => $supplierMasters->supplierCodeSystem,
		        'contactTypeID' => $value->contactTypeID,
		        'contactPersonName' => $value->contactPersonName,
		        'contactPersonTelephone' => $value->contactPersonTelephone,
		        'contactPersonFax' => $value->contactPersonFax,
		        'contactPersonEmail' => $value->contactPersonEmail,
		        'isDefault' => $value->isDefault,
        	];

        	$contactRes = SupplierContactDetails::create($contactData);
        }

        $registeredSupplierAttachments = RegisteredSupplierAttachment::where('resgisteredSupplierID', $input['id'])
        														 		  ->get();

        $companyMaster = Company::where('companySystemID', $registeredData->companySystemID)->first();

        foreach ($registeredSupplierAttachments as $key => $value) {
        	$attachmentData = [
        		'companySystemID' => $registeredData->companySystemID,
		        'companyID' => $companyMaster->CompanyID,
		        'documentSystemID' => 56,
		        'documentID' => 'SUPM',
		        'documentSystemCode' => $supplierMasters->supplierCodeSystem,
		        'attachmentDescription' => $value->attachmentDescription,
		        'originalFileName' => $value->originalFileName,
		        'myFileName' => $value->myFileName,
		        'sizeInKbs' => $value->sizeInKbs,
		        'isUploaded' => $value->isUploaded,
		        'path' => $value->path
        	];

        	$attachemntRes = DocumentAttachments::create($attachmentData);
        }

		$businessCategoryData = RegisterSupplierBusinessCategoryAssign::where('supplierID', $input['id'])
								->get();

		foreach($businessCategoryData as $key => $value)
		{
			$businessCategoryAssign = new SupplierBusinessCategoryAssign();
			$businessCategoryAssign->supplierID = $supplierMasters->supplierCodeSystem;
			$businessCategoryAssign->supCategoryMasterID = $value->supCategoryMasterID;
			$businessCategoryAssign->save();
		}

		$businessSubCategoryData = RegisterSupplierSubcategoryAssign::where('supplierID', $input['id'])
								->get();

		foreach($businessSubCategoryData as $key => $value)
		{
			$businessSubCategoryAssign = new SupplierSubCategoryAssign();
			$businessSubCategoryAssign->supplierID = $supplierMasters->supplierCodeSystem;
			$businessSubCategoryAssign->supSubCategoryID = $value->supSubCategoryID;
			$businessSubCategoryAssign->save();
		}



        return ['status' => true, 'message' => 'success'];
	}
}
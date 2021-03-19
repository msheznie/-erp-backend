<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\ChartOfAccount;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateDetails;

class ChartOfAccountDependency
{
	public static function assignToReports($chartOfAccountSystemID)
	{
		$chartOfAccountData = ChartOfAccount::where('catogaryBLorPL', 'PL')
                                        	->where('chartOfAccountSystemID', $chartOfAccountSystemID)
                                        	->first();
        if ($chartOfAccountData) {
	        $reportTemplates = ReportTemplate::where('reportID', 1)
	        								 ->with(['details' => function($query) use ($chartOfAccountSystemID) {
	        								 	$query->with(['subcategory'  => function($query) use ($chartOfAccountSystemID) {
		        								 			$query->whereNotNull('masterID')
		        								 				  ->where('itemType', 4);
			        								 	}])
	        								 		  ->whereHas('subcategory', function($query) use ($chartOfAccountSystemID) {
	        								 		  		$query->whereNotNull('masterID')
		        								 				  ->where('itemType', 4);
	        								 		  })
	        								 		  ->where('itemType', 4);
	        								 }])
	        								 ->whereHas('details', function($query) use ($chartOfAccountSystemID) {
	        								 		$query->whereHas('subcategory', function($query) use ($chartOfAccountSystemID) {
	        								 		  		$query->whereNotNull('masterID')
		        								 				  ->where('itemType', 4);
	        								 		  })
	        								 		  ->where('itemType', 4);
	        								 })
	        								 ->get();

	        foreach ($reportTemplates as $key => $value) {
	        	foreach ($value->details as $key1 => $value1) {
	        		foreach ($value1->subcategory as $key2 => $value2) {
	        			$checkWhetherAccountIsSync = ReportTemplateLinks::where('templateDetailID', $value2->detID)
	        															->where('glAutoID', $chartOfAccountSystemID)
	        															->first();
	        			if (!$checkWhetherAccountIsSync) {
	        				$maxSortOrder = ReportTemplateLinks::where('templateDetailID', $value2->detID)
                                               ->max('sortOrder');

	        				$data3['templateMasterID'] = $value2->companyReportTemplateID;
			                $data3['templateDetailID'] = $value2->detID;
			                $data3['sortOrder'] = ((isset($maxSortOrder) && $maxSortOrder != null) ? $maxSortOrder : 0) + 1;
			                $data3['glAutoID'] = $chartOfAccountData->chartOfAccountSystemID;
			                $data3['glCode'] = $chartOfAccountData->AccountCode;
			                $data3['glDescription'] = $chartOfAccountData->AccountDescription;
			                $data3['companySystemID'] = $value2->companySystemID;
			                $data3['companyID'] = $value2->companyID;
			                $data3['createdPCID'] = gethostname();
			                $data3['createdUserID'] = \Helper::getEmployeeID();
			                $data3['createdUserSystemID'] = \Helper::getEmployeeSystemID();
			                ReportTemplateLinks::create($data3);


			                $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $value2->detID)->update(['isFinalLevel' => 1]);
	        			}
	        		}
	        	}
	        }
        }

		return ['status' => true];
	}
}
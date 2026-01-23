<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\ReportTemplate;
use App\Models\ReportTemplateLinks;
use App\Models\ReportTemplateDetails;
use App\Models\ChartOfAccountsAssigned;
use App\helper\Helper;

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
			                $data3['createdUserID'] = Helper::getEmployeeID();
			                $data3['createdUserSystemID'] = Helper::getEmployeeSystemID();
			                ReportTemplateLinks::create($data3);


			                $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $value2->detID)->update(['isFinalLevel' => 1]);
	        			}
	        		}
	        	}
	        }
        }

		return ['status' => true];
	}

	public static function assignToTemplateCategory($chartOfAccountSystemID, $companySystemID)
	{
		$chartOfAccountData = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)
                                        	->first();
        if ($chartOfAccountData) {
        	$templateDetailData = ReportTemplateDetails::find($chartOfAccountData->reportTemplateCategory);
        	if ($templateDetailData) {
        		$checkCategoryValid = ReportTemplateDetails::where('masterID',$chartOfAccountData->reportTemplateCategory)
        												   ->first();
       			if ($checkCategoryValid) {
       				return ['status' => false, 'message' => "You cannot assign chart Of Account to this template category, since it have some sub category"];
       			}


        		$res = self::addChartOfAccountToTemplate($chartOfAccountSystemID, $companySystemID, $templateDetailData->companyReportTemplateID, $chartOfAccountData->reportTemplateCategory);

        		if (!$res['status']) {
        			return $res;
        		}
        	}

        }

        return ['status' => true];
	}

	public static function addChartOfAccountToTemplate($chartOfAccountSystemID, $companySystemID, $selectedReportTemplate, $selectedReportCategory)
	{
		
		$company = Company::find($companySystemID);
		$companyID = null;
        if ($company) {
            $companyID = $company->CompanyID;
        }

        $reportTemplateMaster = ReportTemplate::find($selectedReportTemplate);

        $tempDetail = ReportTemplateLinks::ofTemplate($selectedReportTemplate)->pluck('glAutoID')->toArray();

        $finalError = array(
            'already_gl_linked' => array(),
        );
        $error_count = 0;
         $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();
        if ($chartOfAccountSystemID) {
            if (in_array($chartOfAccountSystemID, $tempDetail)) {
                array_push($finalError['already_gl_linked'], $chartOfAccount->AccountCode . ' | ' . $chartOfAccount->AccountDescription);
                $error_count++;
            }

            $confirm_error = array('type' => 'already_gl_linked', 'data' => $finalError);
            if ($error_count > 0) {
                return ['status' => false, 'message' => "You cannot add gl codes as it is already assigned"];
            }else{
                if (!in_array($chartOfAccountSystemID, $tempDetail)) {
                    $data['templateMasterID'] = $selectedReportTemplate;
                    $data['templateDetailID'] = $selectedReportCategory;
                    $data['sortOrder'] = 1;
                    $data['glAutoID'] = $chartOfAccountSystemID;
                    $data['glCode'] = $chartOfAccount->AccountCode;
                    $data['glDescription'] = $chartOfAccount->AccountDescription;
                    $data['companySystemID'] = $companySystemID;
                    $data['companyID'] = $companyID;
                    if($reportTemplateMaster->reportID == 1) {
                        if ($chartOfAccount->controlAccounts == 'BSA') {
                            $data['categoryType'] = 1;
                        } else {
                            $data['categoryType'] = 2;
                        }
                    }
                    $data['createdPCID'] = gethostname();
                    $data['createdUserID'] = Helper::getEmployeeID();
                    $data['createdUserSystemID'] = Helper::getEmployeeSystemID();
                    $reportTemplateLinks = ReportTemplateLinks::create($data);
                }
            }
        }

        $updateTemplateDetailAsFinal = ReportTemplateDetails::where('detID', $selectedReportCategory)->update(['isFinalLevel' => 1]);

        $lastSortOrder = ReportTemplateLinks::ofTemplate($selectedReportTemplate)->where('templateDetailID',$selectedReportCategory)->orderBy('linkID','asc')->get();
        if(count($lastSortOrder) > 0){
            foreach ($lastSortOrder as $key => $val) {
                $data2['sortOrder'] = $key + 1;
                $reportTemplateLinks = ReportTemplateLinks::where('linkID',$val->linkID)->update($data2);
            }
        }

        return ['status' => true];
	}


    public static function checkAndAssignToRelatedParty($chartOfAccountSystemID, $companySystemID)
    {
        $chartOfAccount = ChartOfAccount::where('chartOfAccountSystemID', $chartOfAccountSystemID)->first();
        if (!$chartOfAccount) {
            return ['status' => false, 'message' => "Chart Of Account not found"];
        }

        if ($chartOfAccount->relatedPartyYN && !is_null($chartOfAccount->interCompanySystemID)) {
            $checkAlradyAssigned = ChartOfAccountsAssigned::where('chartOfAccountSystemID', $chartOfAccountSystemID)
                                                          ->where('companySystemID', $chartOfAccount->interCompanySystemID)
                                                          ->first();

            if (!$checkAlradyAssigned) {
                $chartOfAccountRela = ChartOfAccount::selectRaw('interCompanySystemID as companySystemID,interCompanyID as companyID,chartOfAccountSystemID,AccountCode,AccountDescription,masterAccount,catogaryBLorPLID,catogaryBLorPL,controllAccountYN,controlAccountsSystemID,controlAccounts,isActive,isBank,AllocationID,relatedPartyYN,-1 as isAssigned,NOW() as timeStamp')->find($chartOfAccountSystemID);

                $chartOfAccountAssign = ChartOfAccountsAssigned::insert($chartOfAccountRela->toArray());
            }        
        }

        return ['status' => true];
    }
}
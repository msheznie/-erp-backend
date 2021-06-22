<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Budjetdetails;
use App\Models\BudgetDetailHistory;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetConsumedData;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\ReportTemplateDetails;
use App\Models\ProcumentOrder;

class BudgetConsumptionService
{
	public static function checkBudget($documentSystemID, $documentSystemCode)
	{
		$budgetData = self::getConsumptionData($documentSystemID, $documentSystemCode);

		if ($budgetData['status']) {
			if (sizeof($budgetData['data']) > 0) {
				$userMessageE = "";
				foreach ($budgetData['data'] as $key => $value) {
					$totalConsumedAmount = $value['consumedAmount'] + $value['currenctDocumentConsumption'] + $value['pendingDocumentAmount'];
					$totalBudgetRptAmount = $value['budgetAmount'];

					if ($totalConsumedAmount > $totalBudgetRptAmount) {
						$userMessageE .= "Budget Exceeded Category ". $value['templateCategory'] ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Budget Amount : " . round($totalBudgetRptAmount, 2) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Document Amount : " . round($value['currenctDocumentConsumption'], 2) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Consumed Amount : " . round($value['consumedAmount'], 2) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Pending Document Amount : " . round($value['pendingDocumentAmount'], 2) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Total Consumed Amount : " . round($totalConsumedAmount, 2);
                        $userMessageE .= "<br>";
					}
				}

				return ['status' => true, 'message' => $userMessageE];
			} else {
				return ['status' => true, 'message' => ""];
			}
		} else {
			return ['status' => false, 'message' => $budgetData['message']];
		}
	}

	public static function getConsumptionData($documentSystemID, $documentSystemCode)
	{
		$documentLevelCheckBudget = true;
		$budgetFormData = [];
		switch ($documentSystemID) {
			case 1:
			case 50:
			case 51:
				$masterData = PurchaseRequest::find($documentSystemCode);

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = ($masterData->checkBudgetYN == -1) ? true: false;
				$budgetFormData['financeCategory'] = $masterData->financeCategory;

				$budgetFormData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                $budgetFormData['budgetYear'] = $masterData->budgetYear;
                $budgetFormData['currency'] = $masterData->currency;

                $detailData = PurchaseRequestDetails::where('purchaseRequestID', $documentSystemCode)
                								   ->get();

                $budgetFormData['financeGLcodePLSystemIDs'] = $detailData->pluck('financeGLcodePLSystemID')->toArray();
                $budgetFormData['financeGLcodebBSSystemIDs'] = $detailData->pluck('financeGLcodebBSSystemID')->toArray();
				break;
			case 2:
			case 5:
			case 52:
				$masterData = ProcumentOrder::find($documentSystemCode);

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = true;
				$budgetFormData['financeCategory'] = $masterData->financeCategory;

				$budgetFormData['serviceLineSystemID'] = $masterData->serviceLineSystemID;
                $budgetFormData['budgetYear'] = $masterData->budgetYear;
                $budgetFormData['currency'] = $masterData->supplierTransactionCurrencyID;

                $detailData = PurchaseOrderDetails::where('purchaseOrderMasterID', $documentSystemCode)
                								   ->get();

                $budgetFormData['financeGLcodePLSystemIDs'] = $detailData->pluck('financeGLcodePLSystemID')->toArray();
                $budgetFormData['financeGLcodebBSSystemIDs'] = $detailData->pluck('financeGLcodebBSSystemID')->toArray();
				break;
			default:
				return ['status' => false, 'message' => "Budget check is not set for this documnt"];
				break;
		}

		$budgetFormData['documentSystemID'] = $documentSystemID;
		$budgetFormData['documentSystemCode'] = $documentSystemCode;


		$checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
	                                    ->where('companySystemID', $budgetFormData['companySystemID'])
	                                    ->first();

	    $checkBudgetBasedOnGL = CompanyPolicyMaster::where('companyPolicyCategoryID', 55)
	                                    ->where('companySystemID', $budgetFormData['companySystemID'])
	                                    ->first();

	    $departmentWiseCheckBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 33)
                                    ->where('companySystemID', $budgetFormData['companySystemID'])
                                    ->first();

	    $checkBudgetBasedOnGLPolicy = false;
	    if ($checkBudgetBasedOnGL && $checkBudgetBasedOnGL->isYesNO == 1) {
	    	$checkBudgetBasedOnGLPolicy = true;
	    }
		
		$departmentWiseCheckBudgetPolicy = false;
	    if ($departmentWiseCheckBudget && $departmentWiseCheckBudget->isYesNO == 1) {
	    	$departmentWiseCheckBudgetPolicy = true;
	    }

	    $budgetFormData['departmentWiseCheckBudgetPolicy'] = $departmentWiseCheckBudgetPolicy;
	    $budgetData = [];
	    if ($checkBudget && $checkBudget->isYesNO == 1 && $documentLevelCheckBudget && !is_null($budgetFormData['financeCategory'])) {
	    	if ($budgetFormData['financeCategory'] != 3) {
	    		$budgetFormData['glCodes'] = $budgetFormData['financeGLcodePLSystemIDs'];
	    		$budgetFormData['glColumnName'] = "financeGLcodePLSystemID";
	    		if (!$checkBudgetBasedOnGLPolicy) {
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData);
	    		} else {
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData, $budgetFormData['glCodes']);
	    		}
	    	} else {
	    		$budgetFormData['glCodes'] = $budgetFormData['financeGLcodebBSSystemIDs'];
	    		$budgetFormData['glColumnName'] = "financeGLcodebBSSystemID";
	    		if (!$checkBudgetBasedOnGLPolicy) {
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData, [], true);
	    		} else {
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData, $budgetFormData['glCodes'], true);
	    		}
	    	}
	    }

	    return ['status' => true, 'data' => $budgetData];
	}

	public static function budgetConsumptionByTemplate($budgetFormData, $glCodes = [], $fixedAssetFlag = false)
	{
		$checkBudgetConfiguration = Budjetdetails::with(['chart_of_account'])
														->whereIn('chartOfAccountID', $budgetFormData['glCodes'])
														 ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 })
														 ->groupBy('templateDetailID')
														 ->get();

		$templateCategoryIDs = [];

		if (count($checkBudgetConfiguration) > 0) {
			$templateCategoryIDs = $checkBudgetConfiguration->pluck('templateDetailID')->toArray();

			$budgetAmount = self::budgetAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);

			$consumedAmount = self::consumedAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);

			$pendingPoAmounts = self::pendingPoQryForNonFixedAsset($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);

			$documentAmount = self::documentAmountQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);

			$finalData = [];
			foreach ($templateCategoryIDs as $key => $value) {
				$templateDetail = ReportTemplateDetails::find($value);

				$budgetAmountData = collect($budgetAmount)->firstWhere('templateDetailID', $value);
				$consumedAmountData = collect($consumedAmount)->firstWhere('templateDetailID', $value);
				$pendingPoAmountsData = collect($pendingPoAmounts)->firstWhere('templateDetailID', $value);
				$documentAmountData = collect($documentAmount)->firstWhere('templateDetailID', $value);

				$currenctDocumentConsumption = 0;
				if (isset($documentAmountData['totalCost'])) {
					$currencyConversionRptAmount = Helper::currencyConversion($budgetFormData['companySystemID'], $budgetFormData['currency'], $budgetFormData['currency'], $documentAmountData['totalCost']);
					 $currenctDocumentConsumption = $currencyConversionRptAmount['reportingAmount'];
				}

				$budgetAmountValue = (isset($budgetAmountData->budgetRptAmount) ? $budgetAmountData->budgetRptAmount : 0);

				$totalBudgetRptAmount = (!$fixedAssetFlag) ?  ($budgetAmountValue * -1) : abs($budgetAmountValue);

				$totalConsumedAmount = $currenctDocumentConsumption +  (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0) + (isset($pendingPoAmountsData['rptAmt']) ? $pendingPoAmountsData['rptAmt'] : 0);

				$availableAmount = $totalBudgetRptAmount - $totalConsumedAmount;


				$finalData[$value]['templateCategory'] = (isset($templateDetail->description) ? $templateDetail->description : 0);
				$finalData[$value]['budgetAmount'] = $totalBudgetRptAmount;
				$finalData[$value]['currenctDocumentConsumption'] = $currenctDocumentConsumption;
				$finalData[$value]['availableAmount'] = $availableAmount;
				$finalData[$value]['consumedAmount'] = (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0);
				$finalData[$value]['pendingDocumentAmount'] = (isset($pendingPoAmountsData['rptAmt']) ? $pendingPoAmountsData['rptAmt'] : 0);

			}

			$finalResData = [];
			foreach ($finalData as $key => $value) {
				$finalResData[] = $value;
			}

			return $finalResData;

		} else {
			return [];
		}
	}


	public static function pendingPoQryForNonFixedAsset($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, '.$budgetFormData['glColumnName'].', companySystemID, serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
											 })
											 ->whereHas($budgetRelationName,function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
											 	$query->where('companySystemID', $budgetFormData['companySystemID'])
											 		   ->where('Year', $budgetFormData['budgetYear'])
											 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
											 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
														 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 });
											 })
											 ->with([$budgetRelationName => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
	 										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		   ->where('Year', $budgetFormData['budgetYear'])
	 										 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
	 										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 													 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->whereHas('order', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('poCancelledYN', 0)
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		foreach ($pendingPoQry as $key => $value) {
			if ($fixedAssetFlag) {
				if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
					$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
				}
			} else {
				if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
					$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
				}
			}
		}

		$groups = collect($pendingPoQry)->groupBy('templateDetailID'); 

		$pendingPoQryData = $groups->map(function ($group) use ($budgetFormData){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()[$budgetFormData['glColumnName']],
		        'companySystemID' => $group->first()['companySystemID'],
		        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'localAmt' => $group->sum('localAmt'),
		        'rptAmt' => $group->sum('rptAmt'),
		    ];
		});


		$finalData = [];			
		foreach ($pendingPoQryData as $key => $value) {
			$finalData[] = $value;
		}

		return $finalData;
	}

	public static function consumedAmountQry($budgetFormData, $templateCategoryIDs, $glCodes = [])
	{
		$consumedAmount = BudgetConsumedData::selectRaw('SUM(consumedLocalAmount) AS ConsumedLocalAmount, SUM(consumedRptAmount) AS ConsumedRptAmount, chartOfAccountID, companySystemID, serviceLineSystemID, year')
						 		     ->where('year', $budgetFormData['budgetYear'])
						 		     ->where('companySystemID', $budgetFormData['companySystemID'])
						 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
									 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
									 })
									 ->whereHas('budget_detail',function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
									 	$query->where('companySystemID', $budgetFormData['companySystemID'])
									 		   ->where('Year', $budgetFormData['budgetYear'])
									 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
												 	$query->whereIn('templateDetailID', $templateCategoryIDs);
												 })
												 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
												 	$query->whereIn('chartOfAccountID', $glCodes);
												 })
									 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
												 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
												 })
									 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
												 	$query->where('companySystemID', $budgetFormData['companySystemID'])
												 		  ->where('approvedYN', -1)
												 		   ->where('Year', $budgetFormData['budgetYear'])
												 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
															 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
															 });
												 });
									 })
									 ->with(['budget_detail' => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
										 		   ->where('Year', $budgetFormData['budgetYear'])
										 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
													 	$query->whereIn('templateDetailID', $templateCategoryIDs);
													 })
													 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
													 	$query->whereIn('chartOfAccountID', $glCodes);
													 })
										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
													 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
													 })
										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
													 		  ->where('approvedYN', -1)
													 		   ->where('Year', $budgetFormData['budgetYear'])
													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																 });
													 });
										 }])
									 ->groupBy('chartOfAccountID')
									 ->get();



		foreach ($consumedAmount as $key => $value) {
			if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
				$value->templateDetailID = $value->budget_detail->templateDetailID;
			}
		}

		$groups = collect($consumedAmount)->groupBy('templateDetailID'); 

		$consumedAmountData = $groups->map(function ($group) {
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()['chartOfAccountID'],
		        'companySystemID' => $group->first()['companySystemID'],
		        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
		        'year' => $group->first()['year'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'ConsumedLocalAmount' => $group->sum('ConsumedLocalAmount'),
		        'ConsumedRptAmount' => $group->sum('ConsumedRptAmount'),
		    ];
		});


		$consumedDataFinal = [];			
		foreach ($consumedAmountData as $key => $value) {
			$consumedDataFinal[] = $value;
		}

		return $consumedDataFinal;
	}

	public static function budgetAmountQry($budgetFormData, $templateCategoryIDs, $glCodes = [])
	{
		return Budjetdetails::selectRaw('SUM(budjetAmtLocal) AS budgetLocalAmount, SUM(budjetAmtRpt) AS budgetRptAmount, budgetmasterID, companySystemID, serviceLineSystemID, templateDetailID, Year, chartOfAccountID')
										 ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
										 	$query->whereIn('templateDetailID', $templateCategoryIDs);
										 })
										 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
										 	$query->whereIn('chartOfAccountID', $glCodes);
										 })
										 ->whereHas('budget_master',function($query) use ($budgetFormData) {
										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
										 		  ->where('approvedYN', -1)
										 		   ->where('Year', $budgetFormData['budgetYear'])
										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
													 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
													 });
										 })
										 ->groupBy('templateDetailID')
										 ->get();
	}


	public static function documentAmountQry($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$documentAmount = [];
		switch ($budgetFormData['documentSystemID']) {
			case 1:
			case 50:
			case 51:
				$documentAmount = self::purchaseRequestDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);
				break;
			case 2:
			case 5:
			case 52:
				$documentAmount = self::purchaseOrderDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);
				break;
			
			default:
				# code...
				break;
		}


		return $documentAmount;
	}

	public static function purchaseRequestDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseRequestDetails::selectRaw('SUM(estimatedCost * quantityRequested) AS totalCost, purchaseRequestID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'])
											 ->whereHas($budgetRelationName,function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
											 	$query->where('companySystemID', $budgetFormData['companySystemID'])
											 		   ->where('Year', $budgetFormData['budgetYear'])
											 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
											 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
														 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 });
											 })
											 ->with([$budgetRelationName => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
	 										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		   ->where('Year', $budgetFormData['budgetYear'])
	 										 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
	 										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 													 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->where('purchaseRequestID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		foreach ($docAmountQry as $key => $value) {
			if ($fixedAssetFlag) {
				if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
					$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
				}
			} else {
				if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
					$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
				}
			}
		}

		$groups = collect($docAmountQry)->groupBy('templateDetailID'); 

		$pendingPoQryData = $groups->map(function ($group) {
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'budgetYear' => $group->first()['budgetYear'],
		        'companySystemID' => $group->first()['companySystemID'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'totalCost' => $group->sum('totalCost'),
		    ];
		});


		$finalData = [];			
		foreach ($pendingPoQryData as $key => $value) {
			$finalData[] = $value;
		}

		return $finalData;
	}

	public static function purchaseOrderDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitSupTransCur * noQty) AS totalCost, purchaseOrderMasterID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'])
											 ->whereHas($budgetRelationName,function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
											 	$query->where('companySystemID', $budgetFormData['companySystemID'])
											 		   ->where('Year', $budgetFormData['budgetYear'])
											 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
											 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
														 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 });
											 })
											 ->with([$budgetRelationName => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
	 										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		   ->where('Year', $budgetFormData['budgetYear'])
	 										 		   ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
	 										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 													 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->where('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->where('purchaseOrderMasterID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		foreach ($docAmountQry as $key => $value) {
			if ($fixedAssetFlag) {
				if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
					$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
				}
			} else {
				if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
					$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
				}
			}
		}

		$groups = collect($docAmountQry)->groupBy('templateDetailID'); 

		$pendingPoQryData = $groups->map(function ($group) {
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'budgetYear' => $group->first()['budgetYear'],
		        'companySystemID' => $group->first()['companySystemID'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'totalCost' => $group->sum('totalCost'),
		    ];
		});


		$finalData = [];			
		foreach ($pendingPoQryData as $key => $value) {
			$finalData[] = $value;
		}

		return $finalData;
	}
}
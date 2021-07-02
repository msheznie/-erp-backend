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
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\DirectPaymentDetails;

class BudgetConsumptionService
{
	public static function checkBudget($documentSystemID, $documentSystemCode)
	{
		$budgetData = self::getConsumptionData($documentSystemID, $documentSystemCode, true);

		if ($budgetData['status']) {
			if (sizeof($budgetData['data']) > 0) {
				$userMessageE = "";
				foreach ($budgetData['data'] as $key => $value) {
					$totalConsumedAmount = $value['consumedAmount'] + $value['currenctDocumentConsumption'] + $value['pendingDocumentAmount'];
					$totalBudgetRptAmount = $value['budgetAmount'];

					if ($totalConsumedAmount > $totalBudgetRptAmount) {
						if ($userMessageE != "") {
							$userMessageE .= "<br><br>";
						}
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

	public static function getConsumptionData($documentSystemID, $documentSystemCode, $checkBudgetWhileApprove = false)
	{
		$documentLevelCheckBudget = true;
		$directDocument = false;
		$budgetFormData = [];
		switch ($documentSystemID) {
			case 1:
			case 50:
			case 51:
				$masterData = PurchaseRequest::find($documentSystemCode);

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = ($masterData->checkBudgetYN == -1) ? true: false;
				$budgetFormData['financeCategory'] = $masterData->financeCategory;

				$budgetFormData['serviceLineSystemID'] = [$masterData->serviceLineSystemID];
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

				if ($masterData->poTypeID != 2 && $checkBudgetWhileApprove) {
					return ['status' => true, 'data' => []];
				}

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = true;
				$budgetFormData['financeCategory'] = $masterData->financeCategory;

				$budgetFormData['serviceLineSystemID'] = [$masterData->serviceLineSystemID];
                $budgetFormData['budgetYear'] = $masterData->budgetYear;
                $budgetFormData['currency'] = $masterData->supplierTransactionCurrencyID;

                $detailData = PurchaseOrderDetails::where('purchaseOrderMasterID', $documentSystemCode)
                								   ->get();

                $budgetFormData['financeGLcodePLSystemIDs'] = $detailData->pluck('financeGLcodePLSystemID')->toArray();
                $budgetFormData['financeGLcodebBSSystemIDs'] = $detailData->pluck('financeGLcodebBSSystemID')->toArray();
				break;
			case 11:
				$masterData = BookInvSuppMaster::find($documentSystemCode);

				if ($masterData->documentType != 1 && $checkBudgetWhileApprove) {
					return ['status' => true, 'data' => []];
				}

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = true;
				$budgetFormData['financeCategory'] = 0;

                $budgetFormData['currency'] = $masterData->supplierTransactionCurrencyID;

                $detailData = DirectInvoiceDetails::where('directInvoiceAutoID', $documentSystemCode)
                								   ->get();

                $budgetFormData['budgetYear'] = collect($detailData)->first()->budgetYear;

                $budgetFormData['financeGLcodePLSystemIDs'] = $detailData->pluck('chartOfAccountSystemID')->toArray();
                $budgetFormData['financeGLcodebBSSystemIDs'] = $detailData->pluck('chartOfAccountSystemID')->toArray();
                $budgetFormData['serviceLineSystemID'] = $detailData->pluck('serviceLineSystemID')->unique()->toArray();
                $directDocument = true;
				break;
			case 4:
				$masterData = PaySupplierInvoiceMaster::find($documentSystemCode);

				if ($masterData->invoiceType != 3 && $checkBudgetWhileApprove) {
					return ['status' => true, 'data' => []];
				}

				$budgetFormData['companySystemID'] = $masterData->companySystemID;
				$documentLevelCheckBudget = true;
				$budgetFormData['financeCategory'] = 0;

                $budgetFormData['currency'] = $masterData->supplierTransCurrencyID;

                $detailData = DirectPaymentDetails::where('directPaymentAutoID', $documentSystemCode)
                								   ->get();

                $budgetFormData['budgetYear'] = collect($detailData)->first()->budgetYear;

                $budgetFormData['financeGLcodePLSystemIDs'] = $detailData->pluck('chartOfAccountSystemID')->toArray();
                $budgetFormData['financeGLcodebBSSystemIDs'] = $detailData->pluck('chartOfAccountSystemID')->toArray();
                $budgetFormData['serviceLineSystemID'] = $detailData->pluck('serviceLineSystemID')->unique()->toArray();
                $directDocument = true;
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
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData, [], false, $directDocument);
	    		} else {
	    			$budgetData = self::budgetConsumptionByTemplate($budgetFormData, $budgetFormData['glCodes'], false, $directDocument);
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

	public static function budgetConsumptionByTemplate($budgetFormData, $glCodes = [], $fixedAssetFlag = false, $directDocument = false)
	{
		$checkBudgetConfiguration = Budjetdetails::with(['chart_of_account'])
														->whereIn('chartOfAccountID', $budgetFormData['glCodes'])
														 ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 })
														 ->groupBy('templateDetailID')
														 ->get();

		$templateCategoryIDs = [];

		if (count($checkBudgetConfiguration) > 0) {
			$templateCategoryIDs = $checkBudgetConfiguration->pluck('templateDetailID')->toArray();

			$budgetAmount = self::budgetAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);

			$consumedAmount = self::consumedAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);

			$pendingPoAmounts = self::pendingPoQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);
			
			// $pendingPrAmounts = self::pendingPurchaseRequestQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);
			$pendingPrAmounts = [];

			$pendingSupplierInvoiceAmounts = self::pendingSupplierInvoiceQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);

			$pendingPaymentVoucherAmounts = self::pendingPaymentVoucherQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);

			$documentAmount = self::documentAmountQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);

			$finalData = [];
			foreach ($templateCategoryIDs as $key => $value) {
				$templateDetail = ReportTemplateDetails::find($value);

				$budgetAmountData = collect($budgetAmount)->firstWhere('templateDetailID', $value);
				$consumedAmountData = collect($consumedAmount)->firstWhere('templateDetailID', $value);
				$pendingPoAmountsData = collect($pendingPoAmounts)->firstWhere('templateDetailID', $value);
				$pendingPrAmountsData = collect($pendingPrAmounts)->firstWhere('templateDetailID', $value);
				$documentAmountData = collect($documentAmount)->firstWhere('templateDetailID', $value);
				$pendingSupplierInvoiceAmountsData = collect($pendingSupplierInvoiceAmounts)->firstWhere('templateDetailID', $value);
				$pendingPaymentVoucherAmountsData = collect($pendingPaymentVoucherAmounts)->firstWhere('templateDetailID', $value);

				$currenctDocumentConsumption = 0;
				if (isset($documentAmountData['totalCost'])) {
					$currencyConversionRptAmount = Helper::currencyConversion($budgetFormData['companySystemID'], $budgetFormData['currency'], $budgetFormData['currency'], $documentAmountData['totalCost']);
					 $currenctDocumentConsumption = $currencyConversionRptAmount['reportingAmount'];
				}

				$budgetAmountValue = (isset($budgetAmountData->budgetRptAmount) ? $budgetAmountData->budgetRptAmount : 0);

				$totalBudgetRptAmount = (!$fixedAssetFlag) ?  ($budgetAmountValue * -1) : abs($budgetAmountValue);

				$pendingDocumentAmount = (isset($pendingPrAmountsData['rptAmt']) ? $pendingPrAmountsData['rptAmt'] : 0) + (isset($pendingPoAmountsData['rptAmt']) ? $pendingPoAmountsData['rptAmt'] : 0) + (isset($pendingSupplierInvoiceAmountsData['rptAmt']) ? $pendingSupplierInvoiceAmountsData['rptAmt'] : 0) + (isset($pendingPaymentVoucherAmountsData['rptAmt']) ? $pendingPaymentVoucherAmountsData['rptAmt'] : 0);

				$totalConsumedAmount = $currenctDocumentConsumption +  (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0) + $pendingDocumentAmount;

				$availableAmount = $totalBudgetRptAmount - $totalConsumedAmount;


				$finalData[$value]['templateCategory'] = (isset($templateDetail->description) ? $templateDetail->description : 0);
				$finalData[$value]['budgetAmount'] = $totalBudgetRptAmount;
				$finalData[$value]['currenctDocumentConsumption'] = $currenctDocumentConsumption;
				$finalData[$value]['availableAmount'] = $availableAmount;
				$finalData[$value]['consumedAmount'] = (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0);
				$finalData[$value]['pendingDocumentAmount'] = $pendingDocumentAmount;
				$finalData[$value]['templateDetailID'] = $value;
				$finalData[$value]['companyReportTemplateID'] = $templateDetail->companyReportTemplateID;
				$finalData[$value]['glCodes'] = $glCodes;

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


	public static function pendingPoQry($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag, $directDocument)
	{
		if ($directDocument) {
			return self::pendingPoQryValuesForDirectDocs($budgetFormData, $templateCategoryIDs, $glCodes = []);
		}
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, '.$budgetFormData['glColumnName'].', companySystemID, serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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

	public static function pendingSupplierInvoiceQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument)
	{
		$pendingSupInvQry = DirectInvoiceDetails::selectRaw('SUM(netAmountLocal) AS localAmt, SUM(netAmountRpt) AS rptAmt, chartOfAccountSystemID, companySystemID, serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->whereHas('supplier_invoice_master', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('cancelYN', 0)
	 										 		  ->where('documentType', 1)
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [11]), function($query) use ($budgetFormData) {
	 										 	$query->where('directInvoiceAutoID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->groupBy('chartOfAccountSystemID')
											 ->get();

		foreach ($pendingSupInvQry as $key => $value) {
			if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
				$value->templateDetailID = $value->budget_detail->templateDetailID;
			}
		}

		$groups = collect($pendingSupInvQry)->groupBy('templateDetailID'); 

		$pendingSupInvQryData = $groups->map(function ($group) use ($budgetFormData){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()['chartOfAccountSystemID'],
		        'companySystemID' => $group->first()['companySystemID'],
		        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'localAmt' => $group->sum('localAmt'),
		        'rptAmt' => $group->sum('rptAmt'),
		    ];
		});


		$finalData = [];			
		foreach ($pendingSupInvQryData as $key => $value) {
			$finalData[] = $value;
		}

		return $finalData;
	}

	public static function pendingPurchaseRequestQry($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$pendingPoQry = PurchaseRequestDetails::selectRaw('(estimatedCost * quantityRequested) AS transAmount, '.$budgetFormData['glColumnName'].', companySystemID, purchaseRequestID')
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }, 'purchase_request'])
	 										 ->whereHas('purchase_request', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('cancelledYN', 0)
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		  ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
													  })
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [1,50,51]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseRequestID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->get();

		foreach ($pendingPoQry as $key => $value) {
			$currencyConversionRptAmount = Helper::currencyConversion($value->companySystemID, $value->purchase_request->currency, $value->purchase_request->currency, $value->transAmount);
			$value->rptAmt = $currencyConversionRptAmount['reportingAmount'];
			$value->localAmt = $currencyConversionRptAmount['localAmount'];
		}

		$groupedPending = collect($pendingPoQry)->groupBy($budgetFormData['glColumnName'])->all();

		$finalPending = [];
		foreach ($groupedPending as $key => $value) {
			$temp['companySystemID'] = $value[0]['companySystemID'];
			$temp['serviceLineSystemID'] = $value[0]['purchase_request']['companySystemID'];
			$temp[$budgetRelationName] = $value[0][$budgetRelationName];
			$temp['localAmt'] = collect($value)->sum('localAmt');
			$temp['rptAmt'] = collect($value)->sum('rptAmt');
			$temp[$budgetFormData['glColumnName']] = $value[0][$budgetFormData['glColumnName']];

			$finalPending[] = $temp;
		}


		$finalPendingData = [];
		foreach ($finalPending as $key => $value) {
			if ($fixedAssetFlag) {
				if (isset($value['budget_detail_bs']) && !is_null($value['budget_detail_bs'])) {
					$value['templateDetailID'] = $value['budget_detail_bs']['templateDetailID'];
				}
			} else {
				if (isset($value['budget_detail_pl']) && !is_null($value['budget_detail_pl'])) {
					$value['templateDetailID'] = $value['budget_detail_pl']['templateDetailID'];
				}
			}
			$finalPendingData[] = $value;
		}

		$groups = collect($finalPendingData)->groupBy('templateDetailID'); 

		$pendingPoQryData = $groups->map(function ($group) use ($budgetFormData){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()[$budgetFormData['glColumnName']],
		        'companySystemID' => $group->first()['companySystemID'],
		        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
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

	public static function pendingPaymentVoucherQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument)
	{
		$pendingSupInvQry = DirectPaymentDetails::selectRaw('SUM(localAmount) AS localAmt, SUM(comRptAmount) AS rptAmt, chartOfAccountSystemID, companySystemID, serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->whereHas('master', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('cancelYN', 0)
	 										 		  ->where('invoiceType', 3)
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [4]), function($query) use ($budgetFormData) {
	 										 	$query->where('directPaymentAutoID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->groupBy('chartOfAccountSystemID')
											 ->get();

		foreach ($pendingSupInvQry as $key => $value) {
			if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
				$value->templateDetailID = $value->budget_detail->templateDetailID;
			}
		}

		$groups = collect($pendingSupInvQry)->groupBy('templateDetailID'); 

		$pendingSupInvQryData = $groups->map(function ($group) use ($budgetFormData){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()['chartOfAccountSystemID'],
		        'companySystemID' => $group->first()['companySystemID'],
		        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'localAmt' => $group->sum('localAmt'),
		        'rptAmt' => $group->sum('rptAmt'),
		    ];
		});


		$finalData = [];			
		foreach ($pendingSupInvQryData as $key => $value) {
			$finalData[] = $value;
		}

		return $finalData;
	}

	public static function pendingPoQryValuesForDirectDocs($budgetFormData, $templateCategoryIDs, $glCodes)
	{
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, financeGLcodePLSystemID, financeGLcodebBSSystemID, companySystemID, serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
											 })
											 ->with(['budget_detail_pl' => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
	 										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		   ->where('Year', $budgetFormData['budgetYear'])
	 										 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
	 										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }, 'budget_detail_bs' => function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
	 										 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		   ->where('Year', $budgetFormData['budgetYear'])
	 										 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
														 	$query->whereIn('templateDetailID', $templateCategoryIDs);
														 })
														 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
														 	$query->whereIn('chartOfAccountID', $glCodes);
														 })
	 										 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
											 ->where(function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
												 $query->whereHas('budget_detail_pl',function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
													 		   ->where('Year', $budgetFormData['budgetYear'])
													 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
																 	$query->whereIn('templateDetailID', $templateCategoryIDs);
																 })
																 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
																 	$query->whereIn('chartOfAccountID', $glCodes);
																 })
													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																 })
													 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
																 	$query->where('companySystemID', $budgetFormData['companySystemID'])
																 		  ->where('approvedYN', -1)
																 		   ->where('Year', $budgetFormData['budgetYear'])
																 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																			 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																			 });
																 });
													 })
												 	 ->orWhereHas('budget_detail_bs',function($query) use ($budgetFormData, $templateCategoryIDs, $glCodes) {
													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
													 		   ->where('Year', $budgetFormData['budgetYear'])
													 		    ->when(count($glCodes) == 0, function($query) use ($templateCategoryIDs) {
																 	$query->whereIn('templateDetailID', $templateCategoryIDs);
																 })
																 ->when(count($glCodes) > 0, function($query) use ($glCodes) {
																 	$query->whereIn('chartOfAccountID', $glCodes);
																 })
													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																 })
													 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
																 	$query->where('companySystemID', $budgetFormData['companySystemID'])
																 		  ->where('approvedYN', -1)
																 		   ->where('Year', $budgetFormData['budgetYear'])
																 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																			 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																			 });
																 });
													 });
											 })
	 										 ->whereHas('order', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('poCancelledYN', 0)
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->get();

		foreach ($pendingPoQry as $key => $value) {
			if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
				$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
			}

			if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
				$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
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
									 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
												 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
												 })
									 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
												 	$query->where('companySystemID', $budgetFormData['companySystemID'])
												 		  ->where('approvedYN', -1)
												 		   ->where('Year', $budgetFormData['budgetYear'])
												 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
															 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
													 })
										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
													 		  ->where('approvedYN', -1)
													 		   ->where('Year', $budgetFormData['budgetYear'])
													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
			case 11:
				$documentAmount = self::supplierInvoiceDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);
				break;
			case 4:
				$documentAmount = self::paymentVoucherDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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

	public static function supplierInvoiceDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$docAmountQry = DirectInvoiceDetails::selectRaw('SUM(netAmount) AS totalCost, directInvoiceAutoID, companySystemID, budgetYear,chartOfAccountSystemID')
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->where('directPaymentAutoID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 ->groupBy('chartOfAccountSystemID')
											 ->get();

		foreach ($docAmountQry as $key => $value) {
			if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
				$value->templateDetailID = $value->budget_detail->templateDetailID;
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

	public static function paymentVoucherDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$docAmountQry = DirectPaymentDetails::selectRaw('SUM(DPAmount) AS totalCost, directPaymentAutoID, companySystemID, budgetYear,chartOfAccountSystemID')
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 																 });
	 													 });
	 										 }])
	 										 ->where('directPaymentAutoID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 ->groupBy('chartOfAccountSystemID')
											 ->get();

		foreach ($docAmountQry as $key => $value) {
			if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
				$value->templateDetailID = $value->budget_detail->templateDetailID;
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
														 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
														 })
											 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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
	 													 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
	 													 })
	 										 		    ->whereHas('budget_master',function($query) use ($budgetFormData) {
	 													 	$query->where('companySystemID', $budgetFormData['companySystemID'])
	 													 		  ->where('approvedYN', -1)
	 													 		   ->where('Year', $budgetFormData['budgetYear'])
	 													 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
	 																 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
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

	public static function budgetCheckDocumentList($documentSystemID)
	{
		return (in_array($documentSystemID, [1, 50, 51, 2, 5, 52, 11, 4]) ? true : false);
	}

	public static function budgetBlockUpdateDocumentList($documentSystemID)
	{
		return (in_array($documentSystemID, [1, 50, 51, 2, 5, 52]) ? true : false);
	}

	public static function budgetConsumedDocumentList($documentSystemID)
	{
		return (in_array($documentSystemID, [2, 5, 52]) ? true : false);
	}

	public static function insertBudgetConsumedData($documentSystemID, $documentSystemCode)
	{
		$result = ['status' => true];
		switch ($documentSystemID) {
			case 2:
			case 5:
			case 52:
				$result = self::poBudgetConsumption($documentSystemCode);
				break;
			case 11:
				// $result = self::supplierInvoiceBudgetConsumption($documentSystemCode);
				break;
			case 4:
				// $result = self::paymentVoucherBudgetConsumption($documentSystemCode);
				break;
			default:
				return ['status' => false, 'message' => "Budget consumption is not set for this documnt"];
				break;
		}


		return $result;		
	}

	public static function poBudgetConsumption($documentSystemCode)
	{
		$poMaster = ProcumentOrder::selectRaw('MONTH(createdDateTime) as month, purchaseOrderCode,documentID,documentSystemID, financeCategory')->find($documentSystemCode);
		$budgetConsumeData = array();
        if ($poMaster->financeCategory == 3) {
            $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $documentSystemCode . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.budgetYear');
            if (!empty($poDetail)) {
                foreach ($poDetail as $value) {
                    $budgetConsumeData[] = array(
                        "companySystemID" => $value->companySystemID,
                        "companyID" => $value->companyID,
                        "serviceLineSystemID" => $value->serviceLineSystemID,
                        "serviceLineCode" => $value->serviceLineCode,
                        "documentSystemID" => $poMaster["documentSystemID"],
                        "documentID" => $poMaster["documentID"],
                        "documentSystemCode" => $documentSystemCode,
                        "documentCode" => $poMaster["purchaseOrderCode"],
                        "chartOfAccountID" => 9,
                        "GLCode" => 10000,
                        "year" => $value->budgetYear,
                        "month" => $poMaster["month"],
                        "consumedLocalCurrencyID" => $value->localCurrencyID,
                        "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                        "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                        "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                        "timestamp" => date('d/m/Y H:i:s A')
                    );
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        } else {
            $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*erp_purchaseorderdetails.noQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $documentSystemCode . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,erp_purchaseorderdetails.serviceLineSystemID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.budgetYear');
            if (!empty($poDetail)) {
                foreach ($poDetail as $value) {
                    if ($value->financeGLcodePLSystemID != "") {
                        $budgetConsumeData[] = array(
                            "companySystemID" => $value->companySystemID,
                            "companyID" => $value->companyID,
                            "serviceLineSystemID" => $value->serviceLineSystemID,
                            "serviceLineCode" => $value->serviceLineCode,
                            "documentSystemID" => $poMaster["documentSystemID"],
                            "documentID" => $poMaster["documentID"],
                            "documentSystemCode" => $documentSystemCode,
                            "documentCode" => $poMaster["purchaseOrderCode"],
                            "chartOfAccountID" => $value->financeGLcodePLSystemID,
                            "GLCode" => $value->financeGLcodePL,
                            "year" => $value->budgetYear,
                            "month" => $poMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrencyID,
                            "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                            "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                            "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                            "timestamp" => date('d/m/Y H:i:s A')
                        );
                    }
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        }

        return ['status' => true];
	}

	public static function supplierInvoiceBudgetConsumption($documentSystemCode)
	{
		$siMaster = BookInvSuppMaster::selectRaw('MONTH(createdDateAndTime) as month, bookingInvCode,documentID,documentSystemID, documentType')->find($documentSystemCode);
		$budgetConsumeData = array();
        if ($siMaster->documentType == 1) {
            $siDetail = \DB::select('SELECT SUM(erp_directinvoicedetails.netAmountLocal) as netAmountLocal,SUM(erp_directinvoicedetails.netAmountRpt) as netAmountRpt,erp_directinvoicedetails.comRptCurrency,erp_directinvoicedetails.chartOfAccountSystemID,erp_directinvoicedetails.glCode,erp_directinvoicedetails.companyID,erp_directinvoicedetails.companySystemID,erp_directinvoicedetails.serviceLineSystemID,erp_directinvoicedetails.serviceLineCode,erp_directinvoicedetails.budgetYear,erp_directinvoicedetails.localCurrency FROM erp_directinvoicedetails INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_directinvoicedetails.directInvoiceAutoID  WHERE erp_directinvoicedetails.directInvoiceAutoID = ' . $documentSystemCode . ' AND erp_bookinvsuppmaster.documentType = 1 GROUP BY erp_directinvoicedetails.companySystemID,erp_directinvoicedetails.serviceLineSystemID,erp_directinvoicedetails.chartOfAccountSystemID,erp_directinvoicedetails.budgetYear');
            if (!empty($siDetail)) {
                foreach ($siDetail as $value) {
                    if ($value->chartOfAccountSystemID != "") {
                        $budgetConsumeData[] = array(
                            "companySystemID" => $value->companySystemID,
                            "companyID" => $value->companyID,
                            "serviceLineSystemID" => $value->serviceLineSystemID,
                            "serviceLineCode" => $value->serviceLineCode,
                            "documentSystemID" => $siMaster["documentSystemID"],
                            "documentID" => $siMaster["documentID"],
                            "documentSystemCode" => $documentSystemCode,
                            "documentCode" => $siMaster["bookingInvCode"],
                            "chartOfAccountID" => $value->chartOfAccountSystemID,
                            "GLCode" => $value->glCode,
                            "year" => $value->budgetYear,
                            "month" => $siMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrency,
                            "consumedLocalAmount" => $value->netAmountLocal,
                            "consumedRptCurrencyID" => $value->comRptCurrency,
                            "consumedRptAmount" => $value->netAmountRpt,
                            "timestamp" => date('d/m/Y H:i:s A')
                        );
                    }
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        }

        return ['status' => true];
	}

	public static function paymentVoucherBudgetConsumption($documentSystemCode)
	{
		$siMaster = PaySupplierInvoiceMaster::selectRaw('MONTH(createdDateTime) as month, BPVcode,documentID,documentSystemID, invoiceType')->find($documentSystemCode);
		$budgetConsumeData = array();
        if ($siMaster->invoiceType == 3) {
            $siDetail = \DB::select('SELECT SUM(erp_directpaymentdetails.localAmount) as localAmount,SUM(erp_directpaymentdetails.comRptAmount) as comRptAmount,erp_directpaymentdetails.comRptCurrency,erp_directpaymentdetails.chartOfAccountSystemID,erp_directpaymentdetails.glCode,erp_directpaymentdetails.companyID,erp_directpaymentdetails.companySystemID,erp_directpaymentdetails.serviceLineSystemID,erp_directpaymentdetails.serviceLineCode,erp_directpaymentdetails.budgetYear,erp_directpaymentdetails.localCurrency FROM erp_directpaymentdetails INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_directpaymentdetails.directPaymentAutoID  WHERE erp_directpaymentdetails.directPaymentAutoID = ' . $documentSystemCode . ' AND erp_paysupplierinvoicemaster.invoiceType = 3 GROUP BY erp_directpaymentdetails.companySystemID,erp_directpaymentdetails.serviceLineSystemID,erp_directpaymentdetails.chartOfAccountSystemID,erp_directpaymentdetails.budgetYear');
            if (!empty($siDetail)) {
                foreach ($siDetail as $value) {
                    if ($value->chartOfAccountSystemID != "") {
                        $budgetConsumeData[] = array(
                            "companySystemID" => $value->companySystemID,
                            "companyID" => $value->companyID,
                            "serviceLineSystemID" => $value->serviceLineSystemID,
                            "serviceLineCode" => $value->serviceLineCode,
                            "documentSystemID" => $siMaster["documentSystemID"],
                            "documentID" => $siMaster["documentID"],
                            "documentSystemCode" => $documentSystemCode,
                            "documentCode" => $siMaster["BPVcode"],
                            "chartOfAccountID" => $value->chartOfAccountSystemID,
                            "GLCode" => $value->glCode,
                            "year" => $value->budgetYear,
                            "month" => $siMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrency,
                            "consumedLocalAmount" => $value->localAmount,
                            "consumedRptCurrencyID" => $value->comRptCurrency,
                            "consumedRptAmount" => $value->comRptAmount,
                            "timestamp" => date('d/m/Y H:i:s A')
                        );
                    }
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        }

        return ['status' => true];
	}
}
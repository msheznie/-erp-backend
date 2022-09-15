<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\Budjetdetails;
use App\Models\Company;
use App\Models\BudgetDetailHistory;
use App\Models\CurrencyMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\BudgetConsumedData;
use App\Models\PurchaseRequest;
use App\Models\CompanyFinanceYear;
use App\Models\PurchaseRequestDetails;
use App\Models\PurchaseOrderDetails;
use App\Models\ReportTemplateDetails;
use App\Models\ProcumentOrder;
use App\Models\BookInvSuppMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\ProjectGlDetail;
use App\Models\SegmentAllocatedItem;
use App\Models\PaySupplierInvoiceMaster;
use App\Models\DirectPaymentDetails;
use App\Models\ErpProjectMaster;
use App\Models\ChartOfAccount;
use App\Models\SegmentMaster;

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

					if ($totalConsumedAmount > $totalBudgetRptAmount &&  $value['currenctDocumentConsumption'] > 0) {
						if ($userMessageE != "") {
							$userMessageE .= "<br><br>";
						}

						$budgetExceMsg = "";
						if (isset($budgetData['projectBased']) && $budgetData['projectBased']) {
							$budgetExceMsg = "Budget Exceeded Project : ";
						} else if (isset($budgetData['checkBudgetBasedOnGLPolicy']) && $budgetData['checkBudgetBasedOnGLPolicy']) {
							$budgetExceMsg = "Budget Exceeded GL Account : ";
						} else {
							$budgetExceMsg = "Budget Exceeded Category : ";
						}


						$userMessageE .= $budgetExceMsg. $value['templateCategory'] ;
                        $userMessageE .= "<br>";

                        if (isset($budgetData['departmentWiseCheckBudgetPolicy']) && $budgetData['departmentWiseCheckBudgetPolicy'] && (!isset($budgetData['projectBased']) || (isset($budgetData['projectBased']) && !$budgetData['projectBased']))) {
                        	$userMessageE .= "Segment : ". $value['serviceLine'] ;
                        	$userMessageE .= "<br>";
                        }
                        
                        if (isset($budgetData['checkBudgetBasedOnGLPolicyProject']) && $budgetData['checkBudgetBasedOnGLPolicyProject'] &&  (isset($budgetData['projectBased']) && $budgetData['projectBased'])) {
                        	$userMessageE .= "GL Account : ". $value['serviceLine'] ;
                        	$userMessageE .= "<br>";
                        }

                        $currencyDecimal = isset($budgetData['rptCurrency']) ? $budgetData['rptCurrency']['DecimalPlaces'] : 2;

                        $userMessageE .= "Budget Amount : " . round($totalBudgetRptAmount, $currencyDecimal) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Document Amount : " . round($value['currenctDocumentConsumption'], $currencyDecimal) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Consumed Amount : " . round($value['consumedAmount'], $currencyDecimal) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Pending Document Amount : " . round($value['pendingDocumentAmount'], $currencyDecimal) ;
                        $userMessageE .= "<br>";
                        $userMessageE .= "Total Consumed Amount : " . round($totalConsumedAmount, $currencyDecimal);
					}
				}

				if (isset($budgetData['validateArray']) && count($budgetData['validateArray']) > 0) {
					$userMessageE .= "<br>";
					$userMessageE .= "<br>";
					$userMessageE .= "Budget not configured for below GL codes";
					$userMessageE .= "<br>";
					foreach ($budgetData['validateArray'] as $key => $value) {
						$userMessageE .= $value;
						$userMessageE .= "<br>";
					}
				}

				return ['status' => true, 'message' => $userMessageE];
			} else {
				if (isset($budgetData['budgetCheckPolicy']) && $budgetData['budgetCheckPolicy'] && (isset($budgetData['glCodes']) && count($budgetData['glCodes']) > 0)) {
					return ['status' => true, 'message' => "Budget not configured for this document"];
				} else {
					return ['status' => true, 'message' => ""];
				}
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
				$budgetFormData['projectID'] = null;

				$budgetFormData['serviceLineSystemID'] = [$masterData->serviceLineSystemID];
                $budgetFormData['budgetYear'] = $masterData->budgetYear;
                $budgetFormData['currency'] = $masterData->currency;

                $detailData = PurchaseRequestDetails::where('purchaseRequestID', $documentSystemCode)
                								   ->get();


                $segmentAllocationData = SegmentAllocatedItem::where('documentMasterAutoID', $documentSystemCode)
                											 ->where('documentSystemID', $documentSystemID)
                											 ->groupBy('serviceLineSystemID')
                											 ->get();


                $budgetFormData['serviceLineSystemID'] = (count($segmentAllocationData) > 0) ? $segmentAllocationData->pluck('serviceLineSystemID')->toArray() : [$masterData->serviceLineSystemID];
                $budgetFormData['financeGLcodePLSystemIDs'] = array_filter($detailData->pluck('financeGLcodePLSystemID')->toArray());
                $budgetFormData['financeGLcodebBSSystemIDs'] = array_filter($detailData->pluck('financeGLcodebBSSystemID')->toArray());
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
				$budgetFormData['projectID'] = $masterData->projectID;

				$budgetFormData['serviceLineSystemID'] = [$masterData->serviceLineSystemID];
                $budgetFormData['budgetYear'] = $masterData->budgetYear;
                $budgetFormData['currency'] = $masterData->supplierTransactionCurrencyID;

                $detailData = PurchaseOrderDetails::where('purchaseOrderMasterID', $documentSystemCode)
                								   ->get();

                $segmentAllocationData = SegmentAllocatedItem::where('documentMasterAutoID', $documentSystemCode)
                											 ->where('documentSystemID', $documentSystemID)
                											 ->groupBy('serviceLineSystemID')
                											 ->get();


                $budgetFormData['serviceLineSystemID'] = (count($segmentAllocationData) > 0) ? $segmentAllocationData->pluck('serviceLineSystemID')->toArray() : [$masterData->serviceLineSystemID];

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
				$budgetFormData['projectID'] = null;

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
				$budgetFormData['projectID'] = null;

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

		$company = Company::where('companySystemID', $budgetFormData['companySystemID'])->first();

        $rptCurrency = CurrencyMaster::where('currencyID', $company->reportingCurrency)->first();


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
	    $budgetFormData['glCodes'] = [];
	    $budgetData = [];
	    $validateArray = [];

	    $budgetCheckPolicy = false;
	    if ($checkBudget && $checkBudget->isYesNO == 1 && (!is_null($budgetFormData['projectID']) && $budgetFormData['projectID'] != 0))
	    {
	    	$budgetCheckPolicy = true;
	    	$checkBudgetOnGlCode = CompanyPolicyMaster::where('companyPolicyCategoryID', 59)
						                                    ->where('companySystemID', $budgetFormData['companySystemID'])
						                                    ->first();

			$checkBudgetBasedOnGLPolicyProject = false;
		    if ($checkBudgetOnGlCode && $checkBudgetOnGlCode->isYesNO == 1) {
		    	$checkBudgetBasedOnGLPolicyProject = true;
		    }
		    $budgetFormData['checkBudgetBasedOnGLPolicyProject'] = $checkBudgetBasedOnGLPolicyProject;
		    if ($budgetFormData['financeCategory'] != 3) {
	    		$budgetFormData['glCodes'] = $budgetFormData['financeGLcodePLSystemIDs'];
	    		$budgetFormData['glColumnName'] = "financeGLcodePLSystemID";
	    	} else {
	    		$budgetFormData['glCodes'] = $budgetFormData['financeGLcodebBSSystemIDs'];
	    		$budgetFormData['glColumnName'] = "financeGLcodebBSSystemID";
	    	}
	    	$budgetData = self::budgetConsumptionByProject($budgetFormData);
	    	return ['status' => true, 'data' => $budgetData, 'projectBased' => true, 'budgetCheckPolicy' => $budgetCheckPolicy, 'checkBudgetBasedOnGLPolicy' => $checkBudgetBasedOnGLPolicy, 'departmentWiseCheckBudgetPolicy' => $departmentWiseCheckBudgetPolicy, 'checkBudgetBasedOnGLPolicyProject' => $checkBudgetBasedOnGLPolicyProject, 'rptCurrency' => $rptCurrency, 'budgetmasterIDs' => []];
	    }

	    if ($checkBudget && $checkBudget->isYesNO == 1 && $documentLevelCheckBudget && !is_null($budgetFormData['financeCategory'])) {
	    	$budgetCheckPolicy = true;
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

	    	$validateArray = self::validateBudget($budgetFormData);
	    }

	    return ['status' => true, 'data' => (isset($budgetData['finalResData']) ? $budgetData['finalResData'] : []), 'budgetCheckPolicy' => $budgetCheckPolicy, 'validateArray' => $validateArray, 'checkBudgetBasedOnGLPolicy' => $checkBudgetBasedOnGLPolicy, 'departmentWiseCheckBudgetPolicy' => $departmentWiseCheckBudgetPolicy, 'glCodes' => $budgetFormData['glCodes'], 'rptCurrency' => $rptCurrency, 'budgetmasterIDs' => (isset($budgetData['budgetmasterIDs']) ? $budgetData['budgetmasterIDs'] : [])];
	}

	public static function budgetConsumptionByProject($budgetFormData)
	{
		$projectData = ErpProjectMaster::find($budgetFormData['projectID']);
		$finalData = [];
		if ($projectData) {
			$projectBudget = ProjectGlDetail::selectRaw('SUM(amount) AS projectBudgetAmount, projectID, chartOfAccountSystemID')
												  ->where('projectID', $budgetFormData['projectID'])
												  ->when($budgetFormData['checkBudgetBasedOnGLPolicyProject'] == true, function($query) use ($budgetFormData) {
												  	$query->whereIn('chartOfAccountSystemID', $budgetFormData['glCodes'])
												  		  ->groupBy('chartOfAccountSystemID');
												  })
												  ->get();
			
			$consumedAmount = self::consumedProjectBudgetAmountQry($budgetFormData);

			$pendingPoAmounts = self::pendingProjectPoQry($budgetFormData);
			
			$documentAmount = self::documentAmountQryOfProjectBasedPo($budgetFormData);


			if ($budgetFormData['checkBudgetBasedOnGLPolicyProject']) {
				foreach ($budgetFormData['glCodes'] as $key => $value) {
					$chartOfAcData = ChartOfAccount::find($value);

					$budgetAmountData = collect($projectBudget)->firstWhere('chartOfAccountSystemID', $value);
					$consumedAmountData = collect($consumedAmount)->firstWhere('chartOfAccountID', $value);
					$pendingPoAmountsData = collect($pendingPoAmounts)->firstWhere('chartOfAccountID', $value);
					$documentAmountData = collect($documentAmount)->firstWhere('chartOfAccountID', $value);

					$projectBudgetAmount = isset($budgetAmountData->projectBudgetAmount) ? $budgetAmountData->projectBudgetAmount : 0;

					$currencyConversionRptAmount = Helper::currencyConversion($budgetFormData['companySystemID'], $projectData->projectCurrencyID, $projectData->projectCurrencyID, $projectBudgetAmount);
					$budgetRptAmount = $currencyConversionRptAmount['reportingAmount'];
					$budgetLocalAmount = $currencyConversionRptAmount['localAmount'];
					
					$finalData[$value]['budgetAmount'] = $budgetRptAmount;
					$finalData[$value]['templateCategory'] = ($projectData) ? $projectData->description : "";
					$finalData[$value]['serviceLine'] = ($chartOfAcData) ? $chartOfAcData->AccountCode.' - '.$chartOfAcData->AccountDescription : "";
					$finalData[$value]['currenctDocumentConsumption'] = (isset($documentAmountData->rptAmt) && $documentAmountData->rptAmt > 0) ? $documentAmountData->rptAmt : 0;
					$finalData[$value]['pendingDocumentAmount'] = (isset($pendingPoAmountsData->rptAmt) && $pendingPoAmountsData->rptAmt > 0) ? $pendingPoAmountsData->rptAmt : 0;
					$finalData[$value]['consumedAmount'] = ($consumedAmountData) ? $consumedAmountData->ConsumedRptAmount : 0;

					$totalConsumedAmount = $finalData[$value]['currenctDocumentConsumption'] +  $finalData[$value]['consumedAmount'] + $finalData[$value]['pendingDocumentAmount'];

					$finalData[$value]['availableAmount'] = $finalData[$value]['budgetAmount'] - $totalConsumedAmount;
				}


				$finalResData = [];
				foreach ($finalData as $key => $value) {
					$finalResData[] = $value;
				}

				return $finalResData;
			} else {

				$budgetAmountData = collect($projectBudget)->first();
				$consumedAmountData = collect($consumedAmount)->first();
				$pendingPoAmountsData = collect($pendingPoAmounts)->first();
				$documentAmountData = collect($documentAmount)->first();

				$projectBudgetAmount = isset($budgetAmountData->projectBudgetAmount) ? $budgetAmountData->projectBudgetAmount : 0;

				$currencyConversionRptAmount = Helper::currencyConversion($budgetFormData['companySystemID'], $projectData->projectCurrencyID, $projectData->projectCurrencyID, $projectBudgetAmount);
				$budgetRptAmount = $currencyConversionRptAmount['reportingAmount'];
				$budgetLocalAmount = $currencyConversionRptAmount['localAmount'];
				
				$finalData['budgetAmount'] = $budgetRptAmount;
				$finalData['templateCategory'] = ($projectData) ? $projectData->description : "";
				$finalData['currenctDocumentConsumption'] = (isset($documentAmountData->rptAmt) && $documentAmountData->rptAmt > 0) ? $documentAmountData->rptAmt : 0;
				$finalData['pendingDocumentAmount'] = (isset($pendingPoAmountsData->rptAmt) && $pendingPoAmountsData->rptAmt > 0) ? $pendingPoAmountsData->rptAmt : 0;
				$finalData['consumedAmount'] = ($consumedAmountData) ? $consumedAmountData->ConsumedRptAmount : 0;

				$totalConsumedAmount = $finalData['currenctDocumentConsumption'] +  $finalData['consumedAmount'] + $finalData['pendingDocumentAmount'];

				$finalData['availableAmount'] = $finalData['budgetAmount'] - $totalConsumedAmount;

				return [$finalData];
			}

		} else {
			return [];
		}
	}

	public static function validateBudget($budgetFormData)
	{
		$invalidArray = [];
		foreach ($budgetFormData['glCodes'] as $key => $value) {
			$checkBudgetConfiguration = self::checkChartAccountBudgetStatus($value, $budgetFormData);
			if (!$checkBudgetConfiguration) {
				$chartOfAccount = ChartOfAccount::find($value);
				if ($chartOfAccount) {
					$invalidArray[] = $chartOfAccount->AccountCode." - ".$chartOfAccount->AccountDescription;
				}
			}
		}

		return $invalidArray;
	}

	public static function checkChartAccountBudgetStatus($chartOfAccountID, $budgetFormData)
	{
		return Budjetdetails::where('chartOfAccountID', $chartOfAccountID)
													 ->whereHas('budget_master',function($query) use ($budgetFormData) {
														 	$query->where('companySystemID', $budgetFormData['companySystemID'])
														 		  ->where('approvedYN', -1)
														 		   ->where('Year', $budgetFormData['budgetYear'])
														 		   ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
																	 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID']);
																	 });
														 })
														 ->groupBy('templateDetailID')
														 ->first();
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
		$budgetmasterIDs = [];

		if (count($checkBudgetConfiguration) > 0) {
			$templateCategoryIDs = $checkBudgetConfiguration->pluck('templateDetailID')->toArray();

			$budgetAmount = self::budgetAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);
			$budgetmasterIDs = collect($budgetAmount)->pluck('budgetmasterID')->toArray();

			$consumedAmount = self::consumedAmountQry($budgetFormData, $templateCategoryIDs, $glCodes);

			$pendingPoAmounts = self::pendingPoQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);
			
			// $pendingPrAmounts = self::pendingPurchaseRequestQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);
			$pendingPrAmounts = [];

			$pendingSupplierInvoiceAmounts = self::pendingSupplierInvoiceQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);

			$pendingPaymentVoucherAmounts = self::pendingPaymentVoucherQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);

			$documentAmount = self::documentAmountQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);

			$finalData = [];

			if (count($glCodes) == 0) {
				if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
					foreach ($templateCategoryIDs as $key => $value) {
						foreach ($budgetFormData['serviceLineSystemID'] as $keyServ => $serviceLineSystemID) {
							$templateDetail = ReportTemplateDetails::find($value);
							$segmentData = SegmentMaster::find($serviceLineSystemID);

							$budgetAmountData = collect($budgetAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$consumedAmountData = collect($consumedAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$pendingPoAmountsData = collect($pendingPoAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$pendingPrAmountsData = collect($pendingPrAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$documentAmountData = collect($documentAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$pendingSupplierInvoiceAmountsData = collect($pendingSupplierInvoiceAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();
							$pendingPaymentVoucherAmountsData = collect($pendingPaymentVoucherAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('templateDetailID', $value)->first();

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


							$finalData[$value.$serviceLineSystemID]['templateCategory'] = (isset($templateDetail->description) ? $templateDetail->description : 0);
							$finalData[$value.$serviceLineSystemID]['serviceLine'] = ($segmentData) ? $segmentData->ServiceLineCode.' - '.$segmentData->ServiceLineDes : "";
							$finalData[$value.$serviceLineSystemID]['budgetAmount'] = $totalBudgetRptAmount;
							$finalData[$value.$serviceLineSystemID]['currenctDocumentConsumption'] = $currenctDocumentConsumption;
							$finalData[$value.$serviceLineSystemID]['availableAmount'] = $availableAmount;
							$finalData[$value.$serviceLineSystemID]['consumedAmount'] = (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0);
							$finalData[$value.$serviceLineSystemID]['pendingDocumentAmount'] = $pendingDocumentAmount;
							$finalData[$value.$serviceLineSystemID]['templateDetailID'] = $value;
							$finalData[$value.$serviceLineSystemID]['companyReportTemplateID'] = $templateDetail->companyReportTemplateID;
							$finalData[$value.$serviceLineSystemID]['serviceLineSystemID'] = $serviceLineSystemID;
							$finalData[$value.$serviceLineSystemID]['glCodes'] = $glCodes;
						}

					}
				} else {
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
				}
			} else {
				if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
					foreach ($glCodes as $key => $value) {
						$templateStatus = self::checkChartAccountBudgetStatus($value, $budgetFormData);
						if ($templateStatus) {
							$templateDetail = ReportTemplateDetails::find($templateStatus->templateDetailID);
							foreach ($budgetFormData['serviceLineSystemID'] as $keyServ => $serviceLineSystemID) {
								$chartOfAcData = ChartOfAccount::find($value);
								$segmentData = SegmentMaster::find($serviceLineSystemID);

								$budgetAmountData = collect($budgetAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$consumedAmountData = collect($consumedAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$pendingPoAmountsData = collect($pendingPoAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$pendingPrAmountsData = collect($pendingPrAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$documentAmountData = collect($documentAmount)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$pendingSupplierInvoiceAmountsData = collect($pendingSupplierInvoiceAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();
								$pendingPaymentVoucherAmountsData = collect($pendingPaymentVoucherAmounts)->where('serviceLineSystemID', $serviceLineSystemID)->where('chartOfAccountID', $value)->first();

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


								$finalData[$value.$serviceLineSystemID]['templateCategory'] = ($chartOfAcData) ? $chartOfAcData->AccountCode.' - '.$chartOfAcData->AccountDescription : "";
								$finalData[$value.$serviceLineSystemID]['serviceLine'] = ($segmentData) ? $segmentData->ServiceLineCode.' - '.$segmentData->ServiceLineDes : "";
								$finalData[$value.$serviceLineSystemID]['budgetAmount'] = $totalBudgetRptAmount;
								$finalData[$value.$serviceLineSystemID]['currenctDocumentConsumption'] = $currenctDocumentConsumption;
								$finalData[$value.$serviceLineSystemID]['availableAmount'] = $availableAmount;
								$finalData[$value.$serviceLineSystemID]['consumedAmount'] = (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0);
								$finalData[$value.$serviceLineSystemID]['pendingDocumentAmount'] = $pendingDocumentAmount;
								$finalData[$value.$serviceLineSystemID]['templateDetailID'] = $value;
								$finalData[$value.$serviceLineSystemID]['companyReportTemplateID'] = $templateDetail->companyReportTemplateID;
								$finalData[$value.$serviceLineSystemID]['serviceLineSystemID'] = $serviceLineSystemID;
								$finalData[$value.$serviceLineSystemID]['glCodes'] = $glCodes;
							}
						}
					}
				} else {
					foreach ($glCodes as $key => $value) {
						$templateStatus = self::checkChartAccountBudgetStatus($value, $budgetFormData);
						if ($templateStatus) {
							$templateDetail = ReportTemplateDetails::find($templateStatus->templateDetailID);
							$chartOfAcData = ChartOfAccount::find($value);

							$budgetAmountData = collect($budgetAmount)->firstWhere('chartOfAccountID', $value);
							$consumedAmountData = collect($consumedAmount)->firstWhere('chartOfAccountID', $value);
							$pendingPoAmountsData = collect($pendingPoAmounts)->firstWhere('chartOfAccountID', $value);
							$pendingPrAmountsData = collect($pendingPrAmounts)->firstWhere('chartOfAccountID', $value);
							$documentAmountData = collect($documentAmount)->firstWhere('chartOfAccountID', $value);
							$pendingSupplierInvoiceAmountsData = collect($pendingSupplierInvoiceAmounts)->firstWhere('chartOfAccountID', $value);
							$pendingPaymentVoucherAmountsData = collect($pendingPaymentVoucherAmounts)->firstWhere('chartOfAccountID', $value);

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


							$finalData[$value]['templateCategory'] = ($chartOfAcData) ? $chartOfAcData->AccountCode.' - '.$chartOfAcData->AccountDescription : "";
							$finalData[$value]['budgetAmount'] = $totalBudgetRptAmount;
							$finalData[$value]['currenctDocumentConsumption'] = $currenctDocumentConsumption;
							$finalData[$value]['availableAmount'] = $availableAmount;
							$finalData[$value]['consumedAmount'] = (isset($consumedAmountData['ConsumedRptAmount']) ? $consumedAmountData['ConsumedRptAmount'] : 0);
							$finalData[$value]['pendingDocumentAmount'] = $pendingDocumentAmount;
							$finalData[$value]['templateDetailID'] = $value;
							$finalData[$value]['companyReportTemplateID'] = $templateDetail->companyReportTemplateID;
							$finalData[$value]['glCodes'] = $glCodes;
						}
					}

				}

			}


			$finalResData = [];
			foreach ($finalData as $key => $value) {
				$finalResData[] = $value;
			}

			return ['finalResData' => $finalResData, 'budgetmasterIDs' => $budgetmasterIDs];

		} else {
			return ['finalResData' => [], 'budgetmasterIDs' => []];
		}
	}


	public static function pendingPoQry($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag, $directDocument)
	{

		if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
			return self::pendingPoQryDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument);
		}

		if ($directDocument) {
			return self::pendingPoQryValuesForDirectDocs($budgetFormData, $templateCategoryIDs, $glCodes);
		}
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, '.$budgetFormData['glColumnName'].', companySystemID, '.$budgetFormData['glColumnName'].' as chartOfAccountID,serviceLineSystemID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereHas('allocations', function($query) use ($budgetFormData) {
											 				$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
												 	});
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
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear'])
	 										 		  ->where(function($query) {
													 	$query->whereNull('projectID')
													 		  ->orWhere('projectID', 0);
													  });
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		if (count($glCodes) == 0) {
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
		} else {
			return $pendingPoQry;
		}
	}

	public static function pendingProjectPoQry($budgetFormData)
	{
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, companySystemID, serviceLineSystemID,'.$budgetFormData["glColumnName"].' as chartOfAccountID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
	 										 ->whereHas('order', function($query) use ($budgetFormData) {
	 										 	$query->where('approved', 0)
	 										 		  ->where('poCancelledYN', 0)
	 										 		  ->where('projectID', $budgetFormData['projectID'])
	 										 		  ->where('companySystemID', $budgetFormData['companySystemID'])
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear']);
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
	 										 ->when(($budgetFormData['checkBudgetBasedOnGLPolicyProject'] == true), function($query) use ($budgetFormData) {
									 				$query->whereIn($budgetFormData['glColumnName'], $budgetFormData['glCodes'])
									 					  ->groupBy($budgetFormData['glColumnName']);
											 })
											 ->get();

		return $pendingPoQry;
	}

	public static function documentAmountQryOfProjectBasedPo($budgetFormData)
	{
		$pendingPoQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitLocalCur * noQty) AS localAmt, SUM(GRVcostPerUnitComRptCur * noQty) AS rptAmt, companySystemID, serviceLineSystemID,'.$budgetFormData["glColumnName"].' as chartOfAccountID')
								 		     ->when(($budgetFormData['checkBudgetBasedOnGLPolicyProject'] == true), function($query) use ($budgetFormData) {
									 				$query->whereIn($budgetFormData['glColumnName'], $budgetFormData['glCodes'])
									 					  ->groupBy($budgetFormData['glColumnName']);
											 })
	 										 ->where('purchaseOrderMasterID',$budgetFormData['documentSystemCode'])
											 ->get();
		return $pendingPoQry;
	}

	public static function pendingSupplierInvoiceQry($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag, $directDocument)
	{
		$pendingSupInvQry = DirectInvoiceDetails::selectRaw('SUM(netAmountLocal) AS localAmt, SUM(netAmountRpt) AS rptAmt, chartOfAccountSystemID, companySystemID, serviceLineSystemID, chartOfAccountSystemID as chartOfAccountID')
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
	 										 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID', 'serviceLineSystemID');
											 })
											  ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == false), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID');
											 })
											 ->get();

		if (count($glCodes) == 0) {
			foreach ($pendingSupInvQry as $key => $value) {
				if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
					$value->templateDetailID = $value->budget_detail->templateDetailID;
				}
			}

			if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
				$groups = collect($pendingSupInvQry)->groupBy(function ($item, $key) {
					                    return $item['templateDetailID'].$item['serviceLineSystemID'];
					                });
				
			} else {
				$groups = collect($pendingSupInvQry)->groupBy('templateDetailID');
			}


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
		} else {
			return $pendingSupInvQry;
		}

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
		$pendingSupInvQry = DirectPaymentDetails::selectRaw('SUM(localAmount) AS localAmt, SUM(comRptAmount) AS rptAmt, chartOfAccountSystemID, companySystemID, serviceLineSystemID, chartOfAccountSystemID as chartOfAccountID')
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
	 										 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID', 'serviceLineSystemID');
											 })
											  ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == false), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID');
											 })
											 ->get();

		if (count($glCodes) == 0) {
			foreach ($pendingSupInvQry as $key => $value) {
				if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
					$value->templateDetailID = $value->budget_detail->templateDetailID;
				}
			}

			if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
				$groups = collect($pendingSupInvQry)->groupBy(function ($item, $key) {
					                    return $item['templateDetailID'].$item['serviceLineSystemID'];
					                });
				
			} else {
				$groups = collect($pendingSupInvQry)->groupBy('templateDetailID');
			}

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
		} else {
			return $pendingSupInvQry;
		}

	}

	public static function pendingPoQryValuesForDirectDocs($budgetFormData, $templateCategoryIDs, $glCodes)
	{
		$pendingPoQry = PurchaseOrderDetails::selectRaw('(GRVcostPerUnitLocalCur * noQty) AS localAmt, (GRVcostPerUnitComRptCur * noQty) AS rptAmt, financeGLcodePLSystemID, financeGLcodebBSSystemID, companySystemID, serviceLineSystemID, purchaseOrderMasterID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereHas('allocations', function($query) use ($budgetFormData) {
											 				$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
												 	});
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
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear'])
	 										 		  ->where(function($query) {
													 	 $query->whereNull('projectID')
													 		  ->orWhere('projectID', 0);
													  });
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->get();

		foreach ($pendingPoQry as $key => $value) {
			if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
				$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
				$value->chartOfAccountIDGrp = $value->financeGLcodebBSSystemID;
			}

			if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
				$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
				$value->chartOfAccountIDGrp = $value->financeGLcodePLSystemID;
			}
		}
		if (count($glCodes) == 0) {
			$groups = collect($pendingPoQry)->groupBy('templateDetailID'); 
		} else {
			$groups = collect($pendingPoQry)->groupBy('chartOfAccountIDGrp'); 
		}

		$pendingPoQryData = $groups->map(function ($group) use ($budgetFormData, $glCodes){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => (count($glCodes) == 0) ? $group->first()[$budgetFormData['glColumnName']] : $group->first()['chartOfAccountIDGrp'],
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
									 ->where(function($query) {
									 	$query->whereNull('projectID')
									 		  ->orWhere('projectID', 0);
									 })
									 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
									 	$query->groupBy('chartOfAccountID', 'serviceLineSystemID');
									 })
									 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == false), function($query) use ($budgetFormData) {
									 	$query->groupBy('chartOfAccountID');
									 })
									 ->get();

		if (count($glCodes) == 0) {
			foreach ($consumedAmount as $key => $value) {
				if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
					$value->templateDetailID = $value->budget_detail->templateDetailID;
				}
			}


			if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
				$groups = collect($consumedAmount)->groupBy(function ($item, $key) {
					                    return $item['templateDetailID'].$item['serviceLineSystemID'];
					                });
				
			} else {
				$groups = collect($consumedAmount)->groupBy('templateDetailID');
			}


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
		} else {
			return $consumedAmount;
		}
	}

	public static function consumedProjectBudgetAmountQry($budgetFormData)
	{
		$consumedAmount = BudgetConsumedData::selectRaw('SUM(consumedLocalAmount) AS ConsumedLocalAmount, SUM(consumedRptAmount) AS ConsumedRptAmount, chartOfAccountID, companySystemID, serviceLineSystemID, year, projectID')
						 		     ->where('year', $budgetFormData['budgetYear'])
						 		     ->where('companySystemID', $budgetFormData['companySystemID'])
						 		     ->where('projectID', $budgetFormData['projectID'])
									 ->when(($budgetFormData['checkBudgetBasedOnGLPolicyProject'] == true), function($query) use ($budgetFormData) {
									 	$query->whereIn('chartOfAccountID', $budgetFormData['glCodes'])
									 		 ->groupBy('chartOfAccountID');
									 })
									 ->when(($budgetFormData['checkBudgetBasedOnGLPolicyProject'] == false), function($query) use ($budgetFormData) {
									 	$query->groupBy('projectID');
									 })
									 ->get();

		return $consumedAmount;
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
										 ->when(count($glCodes) > 0 && $budgetFormData['departmentWiseCheckBudgetPolicy'] == true, function($query) use ($glCodes) {
										 	$query->groupBy('chartOfAccountID', 'serviceLineSystemID');
										 })
										 ->when(count($glCodes) > 0 && $budgetFormData['departmentWiseCheckBudgetPolicy'] == false, function($query) use ($glCodes) {
										 	$query->groupBy('chartOfAccountID');
										 })
										 ->when(count($glCodes) == 0 && $budgetFormData['departmentWiseCheckBudgetPolicy'] == true, function($query) use ($glCodes) {
										 	$query->groupBy('templateDetailID', 'serviceLineSystemID');
										 })
										 ->when(count($glCodes) == 0 && $budgetFormData['departmentWiseCheckBudgetPolicy'] == false, function($query) use ($glCodes) {
										 	$query->groupBy('templateDetailID');
										 })
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
		if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
			return self::purchaseRequestDocumentAmountByTemplateDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);	
		}

		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseRequestDetails::selectRaw('SUM(estimatedCost * quantityRequested) AS totalCost, purchaseRequestID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'].','.$budgetFormData['glColumnName'] .' as chartOfAccountID')
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

		if (count($glCodes) == 0) {
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
		} else {
			return $docAmountQry;
		}

	}

	public static function supplierInvoiceDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$docAmountQry = DirectInvoiceDetails::selectRaw('SUM(netAmount) AS totalCost, directInvoiceAutoID, companySystemID, budgetYear,chartOfAccountSystemID, chartOfAccountSystemID as chartOfAccountID,serviceLineSystemID')
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
	 										 ->where('directInvoiceAutoID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
	 										 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID', 'serviceLineSystemID');
											 })
											  ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == false), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID');
											 })
											 ->get();

		if (count($glCodes) == 0) {
			foreach ($docAmountQry as $key => $value) {
				if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
					$value->templateDetailID = $value->budget_detail->templateDetailID;
				}
			}

			if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
				$groups = collect($docAmountQry)->groupBy(function ($item, $key) {
					                    return $item['templateDetailID'].$item['serviceLineSystemID'];
					                });
				
			} else {
				$groups = collect($docAmountQry)->groupBy('templateDetailID');
			}

			$pendingPoQryData = $groups->map(function ($group) {
			    return [
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'totalCost' => $group->sum('totalCost'),
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;
		} else {
			return $docAmountQry;
		}

	}

	public static function paymentVoucherDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$docAmountQry = DirectPaymentDetails::selectRaw('SUM(DPAmount) AS totalCost, directPaymentAutoID, companySystemID, budgetYear,chartOfAccountSystemID, chartOfAccountSystemID as chartOfAccountID, serviceLineSystemID')
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
											 ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID', 'serviceLineSystemID');
											 })
											  ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == false), function($query) use ($budgetFormData) {
											 	$query->groupBy('chartOfAccountSystemID');
											 })
											 ->get();

		if (count($glCodes) == 0) {
			foreach ($docAmountQry as $key => $value) {
				if (isset($value->budget_detail) && !is_null($value->budget_detail)) {
					$value->templateDetailID = $value->budget_detail->templateDetailID;
				}
			}

			if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
				$groups = collect($docAmountQry)->groupBy(function ($item, $key) {
					                    return $item['templateDetailID'].$item['serviceLineSystemID'];
					                });
				
			} else {
				$groups = collect($docAmountQry)->groupBy('templateDetailID');
			}

			$pendingPoQryData = $groups->map(function ($group) {
			    return [
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'totalCost' => $group->sum('totalCost'),
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;
		} else {
			return $docAmountQry;
		}
	}

	public static function purchaseOrderDocumentAmountByTemplate($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		if ($budgetFormData['departmentWiseCheckBudgetPolicy']) {
			return self::purchaseOrderDocumentAmountByTemplateDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes, $fixedAssetFlag);
		}

		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseOrderDetails::selectRaw('SUM(GRVcostPerUnitSupTransCur * noQty) AS totalCost, purchaseOrderMasterID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'].','.$budgetFormData['glColumnName'].' as chartOfAccountID')
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

		if (count($glCodes) == 0) {
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
		} else {
			return $docAmountQry;
		}

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
            $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*segment_allocated_items.allocatedQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*segment_allocated_items.allocatedQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,serviceline.serviceLineSystemID,serviceline.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID, erp_purchaseordermaster.projectID, erp_purchaseorderdetails.detail_project_id FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID  INNER JOIN segment_allocated_items ON erp_purchaseordermaster.documentSystemID = segment_allocated_items.documentSystemID AND erp_purchaseorderdetails.purchaseOrderDetailsID = segment_allocated_items.documentDetailAutoID INNER JOIN serviceline ON serviceline.serviceLineSystemID = segment_allocated_items.serviceLineSystemID WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $documentSystemCode . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,segment_allocated_items.serviceLineSystemID,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.detail_project_id');
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
                        "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $value->companySystemID),
                        "month" => $poMaster["month"],
                        "consumedLocalCurrencyID" => $value->localCurrencyID,
                        "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                        "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                        "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                        "projectID" => $value->detail_project_id,
                        "timestamp" => date('d/m/Y H:i:s A')
                    );
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        } else {
            $poDetail = \DB::select('SELECT SUM(erp_purchaseorderdetails.GRVcostPerUnitLocalCur*segment_allocated_items.allocatedQty) as GRVcostPerUnitLocalCur,SUM(erp_purchaseorderdetails.GRVcostPerUnitComRptCur*segment_allocated_items.allocatedQty) as GRVcostPerUnitComRptCur,erp_purchaseorderdetails.companyReportingCurrencyID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.financeGLcodePL,erp_purchaseorderdetails.companyID,erp_purchaseorderdetails.companySystemID,serviceline.serviceLineSystemID,serviceline.serviceLineCode,erp_purchaseordermaster.budgetYear,erp_purchaseorderdetails.localCurrencyID, erp_purchaseordermaster.projectID, erp_purchaseorderdetails.detail_project_id FROM erp_purchaseorderdetails INNER JOIN erp_purchaseordermaster ON erp_purchaseordermaster.purchaseOrderID = erp_purchaseorderdetails.purchaseOrderMasterID INNER JOIN segment_allocated_items ON erp_purchaseordermaster.documentSystemID = segment_allocated_items.documentSystemID AND erp_purchaseorderdetails.purchaseOrderDetailsID = segment_allocated_items.documentDetailAutoID INNER JOIN serviceline ON serviceline.serviceLineSystemID = segment_allocated_items.serviceLineSystemID WHERE erp_purchaseorderdetails.purchaseOrderMasterID = ' . $documentSystemCode . ' AND erp_purchaseordermaster.poType_N IN(1,2,3,4,5) GROUP BY erp_purchaseorderdetails.companySystemID,segment_allocated_items.serviceLineSystemID,erp_purchaseorderdetails.financeGLcodePLSystemID,erp_purchaseorderdetails.budgetYear,erp_purchaseorderdetails.detail_project_id');
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
                            "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $value->companySystemID),
                            "month" => $poMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrencyID,
                            "consumedLocalAmount" => $value->GRVcostPerUnitLocalCur,
                            "consumedRptCurrencyID" => $value->companyReportingCurrencyID,
                            "consumedRptAmount" => $value->GRVcostPerUnitComRptCur,
                            "projectID" => $value->detail_project_id,
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
            $siDetail = \DB::select('SELECT SUM(erp_directinvoicedetails.netAmountLocal) as netAmountLocal,SUM(erp_directinvoicedetails.netAmountRpt) as netAmountRpt,erp_directinvoicedetails.comRptCurrency,erp_directinvoicedetails.chartOfAccountSystemID,erp_directinvoicedetails.glCode,erp_directinvoicedetails.companyID,erp_directinvoicedetails.companySystemID,erp_directinvoicedetails.serviceLineSystemID,erp_directinvoicedetails.serviceLineCode,erp_directinvoicedetails.budgetYear,erp_directinvoicedetails.localCurrency,erp_directinvoicedetails.detail_project_id FROM erp_directinvoicedetails INNER JOIN erp_bookinvsuppmaster ON erp_bookinvsuppmaster.bookingSuppMasInvAutoID = erp_directinvoicedetails.directInvoiceAutoID  WHERE erp_directinvoicedetails.directInvoiceAutoID = ' . $documentSystemCode . ' AND erp_bookinvsuppmaster.documentType = 1 GROUP BY erp_directinvoicedetails.companySystemID,erp_directinvoicedetails.serviceLineSystemID,erp_directinvoicedetails.chartOfAccountSystemID,erp_directinvoicedetails.budgetYear,erp_directinvoicedetails.detail_project_id');
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
                            "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $value->companySystemID),
                            "month" => $siMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrency,
                            "consumedLocalAmount" => $value->netAmountLocal,
                            "consumedRptCurrencyID" => $value->comRptCurrency,
                            "consumedRptAmount" => $value->netAmountRpt,
                            "projectID" => $value->detail_project_id,
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
            $siDetail = \DB::select('SELECT SUM(erp_directpaymentdetails.localAmount) as localAmount,SUM(erp_directpaymentdetails.comRptAmount) as comRptAmount,erp_directpaymentdetails.comRptCurrency,erp_directpaymentdetails.chartOfAccountSystemID,erp_directpaymentdetails.glCode,erp_directpaymentdetails.companyID,erp_directpaymentdetails.companySystemID,erp_directpaymentdetails.serviceLineSystemID,erp_directpaymentdetails.serviceLineCode,erp_directpaymentdetails.budgetYear,erp_directpaymentdetails.localCurrency,erp_directpaymentdetails.detail_project_id FROM erp_directpaymentdetails INNER JOIN erp_paysupplierinvoicemaster ON erp_paysupplierinvoicemaster.PayMasterAutoId = erp_directpaymentdetails.directPaymentAutoID  WHERE erp_directpaymentdetails.directPaymentAutoID = ' . $documentSystemCode . ' AND erp_paysupplierinvoicemaster.invoiceType = 3 GROUP BY erp_directpaymentdetails.companySystemID,erp_directpaymentdetails.serviceLineSystemID,erp_directpaymentdetails.chartOfAccountSystemID,erp_directpaymentdetails.budgetYear,erp_directpaymentdetails.detail_project_id');
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
                            "companyFinanceYearID" => CompanyFinanceYear::financeYearID($value->budgetYear, $value->companySystemID),
                            "month" => $siMaster["month"],
                            "consumedLocalCurrencyID" => $value->localCurrency,
                            "consumedLocalAmount" => $value->localAmount,
                            "consumedRptCurrencyID" => $value->comRptCurrency,
                            "consumedRptAmount" => $value->comRptAmount,
                            "projectID" => $value->detail_project_id,
                            "timestamp" => date('d/m/Y H:i:s A')
                        );
                    }
                }
                $budgetConsume = BudgetConsumedData::insert($budgetConsumeData);
            }
        }

        return ['status' => true];
	}


	public static function pendingPoQryDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag, $directDocument)
	{
		if ($directDocument) {
			return self::pendingPoQryValuesForDirectDocsDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes);
		}
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$pendingPoQry = PurchaseOrderDetails::selectRaw('GRVcostPerUnitLocalCur AS localAmt, GRVcostPerUnitComRptCur AS rptAmt, '.$budgetFormData['glColumnName'].', companySystemID, '.$budgetFormData['glColumnName'].' as chartOfAccountID,serviceLineSystemID, purchaseOrderDetailsID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereHas('allocations', function($query) use ($budgetFormData) {
											 				$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
												 	})
											 		->with(['allocations' => function($query) use ($budgetFormData) {
											 					$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  	  ->whereIn('documentSystemID', [2,5,52]);
									 					  }]);
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
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear'])
	 										 		  ->where(function($query) {
													 	$query->whereNull('projectID')
													 		  ->orWhere('projectID', 0);
													  });
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 // ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		if (count($glCodes) == 0) {
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

			$pendingData = [];
			foreach ($pendingPoQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['localAmt'] = $value->localAmt * $value1->allocatedQty;
					$temp['rptAmt'] = $value->rptAmt * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['templateDetailID'] = $value->templateDetailID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;

					$pendingData[] = $temp;
				}
			}


			$groups = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['templateDetailID'].$item['serviceLineSystemID'];
				                });

			$pendingPoQryData = $groups->map(function ($group) use ($budgetFormData){
			    return [
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'chartOfAccountID' => $group->first()['chartOfAccountID'],
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
		} else {
			$pendingData = [];
			foreach ($pendingPoQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['localAmt'] = $value->localAmt * $value1->allocatedQty;
					$temp['rptAmt'] = $value->rptAmt * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;

					$pendingData[] = $temp;
				}
			}

			$grouped = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['chartOfAccountID'].$item['serviceLineSystemID'];
				                });

			$pendingPoQryData = $grouped->map(function ($group) use ($budgetFormData){
			    return [
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'chartOfAccountID' => $group->first()['chartOfAccountID'],
			        'companySystemID' => $group->first()['companySystemID'],
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
	}

	public static function purchaseRequestDocumentAmountByTemplateDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseRequestDetails::selectRaw('estimatedCost, purchaseRequestID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'].','.$budgetFormData['glColumnName'] .' as chartOfAccountID, purchaseRequestDetailsID')
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
	 										 ->with(['allocations' => function ($query) use ($budgetFormData){
	 										 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [1,50,51]);
	 										 }])
	 										 ->where('purchaseRequestID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 // ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		if (count($glCodes) == 0) {
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

			$pendingData = [];
			foreach ($docAmountQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['totalCost'] = $value->estimatedCost * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;
					$temp['budgetYear'] = $value->budgetYear;
					$temp['templateDetailID'] = $value->templateDetailID;

					$pendingData[] = $temp;
				}
			}

			$groups = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['templateDetailID'].$item['serviceLineSystemID'];
				                });


			$pendingPoQryData = $groups->map(function ($group) {
			    return [
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'totalCost' => $group->sum('totalCost'),
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;
		} else {
			$pendingData = [];
			foreach ($docAmountQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['totalCost'] = $value->estimatedCost * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;
					$temp['budgetYear'] = $value->budgetYear;

					$pendingData[] = $temp;
				}
			}

			$grouped = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['chartOfAccountID'].$item['serviceLineSystemID'];
				                });

			$pendingPoQryData = $grouped->map(function ($group) use ($budgetFormData){
			    return [
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'chartOfAccountID' => $group->first()['chartOfAccountID'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'totalCost' => $group->sum('totalCost')
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;

		}
	}

	public static function purchaseOrderDocumentAmountByTemplateDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes = [], $fixedAssetFlag)
	{
		$budgetRelationName = ($fixedAssetFlag) ? 'budget_detail_bs' : 'budget_detail_pl';
		$docAmountQry = PurchaseOrderDetails::selectRaw('GRVcostPerUnitSupTransCur, purchaseOrderMasterID, companySystemID, budgetYear,'.$budgetFormData['glColumnName'].','.$budgetFormData['glColumnName'].' as chartOfAccountID, purchaseOrderDetailsID')
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
	 										 ->with(['allocations' => function ($query) use ($budgetFormData){
	 										 	$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
	 										 }])
	 										 ->where('purchaseOrderMasterID', $budgetFormData['documentSystemCode'])
	 										 ->where('budgetYear', $budgetFormData['budgetYear'])
											 // ->groupBy($budgetFormData['glColumnName'])
											 ->get();

		if (count($glCodes) == 0) {
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

			$pendingData = [];
			foreach ($docAmountQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['totalCost'] = $value->GRVcostPerUnitSupTransCur * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;
					$temp['budgetYear'] = $value->budgetYear;
					$temp['templateDetailID'] = $value->templateDetailID;

					$pendingData[] = $temp;
				}
			}

			$groups = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['templateDetailID'].$item['serviceLineSystemID'];
				                });


			$pendingPoQryData = $groups->map(function ($group) {
			    return [
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'templateDetailID' => $group->first()['templateDetailID'],
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'totalCost' => $group->sum('totalCost'),
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;
		} else {
			$pendingData = [];
			foreach ($docAmountQry as $key => $value) {
				foreach ($value->allocations as $key1 => $value1) {
					$temp = [];
					$temp['totalCost'] = $value->GRVcostPerUnitSupTransCur * $value1->allocatedQty;
					$temp['chartOfAccountID'] = $value->chartOfAccountID;
					$temp['companySystemID'] = $value->companySystemID;
					$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;
					$temp['budgetYear'] = $value->budgetYear;

					$pendingData[] = $temp;
				}
			}

			$grouped = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['chartOfAccountID'].$item['serviceLineSystemID'];
				                });

			$pendingPoQryData = $grouped->map(function ($group) use ($budgetFormData){
			    return [
			        'serviceLineSystemID' => $group->first()['serviceLineSystemID'],
			        'chartOfAccountID' => $group->first()['chartOfAccountID'],
			        'companySystemID' => $group->first()['companySystemID'],
			        'budgetYear' => $group->first()['budgetYear'],
			        'totalCost' => $group->sum('totalCost')
			    ];
			});


			$finalData = [];			
			foreach ($pendingPoQryData as $key => $value) {
				$finalData[] = $value;
			}

			return $finalData;
		}

	}

	public static function pendingPoQryValuesForDirectDocsDepartmentWise($budgetFormData, $templateCategoryIDs, $glCodes)
	{
		$pendingPoQry = PurchaseOrderDetails::selectRaw('GRVcostPerUnitLocalCur AS localAmt, GRVcostPerUnitComRptCur AS rptAmt, financeGLcodePLSystemID, financeGLcodebBSSystemID, companySystemID, serviceLineSystemID, purchaseOrderDetailsID')
								 		     ->where('companySystemID', $budgetFormData['companySystemID'])
								 		     ->when(($budgetFormData['departmentWiseCheckBudgetPolicy'] == true), function($query) use ($budgetFormData) {
											 	$query->whereHas('allocations', function($query) use ($budgetFormData) {
											 				$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
												 	})
											 		->with(['allocations' => function($query) use ($budgetFormData) {
											 				$query->whereIn('serviceLineSystemID', $budgetFormData['serviceLineSystemID'])
											 					  ->whereIn('documentSystemID', [2,5,52]);
												 	}]);
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
	 										 		  ->where('budgetYear', $budgetFormData['budgetYear'])
	 										 		  ->where(function($query) {
													 	 $query->whereNull('projectID')
													 		  ->orWhere('projectID', 0);
													  });
	 										 })
	 										 ->when(in_array($budgetFormData['documentSystemID'], [2,5,52]), function($query) use ($budgetFormData) {
	 										 	$query->where('purchaseOrderMasterID', '!=' ,$budgetFormData['documentSystemCode']);
	 										 })
											 ->get();

		foreach ($pendingPoQry as $key => $value) {
			if (isset($value->budget_detail_bs) && !is_null($value->budget_detail_bs)) {
				$value->templateDetailID = $value->budget_detail_bs->templateDetailID;
				$value->chartOfAccountIDGrp = $value->financeGLcodebBSSystemID;
			}

			if (isset($value->budget_detail_pl) && !is_null($value->budget_detail_pl)) {
				$value->templateDetailID = $value->budget_detail_pl->templateDetailID;
				$value->chartOfAccountIDGrp = $value->financeGLcodePLSystemID;
			}
		}

		$pendingData = [];
		foreach ($pendingPoQry as $key => $value) {
			foreach ($value->allocations as $key1 => $value1) {
				$temp = [];
				$temp['localAmt'] = $value->localAmt * $value1->allocatedQty;
				$temp['rptAmt'] = $value->rptAmt * $value1->allocatedQty;
				$temp['chartOfAccountIDGrp'] = $value->chartOfAccountIDGrp;
				$temp['companySystemID'] = $value->companySystemID;
				$temp['templateDetailID'] = $value->templateDetailID;
				$temp['serviceLineSystemID'] = $value1->serviceLineSystemID;

				$pendingData[] = $temp;
			}
		}


		if (count($glCodes) == 0) {
			$groups = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['templateDetailID'].$item['serviceLineSystemID'];
				                });
		} else {
			$groups = collect($pendingData)->groupBy(function ($item, $key) {
				                    return $item['chartOfAccountIDGrp'].$item['serviceLineSystemID'];
				                }); 
		}

		$pendingPoQryData = $groups->map(function ($group) use ($budgetFormData, $glCodes){
		    return [
		        'templateDetailID' => $group->first()['templateDetailID'],
		        'chartOfAccountID' => $group->first()['chartOfAccountIDGrp'],
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

	public static function getCommitedConsumedAmount($detail, $DLBCPolicy, $departmentsWiseCheck = false)
    {
        $consumedAmountOfPO = BudgetConsumedData::with(['purchase_order' => function ($query) {
                                                $query->where('grvRecieved', '!=', 2);
                                            }])
                                            ->where('consumeYN', -1)
                                            ->where('companySystemID', $detail->companySystemID)
                                            ->when(($DLBCPolicy || $departmentsWiseCheck), function($query) use ($detail){
                                            	$query->where('serviceLineSystemID', $detail->serviceLineSystemID);
                                            })
                                            ->where(function($query) {
                                            	$query->where('projectID', 0)
                                            		  ->orWhereNull('projectID');
                                            })
                                            ->where('chartOfAccountID', $detail->chartOfAccountID)
                                            ->where('companyFinanceYearID', $detail->companyFinanceYearID)
                                            ->where('documentSystemID', 2)
                                            ->whereHas('purchase_order', function ($query) {
                                                $query->where('grvRecieved', '!=', 2);
                                            })
                                            ->get();

        $committedAmount = 0;
        $partiallyReceivedAmount = 0;
        foreach ($consumedAmountOfPO as $key => $value) {
            if (isset($value->purchase_order->grvRecieved) && $value->purchase_order->grvRecieved == 0) {
                $committedAmount += $value->consumedRptAmount;
            } else {
                if (isset($value->purchase_order->financeCategory) && $value->purchase_order->financeCategory != 3) {
                    $glColumnName = 'financeGLcodePLSystemID';
                } else {
                    $glColumnName = 'financeGLcodebBSSystemID';
                }

                $notRecivedPo = PurchaseOrderDetails::selectRaw('SUM((GRVcostPerUnitSupTransCur * segment_allocated_items.allocatedQty) - (GRVcostPerUnitSupTransCur * receivedQty)) as remainingAmount, SUM(GRVcostPerUnitSupTransCur * receivedQty) as receivedAmount')
                                                    ->where($glColumnName, $detail->chartOfAccountID)
                                                    ->where('purchaseOrderMasterID', $value->documentSystemCode)
                                                    ->join('segment_allocated_items', 'documentDetailAutoID', '=', 'purchaseOrderDetailsID')
                                                    ->when(($DLBCPolicy || $departmentsWiseCheck), function($query) use ($detail) {
		                                            	$query->where('segment_allocated_items.serviceLineSystemID', $detail->serviceLineSystemID);
		                                            })
		                                            ->whereHas('order', function($query) {
		                                            	$query->where(function($query) {
				                                            	$query->where('projectID', 0)
				                                            		  ->orWhereNull('projectID');
				                                            });
		                                            })
                                                    ->where('segment_allocated_items.documentSystemID', $value->documentSystemID)
                                                    ->groupBy('purchaseOrderMasterID')
                                                    ->first();

                if ($notRecivedPo) {
                    $currencyConversionRptAmount = \Helper::currencyConversion($detail->companySystemID, $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->remainingAmount);
                    $committedAmount += $currencyConversionRptAmount['reportingAmount'];


                    $currencyConversionRptAmountRec = \Helper::currencyConversion($detail->companySystemID, $value->purchase_order->supplierTransactionCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $notRecivedPo->receivedAmount);
                    $partiallyReceivedAmount += $currencyConversionRptAmountRec['reportingAmount'];
                }
            }
        }

        $actuallConsumptionAmount = $detail->consumed_amount - $committedAmount;

        $pendingSupplierInvoiceAmount = DirectInvoiceDetails::where('companySystemID', $detail->companySystemID)
                                                            ->when(($DLBCPolicy || $departmentsWiseCheck), function($query) use ($detail) {
				                                            	$query->where('serviceLineSystemID', $detail->serviceLineSystemID);
				                                            })
                                                            ->where('chartOfAccountSystemID', $detail->chartOfAccountID)
                                                            ->whereHas('supplier_invoice_master', function($query) use ($detail) {
                                                                $query->where('approved', 0)
                                                                      ->where('cancelYN', 0)
                                                                      ->where('documentType', 1)
                                                                      ->where('companySystemID', $detail->companySystemID);
                                                             })
                                                            ->sum('netAmountRpt');

        $pendingPvAmount = DirectPaymentDetails::where('companySystemID', $detail->companySystemID)
                                                ->when(($DLBCPolicy || $departmentsWiseCheck), function($query) use ($detail) {
	                                            	$query->where('serviceLineSystemID', $detail->serviceLineSystemID);
	                                            })
                                                ->where('chartOfAccountSystemID', $detail->chartOfAccountID)
                                                ->whereHas('master', function($query) use ($detail) {
                                                    $query->where('approved', 0)
                                                          ->where('cancelYN', 0)
                                                          ->where('invoiceType', 3)
                                                          ->where('companySystemID', $detail->companySystemID);
                                                 })
                                                ->sum('comRptAmount');
        
        $pendingDocumentAmount = $detail->pending_po_amount + $pendingSupplierInvoiceAmount + $pendingPvAmount;

        return ['pendingDocumentAmount' => $pendingDocumentAmount, 'actuallConsumptionAmount' => $actuallConsumptionAmount, 'committedAmount' => $committedAmount];

    }

    public static function getBudgetIdsByConsumption($documentSystemID, $documentSystemCode)
    {
    	$budgetConsumeData = BudgetConsumedData::with(['budget_master'])
    										   ->whereHas('budget_master')
    										   ->where('documentSystemID', $documentSystemID)
    										   ->where('documentSystemCode', $documentSystemCode)
    										   ->get();

    	$budgetIds = [];
    	if (count($budgetConsumeData) > 0) {
    		foreach ($budgetConsumeData as $key => $value) {
    			$budgetIds[] = ($value->budget_master) ? $value->budget_master->budgetmasterID : 0;
    		}
    	}

    	return ['budgetmasterIDs' => ((count($budgetIds) > 0)  ? array_unique($budgetIds) : [])];
    }
}
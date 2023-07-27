<?php

namespace App\helper;

use App\helper\Helper;
use App\Models\Company;
use App\Models\CompanyPolicyMaster;
use App\Models\ERPAssetTransfer;
use App\Models\ErpItemLedger;
use App\Models\FinanceItemcategorySubAssigned;
use App\Models\GRVDetails;
use App\Models\ItemAssigned;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\PurchaseRequest;
use App\Models\PurchaseRequestDetails;
use App\Models\SegmentAllocatedItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
class AssetTransferService
{
	
	public static function generatePRForAssetTransfer($input)
	{
		if ($input['type'] == 1) {
			$companyID = $input['company_id'];
			$id = Auth::id();
			$user = User::with(['employee'])->where('id', $id)->first();

			$lastSerial = PurchaseRequest::where('companySystemID', $companyID)
				->where('documentSystemID', 1)
				->orderBy('purchaseRequestID', 'desc')
				->first();

			$lastSerialNumber = 1;
			if ($lastSerial) {
				$lastSerialNumber = intval($lastSerial->serialNumber) + 1;
			}


			$checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
				->where('companySystemID', $companyID)
				->first();

			$checkBudgetYN = 0;
			if ($checkBudget) {
				$checkBudgetYN = $checkBudget->isYesNO;
			}

			$data['serialNumber'] = $lastSerialNumber;
			$company = Company::where('companySystemID', $companyID)->first();
			if ($company) {
				$data['companyID'] = $company->CompanyID;
			}
			$allocateItemToSegment = CompanyPolicyMaster::where('companyPolicyCategoryID', 57)
            ->where('companySystemID', $companyID)
            ->first();

        if ($allocateItemToSegment && $allocateItemToSegment->isYesNO == 1) {
            $data['allocateItemToSegment'] = 1;
        }
			$data['companySystemID'] = $companyID;
			$data['createdPcID'] = gethostname();
			$data['createdUserID'] = $user->employee['empID'];
			$data['createdUserSystemID'] = $user->employee['employeeSystemID'];
			$data['PRRequestedDate'] = now();
			$data['departmentID'] = 'PROC';
			$data['serviceLineCode'] = $input['serviceLineCode'];
			$data['serviceLineSystemID'] = $input['serviceLineSystemID'];
			$data['documentSystemID'] = 1;
			$data['documentID'] = 'PR';
			$data['comments'] =   $input['narration'];
			$data['location'] =  $input['location'];
			$data['priority'] =  1;
			$data['PRRequestedDate'] =  $input['document_date'];
			$data['currency'] =  $company->localCurrencyID;
			$data['financeCategory'] =  3;
			$data['docRefNo'] =  $input['document_code'];

			if ($checkBudgetYN == 1) {
				$data['budgetYear'] =  $input['budgetYear'];
				$data['prBelongsYear'] =  $input['prBelongsYear'];
			}
			$code = str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);
			$data['purchaseRequestCode'] = $data['companyID'] . '\\' . $data['departmentID'] . '\\' . $data['serviceLineCode'] . '\\' . $data['documentID'] . $code;
			$purchaseRequestMaster = PurchaseRequest::create($data);

 			$query = \DB::select("SELECT erp_fa_fa_asset_transfer_details.*, IFNULL(COUNT(erp_fa_fa_asset_transfer_details.id),0) as qtyRequested, 
			erp_fa_fa_asset_request_details.itemCodeSystem,
			erp_fa_fa_asset_request_details.comment
			FROM `erp_fa_fa_asset_transfer_details`
			JOIN erp_fa_fa_asset_request_details ON erp_fa_fa_asset_request_details.id = erp_fa_fa_asset_transfer_details.erp_fa_fa_asset_request_detail_id 
			WHERE erp_fa_fa_asset_transfer_id = {$input['id']} AND fa_master_id = 0 AND erp_fa_fa_asset_transfer_details.company_id = $companyID GROUP BY erp_fa_fa_asset_transfer_details.itemCodeSystem"); 
		
			if (!empty($query)) {
				foreach ($query as $val) {

					$purchaseRequest  = PurchaseRequest::where('purchaseRequestID', $purchaseRequestMaster->purchaseRequestID)
						->first();

					$item = ItemAssigned::where('itemCodeSystem', $val->itemCodeSystem)
						->where('companySystemID', $companyID)
						->first();

					$financeItemCategorySubAssigned = FinanceItemcategorySubAssigned::where('companySystemID', $item->companySystemID)
						->where('mainItemCategoryID', $item->financeCategoryMaster)
						->where('itemCategorySubID', $item->financeCategorySub)
						->first();

					if (empty($financeItemCategorySubAssigned)) {
						return ['status' => false, 'message' => 'Finance category not assigned for the selected item.'];
					}

					if ($item->financeCategoryMaster == 1) {

						$alreadyAdded = PurchaseRequest::where('purchaseRequestID', $purchaseRequestMaster->purchaseRequestID)
							->whereHas('details', function ($query) use ($companyID, $purchaseRequest, $item) {
								$query->where('itemPrimaryCode', $item->itemPrimaryCode);
							})
							->first();

						if ($alreadyAdded) {
							return ['status' => false, 'message' => 'Selected item is already added. Please check again.'];
						}
					}

					$allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
						->where('companySystemID', $purchaseRequest->companySystemID)
						->first();

					if ($allowFinanceCategory) {
						$policy = $allowFinanceCategory->isYesNO;

						if ($policy == 0) {
							if ($purchaseRequest->financeCategory == null || $purchaseRequest->financeCategory == 0) {

								return ['status' => false, 'message' => 'Category is not found.'];
							}

							$pRDetailExistSameItem = PurchaseRequestDetails::select(DB::raw('DISTINCT(itemFinanceCategoryID) as itemFinanceCategoryID'))
								->where('purchaseRequestID', $purchaseRequest->purchaseRequestID)
								->first();

							if ($pRDetailExistSameItem) {
								if ($item->financeCategoryMaster != $pRDetailExistSameItem["itemFinanceCategoryID"]) {
									return ['status' => false, 'message' => 'You cannot add different category item.'];
								}
							}
						}
					}

					$allowPendingApproval = CompanyPolicyMaster::where('companyPolicyCategoryID', 18)
						->where('companySystemID', $companyID)
						->first();

					if ($allowPendingApproval && $item->financeCategoryMaster == 1) {
						if ($allowPendingApproval->isYesNO == 0) {
							$checkWhether = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
								->where('companySystemID', $companyID)
								->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
								->select([
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								])
								->groupBy(
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								);

							$anyPendingApproval = $checkWhether->whereHas('details', function ($query) use ($companyID, $purchaseRequest, $item) {
								$query->where('itemPrimaryCode', $item->itemPrimaryCode)
									->where('manuallyClosed', 0);
							})
								->where('approved', 0)
								->where('cancelledYN', 0)
								->first();


							if (!empty($anyPendingApproval)) {
								return ['status' => false, 'message' => "here is a purchase request (" . $anyPendingApproval->purchaseRequestCode . ") pending for approval for the item you are trying to add. Please check again."];
							}

							$anyApprovedPRButPONotProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
								->where('companySystemID', $companyID)
								->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
								->select([
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								])
								->groupBy(
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								)
								->whereHas('details', function ($query) use ($companyID, $purchaseRequest, $item) {
									$query->where('itemPrimaryCode', $item->itemPrimaryCode)
										->where('selectedForPO', 0)
										->where('prClosedYN', 0)
										->where('fullyOrdered', 0)
										->where('manuallyClosed', 0);
								})
								->where('approved', -1)
								->where('cancelledYN', 0)
								->first();


							if (!empty($anyApprovedPRButPONotProcessed)) {
								return ['status' => false, 'message' => "There is a purchase request (" . $anyApprovedPRButPONotProcessed->purchaseRequestCode . ") approved hense PO is not processed for the item you are trying to add. Please check again"];
							}

							$anyApprovedPRButPOPartiallyProcessed = PurchaseRequest::where('purchaseRequestID', '!=', $purchaseRequest->purchaseRequestID)
								->where('companySystemID', $companyID)
								->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
								->select([
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								])
								->groupBy(
									'erp_purchaserequest.purchaseRequestID',
									'erp_purchaserequest.companySystemID',
									'erp_purchaserequest.serviceLineCode',
									'erp_purchaserequest.purchaseRequestCode',
									'erp_purchaserequest.PRConfirmedYN',
									'erp_purchaserequest.approved',
									'erp_purchaserequest.cancelledYN'
								)->whereHas('details', function ($query) use ($companyID, $purchaseRequest, $item) {
									$query->where('itemPrimaryCode', $item->itemPrimaryCode)
										->where('selectedForPO', 0)
										->where('prClosedYN', 0)
										->where('fullyOrdered', 1)
										->where('manuallyClosed', 0);
								})
								->where('approved', -1)
								->where('cancelledYN', 0)
								->first();


							if (!empty($anyApprovedPRButPOPartiallyProcessed)) {

								return ['status' => false, 'message' => "There is a purchase request (" . $anyApprovedPRButPOPartiallyProcessed->purchaseRequestCode . ") approved and PO is partially processed for the item you are trying to add. Please check again"];
							}

							$checkPOPending = ProcumentOrder::where('companySystemID', $companyID)
								->where('serviceLineSystemID', $purchaseRequest->serviceLineSystemID)
								->whereHas('detail', function ($query) use ($item) {
									$query->where('itemPrimaryCode', $item->itemPrimaryCode)
										->where('manuallyClosed', 0);
								})
								->where('approved', 0)
								->where('poCancelledYN', 0)
								->first();

							if (!empty($checkPOPending)) {
								return ['status' => false, 'message' => "There is a purchase order (" . $checkPOPending->purchaseOrderCode . ") pending for approval for the item you are trying to add. Please check again."];
							}
						}
					}
					$group_companies = Helper::getSimilarGroupCompanies($companyID);
					$poQty = PurchaseOrderDetails::whereHas('order', function ($query) use ($group_companies) {
						$query->whereIn('companySystemID', $group_companies)
							->where('approved', -1)
							->where('poType_N', '!=', 5) // poType_N = 5 =>work order
							->where('poCancelledYN', 0)
							->where('manuallyClosed', 0);
					})
						->where('itemCode', $input['itemCode'])
						->where('manuallyClosed', 0)
						->groupBy('erp_purchaseorderdetails.itemCode')
						->select(
							[
								'erp_purchaseorderdetails.companySystemID',
								'erp_purchaseorderdetails.itemCode',
								'erp_purchaseorderdetails.itemPrimaryCode'
							]
						)
						->sum('noQty');

					$quantityInHand = ErpItemLedger::where('itemSystemCode', $input['itemCode'])
						->where('companySystemID', $companyID)
						->groupBy('itemSystemCode')
						->sum('inOutQty');

					$grvQty = GRVDetails::whereHas('grv_master', function ($query) use ($group_companies) {
						$query->whereIn('companySystemID', $group_companies)
							->where('grvTypeID', 2)
							->where('approved', -1)
							->groupBy('erp_grvmaster.companySystemID');
					})->whereHas('po_detail', function ($query) {
						$query->where('manuallyClosed', 0)
							->whereHas('order', function ($query) {
								$query->where('manuallyClosed', 0);
							});
					})
						->where('itemCode', $input['itemCode'])
						->groupBy('erp_grvdetails.itemCode')
						->select(
							[
								'erp_grvdetails.companySystemID',
								'erp_grvdetails.itemCode'
							]
						)
						->sum('noQty');

					$data_detail['itemCode'] = $val->itemCodeSystem;
					$data_detail['purchaseRequestID'] = $purchaseRequestMaster->purchaseRequestID;
					$data_detail['companySystemID'] = $companyID;
					$data_detail['companyID'] = $company->CompanyID;
					$data_detail['budgetYear'] = $purchaseRequest->budgetYear;
					$data_detail['itemPrimaryCode'] =  $item->itemPrimaryCode;
					$data_detail['itemDescription'] =  $item->itemDescription;
					$data_detail['partNumber'] = $item->secondaryItemCode;
					$data_detail['itemFinanceCategoryID'] = $item->financeCategoryMaster;
					$data_detail['itemFinanceCategorySubID'] =  $item->financeCategorySub;
					$currencyConversion = \Helper::currencyConversion($item->companySystemID, $item->wacValueLocalCurrencyID, $purchaseRequest->currency, $item->wacValueLocal);
					$data_detail['estimatedCost'] = $currencyConversion['documentAmount'];
					$data_detail['companySystemID'] = $item->companySystemID;
					$data_detail['companyID'] = $item->companyID;
					$data_detail['unitOfMeasure'] = $item->itemUnitOfMeasure;
					$data_detail['maxQty'] = $item->maximunQty;
					$data_detail['minQty'] = $item->minimumQty;
					$data_detail['financeGLcodebBSSystemID'] = $financeItemCategorySubAssigned->financeGLcodebBSSystemID;
					$data_detail['financeGLcodebBS'] = $financeItemCategorySubAssigned->financeGLcodebBS;
					$data_detail['financeGLcodePLSystemID'] = $financeItemCategorySubAssigned->financeGLcodePLSystemID;
					$data_detail['financeGLcodePL'] = $financeItemCategorySubAssigned->financeGLcodePL;
					$data_detail['includePLForGRVYN'] = $financeItemCategorySubAssigned->includePLForGRVYN;
					$quantityOnOrder = $poQty - $grvQty;
					$data_detail['poQuantity'] = $poQty;
					$data_detail['quantityOnOrder'] = $quantityOnOrder;
					$data_detail['quantityInHand'] = $quantityInHand;
					$data_detail['itemCategoryID'] = 0;
					$data_detail['quantityRequested'] = $val->qtyRequested;
					$data_detail['totalCost'] = ($data_detail['quantityRequested'] * $data_detail['estimatedCost']);
					$data_detail['comments'] = $val->comment;
					$PurchaseRequestDetails =PurchaseRequestDetails::create($data_detail);
				
					$checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', '!=', $input['serviceLineSystemID'])
					->where('documentSystemID', $purchaseRequestMaster->documentSystemID)
					->where('documentMasterAutoID', $purchaseRequestMaster->purchaseRequestID)
					->where('documentDetailAutoID', $PurchaseRequestDetails->purchaseRequestDetailsID)
					->get();
					if (sizeof($checkAlreadyAllocated) == 0) {
						$checkAlreadyAllocated = SegmentAllocatedItem::where('serviceLineSystemID', $input['serviceLineSystemID'])
						->where('documentSystemID', $purchaseRequestMaster->documentSystemID)
						->where('documentMasterAutoID', $purchaseRequestMaster->purchaseRequestID)
						->where('documentDetailAutoID', $PurchaseRequestDetails->purchaseRequestDetailsID)
						->first();

						if ($checkAlreadyAllocated) {
							return ['status' => false, 'message' => 'Item already allocated for selected segment'];
						}

						$itemData = PurchaseRequestDetails::find($PurchaseRequestDetails->purchaseRequestDetailsID);
        
						if (!$itemData) {
							return ['status' => false, 'message' => 'Item detail not found'];
						} 

						$allocatedQty = SegmentAllocatedItem::where('documentSystemID', 1)
						->where('documentMasterAutoID', $purchaseRequestMaster->purchaseRequestID)
						->where('documentDetailAutoID', $PurchaseRequestDetails->purchaseRequestDetailsID)
						->sum('allocatedQty');

						if ($allocatedQty == $itemData->quantityRequested) {
						return ['status' => false, 'message' => 'No remaining quantity to allocate'];
						}
						
						$allocationData = [
							'documentSystemID' => 1,
							'documentMasterAutoID' => $purchaseRequestMaster->purchaseRequestID,
							'documentDetailAutoID' =>  $PurchaseRequestDetails->purchaseRequestDetailsID,
							'detailQty' => $itemData->quantityRequested,
							'allocatedQty' => $itemData->quantityRequested - $allocatedQty,
							'serviceLineSystemID' => $input['serviceLineSystemID']
						];
				
						$createRes = SegmentAllocatedItem::create($allocationData);
				
						if (!$createRes) {
							return ['status' => false, 'message' => 'Error occured while allocating'];
						}
					}else { 
						$allocatedQty = SegmentAllocatedItem::where('documentSystemID',1)
                                                 ->where('documentMasterAutoID',  $purchaseRequestMaster->purchaseRequestID)
                                                 ->where('documentDetailAutoID', $PurchaseRequestDetails->purchaseRequestDetailsID)
                                                 ->sum('allocatedQty');

                   		 if ($allocatedQty > $input['quantityRequested']) {
							return ['status' => false, 'message' => 'You cannot update the requested quantity. since quantity has been allocated to segments'];
                    	}
					}
				}
			}

			$updateData = [
				'purchaseRequestID' =>  $purchaseRequestMaster->purchaseRequestID,
				'purchaseRequestCode' =>  $purchaseRequestMaster->purchaseRequestCode
			];
			ERPAssetTransfer::where('id',$input['id'])
			->update($updateData);


			return ['status' => true];
		} else {
			return ['status' => true];
		}
	}
}

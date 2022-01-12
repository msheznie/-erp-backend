<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\DocumentSubProduct;
use App\Models\GRVDetails;
use App\Models\ItemSerial;
use App\Models\ItemIssueDetails;
use App\Models\CustomerAssigned;
use App\Models\ItemReturnDetails;
use App\Models\Company;
use App\Models\StockTransferDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerMaster;

class ItemTracking
{
	public static function validateTrackingQuantity($noQty, $documentDetailID, $documentSystemID)
	{
		$checkGeneratedSerialCount =  DocumentSubProduct::where('documentDetailID', $documentDetailID)
                                         				->where('documentSystemID', $documentSystemID)
                                         				->count();

        if ($checkGeneratedSerialCount > $noQty) {
			return ['status' => false, 'message' => "Tracking details added. Please set the tracking details"];
        }
		
		return ['status' => true];
	}

	public static function validateTrackingOnDocumentConfirmation($documentSystemID, $documentSystemCode)
	{
		$errorMessage = [];
		switch ($documentSystemID) {
			case 3:
				$checkTrackingAvaliability = GRVDetails::with(['item_by'])
														->where('grvAutoID', $documentSystemCode)
														->where('trackingType', 2)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->noQty) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}

					if (isset($value->item_by->expiryYN) && $value->item_by->expiryYN == 1) {
						$expireCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNull('expireDate');
													   })
													   ->count();

						if ($expireCheck > 0) {
							$errorMessage[] = "Expiry dates of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is required.";
						}
					}

				}


				break;
			case 8:
				$checkTrackingAvaliability = ItemIssueDetails::where('trackingType', 2)
														->where('itemIssueAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemIssueDetailID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->qtyIssued) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}


				break;
			case 12:
				$checkTrackingAvaliability = ItemReturnDetails::where('trackingType', 2)
														->where('itemReturnAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemReturnDetailID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->qtyIssued) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}

				break;
			case 24:
				$checkTrackingAvaliability = PurchaseReturnDetails::where('trackingType', 2)
														->where('purhaseReturnAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->purhasereturnDetailID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->noQty) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}

				break;
			case 13:
				$checkTrackingAvaliability = StockTransferDetails::where('trackingType', 2)
														->where('stockTransferAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->stockTransferDetailsID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->qty) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}

				break;
			case 71:
				$checkTrackingAvaliability = DeliveryOrderDetail::where('trackingType', 2)
														->where('deliveryOrderID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->deliveryOrderDetailID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->qtyIssued) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}

				break;
			case 20:

				$customerInvoiceData = CustomerInvoiceDirect::find($documentSystemCode);

				if ($customerInvoiceData && $customerInvoiceData->isPerforma != 2) {
					return ['status' => true];
				}


				$checkTrackingAvaliability = CustomerInvoiceItemDetails::where('trackingType', 2)
														->where('custInvoiceDirectAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->customerItemDetailID)
													   ->where('documentSystemID', $documentSystemID)
													   ->whereHas('serial_data', function($query) {
													   		$query->whereNotNull('serialCode');
													   })
													   ->count();

					if ($trackingCheck != $value->qtyIssued) {
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not completed.";
					}
				}

				break;
			default:
				# code...
				break;
		}

        if (count($errorMessage) > 0) {
			return ['status' => false, 'message' => $errorMessage];
        }
		
		return ['status' => true];
	}

	public static function updateTrackingDetailWareHouse($wareHouseSystemID, $documentSystemCode, $documentSystemID)
	{
		switch ($documentSystemID) {
			case 3:
				$checkProduct =  DocumentSubProduct::where('documentSystemCode', $documentSystemCode)
													   ->where('documentSystemID', $documentSystemID)
													   ->where('sold', 1)
													   ->first();

				if ($checkProduct) {
					return ['status' => false, 'message' => "Some serial has been sold. Therefore cannot edit the warehouse"];
				}

				$updateWareHouse = ItemSerial::whereHas('document_product', function($query) use ($documentSystemCode, $documentSystemID) {
												$query->where('documentSystemCode', $documentSystemCode)
													   ->where('documentSystemID', $documentSystemID);
											})
											->update(['wareHouseSystemID' => $wareHouseSystemID]);

				break;
			case 8:
				$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentSystemCode', $documentSystemCode)
                                                         ->where('sold', 1)
                                                         ->first();

	            if ($validateSubProductSold) {
	            	return ['status' => false, 'message' => "Some serial has been sold. Therefore cannot edit the warehouse"];
	            }

	            $subProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode);

	            $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
	            $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

	            if (count($productInIDs) > 0) {
	                $updateSerial = ItemSerial::whereIn('id', $serialIds)
	                                          ->update(['soldFlag' => 0]);

	                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
	                                          ->update(['sold' => 0, 'soldQty' => 0]);

	                $subProduct->delete();
	            }
				break;

			case 12:
				$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentSystemCode', $documentSystemCode)
                                                         ->where('sold', 1)
                                                         ->first();

	            if ($validateSubProductSold) {
	                return ['status' => false, 'message' => "Some serial has been sold. Therefore cannot edit the warehouse"];
	            }

	            $subProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode);

	            $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
	            $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

	            if (count($productInIDs) > 0) {
	                $updateSerial = ItemSerial::whereIn('id', $serialIds)
	                                          ->update(['soldFlag' => 0]);

	                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
	                                          ->update(['sold' => 0, 'soldQty' => 0]);

	                $subProduct->delete();
	            }
				break;
			default:
				# code...
				break;
		}

		return ['status' => true];
	}
}
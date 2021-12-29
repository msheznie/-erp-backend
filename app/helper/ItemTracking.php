<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\DocumentSubProduct;
use App\Models\GRVDetails;
use App\Models\ItemIssueDetails;
use App\Models\CustomerAssigned;
use App\Models\ItemReturnDetails;
use App\Models\Company;
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
				$checkTrackingAvaliability = GRVDetails::where('grvAutoID', $documentSystemCode)
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
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not done/fully done.";
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
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not done/fully done.";
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
						$errorMessage[] = "Tracking details of item ".$value->itemPrimaryCode." - ".$value->itemDescription. " is not done/fully done.";
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
}
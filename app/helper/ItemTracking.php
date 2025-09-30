<?php

namespace App\helper;
use Carbon\Carbon;
use App\Models\DocumentSubProduct;
use App\Models\GRVDetails;
use App\Models\ItemSerial;
use App\Models\ItemBatch;
use App\Models\ItemIssueDetails;
use App\Models\CustomerAssigned;
use App\Models\ItemReturnDetails;
use App\Models\Company;
use App\Models\StockTransferDetails;
use App\Models\PurchaseReturnDetails;
use App\Models\DeliveryOrderDetail;
use App\Models\CustomerInvoiceItemDetails;
use App\Models\CustomerInvoiceDirect;
use App\Models\SalesReturnDetail;
use App\Models\CustomerMaster;

class ItemTracking
{
	public static function validateTrackingQuantity($noQty, $documentDetailID, $documentSystemID)
	{
		$checkGeneratedSerialCount =  DocumentSubProduct::where('documentDetailID', $documentDetailID)
                                         				->where('documentSystemID', $documentSystemID)
                                         				->count();

        if ($checkGeneratedSerialCount > $noQty) {
			return ['status' => false, 'message' => trans('custom.tracking_details_added_please_set')];
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
														->whereIn('trackingType', [1,2])
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {

					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->noQty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}

						if (isset($value->item_by->expiryYN) && $value->item_by->expiryYN == 1) {
							$expireCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNull('expireDate');
														   })
														   ->count();

							if ($expireCheck > 0) {
								$errorMessage[] = trans('custom.expiry_dates_of_item_required', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
							}
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('batch_data', function($query) {
														   		$query->whereNotNull('batchCode');
														   })
														   ->sum('quantity');

						if ($trackingCheck != $value->noQty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}

						if (isset($value->item_by->expiryYN) && $value->item_by->expiryYN == 1) {
							$expireCheck = DocumentSubProduct::where('documentDetailID', $value->grvDetailsID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('batch_data', function($query) {
														   		$query->whereNull('expireDate');
														   })
														   ->count();

							if ($expireCheck > 0) {
								$errorMessage[] = trans('custom.expiry_dates_of_item_required', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
							}
						}
					}
				}

				break;
			case 8:
				$checkTrackingAvaliability = ItemIssueDetails::whereIn('trackingType', [1,2])
														->where('itemIssueAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {

					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemIssueDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemIssueDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('batch_data', function($query) {
														   		$query->whereNotNull('batchCode');
														   })
														   ->sum('quantity');

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}

				}

				break;
			case 12:
				$checkTrackingAvaliability = ItemReturnDetails::whereIn('trackingType', [2, 1])
														->where('itemReturnAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemReturnDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->itemReturnDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('batch_data', function($query) {
														   		$query->whereNotNull('batchCode');
														   })
														   ->sum('quantity');

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}
				}

				break;
			case 24:
				$checkTrackingAvaliability = PurchaseReturnDetails::whereIn('trackingType', [2,1])
														->where('purhaseReturnAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->purhasereturnDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->noQty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->purhasereturnDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('batch_data', function($query) {
														   		$query->whereNotNull('batchCode');
														   })
														   ->sum('quantity');

						if ($trackingCheck != $value->noQty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}
				}

				break;
			case 13:
				$checkTrackingAvaliability = StockTransferDetails::whereIn('trackingType', [2, 1])
														->where('stockTransferAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->stockTransferDetailsID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->stockTransferDetailsID)
								   ->where('documentSystemID', $documentSystemID)
								   ->whereHas('batch_data', function($query) {
								   		$query->whereNotNull('batchCode');
								   })
								   ->sum('quantity');

						if ($trackingCheck != $value->qty) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}
				}

				break;
			case 71:
				$checkTrackingAvaliability = DeliveryOrderDetail::whereIn('trackingType', [2, 1])
														->where('deliveryOrderID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->deliveryOrderDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->deliveryOrderDetailID)
								   ->where('documentSystemID', $documentSystemID)
								   ->whereHas('batch_data', function($query) {
								   		$query->whereNotNull('batchCode');
								   })
								   ->sum('quantity');

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}
				}

				break;
			case 20:

				$customerInvoiceData = CustomerInvoiceDirect::find($documentSystemCode);

				if ($customerInvoiceData && $customerInvoiceData->isPerforma != 2) {
					return ['status' => true];
				}


				$checkTrackingAvaliability = CustomerInvoiceItemDetails::whereIn('trackingType', [2,1])
														->where('custInvoiceDirectAutoID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->customerItemDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->customerItemDetailID)
								   ->where('documentSystemID', $documentSystemID)
								   ->whereHas('batch_data', function($query) {
								   		$query->whereNotNull('batchCode');
								   })
								   ->sum('quantity');

						if ($trackingCheck != $value->qtyIssued) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					}
				}

				break;
			case 87:
				$checkTrackingAvaliability = SalesReturnDetail::whereIn('trackingType', [2,1])
														->where('salesReturnID', $documentSystemCode)
														->get();

				if (count($checkTrackingAvaliability) == 0) {
					return ['status' => true];
				}

				foreach ($checkTrackingAvaliability as $key => $value) {
					if ($value->trackingType == 2) {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->salesReturnDetailID)
														   ->where('documentSystemID', $documentSystemID)
														   ->whereHas('serial_data', function($query) {
														   		$query->whereNotNull('serialCode');
														   })
														   ->count();

						if ($trackingCheck != $value->qtyReturned) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
					} else {
						$trackingCheck = DocumentSubProduct::where('documentDetailID', $value->salesReturnDetailID)
								   ->where('documentSystemID', $documentSystemID)
								   ->whereHas('batch_data', function($query) {
								   		$query->whereNotNull('batchCode');
								   })
								   ->sum('quantity');

						if ($trackingCheck != $value->qtyReturned) {
							$errorMessage[] = trans('custom.tracking_details_of_item_not_completed', ['itemCode' => $value->itemPrimaryCode, 'itemDescription' => $value->itemDescription]);
						}
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
													   ->where(function($query) {
													   		$query->where('sold', 1)
													   			  ->orWhere('soldQty', '>', 0);
													   })
													   ->first();

				if ($checkProduct) {
					return ['status' => false, 'message' => trans('custom.serial_sold_cannot_edit_warehouse')];
				}

				$updateWareHouse = ItemSerial::whereHas('document_product', function($query) use ($documentSystemCode, $documentSystemID) {
												$query->where('documentSystemCode', $documentSystemCode)
													   ->where('documentSystemID', $documentSystemID);
											})
											->update(['wareHouseSystemID' => $wareHouseSystemID]);

				$updateWareHouse = ItemBatch::whereHas('document_product', function($query) use ($documentSystemCode, $documentSystemID) {
												$query->where('documentSystemCode', $documentSystemCode)
													   ->where('documentSystemID', $documentSystemID);
											})
											->update(['wareHouseSystemID' => $wareHouseSystemID]);

				break;
			case 8:
				$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentSystemCode', $documentSystemCode)
                                                          ->where(function($query) {
														   		$query->where('sold', 1)
														   			  ->orWhere('soldQty', '>', 0);
														   })
                                                         ->first();

	            if ($validateSubProductSold) {
	            	return ['status' => false, 'message' => trans('custom.serial_sold_cannot_edit_warehouse')];
	            }

	            $subProductSerial = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode)
	                                             ->whereNull('productBatchID');

	            $productInIDsSerial = ($subProductSerial->count() > 0) ? $subProductSerial->get()->pluck('productInID')->toArray() : [];
	            $serialIds = ($subProductSerial->count() > 0) ? $subProductSerial->get()->pluck('productSerialID')->toArray() : [];

	            if (count($productInIDsSerial) > 0) {
	                $updateSerial = ItemSerial::whereIn('id', $serialIds)
	                                          ->update(['soldFlag' => 0]);

	                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDsSerial)
	                						  ->whereIn('productSerialID', $serialIds)
	                                          ->update(['sold' => 0, 'soldQty' => 0]);



	                $subProductSerial->delete();
	            }

	            $subProductBatch = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode)
	                                             ->whereNull('productSerialID');

	            $productBatchIDs = ($subProductBatch->count() > 0) ? $subProductBatch->get()->pluck('productBatchID')->toArray() : [];


	            foreach ($productBatchIDs as $key1 => $bValue) {
	            	$checkBatch = ItemBatch::find($bValue);

	            	$checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                             ->where('documentSystemCode', $documentSystemCode)
                                                             ->where('productBatchID', $bValue)
                                                             ->get();

		            if ($checkDocumentSubProduct) {
		                $totalQty = 0;
		                foreach ($checkDocumentSubProduct as $key => $value) {
		                    
		                    $soldProduct = DocumentSubProduct::find($value->productInID);
		                    if ($soldProduct) {
		                        $soldProduct->sold = 0;
		                        $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
		                        $soldProduct->save();
		                    }
		                    
		                    $totalQty += $value->quantity;
		                }

		                
	                    $checkBatch->soldFlag = 0;
	                    $checkBatch->copiedQty = $checkBatch->copiedQty - $totalQty;
		                
		                $checkBatch->save();


		                DocumentSubProduct::where('documentSystemID', $documentSystemID)
		                                 ->where('documentSystemCode', $documentSystemCode)
		                                 ->where('productBatchID', $bValue)
		                                 ->delete();
		            }
	            }


				break;

			case 12:
				$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentSystemCode', $documentSystemCode)
                                                          ->where(function($query) {
														   		$query->where('sold', 1)
														   			  ->orWhere('soldQty', '>', 0);
														   })
                                                         ->first();

	            if ($validateSubProductSold) {
	                return ['status' => false, 'message' => trans('custom.serial_sold_cannot_edit_warehouse')];
	            }

	            $subProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode)
	                                             ->whereNull('productBatchID');

	            $productInIDs = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productInID')->toArray() : [];
	            $serialIds = ($subProduct->count() > 0) ? $subProduct->get()->pluck('productSerialID')->toArray() : [];

	            if (count($productInIDs) > 0) {
	                $updateSerial = ItemSerial::whereIn('id', $serialIds)
	                                          ->update(['soldFlag' => 0]);

	                $updateSerial = DocumentSubProduct::whereIn('id', $productInIDs)
	                						  ->whereIn('productSerialID', $serialIds)
	                                          ->update(['sold' => 0, 'soldQty' => 0]);

	                $subProduct->delete();
	            }

	            	            $subProductBatch = DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                             ->where('documentSystemCode', $documentSystemCode)
	                                             ->whereNull('productSerialID');

	            $productBatchIDs = ($subProductBatch->count() > 0) ? $subProductBatch->get()->pluck('productBatchID')->toArray() : [];


	            foreach ($productBatchIDs as $key1 => $bValue) {
	            	$checkBatch = ItemBatch::find($bValue);

	            	$checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                             ->where('documentSystemCode', $documentSystemCode)
                                                             ->where('productBatchID', $bValue)
                                                             ->get();

		            if ($checkDocumentSubProduct) {
		                $totalQty = 0;
		                foreach ($checkDocumentSubProduct as $key => $value) {
		                    
		                    $soldProduct = DocumentSubProduct::find($value->productInID);
		                    if ($soldProduct) {
		                        $soldProduct->sold = 0;
		                        $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
		                        $soldProduct->save();
		                    }
		                    
		                    $totalQty += $value->quantity;
		                }

		                
	                    $checkBatch->soldFlag = (($checkBatch->copiedQty + $totalQty) == $checkBatch->quantity) ? 1 : 0;
	                    $checkBatch->copiedQty = $checkBatch->copiedQty + $totalQty;
		                
		                $checkBatch->save();


		                DocumentSubProduct::where('documentSystemID', $documentSystemID)
		                                 ->where('documentSystemCode', $documentSystemCode)
		                                 ->where('productBatchID', $bValue)
		                                 ->delete();
		            }
	            }

				break;
			default:
				# code...
				break;
		}

		return ['status' => true];
	}

	public static function revertBatchTrackingSoldStatus($documentSystemID, $documentDetailID, $stcokTransfer = false)
	{
		$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentDetailID', $documentDetailID)
                                                         ->where('soldQty', '>', 0)
                                                         ->first();

        if ($validateSubProductSold) {
            return ['status' => false, 'message' => 'You cannot delete this line item. batch details are sold already.'];
        }

        $subProducts = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                         ->where('documentDetailID', $documentDetailID)
                                         ->whereNotNull('productBatchID');

        $batchIds = ($subProducts->count() > 0) ? $subProducts->get()->pluck('productBatchID')->toArray() : [];

        foreach ($batchIds as $keyBatch => $batchId) {
			$checkBatch = ItemBatch::find($batchId);

	        if ($checkBatch) {
				$checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
		                                                             ->where('documentDetailID', $documentDetailID)
		                                                             ->where('productBatchID', $batchId)
		                                                             ->get();

		        if ($checkDocumentSubProduct) {
		            $totalQty = 0;
		            foreach ($checkDocumentSubProduct as $key => $value) {
		                
		                $soldProduct = DocumentSubProduct::find($value->productInID);
		                if ($soldProduct) {
		                    $soldProduct->sold = 0;
		                    $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
		                    $soldProduct->save();
		                }
		                
		                $totalQty += $value->quantity;
		            }

		            if (!$stcokTransfer) {
		                $checkBatch->soldFlag = 0;
		                $checkBatch->copiedQty = $checkBatch->copiedQty - $totalQty;
		                $checkBatch->save();
		            }
		            
		            DocumentSubProduct::where('documentSystemID', $documentSystemID)
		                             ->where('documentDetailID', $documentDetailID)
		                             ->where('productBatchID', $batchId)
		                             ->delete();
		        }
	        }
        }


        return ['status' => true];
	}

	public static function revertBatchTrackingReturnStatus($documentSystemID, $documentDetailID)
	{
		$validateSubProductSold = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                                         ->where('documentDetailID', $documentDetailID)
                                                         ->where('soldQty', '>', 0)
                                                         ->first();

        if ($validateSubProductSold) {
            return ['status' => false, 'message' => 'You cannot delete this line item. batch details are sold already.'];
        }

        $subProducts = DocumentSubProduct::where('documentSystemID', $documentSystemID)
                                         ->where('documentDetailID', $documentDetailID)
                                         ->whereNotNull('productBatchID');

        $batchIds = ($subProducts->count() > 0) ? $subProducts->get()->pluck('productBatchID')->toArray() : [];

        foreach ($batchIds as $keyBatch => $batchId) {
			$checkBatch = ItemBatch::find($batchId);

	        if ($checkBatch) {
				$checkDocumentSubProduct = DocumentSubProduct::where('documentSystemID', $documentSystemID)
		                                                             ->where('documentDetailID', $documentDetailID)
		                                                             ->where('productBatchID', $batchId)
		                                                             ->get();

		        if (count($checkDocumentSubProduct) > 0) {
	                $totalQty = 0;
	                foreach ($checkDocumentSubProduct as $key => $value) {
	                    
	                    $soldProduct = DocumentSubProduct::find($value->productInID);
	                    if ($soldProduct) {
	                        $soldProduct->sold = 0;
	                        $soldProduct->soldQty = $soldProduct->soldQty - $value->quantity;
	                        $soldProduct->save();
	                    }
	                    
	                    $totalQty += $value->quantity;
	                }

	              
	                $checkBatch->soldFlag = (($checkBatch->copiedQty + $totalQty) == $checkBatch->quantity) ? 1 : 0;
	                $checkBatch->copiedQty = $checkBatch->copiedQty + $totalQty;
	                
	                $checkBatch->save();


	                DocumentSubProduct::where('documentSystemID', $documentSystemID)
	                                 ->where('documentDetailID', $documentDetailID)
	                                 ->where('productBatchID', $batchId)
	                                 ->delete();
	            }
	        }
        }


        return ['status' => true];
	}
}
<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\StockCountDetail;
use App\Models\StockCount;

class StockCountService
{
	public static function updateStockCountAdjustmentDetail($input)
	{
		$stockCount = StockCount::find($input['stockCountAutoID']);
		if (!$stockCount) {
			return ['status' => false, 'message' => "Stock Count not found"];
		}

		$stockCountDetails = StockCountDetail::where('stockCountAutoID', $stockCount->stockCountAutoID)
                                            ->get();

        foreach ($stockCountDetails as $key => $value) {
            $data = array('companySystemID' => $stockCount->companySystemID,
                        'itemCodeSystem' => $value->itemCodeSystem,
                        'wareHouseId' => $stockCount->location);

            $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);

            $updateData = [
                'currenctStockQty' => $itemCurrentCostAndQty['currentStockQty'],
                'systemQty' => $itemCurrentCostAndQty['currentStockQty'],
                'adjustedQty' => $value->noQty - $itemCurrentCostAndQty['currentStockQty']
            ];

            StockCountDetail::where('stockCountDetailsAutoID', $value->stockCountDetailsAutoID)
                            ->update($updateData);
        }

		return ['status' => true];
	}
}
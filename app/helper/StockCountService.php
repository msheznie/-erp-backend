<?php

namespace App\helper;
use App\helper\Helper;
use Carbon\Carbon;
use App\Models\StockCountDetail;
use App\Models\ItemAssigned;
use App\Models\StockCount;

class StockCountService
{
	public static function updateStockCountAdjustmentDetail($input)
	{
		$stockCount = StockCount::find($input['stockCountAutoID']);
		if (!$stockCount) {
			return ['status' => false, 'message' => trans('custom.stock_count_not_found')];
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
                'wacAdjRpt' => $itemCurrentCostAndQty['wacValueReporting'],
                'currentWacRpt' => $itemCurrentCostAndQty['wacValueReporting'],
                'adjustedQty' => $value->noQty - $itemCurrentCostAndQty['currentStockQty']
            ];

            $item = ItemAssigned::where('itemCodeSystem', $value->itemCodeSystem)
                                ->where('companySystemID', $stockCount->companySystemID)
                                ->first();

            if ($item) {
                $companyCurrencyConversion = \Helper::currencyConversion($stockCount->companySystemID,$item->wacValueReportingCurrencyID,$item->wacValueReportingCurrencyID,$itemCurrentCostAndQty['wacValueReporting']);
                $updateData['currentWaclocal'] = $companyCurrencyConversion['localAmount'];
                $updateData['wacAdjLocal'] = $companyCurrencyConversion['localAmount'];
                $updateData['wacAdjRptER'] = $companyCurrencyConversion['trasToRptER'];
                $updateData['wacAdjLocalER'] = 1;
            }

            StockCountDetail::where('stockCountDetailsAutoID', $value->stockCountDetailsAutoID)
                            ->update($updateData);
        }

		return ['status' => true];
	}
}
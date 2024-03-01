<?php

namespace App\Http\Controllers\API\SRM;

use App\Models\SrmBudgetItem;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class ThirdPartySystemsController extends AppBaseController
{

    public function __construct()
    {

    }
    public function fetchItemWithAmount(Request $request)
    {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'item_list.*.item_id' => 'required',
                'item_list.*.item_name' => 'required',
                'item_list.*.budget_amount' => 'required|numeric',
            ], [
                'item_list.*.item_id.required' => 'All items must have an item_id.',
                'item_list.*.item_name.required' => 'All items must have an item_name.',
                'item_list.*.budget_amount.required' => 'All items must have a budget_amount.',
                'item_list.*.budget_amount.numeric' => 'Budget amount must be a number.',
            ]);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                return response()->json(['error' => 'Incomplete item received', 'details' => $errors[0]], 422);
            }

            $itemList = $request->input('item_list');

            // Validate the item_list structure
            if (!is_array($itemList)) {
                return response()->json(['error' => 'Invalid item_list format'], 400);
            }

            // Get the existing item IDs
            $existingItemIds = SrmBudgetItem::pluck('item_id')->toArray();

            foreach ($itemList as $item) {
                if (!is_array($item) || empty($item['item_id']) || empty($item['item_name']) || empty($item['budget_amount'])) {
                    return response()->json(['error' => 'Incomplete item received'], 400);
                }

                SrmBudgetItem::updateOrInsert(
                    ['item_id' => $item['item_id']],
                    [
                        'item_name' => $item['item_name'],
                        'budget_amount' => $item['budget_amount'],
                        'is_active' => 1 // Set all received items as is_active = 1
                    ]
                );
            }

            // Set only existing items not in the item_list as is_active = 0
            $receivedItemIds = array_column($itemList, 'item_id');
            $itemsToDeactivate = array_diff($existingItemIds, $receivedItemIds);
            if (!empty($itemsToDeactivate)) {
                SrmBudgetItem::whereIn('item_id', $itemsToDeactivate)->update(['is_active' => 0]);
            }

            DB::commit();
            return response()->json(['message' => 'Data stored successfully'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

}

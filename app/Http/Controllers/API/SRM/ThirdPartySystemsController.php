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
    public function pushBudgetItems(Request $request)
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
            $company_id = $request->input('company_id');

            if (!is_array($itemList)) {
                return response()->json(['error' => 'Invalid item_list format'], 400);
            }

            $existingItemIds = SrmBudgetItem::pluck('item_id')->toArray();

            foreach ($itemList as $item) {
                if (!is_array($item) || empty($item['item_id']) || empty($item['item_name']) || empty($item['budget_amount'])) {
                    return response()->json(['error' => 'Incomplete item received'], 400);
                }

                $existingItem = SrmBudgetItem::where('item_id', $item['item_id'])
                    ->where('company_id', $company_id)
                    ->first();

                $data = [
                    'item_id' => $item['item_id'],
                    'item_name' => $item['item_name'],
                    'budget_amount' => $item['budget_amount'],
                    'is_active' => 1,
                    'company_id' => $company_id,
                ];

                if (!$existingItem) {
                    $data['created_at'] = now();
                    $data['updated_at'] = null;
                }

                SrmBudgetItem::updateOrInsert(['item_id' => $item['item_id'], 'company_id' => $company_id], $data);
            }

            $receivedItemIds = array_column($itemList, 'item_id');
            $itemsToDeactivate = array_diff($existingItemIds, $receivedItemIds);
            if (!empty($itemsToDeactivate)) {
                SrmBudgetItem::whereIn('item_id', $itemsToDeactivate)->where('company_id', $company_id)->update(['is_active' => 0]);
            }

            DB::commit();
            return response()->json(['message' => 'Data stored successfully'], 200);
        } catch (\Exception $exception) {
            DB::rollBack();
            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

}

<?php

namespace App\Services\Inventory;

use App\helper\inventory;
use App\Models\ItemIssueDetails;
use App\Models\ItemIssueMaster;
use App\Models\ItemMaster;
use App\Models\MaterielRequest;

class PullMaterialRequestFromMaterialIssueService
{
    public function getMaterialRequest($input):Array {

        $search = isset($input['search']) ? $input['search'] : null;

        $materielRequests = MaterielRequest::with('details')->where('RequestID',$input['RequestID'])
            ->where("approved", -1)
            ->where("cancelledYN", 0);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $materielRequests = $materielRequests->where(function ($query) use ($search) {
                $query->where('itemIssueCode', 'LIKE', "%{$search}%")
                    ->orWhere('comment', 'LIKE', "%{$search}%");
            });
        }

        $materielRequests = $materielRequests->get(['RequestID', 'RequestCode']);
        $data = array();
        foreach ($materielRequests as $mr) {
            $prvIssuedQnty = 0;
            $currentStock= 0;
            $currentWareHouseStockQty =0;
            foreach ($mr->details as $detail) {
                $materielIssueId = $input['id'];

                $detail['isChecked'] = false;

                $companyId = $input['companyId'];
                $wareHouseId = $input['wareHouseFrom'];
                if($detail->itemCode)
                {
                    $itemId = $detail->itemCode;
                    $materialRequestId = $mr->RequestID;
                    $materielIssuesPrvIssuedDetails = ItemIssueDetails::where('itemCodeSystem',$itemId)->whereHas('master', function($q) use ($materielIssueId,$materialRequestId) {
                        $q->where('itemIssueAutoID','<',$materielIssueId)
                            ->where('reqDocID',$materialRequestId);
                    })->sum('qtyIssued');


                    $dataArray = array(
                        'companySystemID' => $companyId,
                        'itemCodeSystem' => $itemId,
                        'wareHouseId' => $wareHouseId
                    );

                    $itemCurrentCostAndQty = inventory::itemCurrentCostAndQty($dataArray);
                    $currentStock = isset($itemCurrentCostAndQty) ? $itemCurrentCostAndQty['currentStockQty'] : 0;
                    $currentWareHouseStockQty = isset($itemCurrentCostAndQty) ? $itemCurrentCostAndQty['currentWareHouseStockQty'] : 0;
                    $prvIssuedQnty = $materielIssuesPrvIssuedDetails;
                    $currentMaterielIssue = ItemIssueDetails::where("itemIssueAutoID",$materielIssueId)->where('itemCodeSystem',$itemId)->first();


                    if(isset($currentMaterielIssue)) {
                        $detail['isChecked'] = true;
                    }

                }else {
                    $prvIssuedQnty = 0;
                    $currentStock = 0;
                    $currentMaterielIssue = null;

                }


                $itemMaster = ItemMaster::select(['primaryCode'])->where('itemCodeSystem',$detail['itemCode'])->first();
                $detail['itemPrimaryCode'] = ($itemMaster) ? $itemMaster->primaryCode : null;
                $detail['prvIssuedQty'] = $prvIssuedQnty;
                $detail['currentStock'] = $currentStock;
                $detail['currentWareHouseStockQty'] = $currentWareHouseStockQty;
                $detail['qtyIssued'] = 0;
                $detail['itemCodeSystem'] = $detail['itemCode'];
                $detail['mappingItemCode'] = null;

            }


            $mr['details'] = $mr->details;
            $totalQuantityRequested = $mr->details->sum('quantityRequested');
            $materielIssue = ItemIssueMaster::with(['details'])->where('reqDocID',$mr->RequestID)->get();
            $totalIssuedQty = 0;
            foreach ($materielIssue as $mi) {
                $totalIssuedQty += $mi->details->sum('qtyIssued');
            }

            if($totalQuantityRequested != 0 && ($totalQuantityRequested != $totalIssuedQty)) {
                array_push($data,$mr);
            }
        }

        return $data;
    }
}

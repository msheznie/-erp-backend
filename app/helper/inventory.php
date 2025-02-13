<?php
/**
 * =============================================
 * -- File Name : inventory.php
 * -- Project Name : ERP
 * -- Module Name :  email class
 * -- Author : Mohamed Fayas
 * -- Create date : 15 - August 2018
 * -- Description : This file contains the all the common inventory function
 * -- REVISION HISTORY
 */

namespace App\helper;

use App\Models\ErpItemLedger;
use App\Models\ItemAssigned;
use Response;

class inventory
{

    /**
     * get item current wac and qty
     * @param $params : accept parameters as an array
     * $array 1- companySystemID : company auto id
     * $array 2- itemCodeSystem : item Code System
     * $array 3- wareHouseId : wareHouse id
     * @return mixed
     */
    public static function itemCurrentCostAndQty($params)
    {

        $output = array('currentStockQty' => 0,
            'currentWareHouseStockQty' => 0,
            'currentStockQtyInDamageReturn' => 0,
            'wacValueLocal' => 0,
            'wacValueReporting' => 0,
            'totalWacCostLocal' => 0,
            'totalWacCostRpt' => 0,
            'inOutQty' => 0,
            'wacValueLocalWarehouse' => 0,
            'wacValueReportingWarehouse' => 0,
            'totalWacCostLocalWarehouse' => 0,
            'totalWacCostRptWarehouse' => 0,
            'inOutQtyWarehouse' => 0
        );

        if (array_key_exists('itemCodeSystem', $params) && array_key_exists('companySystemID', $params)) {

            $item = ItemAssigned::where('itemCodeSystem', $params['itemCodeSystem'])
                ->where('companySystemID', $params['companySystemID'])
                ->first();

            if (!empty($item)) {

                $itemLedgerRec = ErpItemLedger::selectRaw('companySystemID, 
                                                        itemSystemCode, 
                                                        round(sum(inOutQty),2) as inOutQty,
                                                        if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as wacCostLocal, 
                                                        if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as wacCostRpt,
                                                        round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as totalWacCostLocal,
                                                        round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as totalWacCostRpt')
                    ->where('companySystemID', $params['companySystemID'])
                    ->where('fromDamagedTransactionYN', 0)
                    ->where('itemSystemCode', $params['itemCodeSystem'])
                    ->groupBy('companySystemID', 'itemSystemCode')->first();


                if (!empty($itemLedgerRec)) {
                    $output['wacValueLocal'] = $itemLedgerRec->wacCostLocal;
                    $output['wacValueReporting'] = $itemLedgerRec->wacCostRpt;
                    $output['totalWacCostLocal'] = $itemLedgerRec->totalWacCostLocal;
                    $output['totalWacCostRpt'] = $itemLedgerRec->totalWacCostRpt;
                    $output['inOutQty'] = $itemLedgerRec->inOutQty;
                }

                $output['currentStockQty'] = ErpItemLedger::where('itemSystemCode', $params['itemCodeSystem'])
                    ->where('companySystemID', $params['companySystemID'])
                    ->groupBy('itemSystemCode')
                    ->sum('inOutQty');
                if (array_key_exists('wareHouseId', $params)) {
                    $output['currentWareHouseStockQty'] = ErpItemLedger::where('itemSystemCode', $params['itemCodeSystem'])
                        ->where('companySystemID', $params['companySystemID'])
                        ->where('wareHouseSystemCode', $params['wareHouseId'])
                        ->groupBy('itemSystemCode')
                        ->sum('inOutQty');


                    $itemLedgerRecWarehouse = ErpItemLedger::selectRaw('companySystemID, 
                                                        itemSystemCode, 
                                                        round(sum(inOutQty),2) as inOutQty,
                                                        if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as wacCostLocal, 
                                                        if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as wacCostRpt,
                                                        round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacLocal))/round(sum(inOutQty),2)),9)) as totalWacCostLocal,
                                                        round(sum(inOutQty),2) * if(round(sum(inOutQty),2)=0,0,round((sum((inOutQty*wacRpt))/round(sum(inOutQty),2)),9)) as totalWacCostRpt')
                        ->where('companySystemID', $params['companySystemID'])
                        ->where('wareHouseSystemCode', $params['wareHouseId'])
                        ->where('fromDamagedTransactionYN', 0)
                        ->where('itemSystemCode', $params['itemCodeSystem'])
                        ->groupBy('companySystemID', 'itemSystemCode')->first();

                    if (!empty($itemLedgerRecWarehouse)) {
                        $output['wacValueLocalWarehouse'] = $itemLedgerRecWarehouse->wacCostLocal;
                        $output['wacValueReportingWarehouse'] = $itemLedgerRecWarehouse->wacCostRpt;
                        $output['totalWacCostLocalWarehouse'] = $itemLedgerRecWarehouse->totalWacCostLocal;
                        $output['totalWacCostRptWarehouse'] = $itemLedgerRecWarehouse->totalWacCostRpt;
                        $output['inOutQtyWarehouse'] = $itemLedgerRecWarehouse->inOutQty;
                    }


                    if(isset($params['itemReport']) && $params['itemReport'])
                    {
                        $itemLedgerRec = ErpItemLedger::selectRaw('
                                            erp_itemledger.*, 
                                        itemmaster.trackingType,
                                        SUM(DISTINCT erp_itemledger.inOutQty) as totalQuantity,
                                        round(SUM(DISTINCT erp_itemledger.inOutQty * erp_itemledger.wacLocal)/round(SUM(DISTINCT erp_itemledger.inOutQty),2),9) as wacValueLocalWarehouse,
                                        round(SUM(DISTINCT erp_itemledger.inOutQty * erp_itemledger.wacRpt)/round(SUM(DISTINCT erp_itemledger.inOutQty),2),9) as wacValueReportingWarehouse,
                                        round(SUM(DISTINCT erp_itemledger.inOutQty * erp_itemledger.wacLocal),9) as totalWacCostLocalWarehouse,
                                        round(SUM(DISTINCT erp_itemledger.inOutQty * erp_itemledger.wacRpt),9) as totalWacCostRptWarehouse,
                                            COALESCE
                                            (
                                                CONCAT("[", GROUP_CONCAT(
                                                        JSON_OBJECT(
                                                        "id", document_sub_products.id, 
                                                        "quantity", document_sub_products.quantity, 
                                                        "productBatchID", document_sub_products.productBatchID,
                                                        "binLocation", item_batch.binLocation,
                                                        "binLocationDes", warehousebinlocationmaster.binLocationDes,
                                                        "wac", erp_itemledger.wacLocal,
                                                        "binLocationID", warehousebinlocationmaster.binLocationID
                                                    )
                                                ), "]"), "[]"
                                            ) as newItem
                                        ')
                                        ->where('erp_itemledger.companySystemID', $params['companySystemID'])
                                        ->where('fromDamagedTransactionYN', 0)
                                        ->where('erp_itemledger.itemSystemCode', $params['itemCodeSystem'])
                                        ->where('erp_itemledger.wareHouseSystemCode', $params['wareHouseId'])
                                        ->leftJoin('document_sub_products', function ($join) {
                                            $join->on('erp_itemledger.documentSystemID', '=', 'document_sub_products.documentSystemID')
                                                ->on('erp_itemledger.documentSystemCode', '=', 'document_sub_products.documentSystemCode');
                                        })
                                        ->leftJoin('item_batch', 'document_sub_products.productBatchID', '=', 'item_batch.id')
                                        ->leftJoin('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
                                        ->leftJoin('warehousebinlocationmaster', 'item_batch.binLocation', '=', 'warehousebinlocationmaster.binLocationID') 
                                        ->groupBy('erp_itemledger.itemSystemCode', 'erp_itemledger.companySystemID')
                                        ->first();

                            if (!empty($itemLedgerRec)) 
                                {
                                    $newItems = json_decode($itemLedgerRec->newItem, true); 
                                    $groupedNewItems = [];
                                    if (is_array($newItems)) {
                                    
        
                                        foreach ($newItems as $newItem) {
                                            $binLocationDes = $newItem['binLocationDes'];
                                            $binLocationID = $newItem['binLocationID'];
                                            $totalWacCostLocalWarehouse = round($newItem['quantity'] * $itemLedgerRecWarehouse->wacCostLocal, 2);
                                            $totalWacCostRptWarehouse = round($newItem['quantity'] * $itemLedgerRecWarehouse->wacCostRpt, 2);
                                            $productBatchID = $newItem['productBatchID'];
                                            if (isset($groupedNewItems[$binLocationID])) {
                                                $groupedNewItems[$binLocationID]['quantity'] += $newItem['quantity'];
                                                $groupedNewItems[$binLocationID]['totalWacCostLocal'] += $totalWacCostLocalWarehouse;
                                                $groupedNewItems[$binLocationID]['totalWacCostRpt'] += $totalWacCostRptWarehouse;
                                                if (!in_array($productBatchID, $groupedNewItems[$binLocationID]['IDS'])) {
                                                    $groupedNewItems[$binLocationID]['IDS'][] = $productBatchID;
                                                }
                                            } else {
                                                $groupedNewItems[$binLocationID] = [
                                                    'binLocationDes' => $binLocationDes,
                                                    'binLocationID' => $binLocationID,
                                                    'quantity' => $newItem['quantity'],
                                                    'binLocation' => $newItem['binLocation'],
                                                    'productBatchID' => $newItem['productBatchID'],
                                                    'totalWacCostLocal' => $totalWacCostLocalWarehouse,
                                                    'totalWacCostRpt' => $totalWacCostRptWarehouse,
                                                    'IDS' => [$productBatchID],
                                                ];
                                            }
                                        }
                                    }
                                    $itemLedgerRec->newItem = array_values($groupedNewItems);

                                    $output['binLocation'] = $itemLedgerRec->newItem;
                                    $output['isTrackable'] = (int) $itemLedgerRec->trackingType;
                                }
                    }
                }
                $output['currentStockQtyInDamageReturn'] = ErpItemLedger::where('itemSystemCode', $params['itemCodeSystem'])
                    ->where('companySystemID', $params['companySystemID'])
                    ->where('fromDamagedTransactionYN', 1)
                    ->groupBy('itemSystemCode')
                    ->sum('inOutQty');
            }
        }
        return $output;
    }

}
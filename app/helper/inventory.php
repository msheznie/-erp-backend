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
use App\Models\ItemMaster;
use DB;
use App\Models\PurchaseReturn;
use App\Models\DeliveryOrder;
use App\Models\ItemIssueMaster;
use App\Models\StockTransfer;
use App\Models\GRVMaster;
use App\Services\Currency\CurrencyService;

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
        \DB::select("SET SESSION group_concat_max_len = 1000000");
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

            $itemMaster = ItemMaster::where('itemCodeSystem', $params['itemCodeSystem'])
                ->where('primaryCompanySystemID', $params['companySystemID'])
                ->first();
            if (!empty($item) && !empty($itemMaster)) {

              
                $table = $itemMaster->trackingType == 2 ? 'item_serial' : 'item_batch';
                $column = $itemMaster->trackingType == 2 ? 'productSerialID' : 'productBatchID';

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
                    $output['wacValueLocal'] = CurrencyService::formatNumberWithPrecision(($itemLedgerRec->wacCostLocal));
                    $output['wacValueReporting'] = CurrencyService::formatNumberWithPrecision(($itemLedgerRec->wacCostRpt));
                    $output['totalWacCostLocal'] = CurrencyService::formatNumberWithPrecision(($itemLedgerRec->totalWacCostLocal));
                    $output['totalWacCostRpt'] = CurrencyService::formatNumberWithPrecision(($itemLedgerRec->totalWacCostRpt));
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
                                                        "productBatchID", document_sub_products.' . $column . ',
                                                        "binLocation", ' . $table . '.binLocation,
                                                        "binLocationDes", warehousebinlocationmaster.binLocationDes,
                                                        "wac", erp_itemledger.wacLocal,
                                                        "binLocationID", warehousebinlocationmaster.binLocationID,
                                                        "RemainingQty",document_sub_products.quantity - document_sub_products.soldQty,
                                                        "sold",document_sub_products.soldQty
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
                                        ->leftJoin($table, "document_sub_products.{$column}", '=', "{$table}.id")
                                        ->leftJoin('itemmaster', 'erp_itemledger.itemSystemCode', '=', 'itemmaster.itemCodeSystem')
                                        ->leftJoin('warehousebinlocationmaster', "{$table}.binLocation", '=', 'warehousebinlocationmaster.binLocationID') 
                                        ->where('document_sub_products.productInID', null)
                                        ->groupBy('erp_itemledger.itemSystemCode', 'erp_itemledger.companySystemID')
                                        ->first();

                            if (!empty($itemLedgerRec)) 
                                {
                                    $newItems = json_decode($itemLedgerRec->newItem, true); 
                                    $groupedNewItems = [];
                                    if (is_array($newItems)) {
                                        
                                        foreach ($newItems as $item) {
                                            $batchID = $item['productBatchID'];

                                                if($item['sold'] != 0)
                                                    {                           
                                                        $docHub = DB::table('document_sub_products')->where('productInID', $item['id'])->where($column, $item['productBatchID'])->get();
                                                        $add = 0;
                                                        foreach ($docHub as $hub) 
                                                        {
                                                            $document = null;

                                                            switch ($hub->documentSystemID) {
                                                                case 3: 
                                                                    $document = GRVMaster::where('grvAutoID', $hub->documentSystemCode)->first();
                                                                    if ($document && $document->approved == 0) {
                                                                        $add = $add - $hub->quantity; 
                                                                    }
                                                                    break;

                                                                case 24:
                                                                case 71:
                                                                case 8:
                                                                case 13:
                                                                    $modelMap = [
                                                                        24 => [PurchaseReturn::class, 'purhaseReturnAutoID','approved'],
                                                                        71 => [DeliveryOrder::class, 'deliveryOrderID','approvedYN'],
                                                                        8  => [ItemIssueMaster::class, 'itemIssueAutoID','approved'],
                                                                        13 => [StockTransfer::class, 'stockTransferAutoID','approved'],
                                                                    ];

                                                                    [$model, $columnName,$approve] = $modelMap[$hub->documentSystemID];

                                                                    $document = $model::where($columnName, $hub->documentSystemCode)->where($approve, 0)->first();

                                                                    if ($document) {
                                                                        $add = $add + $hub->quantity; 
                                                                    }
                                                                    break;
                                                            }
                                                        }
                                                        $item['RemainingQty'] += $add;
                                                    }
                                           
                                         
                                            if (!isset($groupedItems[$batchID])) {
                                                $groupedItems[$batchID] = $item;
                                            } else {
                                                $groupedItems[$batchID]['quantity'] += $item['quantity'];
                                            }
                                        }
                                    
                                        $groupedItems = collect(array_values($groupedItems));
                                   
                                
                                        $grouped = collect($groupedItems)
                                        ->groupBy(function ($item) {
                                            return $item['binLocationID'] !== null ? $item['binLocationID'] : uniqid(); 
                                        })
                                        ->map(function ($group) {
                                            if ($group->first()['binLocationID'] === null) {
                                                return $group->first();
                                            }
                                    
                                            return [
                                                'id' => $group->first()['id'],
                                                'wac' => $group->first()['wac'],
                                                'binLocation' => $group->first()['binLocation'],
                                                'binLocationID' => $group->first()['binLocationID'],
                                                'binLocationDes' => $group->first()['binLocationDes'],
                                                'productBatchID' => $group->first()['productBatchID'], 
                                                'RemainingQty' => $group->sum('RemainingQty'),
                                            ];
                                        })
                                        ->values();
                                        
                                        foreach ($groupedItems as $newItem) {
                                            $binLocationDes = $newItem['binLocationDes'];
                                            $binLocationID = $newItem['binLocationID'];
                                            $totalWacCostLocalWarehouse = round($newItem['RemainingQty'] * $itemLedgerRecWarehouse->wacCostLocal, 2);
                                            $totalWacCostRptWarehouse = round($newItem['RemainingQty'] * $itemLedgerRecWarehouse->wacCostRpt, 2);
                                            $productBatchID = $newItem['productBatchID'];

                                            if($binLocationID == null)
                                            {
                                                $groupedNewItems[] = [
                                                    'binLocationDes' => null,
                                                    'binLocationID' => null,
                                                    'quantity' => $newItem['RemainingQty'],
                                                    'binLocation' => null,
                                                    'productBatchID' => $productBatchID,
                                                    'totalWacCostLocal' => $totalWacCostLocalWarehouse,
                                                    'totalWacCostRpt' => $totalWacCostRptWarehouse,
                                                    'IDS' => [$productBatchID]
                                                ];
                                            }

                                            else
                                            {
                                                if (isset($groupedNewItems[$binLocationID])) {
                                                    $groupedNewItems[$binLocationID]['quantity'] += $newItem['RemainingQty'];
                                                    $groupedNewItems[$binLocationID]['totalWacCostLocal'] += $totalWacCostLocalWarehouse;
                                                    $groupedNewItems[$binLocationID]['totalWacCostRpt'] += $totalWacCostRptWarehouse;
                                                    if (!in_array($productBatchID, $groupedNewItems[$binLocationID]['IDS'])) {
                                                        $groupedNewItems[$binLocationID]['IDS'][] = $productBatchID;
                                                    }
                                                } else {
                                                    $groupedNewItems[$binLocationID] = [
                                                        'binLocationDes' => $binLocationDes,
                                                        'binLocationID' => $binLocationID,
                                                        'quantity' => $newItem['RemainingQty'],
                                                        'binLocation' => $newItem['binLocation'],
                                                        'productBatchID' => $newItem['productBatchID'],
                                                        'totalWacCostLocal' => $totalWacCostLocalWarehouse,
                                                        'totalWacCostRpt' => $totalWacCostRptWarehouse,
                                                        'IDS' => [$productBatchID],
                                                    ];
                                                }
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
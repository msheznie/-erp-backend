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
                        'inOutQty' => 0);

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
                                            ->where('companySystemID',$params['companySystemID'])
                                            ->where('fromDamagedTransactionYN', 0)
                                            ->where('itemSystemCode',$params['itemCodeSystem'])
                                            ->groupBy('companySystemID','itemSystemCode')->first();


            if (!empty($itemLedgerRec)) {
                $output['wacValueLocal']     = $itemLedgerRec->wacCostLocal;
                $output['wacValueReporting'] = $itemLedgerRec->wacCostRpt;
                $output['totalWacCostLocal'] = $itemLedgerRec->totalWacCostLocal;
                $output['totalWacCostRpt']   = $itemLedgerRec->totalWacCostRpt;
                $output['inOutQty']          = $itemLedgerRec->inOutQty;
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
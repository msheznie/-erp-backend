<?php

namespace App\helper;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RolReachedNotification
{
    public static function getRolReachedNotification($companyID, $type)
    {
        $records = [];
        if ($type == 0) { // Same Day
            $records = DB::table("itemassigned")
                ->selectRaw("itemDescription, itemCodeSystem, minimumQty, IFNULL(ledger.INoutQty,0) as INoutQty,itemPrimaryCode,secondaryItemCode")
                ->join(DB::raw('(SELECT itemSystemCode, SUM(inOutQty) as INoutQty FROM erp_itemledger WHERE companySystemID = ' . $companyID . ' GROUP BY itemSystemCode) as ledger'), function ($query) {
                    $query->on('itemassigned.itemCodeSystem', '=', 'ledger.itemSystemCode');
                })
                ->where('companySystemID', '=', $companyID)
                ->where('financeCategoryMaster', '=', 1)
                ->where('isActive', '=', 1)
                ->whereRaw('ledger.INoutQty <= minimumQty')->get();
        }

        return $records;
    }

    public static function getRolReachedEmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Following items has been reached minimum order level <br/><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        <th style="text-align: center;border: 1px solid black;">Item Code</th> 
                        <th style="text-align: center;border: 1px solid black;">Item Description</th>
                        <th style="text-align: center;border: 1px solid black;">Min Qty</th>
                        <th style="text-align: center;border: 1px solid black;">Available Stock</th>
                    </tr>
                </thead>';
        $body .= '<tbody>';
        $x = 1;
        foreach ($details as $val) {
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . $val->secondaryItemCode . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->itemDescription . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->minimumQty . '</td> 
                <td style="text-right:left;border: 1px solid black;">' . $val->INoutQty . '</td> 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }


    public static function getReOrderLevelReachedNotification($companyID, $type)
    {
        $records = [];
        if ($type == 0) { // Same Day
            $records = DB::table("itemassigned")
                ->selectRaw("itemDescription, itemCodeSystem, rolQuantity, IFNULL(ledger.INoutQty,0) as INoutQty,itemPrimaryCode,secondaryItemCode")
                ->join(DB::raw('(SELECT itemSystemCode, SUM(inOutQty) as INoutQty FROM erp_itemledger WHERE companySystemID = ' . $companyID . ' GROUP BY itemSystemCode) as ledger'), function ($query) {
                    $query->on('itemassigned.itemCodeSystem', '=', 'ledger.itemSystemCode');
                })
                ->where('companySystemID', '=', $companyID)
                ->where('financeCategoryMaster', '=', 1)
                ->where('isActive', '=', 1)
                ->whereRaw('ledger.INoutQty <= rolQuantity')->get();
        }

        return $records;
    }

    public static function getReOrderLevelReachedEmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Following items has been reached  re-order level <br/><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        <th style="text-align: center;border: 1px solid black;">Item Code</th> 
                        <th style="text-align: center;border: 1px solid black;">Item Description</th>
                        <th style="text-align: center;border: 1px solid black;">Re-Order Level</th>
                        <th style="text-align: center;border: 1px solid black;">Available Stock</th>
                    </tr>
                </thead>';
        $body .= '<tbody>';
        $x = 1;
        foreach ($details as $val) {
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . $val->secondaryItemCode . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->itemDescription . '</td> 
                <td style="text-align:left;border: 1px solid black;">' . $val->rolQuantity . '</td> 
                <td style="text-right:left;border: 1px solid black;">' . $val->INoutQty . '</td> 
                </tr>';
            $x++;
        }
        $body .= '</tbody>
        </table>';
        return $body;
    }

     public static function getReOrderLevelPREmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= $details;
        return $body;
    }
}

<?php

namespace App\helper;

use App\Models\ProcumentOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PurchaseOrderPendingDeliveryNotificationService
{

    public static function getPurchaseOrderPendingDelivery($companyID, $type, $days, $documentID)
    {

        $today = Carbon::today()->toDateString();
        if ($type == 0) { // Same Day
            $whereDate = 'DATE(expectedDeliveryDate) = "' . $today . '"';
        } else if ($type == 1) { // Before 
            $days = $days * -1;
            $whereDate = 'DATE_ADD(DATE(expectedDeliveryDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"';
        } else if ($type == 2) { // After
            $whereDate = 'DATE_ADD(DATE(expectedDeliveryDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"';
        }

        return ProcumentOrder::whereRaw('( invoicedBooked = 2 OR invoicedBooked = 0 )')
            ->where('companySystemID', $companyID)
            ->where('poType_N', $documentID)
            ->whereRaw($whereDate)
            ->get();
    }

    public static function getPurchaseOrderEmailContent($details, $fullName, $documentID)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Following purchase order delivery dates are as follow :<br/><br/>";


        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="text-align: center;border: 1px solid black;">#</th>
                            <th style="text-align: center;border: 1px solid black;">Document Code</th> 
                            <th style="text-align: center;border: 1px solid black;">Expected Delivery Date</th>
                            <th style="text-align: center;border: 1px solid black;">Status</th> 
                        </tr>
                    </thead>';
        $body .= '<tbody>';
        $x = 1;
        foreach ($details as $val) {
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . $val->purchaseOrderCode . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . Carbon::parse($val->expectedDeliveryDate)->format('Y-m-d') . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . (($val->invoicedBooked == 1) ? 'Partial Received' : 'Not Received') . '</td>  
            </tr>';
            $x++;
        }
        $body .= '</tbody>
                </table>';
        return $body;
    }
}

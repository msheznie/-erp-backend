<?php

namespace App\helper;

use App\Models\PoPaymentTerms;
use Carbon\Carbon;

class AdvancePaymentNotification
{
    public static function getadvancePaymentDetails($companyID, $type, $days)
    {
        $today = Carbon::today()->toDateString();
        if ($type == 0) { // Same Day
            $whereDate = 'DATE(comDate) = "' . $today . '"';
        } else if ($type == 1) { // Before 
            $days = $days * -1;
            $whereDate = 'DATE_ADD(DATE(comDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"';
        } else if ($type == 2) { // After
            $whereDate = 'DATE_ADD(DATE(comDate), INTERVAL ' . $days . ' DAY) = "' . $today . '"';
        }

        $records = PoPaymentTerms::with(['purchase_order_master' => function ($q) use ($companyID) {
            $q->where('approved', -1)
                ->where('companySystemID', $companyID);
        }])
            ->where('isRequested', '=', 1)
            ->where('LCPaymentYN', '=', 2)
            ->WhereHas('purchase_order_master', function ($q) use ($companyID) {
                $q->where('approved', -1)
                    ->where('companySystemID', $companyID);
            })
            ->whereRaw($whereDate)
            ->get();
        return $records;
    }

    public static function getAdvancePaymentEmailContent($details, $fullName)
    {
        $body = "Dear {$fullName},<br/>";
        $body .= "Following purchase order advance payment expiry dates are as follow :<br/><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
        <thead>
            <tr>
                <th style="text-align: center;border: 1px solid black;">#</th>
                <th style="text-align: center;border: 1px solid black;">PO Code</th> 
                <th style="text-align: center;border: 1px solid black;">Commitment Date</th>
                <th style="text-align: center;border: 1px solid black;">Amount </th> 
            </tr>
        </thead>';
        $body .= '<tbody>';
        $x = 1;
        foreach ($details as $val) {
            $currencyDecimal = Helper::getCurrencyDecimalPlace($val->purchase_order_master->localCurrencyID);
            $body .= '<tr>
                <td style="text-align:left;border: 1px solid black;">' . $x . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . $val->purchase_order_master->purchaseOrderCode . '</td>  
                <td style="text-align:left;border: 1px solid black;">' . Carbon::parse($val->comDate)->format('Y-m-d') . '</td>   
                <td style="text-align:right;border: 1px solid black;">' . number_format($val->comAmount, $currencyDecimal) . '</td>  
            </tr>';
            $x++;
        }
        $body .= '</tbody>
                </table>';

        return $body;
    }
}

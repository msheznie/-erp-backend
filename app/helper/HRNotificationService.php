<?php

namespace App\helper;

use App\Models\HRDocumentDescriptionForms;
use App\Models\SMEEmpContractType;
use Carbon\Carbon;

class HRNotificationService
{
    public static function emp_expired_docs($company, $type, $days)
    {
        $expiry_date = self::get_expiry_date($type, $days);

        $data = HRDocumentDescriptionForms::selectRaw('DocDesID, PersonID, documentNo, expireDate, Erp_companyID')
            ->where('Erp_companyID', $company)
            ->where('PersonType', 'E')
            ->where('isDeleted', 0)
            ->whereDate('expireDate', $expiry_date);

        $data = $data->whereHas('master');
        $data = $data->whereHas('employee');

        $data = $data->with('master:DocDesID,DocDescription');

        $data = $data->with('employee:EIdNo,ECode,Ename2,EEmail');

        $data = $data->get();
        return $data;
        return [];
    }

    public static function email_body($details)
    {
        $fullName = '232';

        $body = "Dear {$fullName},<br/>";
        $body .= "Following items has been reaches minimum order level <br/><br/>";
        $body .= '<table style="width:100%;border: 1px solid black;border-collapse: collapse;">
                <thead>
                    <tr>
                        <th style="text-align: center;border: 1px solid black;">#</th>
                        <th style="text-align: center;border: 1px solid black;">Employee</th> 
                        <th style="text-align: center;border: 1px solid black;">Document Type</th>
                        <th style="text-align: center;border: 1px solid black;">Document Code</th>                        
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

    public static function emp_contract_docs($company, $type, $days){
        $expiry_date = self::get_expiry_date($type, $days);

        $data = SMEEmpContractType::selectRaw('EmpContractTypeID, Description')
            ->where('typeID', 2)
            ->where('Erp_CompanyID', $company);


        $data = $data->with(['emp_contract'=> function ($q){
            // $q->where('')
        }]);

        return $data->get();
    }

    public static function get_expiry_date($type, $days){
        // for same day $type will be 0 ( zero )
        $expiry_date = Carbon::now();

        if($type == 1){ //Before
            $expiry_date = $expiry_date->addDays($days);
        }
        elseif ($type == 2 ){ // After
            $expiry_date = $expiry_date->subDays($days);
        }

        return $expiry_date->format('Y-m-d');
    }
}

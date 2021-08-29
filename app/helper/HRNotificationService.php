<?php

namespace App\helper;

use App\Models\HRDocumentDescriptionForms;
use Carbon\Carbon;

class HRNotificationService
{
/*
   types = [
       1=> 'Before'   2=> 'After'   0=> 'Same Day
   ];
 * */

    public static function expired_docs($company, $type, $days)
    {

        // for same day $type will be 0 ( zero )
        $expiry_date = Carbon::now();

        if($type == 1){ //Before
            $expiry_date = $expiry_date->addDays($days);
        }
        elseif ($type == 2 ){ // After
            $expiry_date = $expiry_date->subDays($days);
        }

        $expiry_date = $expiry_date->format('Y-m-d');

        $data = HRDocumentDescriptionForms::selectRaw('DocDesID, PersonID, documentNo, expireDate, Erp_companyID')
            ->where('isDeleted', 0)
            ->where('PersonType', 'E')
            ->where('Erp_companyID', $company)
            ->whereDate('expireDate', $expiry_date);

        $data = $data->with('master:DocDesID,DocDescription');

        $data = $data->with(['employee' => function($q){
            $q->selectRaw('EIdNo,ECode,Ename2')
            ->with(['manager'=> function($q){
               $q->where('active', 1);
                $q->selectRaw('empID,managerID');
            }]);
        }]);//

        $data = $data->get();
        return $data;
        return [];
    }
}

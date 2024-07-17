<?php
namespace App\helper;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\DB;


class SME
{
    public static function s3_file_url($key, $min=null): string
    {
        if(empty($key)){
            return '';
        }

        //return Storage::disk('s3')->url($key);
        
        $min = ($min != null)? $min: 60;
        $min = Carbon::now()->addMinutes($min);
        return Storage::disk('s3')->temporaryUrl($key, $min);        
    }
   
    public static function policy($companyID, $code, $docCode){
        $policy = DB::select("SELECT polMas.companypolicymasterID, companyPolicyDescription,
                        polMas.`code`, IFNULL(cp.documentID,'All') AS documentID,
                        IF ( cp.`value` IS NULL, polMas.defaultValue, cp.`value` ) AS policyvalue
                        FROM srp_erp_companypolicymaster AS polMas
                        LEFT JOIN (
                            SELECT * FROM srp_erp_companypolicy WHERE companyID = {$companyID}
                        ) AS cp ON cp.companypolicymasterID = polMas.companypolicymasterID
                        WHERE polMas.`code` = '{$code}' AND polMas.documentID= '{$docCode}'");

        if(empty($policy)){
            return 0;
        }

        return $policy[0]->policyvalue;
    }
    
    public static function leaveBalanceBasedOn($companyID)
    {
        $leaveBalanceBasedOn = SME::policy($companyID, 'LC', 'All');
        return empty($leaveBalanceBasedOn)? 1 : $leaveBalanceBasedOn;
    }

    public static function accrualTriggerBasedOn($companyID)
    {
        $accrualTriggerBasedOn = SME::policy($companyID, 'MAT', 'All');
        return empty($accrualTriggerBasedOn)? 1 : $accrualTriggerBasedOn;
    }

    public static function user_info($column=null, $more_columns = []){
        $more_columns_str = '';

        if($more_columns){
            $more_columns_str = ', ' . implode(', ', $more_columns);
        }

        $empID = Helper::getEmployeeID();
        $data = DB::table('srp_employeesdetails')
                ->selectRaw('EIdNo AS empID, ECode AS empCode, Ename2 AS empName, UserName AS userName,
                    Erp_companyID AS companyID, segmentID, payCurrencyID, payCurrency, SchMasterId, 
                    branchID' . $more_columns_str)
                ->where('EIdNo', $empID)->first();

        return ($column != null)? $data->$column: $data;
    }
}

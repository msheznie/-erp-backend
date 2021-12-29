<?php
namespace App\helper;


use Illuminate\Support\Facades\DB;


class SME
{
   
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
}

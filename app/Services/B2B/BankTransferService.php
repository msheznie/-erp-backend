<?php

namespace App\Services\B2B;

use App\Models\Company;
use Carbon\Carbon;

class BankTransferService
{
    public static function getBankTransferType($transferDetails)
    {
       if(!isset($transferDetails))
           throw new \Exception("Trasnfer details not found");

       if(!isset($transferDetails['from']))
           throw new \Exception("From bank details not found");

       if(!isset($transferDetails['to']))
            throw new \Exception("To bank details not found");


       if($transferDetails['from']['bankID'] == $transferDetails['to']['bankID'])
       {
           return "TRF";
       }

    }


    public function generateBatchNo($companyID,$documentCode = null,$serialNo = 0)
    {

        if(!isset($companyID))
            throw new \Exception("Company ID not found");


        $company = Company::find($companyID,['companyShortCode']);

        $yearAndDocCode = explode($company->companyShortCode,$documentCode)[1] ?? null;

        if(!isset($yearAndDocCode))
            return new \Exception("Cannot generate Doc Code");

        if(isset($serialNo) && $serialNo > 0)
        {
            $serialNo;
        }

        $code = $yearAndDocCode.'/'.$serialNo;

        $formatted = str_replace('/', '\\', $code);

        $parts = explode('\\', $formatted);
        $lastPart = str_pad(end($parts), 3, '0', STR_PAD_LEFT);
        $parts[key($parts)] = $lastPart;

        $output = ltrim(implode('\\', $parts), '\\');

        return $output;
    }
}

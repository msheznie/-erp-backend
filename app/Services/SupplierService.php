<?php

namespace App\Services;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use phpDocumentor\Reflection\DocBlock\Tags\Throws;

class SupplierService
{

    public function __construct()
    {
    }

    /**
     * @param $token
     * @return mixed
     * @throws \Throwable
     */

    public function getTokenData($token)
    {
        $supplierDataUsingToken = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()]
            ])
            ->first();

        if(is_null($supplierDataUsingToken)){
            return false;
        }

        return $supplierDataUsingToken;
    }

 
}

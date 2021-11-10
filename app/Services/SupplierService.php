<?php

namespace App\Services;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;

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

        throw_unless($supplierDataUsingToken, "Invalid Token");

        return $supplierDataUsingToken;
    }

 
}

<?php

namespace App\Services;

use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
           ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()],
           ['status', 0]
        ])
            ->first();

        if(is_null($supplierDataUsingToken)){
            return false;
        }

        return $supplierDataUsingToken;
    }

    public function updateTokenStatus($token)
    {
        return DB::table('srm_supplier_registration_link')->where('token', $token)->update(['status' => 1]);
    }

 
}

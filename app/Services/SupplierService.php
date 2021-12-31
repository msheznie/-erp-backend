<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\SupplierRegistrationLink;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class SupplierService
{

    public function __construct()
    {
    }

    /**
     * @param $token
     * @return mixed
     * @throws Throwable
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

    public function updateTokenStatus($token, $supplierUuid)
    {
        return DB::table('srm_supplier_registration_link')->where('token', $token)->update(['status' => 1, 'uuid' => $supplierUuid]);
    }

    /**
     * create supplier approval setup
     * @param $data
     * @return array
     * @throws Throwable
     */
    public function createSupplierApprovalSetup($data)
    {
        $params = [
            'autoID'    => $data['autoID'],
            'company'   => $data['company'],
            'document'  => $data['documentID'],
            'email'  => $data['email']
        ];

        $confirm = Helper::confirmDocument($params); 
        throw_unless($confirm && $confirm['success'], $confirm['message']);

        return [
            'success'   => $confirm['success'],
            'message'   => $confirm['message'],
            'data'      => $params
        ];
    }
}

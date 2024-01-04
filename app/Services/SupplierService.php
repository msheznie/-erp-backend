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

    public function checkValidTokenData($token)
    {
        // Check Token Expired
        $supplierTokenExpired = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()]
        ])
            ->first();

        if (is_null($supplierTokenExpired)) {
            return 1;
        }

        // Check linked already used
        $supplierDataUsingToken = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()],
            ['status', 0]
        ])
            ->first();

        if (is_null($supplierDataUsingToken)) {
            return 2;
        }
    }

    public function getTokenData($token)
    {
        return $supplierDataUsingToken = SupplierRegistrationLink::where([
            ['token', $token],
            ['token_expiry_date_time', '>', Carbon::now()->toDateTimeString()],
            ['status', 0]
        ])
            ->first();
    }

    public function updateTokenStatus($token, $supplierUuid,$name,$email)
    {
        $data = [
          'status' => 1,
          'uuid' => $supplierUuid,
          'name' => $name,
          'email' => $email,
        ];

        return DB::table('srm_supplier_registration_link')->where('token', $token)->update($data);
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
      //  throw_unless($confirm && $confirm['success'], $confirm['message']);

        return [
            'success'   => $confirm['success'],
            'message'   => $confirm['message'],
            'data'      => $params
        ];
    }
}

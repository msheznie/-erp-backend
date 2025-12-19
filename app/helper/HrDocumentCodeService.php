<?php

namespace App\helper;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class HrDocumentCodeService
{
    public static function generate($company_id, $company_code, $documentID, $count = 0){
        $code = '';

        $data = DB::table('srp_erp_documentcodemaster')
            ->select('documentID', 'prefix', 'serialNo', 'formatLength', 'format_1', 'format_2', 'format_3', 'format_4', 'format_5', 'format_6')
            ->where('documentID', $documentID)->where('companyID', $company_id)->first();

        if (empty($data)) {
            $userId = 11; //this service mostly used by Job, where we do not have a user who perform the Job 
            $userName = 'Admin';

            $data_arr = [
                'documentID' => $documentID, 'prefix' => $documentID, 'companyID' => $company_id, 'companyCode' => $company_code,
                'createdUserID' => $userId, 'createdUserName' => $userName,
                'createdPCID' => gethostname(), 'createdDateTime' => Carbon::now(),
                'startSerialNo' => 1, 'formatLength' => 6,
                'format_1' => 'prefix', 'format_2' => NULL, 'format_3' => NULL, 'format_4' => NULL, 'format_5' => NULL,
                'format_6' => NULL, 'serialNo' => 1,
            ];

            DB::table('srp_erp_documentcodemaster')->insert($data_arr);
            $data = $data_arr;
        }
        else {
            $data = get_object_vars($data); //convert object to array

            DB::table('srp_erp_documentcodemaster')->where([
                'documentID'=> $documentID, 'companyID'=> $company_id
            ])->update(['serialNo'=> ($data['serialNo'] + 1)]);

            $data['serialNo'] = ($data['serialNo'] + 1);
        }


        if ($data['format_1']) {
            if ($data['format_1'] == 'prefix') {
                $code .= $data['prefix'];
            }
            if ($data['format_1'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_1'] == 'yy') {
                $documentYear = date('Y');
                $code .= substr($documentYear, -2);
            }
            if ($data['format_1'] == 'mm') {
                $code .= date('m');
            }
        }
        if ($data['format_2']) {
            $code .= $data['format_2'];
        }
        if ($data['format_3']) {
            if ($data['format_3'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_3'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_3'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_3'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_4']) {
            $code .= $data['format_4'];
        }
        if ($data['format_5']) {
            if ($data['format_5'] == 'mm') {
                $code .= date('m');
            }
            if ($data['format_5'] == 'yyyy') {
                $code .= date('Y');
            }
            if ($data['format_5'] == 'yy') {
                $code .= date('y');
            }
            if ($data['format_5'] == 'prefix') {
                $code .= $data['prefix'];
            }
        }
        if ($data['format_6']) {
            $code .= $data['format_6'];
        }
        if ($count == 0) {
            $number = $data['serialNo'];
        } else {
            $number = $count;
        }
        return ($company_code . '/' . $code . str_pad($number, $data['formatLength'], '0', STR_PAD_LEFT));
    }
}

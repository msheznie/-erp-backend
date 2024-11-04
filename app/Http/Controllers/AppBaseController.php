<?php
/**
 * Created by PhpStorm.
 * User: Fayaz
 * Date: 2/19/2018
 * Time: 12:36 PM
 */

namespace App\Http\Controllers;
use Illuminate\Routing\Controller as BaseController;
use InfyOm\Generator\Utils\ResponseUtil;
use Response;


class AppBaseController extends BaseController
{

    public function  sendResponse($result, $message)
    {
        return Response::json(ResponseUtil::makeResponse($message, $result));
    }
    public function sendError($error, $code = 404,$errorType = array('type' => ''))
    {
        return Response::json(ResponseUtil::makeError($error,$errorType), $code);
    }

    public function sendAPIError($error, $code = 422,$data = [])
    {
        $res = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($data)) {
            $res['errors'] = $data;
        }

        return Response::json($res, $code);
    }

    public function sendReponseWithDetails($data,$message,$type,$detail)
    {
        $res = [
            'success' => true,
            'data' => $data,
            'message' => $message,
            'type' => 1,
            'detail' => $detail
        ];

        return Response::json($res);
    }



    public function convertArrayToValue ($input){
        foreach ($input as $key => $value) {
            if (is_array($input[$key])){
                if(count($input[$key]) > 0){
                    $input[$key] = $input[$key][0];
                }else{
                    $input[$key] = null;
                }
            }
        }
        return $input;
    }

    public function convertArrayToSelectedValue ($input,$params){
        foreach ($input as $key => $value) {
            if(in_array($key,$params)){
                if (is_array($input[$key])){
                    if(count($input[$key]) > 0){
                        $input[$key] = $input[$key][0];
                    }
                }
            }
        }
        return $input;
    }


}

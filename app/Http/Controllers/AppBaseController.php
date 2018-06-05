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
    public function sendError($error, $code = 404)
    {
        return Response::json(ResponseUtil::makeError($error), $code);
    }


    public function convertArrayToValue ($input){
        foreach ($input as $key => $value) {
            if (is_array($input[$key])){
                if(count($input[$key]) > 0){
                    $input[$key] = $input[$key][0];
                }else{
                    $input[$key] = 0;
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
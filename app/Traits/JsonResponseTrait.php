<?php

namespace App\Traits;

trait JsonResponseTrait
{
    public static function sendJsonResponse($status, $message, $code = null, $data = null){
        return ['status' => $status, 'message' => $message, 'code' => $code,  'data' => $data];
    }
}

<?php

namespace App\helper;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;


class IvmsService
{
    protected $url;
    protected $http;
    protected $sessionID;

     public static function performRequest($method, $requestUrl, $formParams = [])
    {

        try {
            $client = new Client([
                'base_uri' => env("IVMS_URL"),
            ]);

            $headers['Content-Type'] = "application/json";

            $response = $client->request($method, $requestUrl, [
                'query' => $formParams,
                'headers' => $headers,
            ]);

            return json_decode($response->getBody(), true); 
        }catch (ClientException $exception) {
            return  $exception->getResponse()->getBody(true);
        }
    }

    public static function postDeliveryOrder($orderParams)
    {
        $tokenResponse = self::refreshSession();
        $orderData = [];
        if ($tokenResponse['status']) {
            $params = [
                'svc' => 'order/update',
                'params' => $orderParams,
                'sid' => $tokenResponse['sessionID']
            ];

            $orderRes = self::performRequest("POST","/wialon/ajax.html",$params);

            if (isset($orderRes['error'])) {
                return ['data' => $orderData, 'status' => false, 'message' => $orderRes['reason']];
            }

            return ['data' => $orderRes, 'status' => true, 'message' => "success"];
        } else {
            return ['data' => $orderData, 'status' => false, 'message' => "token expired"];
        }
    }

    private static function refreshSession()
    {
        $token = env("IVMS_TOKEN");

        if (isset($token) && !is_null($token)) {
            $params = ['svc' => 'token/login','params' => '{"token":"'.$token.'","fl":4}'];

            $res = self::performRequest("GET", "/wialon/ajax.html", $params);  

            if (isset($res['eid'])) {
                return ['status' => true, 'sessionID' => $res['eid']];
            } else {
                 return ['status' => false];
            }
        }
        return ['status' => false];
    }
}

<?php

namespace App\Services;

use App\helper\Helper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Jobs\WebPushNotification;
use GuzzleHttp\Client;

class WebPushNotificationService
{

    public static function sendNotification($data, $type , $userIds = [], $dataBase = "")
    {
        switch ($type) {
            case 1: // confirm doc
                $data['clickable'] = true;
                $data['type'] = 1;
                break;

            case 2: // approve doc
                $data['clickable'] = true;
                $data['type'] = 2;
                break;
            
            default:
                // code...
                break;
        }
        
        $data['time'] = Carbon::now()->format('Y-m-d H:i:s');
        
        foreach ($userIds as $key => $value) {
            WebPushNotification::dispatch($dataBase, $data, $value);
        }

        return ['status' => true];
    }

    public static function getUserNotifications()
    {
        $currentUserID = Helper::getEmployeeSystemID();

        $client = new Client();
        $url = env("WEB_PUSH_URL")."/notifications-by-user/".$currentUserID;

        $response = $client->request('GET', $url);


        if ($response) {
            $notificationData = collect(json_decode($response->getBody(), true))->take(7);

            $notificationDataRes = [];
            foreach ($notificationData as $key => $value) {
                $value['data']['time'] = Carbon::parse($value['data']['time'])->diffForHumans();

                $notificationDataRes[] = $value;
            }


            return ['notifications' => $notificationDataRes, 'newNotificationCount' => collect(json_decode($response->getBody(), true))->where('read', 0)->count()];
        } else {
            return ['notifications' => [], 'newNotificationCount' => 0];
        }
    }

    public static function getAllNotifications()
    {
        $currentUserID = Helper::getEmployeeSystemID();

        $client = new Client();
        $url = env("WEB_PUSH_URL")."/notifications-by-user/".$currentUserID;

        $response = $client->request('GET', $url);


        if ($response) {
            $notificationData = json_decode($response->getBody(), true);

            return $notificationData;
        } else {
            return [];
        }
    }

    public static function updateNotifications($updateData)
    {
        $currentUserID = Helper::getEmployeeSystemID();

        $client = new Client();
        $url = env("WEB_PUSH_URL")."/notifications/".$updateData['_id'];
       
        $params['read'] = 1;

        $response = $client->request('PUT', $url, ['json' => $params]);

        return ['status' => true];
    }
}

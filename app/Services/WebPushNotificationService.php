<?php

namespace App\Services;

use App\helper\Helper;
use App\Models\SupplierRegistrationLink;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\helper\CommonJobService;
use App\Jobs\WebPushNotification;
use GuzzleHttp\Client;

class WebPushNotificationService
{
    /*
        $data = [
            'title' => '',
            'body' => '',
            'clickable' => '',
            'type' => ,
            'time' => ,
            'url' => ['erp' => '', 'portal' => '', 'hrms' => ''],

        ],
        user_id = '',
        apps = '',
        read = '',

    **/
    public static function sendNotification($data, $type , $userIds = [], $dataBase = "", $notifyTo = 'user')
    {
        $apps = [];
        switch ($type) {
            case 1: // confirm doc
                $data['clickable'] = true;
                $data['type'] = 1;
                $data['url'] = ['erp' => $data['url'], 'portal' => $data['url']];
                $apps = ['erp', 'portal'];
                break;

            case 2: // approve doc
                $data['clickable'] = true;
                $data['type'] = 2;
                $data['url'] = ['erp' => $data['url']];
                $apps = ['erp'];
                break;
            case 3: // report
                $data['clickable'] = true;
                $data['type'] = 3;
                $apps = ['erp'];
                break;
            case 4: // SRM Related
                $data['clickable'] = true;
                $data['type'] = 4;
                $data['url'] = ['srm' => $data['url']];
                $apps = ['srm'];
                break;
            
            default:
                // code...
                break;
        }
        
        $data['time'] = Carbon::now()->format('Y-m-d H:i:s');

        if($notifyTo == 'user'){
            foreach ($userIds as $key => $value) {
                $employee = Employee::with(['user_data'])->find($value);
                if ($employee && $employee->user_data) {
                    WebPushNotification::dispatch($dataBase, $data, $employee->user_data->uuid, $apps);
                }
            }
        }elseif($notifyTo == 'supplier') {

            $supplierData = SupplierRegistrationLink::where('id',$userIds)->first();
            $supplierUUID =  $supplierData->uuid_notification;
            WebPushNotification::dispatch($dataBase, $data, $supplierUUID, $apps,107);
        }

        return ['status' => true];
    }

    public static function getUserNotifications()
    {
        $currentUserID = Helper::getEmployeeUUID();

        try {

            $client = new Client();
            $url = env("WEB_PUSH_URL")."/notifications-by-user";

            $params['apps'] = ['erp'];
            $params['uuid'] = $currentUserID;
            $response = $client->request('POST', $url, ['json' => $params]);


            if ($response) {
                $notificationData = collect(json_decode($response->getBody(), true))->take(7);

                $notificationDataRes = [];
                foreach ($notificationData as $key => $value) {
                    $value['data']['time'] = Carbon::parse($value['data']['time'])->diffForHumans();

                    $translatedTitle = trans('custom.'.$value['data']['title']);
                    if($translatedTitle !== 'custom.'.$value['data']['title']){
                        $value['data']['title'] = $translatedTitle;
                    }

                    $notificationDataRes[] = $value;
                }

                return ['notifications' => $notificationDataRes, 'newNotificationCount' => collect(json_decode($response->getBody(), true))->where('read', 0)->count()];
            } else {
                return ['notifications' => [], 'newNotificationCount' => 0];
            }
        } catch (\Exception $exception) {
            return ['notifications' => [], 'newNotificationCount' => 0];
        }
    }

    public static function getAllNotifications()
    {
        $currentUserID = Helper::getEmployeeUUID();
        try {
            $client = new Client();
            $url = env("WEB_PUSH_URL")."/notifications-by-user";

            $params['apps'] = ['erp'];
            $params['uuid'] = $currentUserID;
            $response = $client->request('POST', $url, ['json' => $params]);

            if ($response) {
                $notificationData = json_decode($response->getBody(), true);

                foreach ($notificationData as $key => $value) {
                    $translatedTitle = trans('custom.'.$value['data']['title']);
                    if($translatedTitle !== 'custom.'.$value['data']['title']){
                        $notificationData[$key]['data']['title'] = $translatedTitle;
                    }
                }

                return $notificationData;
            } else {
                return [];
            }
        } catch (\Exception $exception) {
            return [];
        }
    }

    public static function updateNotifications($updateData)
    {

        try {
            $client = new Client();
            $url = env("WEB_PUSH_URL")."/notifications/".$updateData['_id'];
           
            $params['read'] = 1;

            $response = $client->request('PUT', $url, ['json' => $params]);

            return ['status' => true];
        } catch (\Exception $exception) {
            return ['status' => false];
        }
    }

    public static function processnotificationData($notification)
    {
        switch ($notification['data']['type']) {
            case 1:
            case 2:
                return ['url' => $notification['data']['url']['erp'], 'type' => 'self'];
                break;
            case 3:
                return ['url' => \Helper::getFileUrlFromS3($notification['data']['path']), 'type' => 'external'];
                break;
            default:
                return ['url' => "", 'type' => ''];
                break;
        }
    }
}

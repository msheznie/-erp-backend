<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\Models\FcmToken;
use LaravelFCM\Message\Topics;
use App\helper\CommonJobService;

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $pushNotificationArray;
    protected $pushNotificationUserIds;
    protected $notificationType;
    protected $sendPushNotification = true;
    protected $dataBase;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pushNotificationArray, $pushNotificationUserIds, $notificationType, $dataBase = "")
    {
        if ($dataBase != "") {
            if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
                if(env('IS_MULTI_TENANCY',false)){
                     self::onConnection('database_main');
                }else{
                     self::onConnection('database');
                }
            }else{
                self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
            }
        }

        $this->dataBase = $dataBase;

        $this->pushNotificationArray = $pushNotificationArray;
        $this->pushNotificationUserIds = $pushNotificationUserIds;
        $this->notificationType = $notificationType;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->dataBase != "") {
            CommonJobService::db_switch($this->dataBase);
        }

        Log::useFiles(storage_path() . '/logs/push_notification_created.log');

        if ($this->sendPushNotification) {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);
            $payLoadData = array();
            $description = "";
            $userIDs = [];
            if (!empty($this->pushNotificationArray)) {
                $payLoadData = $this->pushNotificationArray;
                $description = $this->pushNotificationArray['pushNotificationMessage'];
            }

            if (!empty($this->pushNotificationUserIds)) {
                $userIDs = $this->pushNotificationUserIds;
            }
           
            $notification_title = 'GEARS ERP Notification';
            $iosTokens = [];
            $androidTokens = [];
            $tokenArray = FcmToken::whereIn('userID', $userIDs)->get();

            foreach ($tokenArray as $key => $value) {
                if ($value->deviceType == "apple") {
                    $iosTokens[] = $value->fcm_token;
                } else {
                    $androidTokens[] = $value->fcm_token;
                }
            }


            if (!empty($iosTokens)) {
                $added_data = [
                    'notification_type' => $this->notificationType,
                    'title' => $notification_title,
                    'body' => $description,
                    'payload' => $payLoadData
                ];

                $notificationBuilder = new PayloadNotificationBuilder($notification_title);
                $notificationBuilder->setBody($description)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData($added_data);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data = $dataBuilder->build();
                
                $downstreamResponse = FCM::sendTo($iosTokens, $option, $notification, $data);
                $resp = [
                    $downstreamResponse->numberSuccess(),
                    $downstreamResponse->numberFailure(),
                    $downstreamResponse->numberModification(),
                ];
            } else {
                Log::error("IOS FCM token not found");
            }

            if (!empty($androidTokens)) {
                $added_data = [
                    'notification_type' => $this->notificationType,
                    'title' => $notification_title,
                    'body' => $description,
                    'data' => $payLoadData
                ];

                $notificationBuilder2 = new PayloadNotificationBuilder($notification_title);
                $notificationBuilder2->setBody($description)
                    ->setSound('default');

                $dataBuilder2 = new PayloadDataBuilder();
                $dataBuilder2->addData($added_data);

                $option = $optionBuilder->build();
                $notification = $notificationBuilder2->build();
                $data2 = $dataBuilder2->build();
                $downstreamResponse = FCM::sendTo($androidTokens, $option, null, $data2);
                $resp = [
                    $downstreamResponse->numberSuccess(),
                    $downstreamResponse->numberFailure(),
                    $downstreamResponse->numberModification(),
                ];
            } else {
                Log::error("ANDROID FCM token not found");
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

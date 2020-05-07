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

class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $pushNotificationArray;
    protected $pushNotificationUserIds;
    protected $notificationType;
    protected $sendPushNotification = true;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($pushNotificationArray, $pushNotificationUserIds, $notificationType)
    {
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
        Log::useFiles(storage_path() . '/logs/push_notification_created.log');
        Log::info('Successfully start push_notification_created' . date('H:i:s'));

        if ($this->sendPushNotification) {
            $optionBuilder = new OptionsBuilder();
            $optionBuilder->setTimeToLive(60 * 20);
            $data = array();
            $description = "";
            $userIDs = [];
            if (!empty($this->pushNotificationArray)) {
                $data = $this->pushNotificationArray;
                $description = $this->pushNotificationArray['pushNotificationMessage'];
            }

            if (!empty($this->pushNotificationUserIds)) {
                $userIDs = $this->pushNotificationUserIds;
            }
           
            $notification_title = 'GEARS ERP Notification';
            $added_data = [
                'notification_type' => $this->notificationType,
                'title' => $notification_title,
                'body' => $description,
                'data' => $data
            ];
            Log::info("------------ push notification added data ---------");
            $notificationBuilder = new PayloadNotificationBuilder($notification_title);
            $notificationBuilder->setBody($description)
                ->setSound('default');
            $dataBuilder = new PayloadDataBuilder();
            $dataBuilder->addData($added_data);
            $option = $optionBuilder->build();
            $notification = $notificationBuilder->build();
            $data = $dataBuilder->build();
            $tokens = FcmToken::whereIn('userID', $userIDs)->pluck('fcm_token')->toArray();
            if (!empty($tokens)) {
                $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);
                Log::info("------------ push notification sent ... ---------");
                $resp = [
                    $downstreamResponse->numberSuccess(),
                    $downstreamResponse->numberFailure(),
                    $downstreamResponse->numberModification(),
                ];
                Log::info(json_encode($resp));
            } else {
                Log::info("FCM token not found");
            }
        }
        Log::info("------------ push notification end ---------");
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

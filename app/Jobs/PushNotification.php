<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use App\Models\FcmToken;
use App\Jobs\FcmNotification;
use App\helper\CommonJobService;


class PushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $pushNotificationArray;
    protected $pushNotificationUserIds;
    protected $notificationType;
    protected $sendPushNotification = false;
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
            $tokens = [];
            $androidTokens = [];
            $tokenArray = FcmToken::whereIn('userID', $userIDs)->get();

            $tokens = count($tokenArray) > 0 ? collect($tokenArray)->pluck('fcm_token')->toArray() : [];

            if (count($tokens) > 0) {
                FcmNotification::dispatch($tokens, $notification_title, $description, $payLoadData);
            }
        }
    }

    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

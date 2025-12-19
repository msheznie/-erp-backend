<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Log;

class WebPushNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $dispatch_db;
    public $userId;
    public $pushData;
    public $apps;
    public $webPushAppNameDocumentWise;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dispatch_db, $pushData, $userId, $apps, $webPushAppNameDocumentWise = 0)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }
        $this->dispatch_db = $dispatch_db;
        $this->userId = $userId;
        $this->pushData = $pushData;
        $this->apps = $apps;
        $this->webPushAppNameDocumentWise = $webPushAppNameDocumentWise;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles(storage_path() . '/logs/web-push.log');
        $db = $this->dispatch_db;
        $data = $this->pushData;
        $userID = $this->userId;
        $appsList = $this->apps;
        CommonJobService::db_switch($db);

        $webPushAppName = $this->getWebPushNameDocumentWise($this->webPushAppNameDocumentWise);
        try {

            $client = new Client();
            $url = env("WEB_PUSH_URL")."/push-notification-web";

            $params['userId'] = $userID;
            $params['data'] = $data;
            $params['apps'] = $appsList;
            $params['appName'] = $webPushAppName;

            $response = $client->request('POST', $url, ['json' => $params]);

        }catch (ClientException $exception) {
            Log::error("Error");
            Log::error($exception->getResponse()->getBody(true));
        }
    }

    public function getWebPushNameDocumentWise($webPushAppNameDocumentWise){
        $webPushAppName = env("WEB_PUSH_APP_NAME");
        if($webPushAppNameDocumentWise == 107){
            $webPushAppName = env("WEB_PUSH_APP_NAME_SRM");
        }
        return $webPushAppName;
    }

}

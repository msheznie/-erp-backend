<?php

namespace App\Jobs\OSOS_3_0;
use App\Services\OSOS_3_0\DesignationService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;

class DesignationWebHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    protected $postType;
    protected $thirdPartyData;
    protected $dataBase;

    public function __construct($dataBase, $postType, $id, $thirdPartyData)
    {
        if(env('QUEUE_DRIVER_CHANGE','database') == 'database'){
            if(env('IS_MULTI_TENANCY',false)){
                self::onConnection('database_main');
            }else{
                self::onConnection('database');
            }
        }else{
            self::onConnection(env('QUEUE_DRIVER_CHANGE','database'));
        }

        $this->dataBase = $dataBase;
        $this->id = $id;
        $this->postType = $postType;
        $this->thirdPartyData = $thirdPartyData;
    }

    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);

        $designationService = new DesignationService($this->dataBase, $this->id, $this->postType, $this->thirdPartyData);
        $resp = $designationService->execute();

        if(isset($resp['status']) && !$resp['status']){
            $this->callDesignationService($resp['code'], 'Location');
        }
    }

    function callDesignationService($statusCode, $desc)
    {

        if (!in_array($statusCode, [200, 201])) {
            for ($i = 1; $i <= 3; $i++) {
                $msg = 'API Designation attempt' . $i;
                $this->insertToLogTb($msg, 'info', $desc, $this->thirdPartyData['company_id']);

                $locationService = new DesignationService(
                    $this->dataBase,
                    $this->id, $this->postType,
                    $this->thirdPartyData
                );

                $resp = $locationService->execute();
                if (isset($resp['status']) && in_array($resp['status'], [200, 201])) {
                    return true;
                }
            }
        }
        return true;
    }
}

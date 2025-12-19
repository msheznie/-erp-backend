<?php

namespace App\Jobs\OSOS_3_0;

use App\helper\CommonJobService;
use App\Services\OSOS_3_0\EmployeeService;
use App\Traits\OSOS_3_0\JobCommonFunctions;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class EmployeeWebHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $id;
    protected $postType;
    protected $thirdPartyData;
    protected $dataBase;
    use JobCommonFunctions;

    /**
     * Create a new job instance.
     *
     * @return void
     */
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

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);

        $service = new EmployeeService($this->dataBase, $this->id, $this->postType, $this->thirdPartyData);
        $resp = $service->execute();

        if(isset($resp['status']) && !$resp['status']){
            $this->callEmployeeService($resp['code'], 'Employee');
        }
    }

    function callEmployeeService($statusCode, $desc)
    {
        if (!in_array($statusCode, [200, 201])) {
            for ($i = 1; $i <= 3; $i++) {
                $logData = ['message' => 'Api Employee attempt'. $i, 'id' => $this->id ];
                $this->insertToLogTb($logData, 'info', $desc, $this->thirdPartyData['company_id']);

                $service = new EmployeeService(
                    $this->dataBase, $this->id, $this->postType, $this->thirdPartyData
                );

                $resp = $service->execute();
                if (isset($resp['status']) && in_array($resp['status'], [200, 201])) {
                    return true;
                }
            }
        }

        return true;
    }
}

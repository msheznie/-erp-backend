<?php

namespace App\Jobs;

use App\Models\Employee;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\helper\CommonJobService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserWebHook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $api_external_key;
    protected $api_external_url;
    protected $dataBase;
    protected $empID;
    public function __construct($dataBase, $empID, $api_external_key, $api_external_url)
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
        $this->empID = $empID;
        $this->api_external_key = $api_external_key;
        $this->api_external_url = $api_external_url;
    }

    public function handle()
    {
        CommonJobService::db_switch($this->dataBase);
        DB::beginTransaction();
        try {
            Log::useFiles(storage_path().'/logs/create_user_web_hook.log');
            $api_external_key = $this->api_external_key;
            $api_external_url = $this->api_external_url;

            if($api_external_key != null && $api_external_url != null) {

                $employees = Employee::selectRaw('empFullName', 'empEmail', 'empTelMobile')->where('employeeSystemID', $this->empID)->first();

                $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP '.$api_external_key
                ];
                $res = $client->request('POST', $api_external_url . '/create_customer', [
                    'headers' => $headers,
                    'json' => [
                        'data' => $employees
                    ]
                ]);
                $json = $res->getBody();

                Log::info('API guzzle: ' . $json);
            }
        } catch (\Exception $e)
        {
            DB::rollback();
            Log::info('Error Line No: ' . $e->getLine());
            Log::info('Error Line No: ' . $e->getFile());
            Log::info($e->getMessage());
            Log::info('---- GL  End with Error-----' . date('H:i:s'));
        }
    }
}

<?php

namespace App\Jobs;

use App\helper\CommonJobService;
use App\Models\ItemAssigned;
use App\Models\ThirdPartyIntegrationKeys;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ItemWACTriggerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tenantDb;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($tenantDb)
    {
        if(env('IS_MULTI_TENANCY',false)){
            self::onConnection('database_main');
        }else{
            self::onConnection('database');
        }

        $this->tenantDb = $tenantDb;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::useFiles( CommonJobService::get_specific_log_file('item-wac-amount') );
        try {

        CommonJobService::db_switch($this->tenantDb);

        $integrationKeys = ThirdPartyIntegrationKeys::where('api_external_key', '!=', null)->where('api_external_url', '!=', null)->where('third_party_system_id', 2)->get();

        foreach ($integrationKeys as $integrationKey) {


            $queryResult = DB::table('itemassigned')
                ->select('itemCodeSystem', 'companySystemID')->where('itemassigned.isActive', 1)->where('itemassigned.isAssigned', -1)->where('itemassigned.companySystemID', $integrationKey->company_id)
                ->get();


            $data = $queryResult->map(function ($item) {

                $data = array('companySystemID' => $item->companySystemID,
                    'itemCodeSystem' => $item->itemCodeSystem,
                    'wareHouseId' => null);

                $itemCurrentCostAndQty = \Inventory::itemCurrentCostAndQty($data);
                return [
                    'itemAutoID' => $item->itemCodeSystem,
                    'wacAmount' => $itemCurrentCostAndQty['wacValueLocal'],
                ];
            });

            $result = ['itemWacArray' => $data];

            $client = new Client();
                $headers = [
                    'content-type' => 'application/json',
                    'Authorization' => 'ERP ' . $integrationKey->api_external_key
                ];
                $res = $client->request('POST', $integrationKey->api_external_url . '/update_item_wac_amount', [
                    'headers' => $headers,
                    'json' =>  $result
                ]);
                $json = $res->getBody();

            }
        }
        catch (\Exception $e){
            Log::error($this->failed($e));
        }

    }
    public function failed($exception)
    {
        return $exception->getMessage();
    }
}

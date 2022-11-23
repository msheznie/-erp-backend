<?php

namespace App\Services;

use App\Jobs\POSMapping;
use App\Models\POSInvoiceSource;
use App\Models\POSMappingMaster; 
use App\Models\POSTransErrorLog;
use App\Models\POSTransLog;
use App\Models\POSTransStatus;  
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSService
{


    public function __construct()
    {
    }

    public function getMappingData(Request $request)
    {
        $db = $request->input('db');

      /*   if (!Schema::connection('mysql')->hasTable('pos_mapping_master') || !Schema::connection('mysql')->hasTable('pos_mapping_detail')) {
            return [
                'success' => false,
                'message' => 'Mapping table does not exist',
                'data' => null
            ];
        } */

        $getMapping = POSMappingMaster::with(['mapping_detail'])
            ->where('key', $request->input('request'))
            ->whereHas('mapping_detail')
            ->first();

        if (empty($getMapping)) {
            return [
                'success' => false,
                'message' => 'Mapping data does not exist',
                'data' => null
            ];
        }
        $MappingDataArrFilter = collect($getMapping['mapping_detail'])->map(function ($group) use ($request) { 
            return $request->input('data.' . $group['key']); 
        });
 
        $filtered = $MappingDataArrFilter->filter(function ($value, $key) {
            return $value != null;
        })->values()->all();/* 
        return ['success' => false, 'data' =>($filtered)  , 'message' => 'asdasdasdadas'];  */

        if (count($filtered) == 0) {
            return [
                'success' => false,
                'message' => 'No data to sync',
                'data' => null
            ];
        } else {
            return  self::insertStagingTable($getMapping['mapping_detail'], $request, $getMapping['id'], $db);
        }
    }

    public function insertStagingTable($MappingDetail, $request, $mapping_id, $db)
    { 
        $logExist = POSTransLog::where('pos_mapping_id', $mapping_id)->whereIn('status', [1, 4, 2])->first();
        if ($logExist) {
            $logStatus = POSTransStatus::where('id', $logExist['status'])->first();
            return ['success' => false, 'data' => null, 'message' => $logStatus['description'] . ' please try again later'];
        }
        $LogTransactionCreate =  self::LogTransactionCreate($mapping_id, 1, 'c');
        DB::beginTransaction();
        try {
            collect($MappingDetail)->map(function ($group) use ($request, $LogTransactionCreate) {
                $namespacedModel = 'App\Models\\' . $group["model_name"];
                $dataUpdate = $request->input('data.' . $group['key']); 
                $dataUpdate2 = collect($dataUpdate)->map(function ($group2) use ($request, $LogTransactionCreate) {
                    $group2['transaction_log_id']  = $LogTransactionCreate['data'];
                    return $group2;
                });  
                foreach(array_chunk($dataUpdate2->toArray(),1000) as $t){ 
                    $namespacedModel::insert($t);
                } 
            }); 
            $LogTransactionCreate  = self::LogTransactionCreate($mapping_id, 2, 'u', $LogTransactionCreate['data']);
            self::insertSourceTransactionJOB($LogTransactionCreate['data'],$db); 
           // self::createSourceTransaction($LogTransactionCreate['data']);
            DB::commit();
            return ['success' => true, 'message' => 'Data synced', 'data' => 2];
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);
            $transactionErrorLog = self::insertTransactionError($LogTransactionCreate['data'], $e->getMessage());
            $LogTransactionCreate = self::LogTransactionCreate($mapping_id, 3, 'u', $LogTransactionCreate['data']);
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }


    public function insertSourceTransactionJOB($logId, $db)
    {
        $db = isset($db) ? $db : "";
        POSMapping::dispatch($logId, $db);
        return true;
    }

    public static function LogTransactionCreate($mapping_id, $status, $type, $transactionId = null)
    {
        
        try {
            $data['pos_mapping_id'] = $mapping_id;
            $data['status'] = $status;
            if ($type == 'c') {
                $result = POSTransLog::create($data);
                $transactionId = $result['id'];
            } else {
                $result =  POSTransLog::find($transactionId)
                    ->update($data);
            }
            if ($result) {
                return ['success' => true, 'data' => $transactionId, 'message' => 'POS transaction log created successfully'];
            }
        } catch (\Throwable $e) { 
            Log::error($e); 
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }

    public static function createSourceTransaction($logId)
    {
        $posMappingData = POSTransLog::with(['posMappingMaster' => function ($q) {
            $q->with('mapping_detail');
        }])->where('id', $logId)->first();

        self::LogTransactionCreate($posMappingData['pos_mapping_id'], 4, 'u', $logId);
        DB::beginTransaction();
        try {
            collect($posMappingData['posMappingMaster']['mapping_detail'])->map(function ($group) use ($posMappingData, $logId) {
                $namespacedModel = 'App\Models\\' . $group["model_name"];
                $namespacedModelSource = 'App\Models\\' . $group["source_model_name"];
                $data = $namespacedModel::where('transaction_log_id', $logId)->get()->toArray(); 
                foreach(array_chunk($data,1000) as $t){ 
                    $namespacedModelSource::insert($t);
                } 
                //$namespacedModel::truncate();
            });
            
            collect($posMappingData['posMappingMaster']['mapping_detail'])->map(function ($group) {
                $namespacedModel = 'App\Models\\' . $group["model_name"]; 
                $namespacedModel::truncate();
            }); 
            self::LogTransactionCreate($posMappingData['pos_mapping_id'], 5, 'u', $logId); 
            DB::commit(); 
            return ['success' => true, 'message' => 'Successfully synced', 'data' =>  2];
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);
            self::insertTransactionError($logId, $e->getMessage()); 
            self::LogTransactionCreate($posMappingData['pos_mapping_id'], 6, 'u', $logId); 
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }

    public static function insertTransactionError($logId, $errorMsg)
    {
        $data['log_id'] = $logId;
        $data['error'] = $errorMsg;
        POSTransErrorLog::insert($data);
        return true;
      
    }
}

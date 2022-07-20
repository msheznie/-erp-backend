<?php

namespace App\Services;

use App\Models\POSMappingMaster;
use App\Models\POSSTAGInvoice;
use App\Models\POSTransLog;
use App\Models\POSTransStatus;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class POSService
{


    public function __construct()
    {
    }

    public function getMappingData($request)
    {
        if (!Schema::hasTable('pos_mapping_master') || !Schema::hasTable('pos_mapping_detail')) {
            return [
                'success' => false,
                'message' => 'Mapping table does not exist',
                'data' => null
            ];
        }

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
            if (!Schema::hasTable($group['table'])) {
                return $group['table'] . ' table does not exists';
            } else if (!Schema::hasTable($group['source_table_name'])) {
                return $group['table'] . ' sourse table does not exists';
            } else if ($request->input('data.' . $group['key']) == null) {
                return $group['key'] . ' records does not exists';
            } else if ($group['model_name'] == null) {
                return $group['table'] . ' model name does not exists in mapping detail table';
            } else if ($group['source_model_name'] == null) {
                return $group['source_table_name'] . ' model name does not exists in mapping detail table';
            }
        });

        $filtered = $MappingDataArrFilter->filter(function ($value, $key) {
            return $value != null;
        })->values()->all();


        if (count($filtered) > 0) {
            return [
                'success' => false,
                'message' => $filtered,
                'data' => null
            ];
        } else {
            return  self::insertStagingTable($getMapping['mapping_detail'], $request, $getMapping['id']);
        }
    }

    public function insertStagingTable($MappingDetail, $request, $mapping_id)
    {
        $logExist = POSTransLog::whereIn('status', [1, 4])->first();
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
                $namespacedModel::insert($dataUpdate2->toArray());
            });
            DB::commit();
            self::LogTransactionCreate($mapping_id, 2, 'u', $LogTransactionCreate['data']);
            $sourceTableCreate = self::createSourceTransaction($mapping_id, $MappingDetail, $LogTransactionCreate['data']);
            return ['success' => $sourceTableCreate['success'], 'message' => $sourceTableCreate['message'], 'data' =>  $sourceTableCreate['data']];
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);
            self::LogTransactionCreate($mapping_id, 3, 'u', $LogTransactionCreate['data']);
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }

    public function LogTransactionCreate($mapping_id, $status, $type, $transactionId = null)
    {
        DB::beginTransaction();
        try {
            $data['pos_mapping_id'] = $mapping_id;
            $data['status'] = $status;
            if ($type == 'c') {
                $result = POSTransLog::create($data);
            } else {
                $result =  POSTransLog::find($transactionId)
                ->update($data);
            }
            DB::commit();
            if ($result) {
                return ['success' => true, 'data' => $result->id, 'message' => 'POS transaction log created successfully'];
            }
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }

    public function createSourceTransaction($mapping_id, $MappingDetail, $logId)
    {
        DB::beginTransaction();
        self::LogTransactionCreate($mapping_id, 4, 'u', $logId);
        try {
            collect($MappingDetail)->map(function ($group) use ($mapping_id) {
                $namespacedModel = 'App\Models\\' . $group["model_name"];
                $namespacedModelSource = 'App\Models\\' . $group["source_model_name"];
                $data = $namespacedModel::get()->toArray();
                $dataUpdate2 = collect($data)->map(function ($group2) use ($mapping_id) {
                    $group2['mapping_master_id']  = $mapping_id;
                    return $group2;
                });
                DB::commit();
                $namespacedModelSource::insert($dataUpdate2->toArray());
                $namespacedModel::truncate();
            });
            self::LogTransactionCreate($mapping_id, 5, 'u', $logId);
            return ['success' => true, 'message' => 'Successfully synced', 'data' =>  2];
        } catch (\Throwable $e) {
            DB::rollback();
            Log::error($e);
            collect($MappingDetail)->map(function ($group) {
                $namespacedModel = 'App\Models\\' . $group["model_name"];
                $namespacedModel::truncate();
            });
            self::LogTransactionCreate($mapping_id, 6, 'u', $logId);
            return ['success' => false, 'data' => null, 'message' => $e->getMessage()];
        }
    }
}

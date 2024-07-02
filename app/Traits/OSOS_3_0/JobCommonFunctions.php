<?php
namespace App\Traits\OSOS_3_0;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

trait JobCommonFunctions{
    function insertToLogTb($logData, $type, $desc, $companyId){

        $data = [
            'company_id'=> $companyId,
            'module'=> 'OSOS 3.0',
            'description'=> $desc,
            'scenario_id'=> 0,
            'processed_for'=> Carbon::now()->format('Y-m-d H:i:s'),
            'logged_at'=> Carbon::now()->format('Y-m-d H:i:s'),
            'log_type'=> $type,
            'log_data'=> json_encode($logData),
        ];

        DB::table('job_logs')->insert($data);
    }

    function commonValidations($req){

        if(empty($req->postType)){
            $error = 'Post request type is required';
            return ['status' =>false, 'message'=> $error];

        }

        if($this->thirdParty->api_key == null){
            $error = 'Api key not found';
            return ['status' =>false, 'message'=> $error];
        }

        if ($this->thirdParty->api_external_key == null) {
            $error = 'Api external key not found';
            return ['status' =>false, 'message'=> $error];
        }

        if($this->thirdParty->api_external_key == null){
            $error = 'Api external url not found';
            return ['status' =>false, 'message'=> $error];
        }

        return ['status' =>true, 'message'=> 'success'];
    }

    function getUrl($funcName) {
        switch ($funcName) {
            case 'location':
                $this->url = ($this->postType === 'DELETE')
                    ? "hrm/api/Locations?locationId='{$this->locationUuid}'"
                    : 'hrm/api/Locations';
                break;
            default:
                $this->url = '';
                break;
        }
    }

    function getPivotTableId($id){
        $this->pivotTableId =  DB::table('pivot_tbl_reference')
            ->where('id', $id)
            ->value('id');
    }

    function getOperation(){
        $operations = [
            'POST' => 'save',
            'PUT' => 'update',
            'DELETE' => 'delete',
        ];

        $this->operation = $operations[$this->postType] ?? null;
    }

    function getReferenceId(){
        $this->locationUuId = DB::table('third_party_pivot_record')
            ->where('pivot_table_id', $this->pivotTableId)
            ->where('system_id', $this->id)
            ->where('third_party_sys_det_id', $this->detailId)
            ->value('reference_id');
    }

    function capture400Err($msgBody){
        return $this->insertToLogTb($msgBody, 'error', 'Location', $this->companyId);
    }

    function insertOrUpdateThirdPartyPivotTable($referenceId)
    {
        if ($this->postType === 'POST') {
            $existingRecord = $this->checkRecordExits();

            if (!empty($existingRecord)) {
                return True;
            }

            return $this->saveThirdPivotTable($referenceId);
        }
    }

    function saveThirdPivotTable($referenceId){
        return DB::table('third_party_pivot_record')->insert([
            'third_party_sys_det_id' => $this->detailId,
            'pivot_table_id' => $this->pivotTableId,
            'system_id' => $this->id,
            'reference_id' => $referenceId
        ]);
    }

    function checkRecordExits(){
        return DB::table('third_party_pivot_record')
            ->where('third_party_sys_det_id', $this->detailId)
            ->where('pivot_table_id', $this->pivotTableId)
            ->where('system_id', $this->id)
            ->first();
    }
}
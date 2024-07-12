<?php

namespace App\Services\OSOS_3_0;

use App\Jobs\OSOS_3_0\DesignationWebHook;
use App\Traits\OSOS_3_0\JobCommonFunctions;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class DesignationService
{
    protected $apiExternalKey;
    protected $apiExternalUrl;
    protected $designationData;
    protected $thirdPartyData;
    protected $id;
    protected $postType;
    protected $url;
    protected $detailId;
    protected $apiKey;
    protected $dataBase;

    protected $companyId;
    protected $operation;
    protected $pivotTableId;
    protected $masterUuId;

    use JobCommonFunctions;

    public function __construct($dataBase, $id, $postType, $thirdPartyData){

        $this->dataBase = $dataBase;
        $this->postType = trim(strtoupper($postType),'"');
        $this->id = $id;
        $this->detailId = $thirdPartyData['id'];
        $this->apiKey = $thirdPartyData['api_key'];
        $this->apiExternalKey = $thirdPartyData['api_external_key'];
        $this->apiExternalUrl = $thirdPartyData['api_external_url'];
        $this->companyId = $thirdPartyData['company_id'];
        $this->thirdPartyData = $thirdPartyData;

        $this->getOperation();
        $this->getPivotTableId(2);
        $this->getDesignationData();
        $this->getUrl('designation');
    }

    function execute(){
        try {

            $valResp =$this->validateApiResponse();

            if(!$valResp['status']){
                return $this->insertToLogTb($valResp['message'], 'error', 'Designation', $this->companyId);
            }

            $msg = "Designation about to trigger: " . $this->id. ' - '. $this->designationData['name'];
            $this->insertToLogTb($msg, 'info', 'Designation', $this->companyId);

            $client = new Client();
            $headers = [
                'content-type' => 'application/json',
                'auth-key' =>  $this->apiExternalKey,
                'menu-id' =>  'defualt'
            ];

            $res = $client->request("$this->postType", $this->apiExternalUrl . $this->url, [
                'headers' => $headers,
                'body' => json_encode($this->designationData)
            ]);

            $statusCode = $res->getStatusCode();
            $body = $res->getBody()->getContents();

            if (in_array($statusCode, [200, 201])) {

                $je = json_decode($body, true);

                if(!isset($je['id'])){
                    $msg = 'Cannot Find Reference id from response';
                    return $this->insertToLogTb($msg, 'error', 'Designation', $this->companyId);
                }

                $this->insertOrUpdateThirdPartyPivotTable($je['id']);
                $msg = "Api Designation {$this->operation} successfully finished";
                $this->insertToLogTb($msg, 'info', 'Designation', $this->companyId);
                return ['status' => true, 'message' => $msg, 'code' => $statusCode];

            }

            if ($statusCode == 400) {
                $msg = $res->getBody();
                $this->capture400Err(json_decode($msg), 'Designation');
                return ['status' => false, 'message' => $msg, 'code' => $statusCode];
            }

        } catch (\Exception $e) {

            $exStatusCode = $e->getCode();
            if ($exStatusCode == 400) {
                $msg = $e->getMessage();
                return $this->capture400Err($msg, 'Designation');
            }

            $msg = "Exception \n";
            $msg .= "operation : ".$this->operation."\n";
            $msg .= "message : ".$e->getMessage()."\n";
            $msg .= "file : ".$e->getFile()."\n";
            $msg .= "line no : ".$e->getLine()."\n";
            $this->insertToLogTb($msg, 'error', 'Designation', $this->companyId);
            return ['status' => false, 'message' => $msg, 'code' => $exStatusCode];
        }
    }

    function validateApiResponse(){

        if(empty($this->id)){
            $error = 'Designation id is required';
            return ['status' =>false, 'message'=> $error];
        }

        if(empty($this->pivotTableId)){
            $error = 'Pivot table reference not found check pivot_tbl_reference.id';
            return ['status' =>false, 'message'=> $error];
        }

        if (empty($this->designationData)) {
            $error = 'Designation not found';
            return ['status' =>false, 'message'=> $error];
        }

        if($this->postType != 'POST'){
            if(empty($this->designationData['id'])){
                $error = 'Reference id not found';
                return ['status' =>false, 'message'=> $error];
            }
        }

        if(empty($this->designationData['code'])){
            $error = 'Designation code not found';
            return ['status' =>false, 'message'=> $error];
        }
        if(empty($this->validateCompanyReference())){
            $error = 'Company reference not found';
            return ['status' =>false, 'message'=> $error];
        }

        return ['status' =>true, 'message'=> 'success'];
    }

    function validateCompanyReference() {
        return DB::table('pivot_tbl_reference')
            ->where('id', 5)
            ->value('id');
    }

    function getDesignationData()
    {
        $data = DB::table('srp_designation')
            ->selectRaw("DesignationID as id, DesDescription as Name, '' as Description,
                CASE 
                    WHEN isDeleted = 1 THEN 2 
                    WHEN is_active = 0 THEN 0 
                    WHEN is_active = 1 THEN 1 
                END as Status, 
                isDeleted as IsDeleted,
                Erp_companyID as companyId")
            ->where('DesignationID', $this->id)
            ->first();

        if(empty($data)){
            return;
        }

        $this->designationData = [
            "code" => $data->id,
            "name" => $data->Name,
            "description" => $data->Description,
            "status" => $data->Status,
            "isDeleted" => $data->IsDeleted,
            "companyId" => $this->getOtherReferenceId($data->companyId, 5)
        ];

        if($this->postType != "POST"){
            $this->getReferenceId();
            $this->designationData['id'] = $this->masterUuId;
        }
    }
}
